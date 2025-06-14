<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the deliveries.
     */
    public function index()
    {
        $deliveries = Delivery::with('order')
            ->latest()
            ->paginate(15);

        return view('admin.deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create(Request $request)
    {
        $selectedOrder = null;
        
        // Check if order is passed as parameter
        if ($request->has('order')) {
            $selectedOrder = Order::find($request->order);
        }
        
        $orders = $selectedOrder
            ? collect([$selectedOrder->id => 'Order #' . $selectedOrder->id . ' - ' . ($selectedOrder->user->name ?? 'N/A')])
            : Order::whereDoesntHave('delivery')
                ->with('user')
                ->get()
                ->mapWithKeys(fn($order) => [$order->id => 'Order #' . $order->id . ' - ' . ($order->user->name ?? 'N/A')]);

        return view('admin.deliveries.create', compact('orders', 'selectedOrder'));
    }

    /**
     * Store a newly created delivery in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id|unique:deliveries,order_id',
            'carrier' => 'required|string|max:100',
            'tracking_number' => 'nullable|string|max:100|unique:deliveries,tracking_number',
            'shipping_cost' => 'required|numeric|min:0',
            'total_weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => ['required', Rule::in(array_keys(Delivery::$statuses))],
        ]);

        $order = Order::findOrFail($validated['order_id']);
        
        $delivery = new Delivery($validated);
        $delivery->shipping_address = $order->shipping_address;
        $delivery->billing_address = $order->billing_address;
        $delivery->tracking_number = $validated['tracking_number'] ?? strtoupper('TRK' . Str::random(10));
        
        if ($validated['status'] === Delivery::STATUS_IN_TRANSIT && !$delivery->shipped_at) {
            $delivery->shipped_at = now();
        }
        
        if ($validated['status'] === Delivery::STATUS_DELIVERED) {
            $delivery->delivered_at = now();
            if (!$delivery->shipped_at) {
                $delivery->shipped_at = now();
            }
        }
        
        $delivery->save();

        return redirect()
            ->route('admin.deliveries.show', $delivery)
            ->with('success', 'Delivery created successfully.');
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['order.user', 'order.items.product']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified delivery.
     */
    public function edit(Delivery $delivery)
    {
        $delivery->load('order');
        return view('admin.deliveries.edit', compact('delivery'));
    }

    /**
     * Update the specified delivery in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'carrier' => 'required|string|max:100',
            'tracking_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('deliveries')->ignore($delivery->id),
            ],
            'shipping_cost' => 'required|numeric|min:0',
            'total_weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => ['required', Rule::in(array_keys(Delivery::$statuses))],
        ]);

        $statusChanged = $delivery->status !== $validated['status'];
        $delivery->fill($validated);

        // Update timestamps based on status changes
        if ($statusChanged) {
            if ($validated['status'] === Delivery::STATUS_IN_TRANSIT && !$delivery->shipped_at) {
                $delivery->shipped_at = now();
            } elseif ($validated['status'] === Delivery::STATUS_DELIVERED) {
                $delivery->delivered_at = now();
                if (!$delivery->shipped_at) {
                    $delivery->shipped_at = now();
                }
            }
        }

        $delivery->save();

        return redirect()
            ->route('admin.deliveries.show', $delivery)
            ->with('success', 'Delivery updated successfully.');
    }

    /**
     * Update the delivery status.
     */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Delivery::$statuses))],
        ]);

        $oldStatus = $delivery->status;
        $delivery->update($validated);

        // Auto-update timestamps based on status
        if ($validated['status'] === Delivery::STATUS_IN_TRANSIT && !$delivery->shipped_at) {
            $delivery->update(['shipped_at' => now()]);
        } elseif ($validated['status'] === Delivery::STATUS_DELIVERED) {
            $delivery->update([
                'delivered_at' => now(),
                'shipped_at' => $delivery->shipped_at ?? now()
            ]);
        }

        return redirect()
            ->route('admin.deliveries.show', $delivery)
            ->with('success', 'Delivery status updated successfully.');
    }

    /**
     * Convert proforma invoice to final invoice
     */
    public function convertToFinalInvoice(Delivery $delivery)
    {
        try {
            if (!$delivery->hasConvertibleProformaInvoice()) {
                return redirect()->back()->with('error', 'No convertible proforma invoice found for this delivery.');
            }

            $proformaInvoice = $delivery->getConvertibleProformaInvoice();
            
            if (!$proformaInvoice) {
                return redirect()->back()->with('error', 'Unable to find proforma invoice.');
            }

            // Convert proforma to final invoice
            $finalInvoice = $proformaInvoice->convertToFinalInvoice($delivery->id);

            // Update delivery status if needed
            if ($delivery->status === 'pending') {
                $delivery->update(['status' => 'processing']);
            }

            return redirect()
                ->route('admin.invoices.show', $finalInvoice)
                ->with('success', 'Proforma invoice successfully converted to final invoice.');

        } catch (\Exception $e) {
            Log::error('Failed to convert proforma to final invoice: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to convert invoice: ' . $e->getMessage());
        }
    }

    /**
     * Mark the delivery as shipped.
     */
    public function markAsShipped(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
            'carrier' => 'required|string|max:100',
        ]);

        $delivery->markAsShipped($validated['tracking_number'], $validated['carrier']);

        return redirect()
            ->back()
            ->with('success', 'Delivery marked as shipped.');
    }

    /**
     * Mark the delivery as delivered.
     */
    public function markAsDelivered(Delivery $delivery)
    {
        $delivery->markAsDelivered();

        return redirect()
            ->back()
            ->with('success', 'Delivery marked as delivered.');
    }

    /**
     * Remove the specified delivery from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()
            ->route('admin.deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }
}
