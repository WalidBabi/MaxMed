<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatic workflow triggers
        static::updated(function ($delivery) {
            $delivery->handleWorkflowAutomation();
        });
    }

    /**
     * Handle automatic workflow progression
     */
    public function handleWorkflowAutomation()
    {
        // Auto-convert to final invoice when delivery is completed
        if ($this->status === self::STATUS_DELIVERED && 
            $this->delivered_at && 
            $this->hasConvertibleProformaInvoice()) {
            
            $this->autoConvertToFinalInvoice();
        }
    }

    /**
     * Automatically convert proforma invoice to final invoice when delivered
     */
    public function autoConvertToFinalInvoice()
    {
        try {
            $proformaInvoice = $this->getConvertibleProformaInvoice();
            
            if (!$proformaInvoice) {
                return;
            }

            // Convert proforma to final invoice
            $finalInvoice = $proformaInvoice->convertToFinalInvoice($this->id);

            Log::info("Auto-converted proforma invoice {$proformaInvoice->id} to final invoice {$finalInvoice->id} for delivery {$this->id}");

            // Optionally send final invoice email automatically
            $this->autoSendFinalInvoiceEmail($finalInvoice);

        } catch (\Exception $e) {
            Log::error('Failed to auto-convert to final invoice: ' . $e->getMessage());
        }
    }

    /**
     * Automatically send final invoice email
     */
    private function autoSendFinalInvoiceEmail($finalInvoice)
    {
        try {
            // Get customer email from the customer name
            $customer = \App\Models\Customer::where('name', $finalInvoice->customer_name)->first();
            
            if (!$customer || !$customer->email) {
                Log::warning("No email found for customer {$finalInvoice->customer_name}, skipping auto-email");
                return;
            }

            $emailData = [
                'to_email' => $customer->email,
                'cc_emails' => ['sales@maxmedme.com'],
                'subject' => 'Final Invoice ' . $finalInvoice->invoice_number . ' - Delivery Completed',
                'message' => 'Your order has been delivered successfully. Please find the final invoice attached.'
            ];

            Mail::to($customer->email)
                ->cc(['sales@maxmedme.com'])
                ->send(new \App\Mail\InvoiceEmail($finalInvoice, $emailData));

            // Update email history
            $emailHistory = $finalInvoice->email_history ?? [];
            $emailHistory[] = [
                'sent_at' => now()->toISOString(),
                'to' => $customer->email,
                'cc' => ['sales@maxmedme.com'],
                'subject' => $emailData['subject'],
                'auto_sent' => true
            ];

            $finalInvoice->update([
                'email_history' => $emailHistory,
                'sent_at' => now(),
                'status' => 'sent'
            ]);

            Log::info("Auto-sent final invoice email for invoice {$finalInvoice->id}");

        } catch (\Exception $e) {
            Log::error('Failed to auto-send final invoice email: ' . $e->getMessage());
        }
    }

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
     * Get the proforma invoice associated with this delivery through the order
     */
    public function proformaInvoice()
    {
        return $this->hasOneThrough(
            \App\Models\Invoice::class,
            \App\Models\Order::class,
            'id', // Foreign key on orders table (order.id)
            'order_id', // Foreign key on invoices table (invoice.order_id)
            'order_id', // Local key on deliveries table (delivery.order_id)
            'id' // Local key on orders table (order.id)
        )->where('invoices.type', 'proforma');
    }

    /**
     * Alternative method to get proforma invoice directly through order relationship
     */
    public function getProformaInvoiceAttribute()
    {
        return $this->order ? $this->order->invoice : null;
    }

    /**
     * Get the final invoice associated with this delivery
     */
    public function finalInvoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'id', 'delivery_id')
                    ->where('type', 'final');
    }

    /**
     * Check if this delivery has a convertible proforma invoice
     */
    public function hasConvertibleProformaInvoice(): bool
    {
        $proforma = $this->getProformaInvoice();
        
        return $proforma && 
               $proforma->canConvertToFinalInvoice() && 
               $proforma->payment_status !== 'pending' &&
               !$this->finalInvoice()->exists();
    }

    /**
     * Get the proforma invoice that can be converted
     */
    public function getConvertibleProformaInvoice()
    {
        if (!$this->hasConvertibleProformaInvoice()) {
            return null;
        }

        return $this->getProformaInvoice();
    }

    /**
     * Get the proforma invoice through the order relationship
     */
    public function getProformaInvoice()
    {
        if (!$this->order) {
            return null;
        }

        return $this->order->proformaInvoice;
    }

    /**
     * Check if delivery is ready for final invoice conversion
     */
    public function isReadyForFinalInvoice(): bool
    {
        return in_array($this->status, [
            self::STATUS_PROCESSING,
            self::STATUS_IN_TRANSIT,
            self::STATUS_DELIVERED
        ]) && $this->hasConvertibleProformaInvoice();
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
