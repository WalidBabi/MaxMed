<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderTrackingController extends Controller
{
    /**
     * Show order tracking page for guests
     */
    public function track(Order $order)
    {
        try {
            // Load order with related data
            $order->load(['items.product', 'user', 'delivery']);
            
            // Get order status progression
            $statusProgression = $this->getStatusProgression($order->status);
            
            return view('orders.track', compact('order', 'statusProgression'));
            
        } catch (\Exception $e) {
            Log::error('Error loading order tracking page: ' . $e->getMessage());
            return view('orders.track-error')->with('error', 'Order not found or access denied.');
        }
    }

    /**
     * Get status progression for visual timeline
     */
    private function getStatusProgression($currentStatus)
    {
        $allStatuses = [
            'pending' => [
                'label' => 'Order Placed',
                'description' => 'Your order has been received and is being reviewed',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            'awaiting_quotations' => [
                'label' => 'Order Review',
                'description' => 'We are reviewing your order and checking product availability',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            'quotations_received' => [
                'label' => 'Order Confirmed',
                'description' => 'Your order has been confirmed and pricing finalized',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
            ],
            'approved' => [
                'label' => 'Order Approved',
                'description' => 'Your order has been approved and is ready for processing',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            'processing' => [
                'label' => 'Processing',
                'description' => 'Your order is being prepared and processed',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
            ],
            'shipped' => [
                'label' => 'Shipped',
                'description' => 'Your order has been shipped and is on its way',
                'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            'delivered' => [
                'label' => 'Delivered',
                'description' => 'Your order has been delivered',
                'icon' => 'M5 13l4 4L19 7'
            ],
            'completed' => [
                'label' => 'Completed',
                'description' => 'Order completed successfully',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
            ]
        ];

        $progression = [];
        $currentReached = false;
        
        foreach ($allStatuses as $status => $info) {
            $isActive = $status === $currentStatus;
            $isCompleted = !$currentReached && !$isActive;
            
            $progression[] = [
                'status' => $status,
                'label' => $info['label'],
                'description' => $info['description'],
                'icon' => $info['icon'],
                'is_active' => $isActive,
                'is_completed' => $isCompleted,
                'is_pending' => !$isCompleted && !$isActive
            ];
            
            if ($isActive) {
                $currentReached = true;
            }
        }
        
        return $progression;
    }
}