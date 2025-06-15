<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'order_number',
        'total_amount',
        'status',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_phone',
        'notes'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatic workflow triggers
        static::updated(function ($order) {
            $order->handleWorkflowAutomation();
        });
    }

    /**
     * Handle automatic workflow progression
     */
    public function handleWorkflowAutomation()
    {
        // Auto-create delivery when order status changes to shipped
        if ($this->status === 'shipped' && !$this->hasDelivery()) {
            $this->autoCreateDelivery();
        }

        // Auto-update delivery status based on order status
        if ($this->hasDelivery()) {
            $this->syncDeliveryStatus();
        }
    }

    /**
     * Automatically create delivery when order is shipped
     */
    public function autoCreateDelivery()
    {
        try {
            $delivery = Delivery::create([
                'order_id' => $this->id,
                'status' => 'processing',
                'carrier' => 'MaxMed Logistics',
                'tracking_number' => 'TRK' . strtoupper(uniqid()),
                'shipping_address' => $this->getFullShippingAddress(),
                'billing_address' => $this->getFullShippingAddress(), // Use same for now
                'shipping_cost' => 0, // Can be calculated based on weight/distance
                'total_weight' => $this->calculateTotalWeight(),
                'notes' => 'Auto-created delivery for order ' . $this->order_number,
                'shipped_at' => now()
            ]);

            Log::info("Auto-created delivery {$delivery->id} for order {$this->id}");

        } catch (\Exception $e) {
            Log::error('Failed to auto-create delivery for order: ' . $e->getMessage());
        }
    }

    /**
     * Sync delivery status with order status
     */
    public function syncDeliveryStatus()
    {
        $delivery = $this->delivery;
        
        $statusMapping = [
            'shipped' => 'in_transit',
            'delivered' => 'delivered',
            'completed' => 'delivered',
            'cancelled' => 'cancelled'
        ];

        $newStatus = $statusMapping[$this->status] ?? $delivery->status;
        
        if ($newStatus !== $delivery->status) {
            $delivery->update(['status' => $newStatus]);
            
            if ($newStatus === 'delivered' && !$delivery->delivered_at) {
                $delivery->update(['delivered_at' => now()]);
            }
            
            Log::info("Auto-updated delivery {$delivery->id} status to {$newStatus}");
        }
    }

    /**
     * Get full shipping address
     */
    private function getFullShippingAddress()
    {
        return implode("\n", array_filter([
            $this->shipping_address,
            $this->shipping_city,
            $this->shipping_state . ' ' . $this->shipping_zipcode,
            'Phone: ' . $this->shipping_phone
        ]));
    }

    /**
     * Calculate total weight of order items
     */
    private function calculateTotalWeight()
    {
        $totalWeight = 0;
        
        foreach ($this->items as $item) {
            if ($item->product && $item->product->weight) {
                $totalWeight += $item->product->weight * $item->quantity;
            }
        }
        
        return $totalWeight > 0 ? $totalWeight : 1.0; // Default 1kg if no weight data
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    /**
     * Get customer information - works for both new orders (with customer_id) and legacy orders
     */
    public function getCustomerInfo()
    {
        // If we have a direct customer relationship, use it
        if ($this->customer_id && $this->customer) {
            return $this->customer;
        }

        // For legacy orders, try to find customer by user_id
        if ($this->user_id) {
            $customer = \App\Models\Customer::where('user_id', $this->user_id)->first();
            if ($customer) {
                return $customer;
            }
        }

        // Return null if no customer found
        return null;
    }

    /**
     * Get customer name - works for both new and legacy orders
     */
    public function getCustomerName()
    {
        $customer = $this->getCustomerInfo();
        if ($customer) {
            return $customer->name;
        }

        // Fallback to user name
        return $this->user ? $this->user->name : 'N/A';
    }

    /**
     * Get customer email - works for both new and legacy orders
     */
    public function getCustomerEmail()
    {
        $customer = $this->getCustomerInfo();
        if ($customer) {
            return $customer->email ?: ($customer->user ? $customer->user->email : 'N/A');
        }

        // Fallback to user email
        return $this->user ? $this->user->email : 'N/A';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the delivery associated with the order.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * Check if the order has a delivery.
     */
    public function hasDelivery(): bool
    {
        return $this->delivery()->exists();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Get the invoice associated with the order (proforma invoice that created this order).
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'id');
    }

    /**
     * Get the proforma invoice that created this order.
     */
    public function proformaInvoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'id')->where('type', 'proforma');
    }
} 