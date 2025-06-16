<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display orders for supplier processing
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        // Get orders with their deliveries (no customer info)
        $query = Order::with(['delivery', 'items.product'])
            ->whereHas('delivery')
            ->latest();

        // Filter by delivery status if specified
        if ($status !== 'all') {
            $query->whereHas('delivery', function($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $orders = $query->paginate(15);
        
        // Get status counts for tabs
        $statusCounts = [
            'all' => Order::whereHas('delivery')->count(),
            'pending' => Order::whereHas('delivery', function($q) { $q->where('status', 'pending'); })->count(),
            'processing' => Order::whereHas('delivery', function($q) { $q->where('status', 'processing'); })->count(),
            'in_transit' => Order::whereHas('delivery', function($q) { $q->where('status', 'in_transit'); })->count(),
            'delivered' => Order::whereHas('delivery', function($q) { $q->where('status', 'delivered'); })->count(),
        ];

        return view('supplier.orders.index', compact('orders', 'statusCounts', 'status'));
    }

    /**
     * Show order details and delivery management
     */
    public function show(Order $order)
    {
        $order->load(['delivery', 'items.product']);
        
        // Check if this order has a delivery
        if (!$order->delivery) {
            return redirect()->route('supplier.orders.index')
                ->with('error', 'No delivery found for this order.');
        }

        return view('supplier.orders.show', compact('order'));
    }

    /**
     * Mark order as being processed by supplier
     * This changes delivery status from 'pending' to 'processing'
     */
    public function markAsProcessing(Order $order)
    {
        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            if ($delivery->status !== 'pending' && $delivery->status !== 'in_transit') {
                return redirect()->back()->with('error', 'Order can only be marked as processing from pending or in_transit status.');
            }

            $delivery->update([
                'status' => 'processing',
                'processed_by_supplier_at' => now(),
                'supplier_notes' => 'Order marked as processing by supplier'
            ]);

            Log::info("Order {$order->order_number} marked as processing by supplier. Delivery ID: {$delivery->id}");

            return redirect()->back()->with('success', 'Order marked as processing successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to mark order as processing: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status.');
        }
    }

    /**
     * Mark order back to pending status
     * This changes delivery status from 'processing' to 'pending'
     */
    public function markAsPending(Order $order)
    {
        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            if ($delivery->status !== 'processing') {
                return redirect()->back()->with('error', 'Order can only be marked as pending from processing status.');
            }

            $delivery->update([
                'status' => 'pending',
                'supplier_notes' => 'Order marked back to pending by supplier'
            ]);

            Log::info("Order {$order->order_number} marked as pending by supplier. Delivery ID: {$delivery->id}");

            return redirect()->back()->with('success', 'Order marked as pending successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to mark order as pending: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status.');
        }
    }

    /**
     * Submit documents and mark as sent to carrier
     * This changes delivery status from 'processing' to 'in_transit'
     */
    public function submitDocuments(Request $request, Order $order)
    {
        $request->validate([
            'packing_list' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'commercial_invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'carrier' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'supplier_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            if ($delivery->status !== 'processing') {
                return redirect()->back()->with('error', 'Order must be in processing status to submit documents.');
            }

            // Upload packing list
            $packingListPath = null;
            if ($request->hasFile('packing_list')) {
                $packingListPath = $request->file('packing_list')->store('deliveries/packing-lists', 'public');
            }

            // Upload commercial invoice
            $commercialInvoicePath = null;
            if ($request->hasFile('commercial_invoice')) {
                $commercialInvoicePath = $request->file('commercial_invoice')->store('deliveries/commercial-invoices', 'public');
            }

            // Update delivery with documents and carrier info
            $delivery->update([
                'status' => 'in_transit',
                'carrier' => $request->carrier,
                'tracking_number' => $request->tracking_number,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'packing_list_file' => $packingListPath,
                'commercial_invoice_file' => $commercialInvoicePath,
                'sent_to_carrier_at' => now(),
                'shipped_at' => now(),
                'supplier_notes' => $request->supplier_notes
            ]);

            // Also update the order status to 'shipped'
            $order->update([
                'status' => 'shipped'
            ]);

            Log::info("Order {$order->order_number} documents submitted, delivery marked as in_transit, and order status changed to shipped. Delivery ID: {$delivery->id}");

            // Trigger invoice conversion if applicable
            $delivery->autoConvertToFinalInvoice();

            return redirect()->back()->with('success', 'The product is now on the way to MaxMed.');

        } catch (\Exception $e) {
            Log::error('Failed to submit documents: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit documents: ' . $e->getMessage());
        }
    }

    /**
     * Download packing list file
     */
    public function downloadPackingList(Order $order)
    {
        $delivery = $order->delivery;
        
        if (!$delivery || !$delivery->packing_list_file) {
            return redirect()->back()->with('error', 'Packing list not found.');
        }

        $filePath = storage_path('app/public/' . $delivery->packing_list_file);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Packing list file not found.');
        }

        return response()->download($filePath, 'packing-list-' . $order->order_number . '.pdf');
    }

    /**
     * Download commercial invoice file
     */
    public function downloadCommercialInvoice(Order $order)
    {
        $delivery = $order->delivery;
        
        if (!$delivery || !$delivery->commercial_invoice_file) {
            return redirect()->back()->with('error', 'Commercial invoice not found.');
        }

        $filePath = storage_path('app/public/' . $delivery->commercial_invoice_file);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Commercial invoice file not found.');
        }

        return response()->download($filePath, 'commercial-invoice-' . $order->order_number . '.pdf');
    }

    /**
     * Update delivery status (for testing purposes)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,in_transit,delivered,cancelled'
        ]);

        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            $oldStatus = $delivery->status;
            $newStatus = $request->status;

            $delivery->update(['status' => $newStatus]);

            // Update timestamps based on status
            if ($newStatus === 'processing' && !$delivery->processed_by_supplier_at) {
                $delivery->update(['processed_by_supplier_at' => now()]);
            }

            if ($newStatus === 'in_transit' && !$delivery->sent_to_carrier_at) {
                $delivery->update([
                    'sent_to_carrier_at' => now(),
                    'shipped_at' => now()
                ]);
            }

            if ($newStatus === 'delivered' && !$delivery->delivered_at) {
                $delivery->update(['delivered_at' => now()]);
            }

            Log::info("Delivery {$delivery->id} status changed from {$oldStatus} to {$newStatus}");

            return redirect()->back()->with('success', 'Delivery status updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update delivery status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update delivery status.');
        }
    }
} 