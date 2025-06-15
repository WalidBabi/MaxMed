<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    /**
     * Show delivery tracking page for customers
     */
    public function track(Request $request)
    {
        $trackingNumber = $request->get('tracking');
        $delivery = null;
        
        if ($trackingNumber) {
            $delivery = Delivery::with(['order.items.product', 'order.customer'])
                ->where('tracking_number', $trackingNumber)
                ->first();
        }
        
        return view('delivery.track', compact('delivery', 'trackingNumber'));
    }

    /**
     * Show delivery signature page
     */
    public function signature($trackingNumber)
    {
        $delivery = Delivery::with(['order.items.product', 'order.customer'])
            ->where('tracking_number', $trackingNumber)
            ->first();
            
        if (!$delivery) {
            abort(404, 'Delivery not found');
        }
        
        if ($delivery->status !== 'in_transit') {
            return redirect()->route('delivery.track', ['tracking' => $trackingNumber])
                ->with('error', 'This delivery is not ready for signature.');
        }
        
        if ($delivery->status === 'delivered') {
            return redirect()->route('delivery.track', ['tracking' => $trackingNumber])
                ->with('info', 'This delivery has already been signed for.');
        }
        
        return view('delivery.signature', compact('delivery'));
    }

    /**
     * Process customer signature
     */
    public function processSignature(Request $request, $trackingNumber)
    {
        $request->validate([
            'signature' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'delivery_conditions' => 'nullable|string|max:500'
        ]);

        $delivery = Delivery::where('tracking_number', $trackingNumber)->first();
        
        if (!$delivery) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
        
        if ($delivery->status !== 'in_transit') {
            return response()->json(['error' => 'Delivery is not ready for signature'], 400);
        }

        try {
            // Update delivery with signature
            $delivery->update([
                'status' => 'delivered',
                'customer_signature' => $request->signature,
                'signature_ip_address' => $request->ip(),
                'signed_at' => now(),
                'delivered_at' => now(),
                'delivery_conditions' => $request->delivery_conditions
            ]);

            // Update order status
            $delivery->order->update(['status' => 'delivered']);

            Log::info("Delivery {$delivery->id} signed by customer. Order {$delivery->order->order_number} marked as delivered.");

            return response()->json([
                'success' => true,
                'message' => 'Delivery signed successfully!',
                'redirect' => route('delivery.track', ['tracking' => $trackingNumber])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process signature: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process signature'], 500);
        }
    }

    /**
     * Download delivery receipt
     */
    public function downloadReceipt($trackingNumber)
    {
        $delivery = Delivery::with(['order.items.product', 'order.customer'])
            ->where('tracking_number', $trackingNumber)
            ->first();
            
        if (!$delivery || $delivery->status !== 'delivered') {
            abort(404, 'Delivery receipt not found');
        }
        
        // Generate PDF receipt (you can implement PDF generation here)
        return view('delivery.receipt', compact('delivery'));
    }
} 