<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierQuotation;
use App\Models\QuotationRequest;
use App\Models\SupplierInquiry;
use App\Models\User;
use App\Notifications\QuotationApprovedNotification;
use App\Notifications\QuotationRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class QuotationController extends Controller
{
    /**
     * Display a listing of quotations
     */
    public function index(Request $request)
    {
        $query = SupplierQuotation::with(['supplier', 'product', 'quotationRequest', 'supplierInquiry'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by currency
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                      $supplierQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $quotations = $query->paginate(15);

        // Get statistics for dashboard cards
        $stats = [
            'total' => SupplierQuotation::count(),
            'pending' => SupplierQuotation::where('status', 'submitted')->count(),
            'approved' => SupplierQuotation::where('status', 'accepted')->count(),
            'rejected' => SupplierQuotation::where('status', 'rejected')->count(),
        ];

        return view('admin.quotations.index', compact('quotations', 'stats'));
    }

    /**
     * Show specific quotation details
     */
    public function show(SupplierQuotation $quotation)
    {
        $quotation->load(['supplier', 'product', 'quotationRequest', 'supplierInquiry']);
        
        return view('admin.quotations.show', compact('quotation'));
    }

    /**
     * Approve a quotation
     */
    public function approve(Request $request, SupplierQuotation $quotation)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $quotation->update([
                'status' => SupplierQuotation::STATUS_ACCEPTED,
                'admin_notes' => $validated['notes'] ?? null,
                'approved_at' => now(),
            ]);

            // Update related inquiry status
            if ($quotation->quotationRequest) {
                $quotation->quotationRequest->update([
                    'status' => 'quote_created'
                ]);
            } elseif ($quotation->supplierInquiry) {
                $quotation->supplierInquiry->update([
                    'status' => 'quoted'
                ]);
                
                // Update the supplier response status to 'accepted'
                if ($quotation->supplier_inquiry_response_id) {
                    $response = $quotation->supplierInquiryResponse;
                    if ($response) {
                        $response->update(['status' => \App\Models\SupplierInquiryResponse::STATUS_ACCEPTED]);
                    }
                } else {
                    // If no response ID, find the response by supplier_inquiry_id and user_id
                    $response = \App\Models\SupplierInquiryResponse::where('supplier_inquiry_id', $quotation->supplier_inquiry_id)
                        ->where('user_id', $quotation->supplier_id)
                        ->first();
                    if ($response) {
                        $response->update(['status' => \App\Models\SupplierInquiryResponse::STATUS_ACCEPTED]);
                    }
                }
            }

            // Send notification to supplier
            $quotation->supplier->notify(new QuotationApprovedNotification($quotation));

            DB::commit();

            return redirect()->back()->with('success', 'Quotation approved successfully. Supplier has been notified.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve quotation.');
        }
    }



    /**
     * Bulk action for quotations
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'quotation_ids' => 'required|array|min:1',
            'quotation_ids.*' => 'exists:supplier_quotations,id',
            'action' => 'required|in:approve',
            'notes' => 'nullable|string|max:1000',
        ]);

        $quotations = SupplierQuotation::whereIn('id', $validated['quotation_ids'])->get();
        
        if ($quotations->isEmpty()) {
            return redirect()->back()->with('error', 'No valid quotations found for bulk action.');
        }

        DB::beginTransaction();
        try {
            $successCount = 0;
            
            foreach ($quotations as $quotation) {
                $quotation->update([
                    'status' => SupplierQuotation::STATUS_ACCEPTED,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'admin_notes' => $validated['notes'] ?? null,
                ]);
                
                // Update related inquiry status
                if ($quotation->quotationRequest) {
                    $quotation->quotationRequest->update([
                        'status' => 'quote_created'
                    ]);
                } elseif ($quotation->supplierInquiry) {
                    $quotation->supplierInquiry->update([
                        'status' => 'quoted'
                    ]);
                    
                    // Update the supplier response status to 'accepted'
                    if ($quotation->supplier_inquiry_response_id) {
                        $response = $quotation->supplierInquiryResponse;
                        if ($response) {
                            $response->update(['status' => \App\Models\SupplierInquiryResponse::STATUS_ACCEPTED]);
                        }
                    } else {
                        // If no response ID, find the response by supplier_inquiry_id and user_id
                        $response = \App\Models\SupplierInquiryResponse::where('supplier_inquiry_id', $quotation->supplier_inquiry_id)
                            ->where('user_id', $quotation->supplier_id)
                            ->first();
                        if ($response) {
                            $response->update(['status' => \App\Models\SupplierInquiryResponse::STATUS_ACCEPTED]);
                        }
                    }
                }
                
                $quotation->supplier->notify(new QuotationApprovedNotification($quotation));
                $successCount++;
            }

            DB::commit();

            return redirect()->back()->with('success', 
                "Successfully approved {$successCount} quotation(s). Suppliers have been notified.");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in bulk quotation action: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }

    /**
     * Get quotation workflow status for AJAX requests
     */
    public function workflowStatus(SupplierQuotation $quotation)
    {
        $workflow = [
            'current_status' => $quotation->status,
            'created_at' => $quotation->created_at,
            'submitted_at' => $quotation->created_at, // Same as created for submitted quotations
            'approved_at' => $quotation->approved_at,
            'rejected_at' => $quotation->rejected_at,
            'supplier' => $quotation->supplier->name,
            'quotation_number' => $quotation->quotation_number,
        ];

        return response()->json($workflow);
    }
} 