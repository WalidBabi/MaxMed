<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = PurchaseOrder::with(['order', 'order.delivery']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $purchaseOrders = $query->latest()->paginate(15);
        
        // Get status counts for tabs
        $statusCounts = [
            'all' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', 'draft')->count(),
            'sent_to_supplier' => PurchaseOrder::where('status', 'sent_to_supplier')->count(),
            'acknowledged' => PurchaseOrder::where('status', 'acknowledged')->count(),
            'in_production' => PurchaseOrder::where('status', 'in_production')->count(),
            'completed' => PurchaseOrder::where('status', 'completed')->count(),
        ];

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'statusCounts', 'status'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create(Request $request)
    {
        // Get orders that don't have purchase orders yet
        $availableOrders = Order::whereDoesntHave('purchaseOrder')
            ->latest()
            ->get();

        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::with(['items.product'])->find($request->order_id);
        }

        return view('admin.purchase-orders.create', compact('availableOrders', 'selectedOrder'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'supplier_name' => 'required|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'delivery_date_requested' => 'required|date|after:today',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            
            // Check if PO already exists for this order
            if ($order->purchaseOrder) {
                return redirect()->back()->with('error', 'Purchase order already exists for this order.');
            }

            $po = PurchaseOrder::createFromOrder($order);
            
            // Update with form data
            $po->update([
                'supplier_name' => $request->supplier_name,
                'supplier_email' => $request->supplier_email,
                'supplier_phone' => $request->supplier_phone,
                'delivery_date_requested' => $request->delivery_date_requested,
                'description' => $request->description,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()
                ->route('admin.purchase-orders.show', $po)
                ->with('success', 'Purchase order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create purchase order.');
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['order.items.product', 'items.product', 'payments']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product']);
        return view('admin.purchase-orders.edit', compact('purchaseOrder'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'delivery_date_requested' => 'required|date',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $purchaseOrder->update([
                'supplier_name' => $request->supplier_name,
                'supplier_email' => $request->supplier_email,
                'supplier_phone' => $request->supplier_phone,
                'delivery_date_requested' => $request->delivery_date_requested,
                'description' => $request->description,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'updated_by' => Auth::id()
            ]);

            return redirect()
                ->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update purchase order.');
        }
    }

    /**
     * Send purchase order to supplier
     */
    public function sendToSupplier(PurchaseOrder $purchaseOrder)
    {
        try {
            if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
                return redirect()->back()->with('error', 'Only draft purchase orders can be sent to supplier.');
            }

            $purchaseOrder->markAsSentToSupplier();

            // TODO: Send email to supplier with PO details
            // Mail::to($purchaseOrder->supplier_email)->send(new PurchaseOrderNotification($purchaseOrder));

            return redirect()->back()->with('success', 'Purchase order sent to supplier successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to send purchase order to supplier: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send purchase order to supplier.');
        }
    }

    /**
     * Mark purchase order as acknowledged
     */
    public function markAsAcknowledged(PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->markAsAcknowledged();
            return redirect()->back()->with('success', 'Purchase order marked as acknowledged.');

        } catch (\Exception $e) {
            Log::error('Failed to acknowledge purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(PurchaseOrder::$statuses))
        ]);

        try {
            $oldStatus = $purchaseOrder->status;
            $newStatus = $request->status;

            $purchaseOrder->update([
                'status' => $newStatus,
                'updated_by' => Auth::id()
            ]);

            // Update timestamps based on status
            if ($newStatus === PurchaseOrder::STATUS_SENT_TO_SUPPLIER && !$purchaseOrder->sent_to_supplier_at) {
                $purchaseOrder->update(['sent_to_supplier_at' => now()]);
            }

            if ($newStatus === PurchaseOrder::STATUS_ACKNOWLEDGED && !$purchaseOrder->acknowledged_at) {
                $purchaseOrder->update(['acknowledged_at' => now()]);
            }

            Log::info("Purchase Order {$purchaseOrder->po_number} status changed from {$oldStatus} to {$newStatus}");

            return redirect()->back()->with('success', 'Purchase order status updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update purchase order status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update purchase order status.');
        }
    }

    /**
     * Create payment for purchase order
     */
    public function createPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $purchaseOrder->getRemainingAmount(),
            'payment_method' => 'required|in:' . implode(',', array_keys(SupplierPayment::$paymentMethods)),
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        try {
            $payment = SupplierPayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'order_id' => $purchaseOrder->order_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'transaction_id' => $request->transaction_id,
                'status' => SupplierPayment::STATUS_PENDING,
                'created_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Payment record created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create supplier payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create payment record.');
        }
    }

    /**
     * Delete purchase order
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        try {
            if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
                return redirect()->back()->with('error', 'Only draft purchase orders can be deleted.');
            }

            $poNumber = $purchaseOrder->po_number;
            $purchaseOrder->delete();

            return redirect()
                ->route('admin.purchase-orders.index')
                ->with('success', "Purchase order {$poNumber} deleted successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to delete purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete purchase order.');
        }
    }
} 