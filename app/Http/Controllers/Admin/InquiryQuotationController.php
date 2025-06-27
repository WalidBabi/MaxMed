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

            // Update related inquiry status
            if ($quotation->supplierInquiry) {
                // Check if this is the first approved quotation for this inquiry
                $approvedQuotationsCount = $quotation->supplierInquiry->quotations()
                    ->where('status', 'accepted')
                    ->count();
                
                if ($approvedQuotationsCount === 1) {
                    $quotation->supplierInquiry->update([
                        'status' => 'quoted'
                    ]);
                }

                // Update the supplier's inquiry response status to 'accepted'
                $supplierResponse = $quotation->supplierInquiry->supplierResponses()
                    ->where('user_id', $quotation->supplier_id)
                    ->first();

                if ($supplierResponse) {
                    $supplierResponse->update([
                        'status' => 'accepted'
                    ]);
                }
                
                // CREATE ORDER AND PURCHASE ORDER FOR APPROVED QUOTATIONS
                // This transitions to order management phase
                $order = $this->createOrderFromQuotation($quotation, $validated['notes'] ?? null);
                if ($order) {
                    // Create Purchase Order
                    $purchaseOrder = $this->createPurchaseOrderFromQuotation($quotation, $order);
                    
                    // Create initial payment record
                    $this->createInitialPaymentRecord($purchaseOrder);
                    
                    Log::info("Created order {$order->order_number} and purchase order {$purchaseOrder->po_number} from approved quotation {$quotation->id}");
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Quotation approved successfully. Purchase order and payment record created.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve quotation.');
        }
    }

    /**
     * Create order from approved quotation
     */
    private function createOrderFromQuotation(SupplierQuotation $quotation, $notes = null)
    {
        try {
            // Get inquiry details
            $inquiry = $quotation->supplierInquiry;
            if (!$inquiry) {
                return null;
            }

            // Create order from the quotation
            $order = Order::create([
                'user_id' => 1, // System user
                'customer_id' => null, // Internal order, no customer
                'total_amount' => $quotation->unit_price * $inquiry->quantity + ($quotation->shipping_cost ?? 0),
                'status' => Order::STATUS_PROCESSING, // Start in processing since quotation is approved
                'shipping_address' => 'Internal Order - MaxMed Office',
                'shipping_city' => 'Dubai',
                'shipping_state' => 'Dubai',
                'shipping_zipcode' => '00000',
                'shipping_phone' => '+971-50-000-0000',
                'notes' => "Order created from approved quotation {$quotation->quotation_number}. " . ($notes ?? ''),
                'requires_quotation' => false, // No longer requires quotation since it's approved
                'quotation_status' => Order::QUOTATION_STATUS_APPROVED
            ]);

            // Create order items
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $quotation->product_id,
                'quantity' => $inquiry->quantity,
                'price' => $quotation->unit_price,
                'variation' => $quotation->size ?? null
            ]);

            return $order;
        } catch (\Exception $e) {
            Log::error('Error creating order from quotation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create purchase order from approved quotation
     */
    private function createPurchaseOrderFromQuotation(SupplierQuotation $quotation, Order $order)
    {
        // Get supplier information
        $supplier = $quotation->supplier;
        $supplierInfo = $supplier->supplierInformation;
        $inquiry = $quotation->supplierInquiry;

        // Create the purchase order
        $purchaseOrder = PurchaseOrder::create([
            'order_id' => $order->id,
            'supplier_id' => $supplier->id,
            'quotation_request_id' => $inquiry->id,
            'supplier_quotation_id' => $quotation->id,
            'supplier_name' => $supplierInfo ? $supplierInfo->company_name : $supplier->name,
            'supplier_email' => $supplier->email,
            'supplier_phone' => $supplierInfo->phone_primary ?? null,
            'supplier_address' => $supplierInfo ? $this->formatSupplierAddress($supplierInfo) : null,
            'po_date' => now()->toDateString(),
            'delivery_date_requested' => now()->addDays(14)->toDateString(),
            'description' => "Purchase order for approved quotation {$quotation->quotation_number} - {$quotation->product->name}",
            'sub_total' => $quotation->unit_price * $inquiry->quantity,
            'shipping_cost' => $quotation->shipping_cost ?? 0,
            'total_amount' => ($quotation->unit_price * $inquiry->quantity) + ($quotation->shipping_cost ?? 0),
            'currency' => $quotation->currency,
            'status' => PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
            'sent_to_supplier_at' => now(),
            'payment_status' => PurchaseOrder::PAYMENT_STATUS_PENDING,
            'payment_due_date' => now()->addDays(30),
            'notes' => $quotation->notes,
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
            foreach ($quotations as $quotation) {
                $quotation->update([
                    'status' => 'accepted',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'admin_notes' => $validated['notes'] ?? null,
                ]);
            }

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