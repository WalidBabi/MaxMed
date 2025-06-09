<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_number',
        'status',
        'carrier',
        'shipping_address',
        'billing_address',
        'shipping_cost',
        'total_weight',
        'notes',
        'shipped_at',
        'delivered_at',
        'customer_signature',
        'signature_ip_address',
        'signed_at',
        'delivery_conditions',
    ];
    
    /**
     * Get the URL for the customer signature
     *
     * @return string|null
     */
    public function getCustomerSignatureUrlAttribute()
    {
        if (!$this->customer_signature) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (filter_var($this->customer_signature, FILTER_VALIDATE_URL)) {
            return $this->customer_signature;
        }
        
        // If it's a storage path, generate the correct URL
        if (strpos($this->customer_signature, 'storage/') === 0) {
            return asset($this->customer_signature);
        }
        
        // If it's a relative path, assume it's in the storage directory
        return asset('storage/' . ltrim($this->customer_signature, '/'));
    }

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'signed_at' => 'datetime',
        'delivery_conditions' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public static $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_IN_TRANSIT => 'In Transit',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    /**
     * Get the order that owns the delivery.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Check if delivery is marked as delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED && $this->delivered_at !== null;
    }

    /**
     * Check if delivery is pending.
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if delivery is in transit.
     * @return bool
     */
    public function isInTransit(): bool
    {
        return $this->status === self::STATUS_IN_TRANSIT;
    }

    /**
     * Mark the delivery as shipped.
     */
    public function markAsShipped(string $trackingNumber = null, string $carrier = null): void
    {
        $this->update([
            'status' => self::STATUS_IN_TRANSIT,
            'tracking_number' => $trackingNumber ?? $this->tracking_number,
            'carrier' => $carrier ?? $this->carrier,
            'shipped_at' => $this->shipped_at ?? now(),
        ]);
    }

    /**
     * Mark the delivery as delivered.
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }
}
