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
            ? collect([$selectedOrder->id => $selectedOrder->order_number . ' - ' . ($selectedOrder->user->name ?? 'N/A')])
            : Order::whereDoesntHave('delivery')
                ->with('user')
                ->get()
                ->mapWithKeys(fn($order) => [$order->id => $order->order_number . ' - ' . ($order->user->name ?? 'N/A')]);

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
     * Convert proforma invoice to final invoice with enhanced payment scenario handling
     */
    public function convertToFinalInvoice(Delivery $delivery)
    {
        try {
            if (!$delivery->hasConvertibleProformaInvoice()) {
                $proformaInvoice = $delivery->getProformaInvoice();
                
                if (!$proformaInvoice) {
                    return redirect()->back()->with('error', 'No proforma invoice found for this delivery.');
                } else {
                    $reason = $this->getConversionBlockReason($proformaInvoice, $delivery);
                    return redirect()->back()->with('error', "Cannot convert proforma invoice: {$reason}");
                }
            }

            $proformaInvoice = $delivery->getConvertibleProformaInvoice();
            
            if (!$proformaInvoice) {
                return redirect()->back()->with('error', 'Unable to find proforma invoice.');
            }

            // Check if conversion requirements are met
            $conversionCheck = $this->checkConversionRequirements($proformaInvoice, $delivery);
            
            if (!$conversionCheck['ready']) {
                return redirect()->back()->with('error', $conversionCheck['message']);
            }

            // Convert proforma to final invoice
            $finalInvoice = $proformaInvoice->convertToFinalInvoice($delivery->id);

            // Update delivery status based on conversion
            $this->updateDeliveryAfterConversion($delivery, $proformaInvoice, $finalInvoice);

            // Update order status if exists
            if ($delivery->order) {
                $this->updateOrderAfterConversion($delivery->order, $finalInvoice);
            }

            $successMessage = $this->generateConversionSuccessMessage($proformaInvoice, $finalInvoice);

            return redirect()
                ->route('admin.invoices.show', $finalInvoice)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('Failed to convert proforma to final invoice: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Failed to convert invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get detailed reason why conversion is blocked
     */
    private function getConversionBlockReason($proformaInvoice, $delivery): string
    {
        if (!$proformaInvoice->canConvertToFinalInvoice()) {
            return "Proforma invoice status is '{$proformaInvoice->status}' (must be 'confirmed')";
        }

        if ($proformaInvoice->childInvoices()->where('type', 'final')->exists()) {
            return "Final invoice already exists for this proforma";
        }

        $paymentTerms = $proformaInvoice->payment_terms;
        $paidAmount = $proformaInvoice->paid_amount;
        $totalAmount = $proformaInvoice->total_amount;
        $deliveryStatus = $delivery->status;

        switch ($paymentTerms) {
            case 'advance_50':
                $requiredAmount = $totalAmount * 0.5;
                if ($paidAmount < $requiredAmount) {
                    return "50% advance payment required. Paid: {$paidAmount} AED, Required: {$requiredAmount} AED";
                }
                break;

            case 'advance_100':
                if ($paidAmount < $totalAmount) {
                    return "Full advance payment required. Paid: {$paidAmount} AED, Required: {$totalAmount} AED";
                }
                break;

            case 'on_delivery':
                if (!in_array($deliveryStatus, ['in_transit', 'delivered'])) {
                    return "For payment on delivery terms, the delivery must be shipped first. Current status: {$deliveryStatus}";
                }
                break;

            case 'net_30':
                if (!in_array($deliveryStatus, ['in_transit', 'delivered'])) {
                    return "For net 30 payment terms, the delivery must be shipped first. Current status: {$deliveryStatus}";
                }
                break;

            case 'custom':
                $advancePercentage = $proformaInvoice->advance_percentage ?? 0;
                if ($advancePercentage > 0) {
                    $requiredAmount = $totalAmount * ($advancePercentage / 100);
                    if ($paidAmount < $requiredAmount) {
                        return "{$advancePercentage}% advance payment required. Paid: {$paidAmount} AED, Required: {$requiredAmount} AED";
                    }
                }
                break;
        }

        return "Unknown conversion requirement not met";
    }

    /**
     * Check if all requirements for conversion are met
     */
    private function checkConversionRequirements($proformaInvoice, $delivery): array
    {
        $paymentTerms = $proformaInvoice->payment_terms;
        $paidAmount = $proformaInvoice->paid_amount;
        $totalAmount = $proformaInvoice->total_amount;
        $deliveryStatus = $delivery->status;

        // Check payment requirements
        switch ($paymentTerms) {
            case 'advance_50':
                $requiredAmount = $totalAmount * 0.5;
                if ($paidAmount < $requiredAmount) {
                    return [
                        'ready' => false,
                        'message' => "50% advance payment not received. Please record the advance payment before converting. (Required: {$requiredAmount} AED, Received: {$paidAmount} AED)"
                    ];
                }
                break;

            case 'advance_100':
                if ($paidAmount < $totalAmount) {
                    return [
                        'ready' => false,
                        'message' => "Full advance payment not received. Please record the full payment before converting. (Required: {$totalAmount} AED, Received: {$paidAmount} AED)"
                    ];
                }
                break;

            case 'on_delivery':
                if (!in_array($deliveryStatus, ['in_transit', 'delivered'])) {
                    return [
                        'ready' => false,
                        'message' => "For payment on delivery terms, the delivery must be shipped first. Current status: {$deliveryStatus}"
                    ];
                }
                break;

            case 'net_30':
                if (!in_array($deliveryStatus, ['in_transit', 'delivered'])) {
                    return [
                        'ready' => false,
                        'message' => "For net 30 payment terms, the delivery must be shipped first. Current status: {$deliveryStatus}"
                    ];
                }
                break;

            case 'custom':
                $advancePercentage = $proformaInvoice->advance_percentage ?? 0;
                if ($advancePercentage > 0) {
                    $requiredAmount = $totalAmount * ($advancePercentage / 100);
                    if ($paidAmount < $requiredAmount) {
                        return [
                            'ready' => false,
                            'message' => "{$advancePercentage}% advance payment not received. Please record the advance payment before converting. (Required: {$requiredAmount} AED, Received: {$paidAmount} AED)"
                        ];
                    }
                }
                break;
        }

        return ['ready' => true, 'message' => 'All requirements met'];
    }

    /**
     * Update delivery status after successful conversion
     */
    private function updateDeliveryAfterConversion($delivery, $proformaInvoice, $finalInvoice)
    {
        $currentStatus = $delivery->status;
        $newStatus = $currentStatus;

        // Update delivery status based on payment situation
        if ($finalInvoice->payment_status === 'paid') {
            // Payment completed, ensure delivery is in appropriate status
            if ($currentStatus === 'pending') {
                $newStatus = 'processing';
            }
        } elseif ($finalInvoice->payment_status === 'pending') {
            // Payment pending, status depends on payment terms
            if ($proformaInvoice->payment_terms === 'on_delivery' && $currentStatus === 'pending') {
                $newStatus = 'processing';
            }
        }

        if ($newStatus !== $currentStatus) {
            $delivery->update(['status' => $newStatus]);
            Log::info("Updated delivery {$delivery->id} status from {$currentStatus} to {$newStatus} after invoice conversion");
        }
    }

    /**
     * Update order status after successful conversion
     */
    private function updateOrderAfterConversion($order, $finalInvoice)
    {
        $currentStatus = $order->status;
        $newStatus = $currentStatus;

        // Update order status based on final invoice payment status
        if ($finalInvoice->payment_status === 'paid') {
            if (in_array($currentStatus, ['pending'])) {
                $newStatus = 'processing';
            }
        } elseif ($finalInvoice->payment_status === 'pending') {
            if ($currentStatus === 'pending') {
                $newStatus = 'processing';
            }
        }

        if ($newStatus !== $currentStatus) {
            $order->update(['status' => $newStatus]);
            Log::info("Updated order {$order->id} status from {$currentStatus} to {$newStatus} after invoice conversion");
        }
    }

    /**
     * Generate appropriate success message based on conversion type
     */
    private function generateConversionSuccessMessage($proformaInvoice, $finalInvoice): string
    {
        $baseMessage = "Proforma invoice {$proformaInvoice->invoice_number} successfully converted to final invoice {$finalInvoice->invoice_number}";

        switch ($proformaInvoice->payment_terms) {
            case 'advance_50':
                if ($finalInvoice->total_amount > 0) {
                    return $baseMessage . ". Remaining balance of {$finalInvoice->total_amount} AED is now due.";
                } else {
                    return $baseMessage . ". No remaining balance - delivery is complete.";
                }

            case 'advance_100':
                return $baseMessage . ". Full payment received - delivery is complete.";

            case 'on_delivery':
                return $baseMessage . ". Payment of {$finalInvoice->total_amount} AED is due upon delivery.";

            case 'net_30':
                return $baseMessage . ". Payment of {$finalInvoice->total_amount} AED is due within 30 days.";

            case 'custom':
                if ($finalInvoice->total_amount > 0) {
                    return $baseMessage . ". Remaining balance of {$finalInvoice->total_amount} AED is now due.";
                } else {
                    return $baseMessage . ". Full payment received - delivery is complete.";
                }

            default:
                return $baseMessage . ".";
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
