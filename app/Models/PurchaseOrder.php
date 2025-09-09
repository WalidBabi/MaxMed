<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'order_id',
        'delivery_id',
        'supplier_id',
        'quotation_request_id',
        'supplier_quotation_id',
        'supplier_name',
        'supplier_address',
        'supplier_email',
        'supplier_phone',
        'po_date',
        'delivery_date_requested',
        'description',
        'terms_conditions',
        'notes',
        'special_instructions',
        'payment_terms',
        'shipping_method',
        'sub_total',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'currency',
        'status',
        'sent_to_supplier_at',
        'acknowledged_at',
        'payment_status',
        'paid_amount',
        'payment_due_date',
        'paid_at',
        'po_file',
        'attachments',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date_requested' => 'date',
        'payment_due_date' => 'datetime',
        'sent_to_supplier_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'paid_at' => 'datetime',
        'sub_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'attachments' => 'array',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT_TO_SUPPLIER = 'sent_to_supplier';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_IN_PRODUCTION = 'in_production';
    const STATUS_READY_TO_SHIP = 'ready_to_ship';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static $statuses = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SENT_TO_SUPPLIER => 'Sent to Supplier',
        self::STATUS_ACKNOWLEDGED => 'Acknowledged',
        self::STATUS_IN_PRODUCTION => 'In Production',
        self::STATUS_READY_TO_SHIP => 'Ready to Ship',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    // Payment status constants
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    public static $paymentStatuses = [
        self::PAYMENT_STATUS_PENDING => 'Pending',
        self::PAYMENT_STATUS_PARTIAL => 'Partial',
        self::PAYMENT_STATUS_PAID => 'Paid',
        self::PAYMENT_STATUS_REFUNDED => 'Refunded',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate PO number when creating
        static::creating(function ($po) {
            if (empty($po->po_number)) {
                $po->po_number = static::generatePONumber();
            }
        });
    }

    /**
     * Generate a unique PO number in format PO-000001
     */
    public static function generatePONumber(): string
    {
        $lastPO = static::where('po_number', 'like', 'PO-%')
            ->orderByRaw('CAST(SUBSTRING(po_number, 4) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastPO && $lastPO->po_number) {
            $numberPart = substr($lastPO->po_number, 3);
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
            }
        }
        
        return 'PO-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
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
     * Get the supplier user
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the original quotation request
     */
    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    /**
     * Get the supplier quotation that was accepted
     */
    public function supplierQuotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class);
    }

    /**
     * Check if this purchase order is linked to a customer order
     */
    public function hasCustomerOrder(): bool
    {
        return $this->order()->exists();
    }

    /**
     * Check if this purchase order is from a supplier inquiry (no customer order)
     */
    public function isFromSupplierInquiry(): bool
    {
        return is_null($this->order_id) && !is_null($this->supplier_quotation_id);
    }

    /**
     * Get the source type of this purchase order
     */
    public function getSourceType(): string
    {
        if ($this->hasCustomerOrder()) {
            return 'customer_order';
        } elseif ($this->isFromSupplierInquiry()) {
            return 'supplier_inquiry';
        } else {
            return 'internal_purchase';
        }
    }

    /**
     * Get display name for the source
     */
    public function getSourceDisplayName(): string
    {
        if ($this->hasCustomerOrder()) {
            return 'Customer Order #' . $this->order->order_number;
        } elseif ($this->isFromSupplierInquiry()) {
            return 'Supplier Inquiry #' . $this->supplier_quotation_id;
        } else {
            return 'Internal Purchase';
        }
    }

    /**
     * Get the source URL for navigation
     */
    public function getSourceUrl(): ?string
    {
        if ($this->hasCustomerOrder()) {
            return route('admin.orders.show', $this->order);
        } elseif ($this->isFromSupplierInquiry() && $this->quotationRequest) {
            return route('admin.inquiries.show', $this->quotationRequest);
        }
        return null;
    }

    /**
     * Get the source type badge class
     */
    public function getSourceTypeBadgeClass(): string
    {
        switch ($this->getSourceType()) {
            case 'customer_order':
                return 'bg-blue-100 text-blue-800';
            case 'supplier_inquiry':
                return 'bg-green-100 text-green-800';
            case 'internal_purchase':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Check if this purchase order can be sent to supplier
     */
    public function canBeSentToSupplier(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if this purchase order can be acknowledged by supplier
     */
    public function canBeAcknowledgedBySupplier(): bool
    {
        return $this->status === self::STATUS_SENT_TO_SUPPLIER;
    }

    /**
     * Get the total amount with currency formatting
     */
    public function getFormattedSubTotalAttribute(): string
    {
        return number_format($this->sub_total, 2);
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return number_format($this->tax_amount, 2);
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return number_format($this->shipping_cost, 2);
    }

    /**
     * Business Logic Methods
     */
    public function calculateTotals()
    {
        $subTotal = $this->items->sum('line_total');
        $this->update([
            'sub_total' => $subTotal,
            'total_amount' => $subTotal + $this->tax_amount + $this->shipping_cost
        ]);
    }

    public function getRemainingAmount()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function canBeAcknowledged(): bool
    {
        return $this->status === self::STATUS_SENT_TO_SUPPLIER;
    }

    public function canStartProduction(): bool
    {
        return $this->status === self::STATUS_ACKNOWLEDGED && $this->isFullyPaid();
    }

    /**
     * Update payment status based on paid amount
     */
    public function updatePaymentStatus()
    {
        if ($this->paid_amount <= 0) {
            $this->payment_status = self::PAYMENT_STATUS_PENDING;
        } elseif ($this->paid_amount >= $this->total_amount) {
            $this->payment_status = self::PAYMENT_STATUS_PAID;
            if (!$this->paid_at) {
                $this->paid_at = now();
            }
        } else {
            $this->payment_status = self::PAYMENT_STATUS_PARTIAL;
        }
        
        $this->save();
    }

    /**
     * Mark as sent to supplier
     */
    public function markAsSentToSupplier()
    {
        $this->update([
            'status' => self::STATUS_SENT_TO_SUPPLIER,
            'sent_to_supplier_at' => now()
        ]);
        
        Log::info("Purchase Order {$this->po_number} sent to supplier");
    }

    /**
     * Mark as acknowledged by supplier
     */
    public function markAsAcknowledged()
    {
        if ($this->status !== self::STATUS_SENT_TO_SUPPLIER) {
            throw new \Exception('PO must be sent to supplier before it can be acknowledged');
        }
        
        $this->update([
            'status' => self::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now()
        ]);
        
        Log::info("Purchase Order {$this->po_number} acknowledged by supplier");
    }

    /**
     * Create PO from order
     */
    public static function createFromOrder(Order $order): self
    {
        $po = static::create([
            'order_id' => $order->id,
            'delivery_id' => $order->delivery?->id,
            'po_date' => now(),
            'delivery_date_requested' => now()->addDays(7), // Default 7 days
            'description' => "Purchase Order for Order #{$order->order_number}",
            'created_by' => auth()->id()
        ]);

        // Create PO items from order items
        foreach ($order->items as $index => $orderItem) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $orderItem->product_id,
                'item_description' => $orderItem->product->name ?? 'Product',
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->price,
                'line_total' => $orderItem->quantity * $orderItem->price,
                'specifications' => $orderItem->specifications ?? null,
                'sort_order' => $index + 1
            ]);
        }

        $po->calculateTotals();
        
        Log::info("Created Purchase Order {$po->po_number} from Order {$order->order_number}");
        
        return $po;
    }

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2);
    }

    public function getFormattedPaidAmountAttribute()
    {
        return number_format($this->paid_amount, 2);
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return number_format($this->getRemainingAmount(), 2);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'sent_to_supplier' => 'bg-blue-100 text-blue-800',
            'acknowledged' => 'bg-yellow-100 text-yellow-800',
            'in_production' => 'bg-purple-100 text-purple-800',
            'ready_to_ship' => 'bg-green-100 text-green-800',
            'shipped' => 'bg-green-100 text-green-800',
            'delivered' => 'bg-green-100 text-green-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'bg-red-100 text-red-800',
            'partial' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
} 