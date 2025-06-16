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
        'delivery_number',
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
        'packing_list_file',
        'commercial_invoice_file',
        'processed_by_supplier_at',
        'sent_to_carrier_at',
        'supplier_notes',
    ];
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate delivery number when creating a new delivery
        static::creating(function ($delivery) {
            if (empty($delivery->delivery_number)) {
                $delivery->delivery_number = static::generateDeliveryNumber();
            }
        });
        
        // Auto-trigger workflow automation when delivery is created or updated
        static::created(function ($delivery) {
            // Auto-convert proforma invoice when delivery is created in certain statuses
            if (in_array($delivery->status, ['in_transit', 'delivered'])) {
                $delivery->autoConvertToFinalInvoice();
            }
        });

        static::updated(function ($delivery) {
            // Auto-convert proforma invoice when delivery status changes to trigger statuses
            if ($delivery->wasChanged('status')) {
                $oldStatus = $delivery->getOriginal('status');
                $newStatus = $delivery->status;
                
                Log::info("Delivery {$delivery->id} status changed from {$oldStatus} to {$newStatus}");
                
                // Trigger conversion when status changes to shipping or delivery states
                if (in_array($newStatus, ['in_transit', 'delivered']) && 
                    !in_array($oldStatus, ['in_transit', 'delivered'])) {
                    
                    Log::info("Triggering auto-conversion for delivery {$delivery->id} due to status change");
                    $delivery->autoConvertToFinalInvoice();
                }
            }
        });
    }

    /**
     * Generate a unique delivery number in format DL-000001
     */
    public static function generateDeliveryNumber(): string
    {
        $lastDelivery = static::where('delivery_number', 'like', 'DL-%')
            ->orderByRaw('CAST(SUBSTRING(delivery_number, 4) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastDelivery && $lastDelivery->delivery_number) {
            // Extract the number part from the delivery number
            $numberPart = substr($lastDelivery->delivery_number, 3); // Remove 'DL-' prefix
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
            }
        }
        
        $deliveryNumber = 'DL-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        // Safety check to ensure uniqueness
        $counter = 0;
        while (static::where('delivery_number', $deliveryNumber)->exists() && $counter < 1000) {
            $nextNumber++;
            $deliveryNumber = 'DL-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $deliveryNumber;
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
     * Auto-convert proforma invoice to final invoice upon delivery
     * Handles different payment scenarios and updates all relevant statuses
     */
    public function autoConvertToFinalInvoice()
    {
        try {
            $proformaInvoice = $this->getConvertibleProformaInvoice();
            
            if (!$proformaInvoice) {
                Log::info("No convertible proforma invoice found for delivery {$this->id}");
                return;
            }

            Log::info("Starting auto-conversion of proforma invoice {$proformaInvoice->id} to final invoice for delivery {$this->id}");
            
            // Check payment requirements based on payment terms
            $conversionReady = $this->isReadyForInvoiceConversion($proformaInvoice);
            
            if (!$conversionReady['ready']) {
                Log::info("Delivery {$this->id} not ready for conversion: {$conversionReady['reason']}");
                return;
            }

            // Convert proforma to final invoice
            $finalInvoice = $proformaInvoice->convertToFinalInvoice($this->id);

            // Update delivery status based on invoice conversion
            $this->updateStatusAfterInvoiceConversion($proformaInvoice, $finalInvoice);

            // Update order status if exists
            if ($this->order) {
                $this->updateOrderStatusAfterConversion($finalInvoice);
            }

            Log::info("Auto-converted proforma invoice {$proformaInvoice->id} to final invoice {$finalInvoice->id} for delivery {$this->id}");

            // Send final invoice email based on payment situation
            $this->handleFinalInvoiceNotification($finalInvoice, $proformaInvoice);

        } catch (\Exception $e) {
            Log::error('Failed to auto-convert to final invoice: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Check if delivery is ready for invoice conversion based on payment terms
     */
    private function isReadyForInvoiceConversion($proformaInvoice): array
    {
        $paymentTerms = $proformaInvoice->payment_terms;
        $paidAmount = $proformaInvoice->paid_amount;
        $totalAmount = $proformaInvoice->total_amount;
        $deliveryStatus = $this->status;

        switch ($paymentTerms) {
            case 'advance_50':
                $requiredAmount = $totalAmount * 0.5;
                if ($paidAmount < $requiredAmount) {
                    return [
                        'ready' => false, 
                        'reason' => "50% advance payment required. Paid: {$paidAmount}, Required: {$requiredAmount}"
                    ];
                }
                break;

            case 'advance_100':
                if ($paidAmount < $totalAmount) {
                    return [
                        'ready' => false, 
                        'reason' => "Full advance payment required. Paid: {$paidAmount}, Required: {$totalAmount}"
                    ];
                }
                break;

            case 'on_delivery':
                // No advance payment required, but delivery should be shipped/in-transit
                if (!in_array($deliveryStatus, ['in_transit', 'delivered'])) {
                    return [
                        'ready' => false, 
                        'reason' => "Delivery must be shipped for payment on delivery terms. Current status: {$deliveryStatus}"
                    ];
                }
                break;

            case 'net_30':
                // Can convert immediately upon shipment
                if (!in_array($deliveryStatus, ['in_transit', 'delivered'])) {
                    return [
                        'ready' => false, 
                        'reason' => "Delivery must be shipped for net 30 terms. Current status: {$deliveryStatus}"
                    ];
                }
                break;

            case 'custom':
                // Check custom advance percentage
                $advancePercentage = $proformaInvoice->advance_percentage ?? 0;
                if ($advancePercentage > 0) {
                    $requiredAmount = $totalAmount * ($advancePercentage / 100);
                    if ($paidAmount < $requiredAmount) {
                        return [
                            'ready' => false, 
                            'reason' => "{$advancePercentage}% advance payment required. Paid: {$paidAmount}, Required: {$requiredAmount}"
                        ];
                    }
                }
                break;
        }

        return ['ready' => true, 'reason' => 'All conditions met'];
    }

    /**
     * Update delivery status after invoice conversion
     */
    private function updateStatusAfterInvoiceConversion($proformaInvoice, $finalInvoice)
    {
        $newStatus = $this->status;
        
        // Update delivery status based on payment terms and current status
        switch ($proformaInvoice->payment_terms) {
            case 'advance_50':
            case 'advance_100':
                // If payment was received and goods are ready, ensure proper status
                if ($this->status === 'pending') {
                    $newStatus = 'processing';
                }
                break;
                
            case 'on_delivery':
                // For payment on delivery, goods should be in transit or delivered
                if ($this->status === 'pending') {
                    $newStatus = 'processing';
                }
                break;
        }

        if ($newStatus !== $this->status) {
            $this->update(['status' => $newStatus]);
            Log::info("Updated delivery {$this->id} status from {$this->status} to {$newStatus}");
        }
    }

    /**
     * Update order status after invoice conversion
     */
    private function updateOrderStatusAfterConversion($finalInvoice)
    {
        $currentOrderStatus = $this->order->status;
        $newOrderStatus = $currentOrderStatus;

        // Determine new order status based on final invoice payment status
        if ($finalInvoice->payment_status === 'paid') {
            // Full payment received, can proceed with fulfillment
            if (in_array($currentOrderStatus, ['pending', 'processing'])) {
                $newOrderStatus = 'processing';
            }
        } elseif ($finalInvoice->payment_status === 'pending' && $finalInvoice->payment_terms === 'on_delivery') {
            // Payment on delivery, update to ready for shipping
            if ($currentOrderStatus === 'pending') {
                $newOrderStatus = 'processing';
            }
        }

        if ($newOrderStatus !== $currentOrderStatus) {
            $this->order->update(['status' => $newOrderStatus]);
            Log::info("Updated order {$this->order->id} status from {$currentOrderStatus} to {$newOrderStatus}");
        }
    }

    /**
     * Handle final invoice notification based on payment situation
     */
    private function handleFinalInvoiceNotification($finalInvoice, $proformaInvoice)
    {
        $shouldSendEmail = false;
        $emailType = 'delivery_completed';

        switch ($proformaInvoice->payment_terms) {
            case 'advance_50':
                // Send email for remaining balance
                if ($finalInvoice->total_amount > 0) {
                    $shouldSendEmail = true;
                    $emailType = 'remaining_balance_due';
                }
                break;

            case 'advance_100':
                // Send confirmation email for completed delivery
                $shouldSendEmail = true;
                $emailType = 'delivery_completed_paid';
                break;

            case 'on_delivery':
                // Send email requesting payment upon delivery
                $shouldSendEmail = true;
                $emailType = 'payment_due_on_delivery';
                break;

            case 'net_30':
                // Send email with 30-day payment terms
                $shouldSendEmail = true;
                $emailType = 'payment_due_net_30';
                break;

            case 'custom':
                // Send email based on remaining balance
                if ($finalInvoice->total_amount > 0) {
                    $shouldSendEmail = true;
                    $emailType = 'remaining_balance_due';
                } else {
                    $shouldSendEmail = true;
                    $emailType = 'delivery_completed_paid';
                }
                break;
        }

        if ($shouldSendEmail) {
            $this->sendFinalInvoiceEmail($finalInvoice, $emailType);
        }
    }

    /**
     * Send final invoice email with context-appropriate message
     */
    private function sendFinalInvoiceEmail($finalInvoice, $emailType)
    {
        try {
            // Get customer email from the customer name
            $customer = \App\Models\Customer::where('name', $finalInvoice->customer_name)->first();
            
            if (!$customer || !$customer->email) {
                Log::warning("No email found for customer {$finalInvoice->customer_name}, skipping auto-email");
                return;
            }

            $emailMessages = [
                'delivery_completed' => 'Your order has been delivered successfully. Please find the final invoice attached.',
                'remaining_balance_due' => 'Your order has been processed and is ready for delivery. Please find the final invoice for the remaining balance attached.',
                'delivery_completed_paid' => 'Your order has been delivered successfully. Thank you for your payment. This final invoice is for your records.',
                'payment_due_on_delivery' => 'Your order has been delivered. Please find the final invoice attached. Payment is due upon delivery.',
                'payment_due_net_30' => 'Your order has been processed. Please find the final invoice attached. Payment is due within 30 days.'
            ];

            $subjects = [
                'delivery_completed' => 'Final Invoice - Delivery Completed',
                'remaining_balance_due' => 'Final Invoice - Remaining Balance Due',
                'delivery_completed_paid' => 'Final Invoice - Delivery Completed (Paid)',
                'payment_due_on_delivery' => 'Final Invoice - Payment Due on Delivery',
                'payment_due_net_30' => 'Final Invoice - Payment Due in 30 Days'
            ];

            $emailData = [
                'to_email' => $customer->email,
                'cc_emails' => ['sales@maxmedme.com'],
                'subject' => ($subjects[$emailType] ?? 'Final Invoice') . ' - ' . $finalInvoice->invoice_number,
                'message' => $emailMessages[$emailType] ?? $emailMessages['delivery_completed']
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
                'type' => $emailType,
                'auto_sent' => true
            ];

            $finalInvoice->update([
                'email_history' => $emailHistory,
                'sent_at' => now(),
                'status' => 'sent'
            ]);

            Log::info("Auto-sent final invoice email ({$emailType}) for invoice {$finalInvoice->id}");

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
        'processed_by_supplier_at' => 'datetime',
        'sent_to_carrier_at' => 'datetime',
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
     * Enhanced to handle different payment scenarios properly
     */
    public function isReadyForFinalInvoice(): bool
    {
        $proformaInvoice = $this->getProformaInvoice();
        
        if (!$proformaInvoice) {
            return false;
        }

        // Check basic conversion requirements
        if (!$proformaInvoice->canConvertToFinalInvoice()) {
            return false;
        }

        // Check if final invoice already exists
        if ($this->finalInvoice()->exists()) {
            return false;
        }

        // Check payment and delivery status requirements
        $conversionReady = $this->isReadyForInvoiceConversion($proformaInvoice);
        
        return $conversionReady['ready'];
    }

    /**
     * Get detailed status of final invoice conversion readiness
     */
    public function getFinalInvoiceConversionStatus(): array
    {
        $proformaInvoice = $this->getProformaInvoice();
        
        if (!$proformaInvoice) {
            return [
                'ready' => false,
                'reason' => 'No proforma invoice found',
                'details' => []
            ];
        }

        if (!$proformaInvoice->canConvertToFinalInvoice()) {
            return [
                'ready' => false,
                'reason' => "Proforma invoice status is '{$proformaInvoice->status}' (must be 'confirmed')",
                'details' => [
                    'proforma_status' => $proformaInvoice->status,
                    'required_status' => 'confirmed'
                ]
            ];
        }

        if ($this->finalInvoice()->exists()) {
            return [
                'ready' => false,
                'reason' => 'Final invoice already exists',
                'details' => [
                    'final_invoice_id' => $this->finalInvoice()->first()->id ?? null
                ]
            ];
        }

        $conversionCheck = $this->isReadyForInvoiceConversion($proformaInvoice);
        
        return [
            'ready' => $conversionCheck['ready'],
            'reason' => $conversionCheck['reason'],
            'details' => [
                'payment_terms' => $proformaInvoice->payment_terms,
                'paid_amount' => $proformaInvoice->paid_amount,
                'total_amount' => $proformaInvoice->total_amount,
                'delivery_status' => $this->status,
                'advance_percentage' => $proformaInvoice->advance_percentage
            ]
        ];
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
