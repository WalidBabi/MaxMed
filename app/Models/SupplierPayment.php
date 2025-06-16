<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class SupplierPayment extends Model
{
    protected $fillable = [
        'payment_number',
        'purchase_order_id',
        'order_id',
        'amount',
        'currency',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'bank_name',
        'account_number',
        'transaction_id',
        'status',
        'processed_at',
        'attachments',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'processed_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public static $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    // Payment method constants
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH = 'cash';
    const METHOD_CHECK = 'check';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_ONLINE_TRANSFER = 'online_transfer';
    const METHOD_OTHER = 'other';

    public static $paymentMethods = [
        self::METHOD_BANK_TRANSFER => 'Bank Transfer',
        self::METHOD_CASH => 'Cash',
        self::METHOD_CHECK => 'Check',
        self::METHOD_CREDIT_CARD => 'Credit Card',
        self::METHOD_ONLINE_TRANSFER => 'Online Transfer',
        self::METHOD_OTHER => 'Other',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate payment number when creating
        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }
        });

        // Update purchase order payment status when payment is saved
        static::saved(function ($payment) {
            $payment->updatePurchaseOrderPaymentStatus();
        });
    }

    /**
     * Generate a unique payment number in format SP-000001
     */
    public static function generatePaymentNumber(): string
    {
        $lastPayment = static::where('payment_number', 'like', 'SP-%')
            ->orderByRaw('CAST(SUBSTRING(payment_number, 4) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastPayment && $lastPayment->payment_number) {
            $numberPart = substr($lastPayment->payment_number, 3);
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
            }
        }
        
        return 'SP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Business Logic Methods
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now()
        ]);
        
        Log::info("Supplier Payment {$this->payment_number} marked as completed");
    }

    public function markAsFailed($reason = null)
    {
        $notes = $this->notes;
        if ($reason) {
            $notes .= "\nFailed: " . $reason;
        }
        
        $this->update([
            'status' => self::STATUS_FAILED,
            'notes' => $notes,
            'processed_at' => now()
        ]);
        
        Log::info("Supplier Payment {$this->payment_number} marked as failed: {$reason}");
    }

    /**
     * Update the purchase order's payment status
     */
    private function updatePurchaseOrderPaymentStatus()
    {
        if ($this->purchaseOrder) {
            // Calculate total paid amount for this PO
            $totalPaid = $this->purchaseOrder->payments()
                ->where('status', self::STATUS_COMPLETED)
                ->sum('amount');
            
            $this->purchaseOrder->update(['paid_amount' => $totalPaid]);
            $this->purchaseOrder->updatePaymentStatus();
        }
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
} 