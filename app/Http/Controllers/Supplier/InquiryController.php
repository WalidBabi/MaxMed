<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\QuotationRequest;
use App\Models\SupplierInquiry;
use App\Models\SupplierInquiryResponse;
use App\Models\SupplierQuotation;
use App\Models\User;
use App\Models\Category;
use App\Notifications\QuotationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Mail\AdminQuotationSubmitted;

class InquiryController extends Controller
{
    /**
     * Display inquiries for the current supplier
     */
    public function index(Request $request)
    {
        $supplierId = Auth::id();
        $viewType = $request->get('view', 'pipeline');
        
        // Build query for new supplier inquiries
        $newInquiriesQuery = SupplierInquiry::with(['product', 'supplierResponses' => function($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            }, 'quotations' => function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }])
            ->whereHas('supplierResponses', function($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            })
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $newInquiriesQuery->where(function($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('product_description', 'like', "%{$search}%")
                    ->orWhere('product_specifications', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::parse($request->input('date_from'))->startOfDay();
            $to = Carbon::parse($request->input('date_to'))->endOfDay();
            $newInquiriesQuery->whereBetween('created_at', [$from, $to]);
        }

        // Get all inquiries
        $allInquiries = $newInquiriesQuery->get();

        // Apply status filter if provided
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            $allInquiries = $allInquiries->filter(function($inquiry) use ($status, $supplierId) {
                $response = $inquiry->supplierResponses->where('user_id', $supplierId)->first();
                return $response && $response->status === $status;
            });
        }

        // Group inquiries by status for kanban board
        $groupedInquiries = collect([
            'pending' => $allInquiries->filter(function($inquiry) use ($supplierId) {
                $response = $inquiry->supplierResponses->where('user_id', $supplierId)->first();
                $quotation = $inquiry->quotations->where('supplier_id', $supplierId)->first();
                return !$quotation && $response && $response->status === 'pending';
            }),
            
            'viewed' => $allInquiries->filter(function($inquiry) use ($supplierId) {
                $response = $inquiry->supplierResponses->where('user_id', $supplierId)->first();
                $quotation = $inquiry->quotations->where('supplier_id', $supplierId)->first();
                return !$quotation && $response && $response->status === 'viewed';
            }),
            
            'quoted' => $allInquiries->filter(function($inquiry) use ($supplierId) {
                $quotation = $inquiry->quotations->where('supplier_id', $supplierId)->first();
                return $quotation && !in_array($quotation->status, ['approved', 'accepted']);
            }),
            
            'accepted' => $allInquiries->filter(function($inquiry) use ($supplierId) {
                $quotation = $inquiry->quotations->where('supplier_id', $supplierId)->first();
                return $quotation && in_array($quotation->status, ['approved', 'accepted']);
            }),
            
            'not_available' => $allInquiries->filter(function($inquiry) use ($supplierId) {
                $response = $inquiry->supplierResponses->where('user_id', $supplierId)->first();
                return $response && $response->status === 'not_available';
            })
        ]);

        // Get counts for statistics
        $counts = [
            'all' => $allInquiries->count(),
            'pending' => $groupedInquiries['pending']->count(),
            'viewed' => $groupedInquiries['viewed']->count(),
            'quoted' => $groupedInquiries['quoted']->count(),
            'accepted' => $groupedInquiries['accepted']->count(),
            'not_available' => $groupedInquiries['not_available']->count()
        ];

        // Get categories for filter
        $categories = Category::whereHas('suppliers', function($query) use ($supplierId) {
            $query->where('users.id', $supplierId)
                ->whereHas('activeSupplierCategories');
        })->get();

        return View::make('supplier.inquiries.index', [
            'inquiries' => $groupedInquiries,
            'allInquiries' => $allInquiries,
            'counts' => $counts,
            'status' => $request->get('status', 'all'),
            'viewType' => $viewType,
            'categories' => $categories
        ]);
    }

    /**
     * Show specific inquiry details
     */
    public function show($inquiry)
    {
        $inquiryModel = $this->findInquiry($inquiry);
        
        // Check if inquiry is already quoted
        if ($inquiryModel instanceof QuotationRequest) {
            if ($inquiryModel->supplier_response === 'available') {
                // For legacy inquiries, just return the view without any status changes
                return view('supplier.inquiries.show', ['inquiry' => $inquiryModel]);
            }
        } else {
            $response = $inquiryModel->supplierResponses->where('user_id', auth()->id())->first();
            if ($response && $response->status === 'quoted') {
                // For new inquiries, just return the view without any status changes
                return view('supplier.inquiries.show', ['inquiry' => $inquiryModel]);
            }
            
            // Only mark as viewed if not quoted
            if ($response) {
                $response->markAsViewed();
            }
        }

        return view('supplier.inquiries.show', [
            'inquiry' => $inquiryModel
        ]);
    }

    /**
     * Respond to inquiry - Not Available
     */
    public function respondNotAvailable(Request $request, $inquiry)
    {
        $inquiryModel = $this->findInquiry($inquiry);
        
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            if ($inquiryModel instanceof QuotationRequest) {
                // Handle legacy QuotationRequest
                if ($inquiryModel->supplier_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to this inquiry.');
                }

                $inquiryModel->update([
                    'supplier_response' => 'not_available',
                    'supplier_responded_at' => now(),
                    'status' => 'supplier_responded',
                    'supplier_notes' => $validated['reason'] ?? 'Product not available',
                ]);
            } else {
                // Handle new SupplierInquiry
                $response = $inquiryModel->supplierResponses->where('user_id', auth()->id())->first();
                if ($response) {
                    $response->update([
                        'status' => 'not_available',
                        'notes' => $validated['reason'] ?? 'Product not available',
                        'viewed_at' => $response->viewed_at ?? now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Response submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting not available response: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit response.');
        }
    }

    /**
     * Show quotation form
     */
    public function quotationForm($inquiry)
    {
        $inquiryModel = $this->findInquiry($inquiry);
        
        if ($inquiryModel instanceof QuotationRequest) {
            // Check authorization for legacy
            if ($inquiryModel->supplier_id !== auth()->id()) {
                abort(403, 'Unauthorized access to this inquiry.');
            }
            
            // Check if quotation already exists
            $existingQuotation = SupplierQuotation::where('quotation_request_id', $inquiryModel->id)
                ->where('supplier_id', auth()->id())
                ->first();
        } else {
            // Handle new SupplierInquiry
            $hasAccess = $inquiryModel->supplierResponses->where('user_id', auth()->id())->isNotEmpty();
            if (!$hasAccess) {
                abort(403, 'Unauthorized access to this inquiry.');
            }
            
            $existingQuotation = SupplierQuotation::where('supplier_inquiry_id', $inquiryModel->id)
                ->where('supplier_id', auth()->id())
                ->first();
        }

        return view('supplier.inquiries.quotation-form', [
            'inquiry' => $inquiryModel, 
            'existingQuotation' => $existingQuotation
        ]);
    }

    /**
     * Store a new quotation
     */
    public function store(Request $request, $inquiry)
    {
        DB::beginTransaction();

        try {
            $inquiryModel = $this->findInquiry($inquiry);
            
            // Validate request
            $quotationData = $request->validate([
                'unit_price' => 'required|numeric|min:0',
                'currency' => 'required|string|in:AED,USD,EUR',
                'shipping_cost' => 'nullable|numeric|min:0',
                'size' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Add supplier ID
            $quotationData['supplier_id'] = Auth::id();
            
            // Generate quotation number based on inquiry reference
            if ($inquiryModel instanceof QuotationRequest) {
                $referenceNumber = 'QR-' . str_pad($inquiryModel->id, 6, '0', STR_PAD_LEFT);
            } else {
                $referenceNumber = $inquiryModel->reference_number ?? 'INQ-' . str_pad($inquiryModel->id, 6, '0', STR_PAD_LEFT);
            }
            // Add supplier ID at the end to ensure uniqueness
            $quotationData['quotation_number'] = str_replace(['INQ-', 'QR-'], 'QT-', $referenceNumber) . '-S' . Auth::id();

            if ($inquiryModel instanceof QuotationRequest) {
                // Handle QuotationRequest
                $quotationData['quotation_request_id'] = $inquiryModel->id;
                $quotation = SupplierQuotation::create($quotationData);

                $inquiryModel->update([
                    'status' => 'quoted',
                    'supplier_responded_at' => now()
                ]);
            } else {
                // Handle SupplierInquiry
                $response = $inquiryModel->supplierResponses->where('user_id', Auth::id())->first();
                if (!$response) {
                    abort(404, 'Supplier response not found.');
                }

                $quotationData['supplier_inquiry_id'] = $inquiryModel->id;
                $quotationData['supplier_inquiry_response_id'] = $response->id;
                $quotation = SupplierQuotation::create($quotationData);

                $response->update([
                    'status' => 'quoted',
                    'viewed_at' => $response->viewed_at ?? now()
                ]);
            }

            // Notify admins about the new quotation
            $this->notifyAdmins($quotation);

            DB::commit();

            return redirect()->route('supplier.inquiries.show', $inquiry)->with('success', 'Quotation submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting quotation: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to submit quotation. Please try again.');
        }
    }

    /**
     * Notify admins about new quotation submission
     */
    private function notifyAdmins(SupplierQuotation $quotation)
    {
        try {
            // Get all admin users
            $admins = User::whereHas('role', function($query) {
                $query->where('name', 'admin');
            })->get();

            if ($admins->count() > 0) {
                // Get the inquiry from the quotation
                $inquiry = $quotation->supplierInquiry ?? $quotation->quotationRequest;
                if ($inquiry) {
                    // Send styled email to admins
                    foreach ($admins as $admin) {
                        \Mail::to($admin->email)->send(new AdminQuotationSubmitted($quotation, $inquiry));
                    }
                    
                    Log::info('Quotation submission notification sent to ' . $admins->count() . ' admin(s) for quotation: ' . $quotation->quotation_number);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send quotation submission notification: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to find inquiry by ID in both models
     */
    private function findInquiry($inquiry)
    {
        if (is_numeric($inquiry)) {
            // Try to find as QuotationRequest first
            $inquiryModel = QuotationRequest::with(['product', 'user', 'supplierQuotations'])->find($inquiry);
            
            if (!$inquiryModel) {
                // Try to find as SupplierInquiry
                $inquiryModel = SupplierInquiry::with(['product', 'supplierResponses', 'quotations'])->find($inquiry);
            }
            
            if (!$inquiryModel) {
                abort(404, 'Inquiry not found.');
            }
            
            return $inquiryModel;
        }
        
        return $inquiry;
    }

    /**
     * Update inquiry status via drag and drop
     */
    public function updateStatus(Request $request, $inquiry)
    {
        $inquiryModel = $this->findInquiry($inquiry);
        
        // Get current status
        $currentStatus = $inquiryModel instanceof QuotationRequest 
            ? $inquiryModel->supplier_response
            : $inquiryModel->supplierResponses->where('user_id', Auth::id())->first()->status ?? 'pending';

        // Check if current status is locked
        $lockedStatuses = ['accepted', 'not_available', 'cancelled', 'expired', 'converted'];
        if (in_array($currentStatus, $lockedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update inquiry status. The current status is locked.'
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,viewed,quoted,not_available',
        ]);

        // Prevent changing back to pending after being viewed
        if ($currentStatus === 'viewed' && $validated['status'] === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status back to pending once inquiry has been viewed.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($inquiryModel instanceof QuotationRequest) {
                // Handle legacy QuotationRequest
                if ($inquiryModel->supplier_id !== Auth::id()) {
                    abort(403, 'Unauthorized access to this inquiry.');
                }

                $inquiryModel->update([
                    'supplier_response' => $validated['status'] === 'not_available' ? 'not_available' : 'pending',
                    'supplier_responded_at' => now(),
                    'status' => 'supplier_responded'
                ]);
            } else {
                // Handle new SupplierInquiry
                $response = $inquiryModel->supplierResponses->where('user_id', Auth::id())->first();
                if (!$response) {
                    abort(404, 'Supplier response not found.');
                }

                $response->update([
                    'status' => $validated['status'],
                    'viewed_at' => $response->viewed_at ?? now()
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating inquiry status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    public function markNotAvailable(Request $request, $inquiry)
    {
        $inquiryModel = $this->findInquiry($inquiry);
        
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            if ($inquiryModel instanceof QuotationRequest) {
                // Handle legacy QuotationRequest
                if ($inquiryModel->supplier_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to this inquiry.');
                }

                if ($inquiryModel->supplier_response === 'available') {
                    abort(403, 'Cannot change status after submitting a quotation.');
                }

                $inquiryModel->update([
                    'supplier_response' => 'not_available',
                    'supplier_responded_at' => now(),
                    'status' => 'supplier_responded',
                    'supplier_notes' => $validated['reason'] ?? 'Product not available',
                ]);
            } else {
                // Handle new SupplierInquiry
                $response = $inquiryModel->supplierResponses->where('user_id', auth()->id())->first();
                if (!$response) {
                    abort(404, 'Supplier response not found.');
                }

                if ($response->status === 'quoted') {
                    abort(403, 'Cannot change status after submitting a quotation.');
                }

                $response->update([
                    'status' => 'not_available',
                    'notes' => $validated['reason'] ?? 'Product not available',
                    'viewed_at' => $response->viewed_at ?? now()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Response submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting not available response: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit response.');
        }
    }
} 