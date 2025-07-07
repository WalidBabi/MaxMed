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
use Illuminate\Support\Facades\Storage;

class InquiryController extends Controller
{
    /**
     * Display inquiries for the current supplier
     */
    public function index(Request $request)
    {
        $supplierId = Auth::id();
        
        // Build query for new supplier inquiries
        $newInquiriesQuery = SupplierInquiry::with(['product.images', 'product.primaryImage', 'items.product.images', 'items.product.primaryImage', 'supplierResponses' => function($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            }, 'quotations' => function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }])
            ->whereHas('supplierResponses', function($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            })
            ->orderBy('created_at', 'desc');

        // Build query for legacy quotation requests
        $legacyInquiriesQuery = QuotationRequest::with(['product.images', 'product.primaryImage', 'supplierQuotations' => function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }])
            ->where('supplier_id', $supplierId)
            ->orderBy('created_at', 'desc');

        // Apply search filter to new inquiries
        if ($request->filled('search')) {
            $search = $request->input('search');
            $newInquiriesQuery->where(function($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('product_description', 'like', "%{$search}%")
                    ->orWhere('product_specifications', 'like', "%{$search}%");
            });
            
            // Apply search filter to legacy inquiries
            $legacyInquiriesQuery->where(function($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('special_requirements', 'like', "%{$search}%")
                    ->orWhere('request_number', 'like', "%{$search}%")
                    ->orWhere('product_description', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::parse($request->input('date_from'))->startOfDay();
            $to = Carbon::parse($request->input('date_to'))->endOfDay();
            $newInquiriesQuery->whereBetween('created_at', [$from, $to]);
            $legacyInquiriesQuery->whereBetween('created_at', [$from, $to]);
        }

        // Get all inquiries
        $newInquiries = $newInquiriesQuery->get();
        $legacyInquiries = $legacyInquiriesQuery->get();

        // Combine and sort all inquiries
        $allInquiries = $newInquiries->concat($legacyInquiries)->sortByDesc('created_at');

        // Apply status filter if provided
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            $allInquiries = $allInquiries->filter(function($inquiry) use ($status, $supplierId) {
                if ($inquiry instanceof QuotationRequest) {
                    // Legacy QuotationRequest status handling
                    $quotation = $inquiry->supplierQuotations->where('supplier_id', $supplierId)->first();
                    if ($quotation) {
                        if ($quotation->status === 'approved' || $quotation->status === 'accepted') {
                            return $status === 'accepted';
                        } else {
                            return $status === 'quoted';
                        }
                    } else {
                        return $inquiry->supplier_response === $status;
                    }
                } else {
                    // New SupplierInquiry status handling
                    $response = $inquiry->supplierResponses->where('user_id', $supplierId)->first();
                    $quotation = $inquiry->quotations->where('supplier_id', $supplierId)->first();
                    
                    if ($quotation) {
                        if ($quotation->status === 'approved' || $quotation->status === 'accepted') {
                            return $status === 'accepted';
                        } else {
                            return $status === 'quoted';
                        }
                    } else {
                        return $response && $response->status === $status;
                    }
                }
            });
        }

        // Get categories for filter
        $categories = Category::whereHas('suppliers', function($query) use ($supplierId) {
            $query->where('users.id', $supplierId)
                ->whereHas('activeSupplierCategories');
        })->get();

        return View::make('supplier.inquiries.index', [
            'allInquiries' => $allInquiries,
            'status' => $request->get('status', 'all'),
            'categories' => $categories
        ]);
    }

    /**
     * Show specific inquiry details
     */
    public function show($inquiry)
    {
        $inquiryModel = $this->findInquiry($inquiry);
        
        // Always mark as viewed when supplier clicks "View" (except for quoted inquiries)
        if ($inquiryModel instanceof QuotationRequest) {
            // For legacy inquiries, mark as available if not already quoted
            // Note: legacy system doesn't have 'viewed' status, so we use 'available'
            if ($inquiryModel->supplier_response !== 'available') {
                $inquiryModel->update([
                    'supplier_response' => 'available',
                    'supplier_responded_at' => now()
                ]);
            }
        } else {
            // For new SupplierInquiry
            $response = $inquiryModel->supplierResponses->where('user_id', auth()->id())->first();
            if ($response && $response->status !== 'quoted') {
                // Mark as viewed regardless of current status (pending, viewed, etc.)
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
                // Handle legacy QuotationRequest (authorization already checked in findInquiry)
                $inquiryModel->update([
                    'supplier_response' => 'not_available',
                    'supplier_responded_at' => now(),
                    'status' => 'supplier_responded',
                    'supplier_notes' => $validated['reason'] ?? 'Product not available',
                ]);
            } else {
                // Handle new SupplierInquiry (authorization already checked in findInquiry)
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
        
        // Authorization is already checked in findInquiry method
        if ($inquiryModel instanceof QuotationRequest) {
            // Check if quotation already exists for legacy system
            $existingQuotation = SupplierQuotation::where('quotation_request_id', $inquiryModel->id)
                ->where('supplier_id', auth()->id())
                ->first();
        } else {
            // Check if quotation already exists for new system
            $existingQuotation = SupplierQuotation::where('supplier_inquiry_id', $inquiryModel->id)
                ->where('supplier_id', auth()->id())
                ->first();
        }

        // Get supplier information to determine default currency
        $supplier = auth()->user();
        $supplierInfo = $supplier->supplierInformation;
        $defaultCurrency = 'AED'; // Default currency
        
        // If supplier is from China, set currency to CNY
        if ($supplierInfo && $supplierInfo->country && strtolower($supplierInfo->country) === 'china') {
            $defaultCurrency = 'CNY';
        }

        return view('supplier.inquiries.quotation-form', [
            'inquiry' => $inquiryModel, 
            'existingQuotation' => $existingQuotation,
            'defaultCurrency' => $defaultCurrency
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
            $supplierId = Auth::id();
            
            // Check if supplier has already submitted a quotation for this inquiry
            $existingQuotation = \App\Models\SupplierQuotation::where('supplier_inquiry_id', $inquiryModel->id)
                ->where('supplier_id', $supplierId)
                ->first();
            
            if ($existingQuotation) {
                return redirect()->back()->withInput()->with('error', 'You have already submitted a quotation for this inquiry.');
            }

            // MULTI-PRODUCT: If inquiry has items, expect items[] in request
            if ($inquiryModel->items && $inquiryModel->items->count() > 0 && $request->has('items')) {
                // Check if this is a PDF-only inquiry
                $hasAttachments = $inquiryModel->attachments && is_array($inquiryModel->attachments) && count($inquiryModel->attachments) > 0;
                
                // Check if the inquiry has any product information
                $hasMainProductInfo = ($inquiryModel->product_id && $inquiryModel->product && $inquiryModel->product->name) || 
                                      $inquiryModel->product_name || 
                                      $inquiryModel->product_description;
                
                // Check if any items have product information
                $hasItemsProductInfo = false;
                if ($inquiryModel->items && $inquiryModel->items->count() > 0) {
                    foreach ($inquiryModel->items as $inquiryItem) {
                        if (($inquiryItem->product_id && $inquiryItem->product && $inquiryItem->product->name) || 
                            $inquiryItem->product_name || 
                            $inquiryItem->product_description) {
                            $hasItemsProductInfo = true;
                            break;
                        }
                    }
                }
                
                // PDF-only inquiry: has attachments but no product information
                $isPdfOnly = $hasAttachments && !$hasMainProductInfo && !$hasItemsProductInfo;
                
                // Create one quotation for all items
                $referenceNumber = $inquiryModel->reference_number ?? 'INQ-' . str_pad($inquiryModel->id, 6, '0', STR_PAD_LEFT);
                $quotationNumber = str_replace(['INQ-', 'QR-'], 'QT-', $referenceNumber) . '-S' . $supplierId;
                
                $quotationData = [
                    'supplier_inquiry_id' => $inquiryModel->id,
                    'supplier_id' => $supplierId,
                    'status' => 'submitted',
                    'quotation_number' => $quotationNumber,
                ];
                
                $quotation = \App\Models\SupplierQuotation::create($quotationData);
                
                // Create quotation items for each product
                foreach ($request->input('items') as $itemId => $itemData) {
                    $validated = [
                        'unit_price' => $itemData['unit_price'] ?? null,
                        'shipping_cost' => $itemData['shipping_cost'] ?? null,
                        'size' => $itemData['size'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                    ];
                    
                    // Validate unit price based on inquiry type
                    if ($isPdfOnly) {
                        // For PDF-only inquiries, unit_price can be 0 or null
                        if ($validated['unit_price'] !== null && (!is_numeric($validated['unit_price']) || $validated['unit_price'] < 0)) {
                            throw new \Exception('Unit price must be a positive number or zero for PDF-only inquiries.');
                        }
                        // Set default values for PDF-only inquiries
                        $validated['unit_price'] = $validated['unit_price'] ?? 0;
                        $validated['currency'] = $itemData['currency'] ?? 'AED';
                    } else {
                        // For regular inquiries, unit_price is required
                        if (!is_numeric($validated['unit_price']) || $validated['unit_price'] < 0) {
                            throw new \Exception('Unit price is required and must be a positive number for all products.');
                        }
                    }
                    
                    // Handle file uploads for this item
                    $attachments = [];
                    if ($request->hasFile("items.$itemId.attachments")) {
                        foreach ($request->file("items.$itemId.attachments") as $file) {
                            $path = $file->store('supplier_quotations', 'public');
                            $attachments[] = [
                                'name' => $file->getClientOriginalName(),
                                'path' => $path
                            ];
                        }
                    }
                    
                    $inquiryItem = $inquiryModel->items->find($itemId);
                    
                    $quotationItemData = [
                        'supplier_quotation_id' => $quotation->id,
                        'supplier_inquiry_item_id' => $itemId,
                        'product_id' => $inquiryItem?->product_id,
                        'product_name' => $inquiryItem?->product_name,
                        'product_description' => $inquiryItem?->product_description,
                        'unit_price' => $validated['unit_price'],
                        'currency' => $validated['currency'] ?? 'AED',
                        'shipping_cost' => $validated['shipping_cost'],
                        'size' => $validated['size'],
                        'notes' => $validated['notes'],
                        'quantity' => $inquiryItem?->quantity,
                        'attachments' => $attachments,
                        'sort_order' => $inquiryItem?->sort_order ?? 0,
                    ];
                    
                    \App\Models\SupplierQuotationItem::create($quotationItemData);
                }
                
                // Mark supplier response as quoted
                $response = $inquiryModel->supplierResponses->where('user_id', $supplierId)->first();
                if ($response) {
                    $response->update([
                        'status' => 'quoted',
                        'viewed_at' => $response->viewed_at ?? now()
                    ]);
                }
                
                // Notify admins about the new quotation
                $this->notifyAdmins($quotation);
                
            } else {
                // SINGLE PRODUCT
                // Check if this is a PDF-only inquiry
                $hasAttachments = $inquiryModel->attachments && is_array($inquiryModel->attachments) && count($inquiryModel->attachments) > 0;
                $hasProductInfo = ($inquiryModel->product_id && $inquiryModel->product && $inquiryModel->product->name) || 
                                 $inquiryModel->product_name || 
                                 $inquiryModel->product_description;
                $isPdfOnly = $hasAttachments && !$hasProductInfo;
                
                $quotationData = $request->validate([
                    'unit_price' => $isPdfOnly ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
                    'currency' => $isPdfOnly ? 'nullable|string|in:AED,CNY,USD,EUR' : 'required|string|in:AED,CNY,USD,EUR',
                    'shipping_cost' => 'nullable|numeric|min:0',
                    'size' => 'nullable|string|max:255',
                    'notes' => 'nullable|string|max:1000',
                ]);
                $quotationData['supplier_id'] = $supplierId;
                $referenceNumber = $inquiryModel instanceof \App\Models\QuotationRequest
                    ? 'QR-' . str_pad($inquiryModel->id, 6, '0', STR_PAD_LEFT)
                    : ($inquiryModel->reference_number ?? 'INQ-' . str_pad($inquiryModel->id, 6, '0', STR_PAD_LEFT));
                $quotationData['quotation_number'] = str_replace(['INQ-', 'QR-'], 'QT-', $referenceNumber) . '-S' . $supplierId;
                
                // Handle file uploads
                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('supplier_quotations', 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path
                        ];
                    }
                }
                $quotationData['attachments'] = $attachments ? json_encode($attachments) : null;
                
                if ($inquiryModel instanceof \App\Models\QuotationRequest) {
                    $quotationData['quotation_request_id'] = $inquiryModel->id;
                    $quotation = \App\Models\SupplierQuotation::create($quotationData);
                    $inquiryModel->update([
                        'status' => 'quoted',
                        'supplier_responded_at' => now()
                    ]);
                } else {
                    $response = $inquiryModel->supplierResponses->where('user_id', $supplierId)->first();
                    if (!$response) {
                        abort(404, 'Supplier response not found.');
                    }
                    $quotationData['supplier_inquiry_id'] = $inquiryModel->id;
                    $quotationData['supplier_inquiry_response_id'] = $response->id;
                    $quotation = \App\Models\SupplierQuotation::create($quotationData);
                    $response->update([
                        'status' => 'quoted',
                        'viewed_at' => $response->viewed_at ?? now()
                    ]);
                }
                
                // Notify admins about the new quotation
                $this->notifyAdmins($quotation);
            }
            
            DB::commit();
            return redirect()->route('supplier.inquiries.index')->with('success', '✅ Quotation submitted successfully! Your response has been sent to the customer and our team has been notified.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error submitting quotation: ' . $e->getMessage());
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
            // Try to find as SupplierInquiry first (new system)
            $inquiryModel = SupplierInquiry::with(['product.images', 'product.primaryImage', 'items.product.images', 'items.product.primaryImage', 'supplierResponses' => function($query) {
                $query->where('user_id', auth()->id());
            }, 'quotations' => function($query) {
                $query->where('supplier_id', auth()->id());
            }])->find($inquiry);
            
            if ($inquiryModel) {
                // Check if supplier has access to this inquiry
                $hasAccess = $inquiryModel->supplierResponses->where('user_id', auth()->id())->isNotEmpty();
                if (!$hasAccess) {
                    abort(403, 'Unauthorized access to this inquiry.');
                }
                return $inquiryModel;
            }
            
            // Try to find as QuotationRequest (legacy system)
            $inquiryModel = QuotationRequest::with(['product.images', 'product.primaryImage', 'user', 'supplierQuotations' => function($query) {
                $query->where('supplier_id', auth()->id());
            }])->find($inquiry);
            
            if ($inquiryModel) {
                // Check if supplier has access to this legacy inquiry
                if ($inquiryModel->supplier_id !== auth()->id()) {
                    abort(403, 'Unauthorized access to this inquiry.');
                }
                return $inquiryModel;
            }
            
            abort(404, 'Inquiry not found.');
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
                // Handle legacy QuotationRequest (authorization already checked in findInquiry)
                $inquiryModel->update([
                    'supplier_response' => $validated['status'] === 'not_available' ? 'not_available' : 'pending',
                    'supplier_responded_at' => now(),
                    'status' => 'supplier_responded'
                ]);
            } else {
                // Handle new SupplierInquiry (authorization already checked in findInquiry)
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
                // Handle legacy QuotationRequest (authorization already checked in findInquiry)
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
                // Handle new SupplierInquiry (authorization already checked in findInquiry)
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