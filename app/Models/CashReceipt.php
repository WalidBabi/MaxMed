<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Log;

class CashReceipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'order_id',
        'customer_id',
        'amount',
        'currency',
        'payment_date',
        'payment_method',
        'description',
        'notes',
        'reference_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'issued_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'issued_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_ISSUED = 'issued';
    const STATUS_CANCELLED = 'cancelled';

    public static $statuses = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_ISSUED => 'Issued',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    // Payment method constants
    const METHOD_CASH = 'cash';
    const METHOD_CHECK = 'check';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';

    public static $paymentMethods = [
        self::METHOD_CASH => 'Cash',
        self::METHOD_CHECK => 'Check',
        self::METHOD_CREDIT_CARD => 'Credit Card',
        self::METHOD_BANK_TRANSFER => 'Bank Transfer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate receipt number when creating
        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = static::generateReceiptNumber();
            }
            
            // Set issued_at if status is issued
            if ($receipt->status === self::STATUS_ISSUED && !$receipt->issued_at) {
                $receipt->issued_at = now();
            }
        });

        // Update issued_at when status changes to issued
        static::updating(function ($receipt) {
            if ($receipt->isDirty('status') && $receipt->status === self::STATUS_ISSUED && !$receipt->issued_at) {
                $receipt->issued_at = now();
            }
        });
    }

    /**
     * Generate a unique receipt number in format CR-000001
     */
    public static function generateReceiptNumber(): string
    {
        $lastReceipt = static::where('receipt_number', 'like', 'CR-%')
            ->orderByRaw('CAST(SUBSTRING(receipt_number, 4) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastReceipt && $lastReceipt->receipt_number) {
            $numberPart = substr($lastReceipt->receipt_number, 3);
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
            }
        }
        
        return 'CR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
     * Get the delivery associated with this cash receipt through the order
     */
    public function delivery()
    {
        return $this->hasOneThrough(
            Delivery::class,
            Order::class,
            'id', // Foreign key on orders table (order.id)
            'order_id', // Foreign key on deliveries table (delivery.order_id)
            'order_id', // Local key on cash_receipts table (cash_receipt.order_id)
            'id' // Local key on orders table (order.id)
        );
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'badge bg-warning',
            self::STATUS_ISSUED => 'badge bg-success',
            self::STATUS_CANCELLED => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Create cash receipt from order
     */
    public static function createFromOrder(Order $order, array $additionalData = []): self
    {
        $customer = $order->customer;
        
        $receiptData = array_merge([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'amount' => $order->total_amount,
            'currency' => 'AED',
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => self::METHOD_CASH,
            'description' => "Cash payment for Order #{$order->order_number}",
            'notes' => null, // Allow notes to be set via additionalData
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => $customer->getFullAddress(),
            'status' => self::STATUS_ISSUED,
            'created_by' => auth()->id(),
        ], $additionalData);

        return static::create($receiptData);
    }

    /**
     * Create cash receipt from invoice
     */
    public static function createFromInvoice(Invoice $invoice, array $additionalData = []): self
    {
        $customer = $invoice->order->customer;
        
        $receiptData = array_merge([
            'order_id' => $invoice->order_id,
            'customer_id' => $customer->id,
            'amount' => $invoice->total_amount,
            'currency' => $invoice->currency ?? 'AED',
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => self::METHOD_CASH,
            'description' => "Cash payment for Invoice #{$invoice->invoice_number}",
            'notes' => null, // Allow notes to be set via additionalData
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => $customer->getFullAddress(),
            'status' => self::STATUS_ISSUED,
            'created_by' => auth()->id(),
        ], $additionalData);

        return static::create($receiptData);
    }

    /**
     * Generate receipt content for PDF or display
     */
    public function getReceiptContent(): array
    {
        return [
            'receipt_number' => $this->receipt_number,
            'date' => $this->payment_date->format('d/m/Y'),
            'amount' => $this->formatted_amount,
            'currency' => $this->currency,
            'payment_method' => static::$paymentMethods[$this->payment_method] ?? $this->payment_method,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'description' => $this->description,
            'notes' => $this->notes,
            'order_number' => $this->order?->order_number,
            'issued_at' => $this->issued_at?->format('d/m/Y H:i'),
        ];
    }
} 