<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierInquiry;
use App\Models\SupplierQuotation;
use App\Models\QuotationRequest;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SupplierPayment;

class InquiryQuotationController extends Controller
{
    /**
     * Display unified view of inquiries and their quotations
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->get('status');
        $quotationStatus = $request->get('quotation_status');
        $search = $request->get('search');
        
        // Build base query for new supplier inquiries
        $inquiriesQuery = SupplierInquiry::with([
            'product',
            'supplierResponses.supplier',
            'quotations.supplier'
        ])->latest();

        // Apply filters
        if ($search) {
            $inquiriesQuery->where(function($query) use ($search) {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('product_description', 'like', "%{$search}%")
                    ->orWhereHas('product', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $inquiriesQuery->where('status', $status);
        }

        // Get inquiries with pagination
        $inquiries = $inquiriesQuery->paginate(20);

        // Get statistics
        $stats = $this->getStatistics();

        // Get quotations if quotation filter is applied
        $quotations = collect();
        if ($quotationStatus) {
            $quotationsQuery = SupplierQuotation::with(['supplier', 'product', 'supplierInquiry', 'quotationRequest'])
                ->where('status', $quotationStatus)
                ->latest();
            
            if ($search) {
                $quotationsQuery->where(function($query) use ($search) {
                    $query->where('quotation_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }
            
            $quotations = $quotationsQuery->get();
        }

        return view('admin.inquiry-quotations.index', compact(
            'inquiries', 
            'quotations', 
            'stats'
        ));
    }

    /**
     * Show detailed view of specific inquiry with all its quotations
     */
    public function show(SupplierInquiry $inquiry)
    {
        $inquiry->load([
            'product',
            'supplierResponses.supplier',
            'quotations.supplier'
        ]);

        // Get all quotations for this inquiry
        $quotations = $inquiry->quotations()->with('supplier')->latest()->get();
        
        // Get supplier response statistics
        $responseStats = [
            'total_sent' => $inquiry->supplierResponses->count(),
            'viewed' => $inquiry->supplierResponses->where('status', 'viewed')->count(),
            'quoted' => $inquiry->supplierResponses->where('status', 'quoted')->count(),
            'not_available' => $inquiry->supplierResponses->where('status', 'not_available')->count(),
        ];

        return view('admin.inquiry-quotations.show', compact(
            'inquiry', 
            'quotations', 
            'responseStats'
        ));
    }

    /**
     * Show quotation details
     */
    public function showQuotation(SupplierQuotation $quotation)
    {
        // Load the quotation with all necessary relationships
        $quotation->load(['supplier', 'product', 'supplierInquiry', 'quotationRequest']);
        
        return view('admin.inquiry-quotations.quotation-show', compact('quotation'));
    }

    /**
     * Approve a quotation
     */
    public function approveQuotation(Request $request, SupplierQuotation $quotation)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $quotation->update([
                'status' => 'accepted',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'admin_notes' => $validated['notes'] ?? null,
            ]);

            // Update related inquiry status based on all quotations
            if ($quotation->supplierInquiry) {
                $this->updateInquiryStatusBasedOnQuotations($quotation->supplierInquiry);

                // Update the supplier's inquiry response status to 'accepted' for this specific product
                $supplierResponse = $quotation->supplierInquiry->supplierResponses()
                    ->where('user_id', $quotation->supplier_id)
                    ->first();

                if ($supplierResponse) {
                    $supplierResponse->update([
                        'status' => 'accepted'
                    ]);
                }
                
                // CREATE PURCHASE ORDER FOR THIS SPECIFIC PRODUCT QUOTATION
                $purchaseOrder = $this->createPurchaseOrderFromSupplierInquiry($quotation, $validated['notes'] ?? null);
                
                if ($purchaseOrder) {
                    // Create initial payment record
                    $this->createInitialPaymentRecord($purchaseOrder);
                    
                    Log::info("Created purchase order {$purchaseOrder->po_number} from approved supplier inquiry quotation {$quotation->id}");
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Quotation approved successfully. Purchase order created and sent to supplier.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve quotation.');
        }
    }

    /**
     * Update inquiry status based on the status of all its quotations
     */
    private function updateInquiryStatusBasedOnQuotations(SupplierInquiry $inquiry)
    {
        // Get all quotations for this inquiry
        $allQuotations = $inquiry->quotations;
        
        if ($allQuotations->isEmpty()) {
            return;
        }

        // Count quotations by status
        $totalQuotations = $allQuotations->count();
        $acceptedQuotations = $allQuotations->where('status', 'accepted')->count();
        $rejectedQuotations = $allQuotations->where('status', 'rejected')->count();
        $pendingQuotations = $allQuotations->where('status', 'submitted')->count();

        // Determine inquiry status based on quotation statuses
        $newStatus = 'in_progress'; // Default status

        if ($acceptedQuotations > 0) {
            if ($acceptedQuotations === $totalQuotations) {
                // All products have approved quotations
                $newStatus = 'converted';
            } else {
                // Some products have approved quotations, others are pending
                $newStatus = 'partially_quoted';
            }
        } elseif ($rejectedQuotations === $totalQuotations) {
            // All quotations were rejected
            $newStatus = 'cancelled';
        } elseif ($pendingQuotations > 0) {
            // Some quotations are still pending
            $newStatus = 'in_progress';
        }

        // Update inquiry status
        $inquiry->update(['status' => $newStatus]);

        Log::info("Updated inquiry {$inquiry->id} status to '{$newStatus}' based on quotations: {$acceptedQuotations} accepted, {$rejectedQuotations} rejected, {$pendingQuotations} pending out of {$totalQuotations} total");
    }

    /**
     * Create purchase order directly from supplier inquiry quotation (no internal order needed)
     */
    private function createPurchaseOrderFromSupplierInquiry(SupplierQuotation $quotation, $notes = null)
    {
        try {
            // Get inquiry details
            $inquiry = $quotation->supplierInquiry;
            if (!$inquiry) {
                return null;
            }

            // Get supplier information
            $supplier = $quotation->supplier;
            $supplierInfo = $supplier->supplierInformation;

            // Create the purchase order directly (no order_id needed)
            $purchaseOrder = PurchaseOrder::create([
                'order_id' => null, // No customer order needed for supplier inquiries
                'supplier_id' => $supplier->id,
                'quotation_request_id' => $inquiry->id,
                'supplier_quotation_id' => $quotation->id,
                'supplier_name' => $supplierInfo ? $supplierInfo->company_name : $supplier->name,
                'supplier_email' => $supplier->email,
                'supplier_phone' => $supplierInfo->phone_primary ?? null,
                'supplier_address' => $supplierInfo ? $this->formatSupplierAddress($supplierInfo) : null,
                'po_date' => now()->toDateString(),
                'delivery_date_requested' => now()->addDays(14)->toDateString(),
                'description' => "Purchase order for approved supplier inquiry quotation {$quotation->quotation_number} - {$quotation->product->name}",
                'sub_total' => $quotation->unit_price * $inquiry->quantity,
                'shipping_cost' => $quotation->shipping_cost ?? 0,
                'total_amount' => ($quotation->unit_price * $inquiry->quantity) + ($quotation->shipping_cost ?? 0),
                'currency' => $quotation->currency,
                'status' => PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
                'sent_to_supplier_at' => now(),
                'payment_status' => PurchaseOrder::PAYMENT_STATUS_PENDING,
                'payment_due_date' => now()->addDays(30),
                'notes' => $quotation->notes . ($notes ? "\n\nAdmin Notes: " . $notes : ''),
                'terms_conditions' => $this->getDefaultTermsAndConditions(),
                'created_by' => auth()->id()
            ]);

            // Create purchase order items
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $quotation->product_id,
                'item_description' => $quotation->product->name,
                'quantity' => $inquiry->quantity,
                'unit_price' => $quotation->unit_price,
                'line_total' => $quotation->unit_price * $inquiry->quantity,
                'unit_of_measure' => 'pcs',
                'specifications' => $quotation->size ?? 'Standard specifications',
                'sort_order' => 1
            ]);

            return $purchaseOrder;

        } catch (\Exception $e) {
            Log::error('Error creating purchase order from supplier inquiry: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create initial payment record for purchase order
     */
    private function createInitialPaymentRecord(PurchaseOrder $purchaseOrder)
    {
        // Create payment record with pending status
        SupplierPayment::create([
            'purchase_order_id' => $purchaseOrder->id,
            'order_id' => $purchaseOrder->order_id,
            'amount' => $purchaseOrder->total_amount,
            'currency' => $purchaseOrder->currency,
            'payment_date' => now()->addDays(30), // Due in 30 days
            'payment_method' => 'bank_transfer',
            'reference_number' => null,
            'notes' => 'Initial payment record for approved quotation',
            'status' => SupplierPayment::STATUS_PENDING,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Format supplier address for purchase order
     */
    private function formatSupplierAddress($supplierInfo): string
    {
        $address = [];
        if ($supplierInfo->business_address) $address[] = $supplierInfo->business_address;
        if ($supplierInfo->city) $address[] = $supplierInfo->city;
        if ($supplierInfo->state) $address[] = $supplierInfo->state;
        if ($supplierInfo->postal_code) $address[] = $supplierInfo->postal_code;
        if ($supplierInfo->country) $address[] = $supplierInfo->country;
        
        return implode(', ', array_filter($address));
    }

    /**
     * Get default terms and conditions for purchase orders
     */
    private function getDefaultTermsAndConditions(): string
    {
        return "1. Payment Terms: Net 30 days from delivery date
2. Delivery: As per agreed delivery date  
3. Quality: All products must meet specified requirements
4. Returns: Defective items may be returned within 7 days
5. Warranty: Standard manufacturer warranty applies
6. Compliance: All products must comply with UAE regulations";
    }

    /**
     * Bulk approve quotations
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'quotation_ids' => 'required|array|min:1',
            'quotation_ids.*' => 'exists:supplier_quotations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $quotations = SupplierQuotation::whereIn('id', $validated['quotation_ids'])->get();
        
        DB::beginTransaction();
        try {
            $processedInquiries = collect();
            
            foreach ($quotations as $quotation) {
                $quotation->update([
                    'status' => 'accepted',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'admin_notes' => $validated['notes'] ?? null,
                ]);

                // Track inquiries that need status updates
                if ($quotation->supplierInquiry) {
                    $processedInquiries->push($quotation->supplierInquiry);
                }
            }

            // Update status for all affected inquiries
            $processedInquiries->unique('id')->each(function ($inquiry) {
                $this->updateInquiryStatusBasedOnQuotations($inquiry);
            });

            DB::commit();

            return redirect()->back()->with('success', 'Selected quotations approved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error bulk approving quotations: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve quotations.');
        }
    }

    /**
     * Get comprehensive statistics
     */
    private function getStatistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            // Inquiry statistics
            'total_inquiries' => SupplierInquiry::count(),
            'pending_inquiries' => SupplierInquiry::where('status', 'pending')->count(),
            'broadcast_inquiries' => SupplierInquiry::where('status', 'broadcast')->count(),
            'quoted_inquiries' => SupplierInquiry::where('status', 'quoted')->count(),
            'partially_quoted_inquiries' => SupplierInquiry::where('status', 'partially_quoted')->count(),
            'inquiries_today' => SupplierInquiry::whereDate('created_at', $today)->count(),
            'inquiries_this_month' => SupplierInquiry::where('created_at', '>=', $thisMonth)->count(),
            
            // Quotation statistics
            'total_quotations' => SupplierQuotation::count(),
            'pending_quotations' => SupplierQuotation::where('status', 'submitted')->count(),
            'approved_quotations' => SupplierQuotation::where('status', 'accepted')->count(),
            'rejected_quotations' => SupplierQuotation::where('status', 'rejected')->count(),
            'quotations_today' => SupplierQuotation::whereDate('created_at', $today)->count(),
            'quotations_this_month' => SupplierQuotation::where('created_at', '>=', $thisMonth)->count(),
            
            // Combined metrics
            'avg_quotations_per_inquiry' => SupplierInquiry::withCount('quotations')->get()->avg('quotations_count'),
            'response_rate' => $this->calculateResponseRate(),
            
            // Not available responses
            'not_available_responses' => DB::table('supplier_inquiry_responses')
                ->where('status', 'not_available')
                ->count(),
        ];
    }

    /**
     * Calculate supplier response rate
     */
    private function calculateResponseRate()
    {
        $totalSent = DB::table('supplier_inquiry_responses')->count();
        $totalResponded = DB::table('supplier_inquiry_responses')
            ->whereIn('status', ['quoted', 'not_available'])
            ->count();
            
        return $totalSent > 0 ? round(($totalResponded / $totalSent) * 100, 1) : 0;
    }
} 