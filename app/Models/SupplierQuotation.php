<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\QuotationApprovedNotification;
use App\Notifications\QuotationRejectedNotification;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Product;
use App\Models\QuotationRequest;
use App\Models\SupplierInquiry;
use App\Models\SupplierInquiryResponse;
use App\Models\SupplierQuotationItem;

class SupplierQuotation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supplier_quotations';

    protected $fillable = [
        'quotation_request_id',
        'supplier_inquiry_id',
        'supplier_inquiry_response_id',
        'supplier_id',
        'product_id',
        'unit_price',
        'currency',
        'shipping_cost',
        'size',
        'notes',
        'status',
        'quotation_number',
        'admin_notes',
        'rejection_reason',
        'attachments'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2,nullable',
    ];

    /**
     * Get the attachments attribute with proper handling
     */
    public function getAttachmentsAttribute($value)
    {
        // If it's already an array, return it
        if (is_array($value)) {
            return $this->normalizeAttachmentPaths($value);
        }
        // If it's a JSON string, decode it
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->normalizeAttachmentPaths($decoded);
            }
        }
        // Return empty array if nothing valid
        return [];
    }

    /**
     * Normalize attachment paths by fixing escaped forward slashes
     */
    private function normalizeAttachmentPaths($attachments)
    {
        if (!is_array($attachments)) {
            return [];
        }
        foreach ($attachments as &$attachment) {
            if (isset($attachment['path'])) {
                $attachment['path'] = str_replace('\\/', '/', $attachment['path']);
            }
        }
        return $attachments;
    }

    /**
     * Set the attachments attribute
     */
    public function setAttachmentsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['attachments'] = json_encode($value);
        } else {
            $this->attributes['attachments'] = $value;
        }
    }

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'submitted';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the legacy quotation request that owns the quotation.
     */
    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    /**
     * Get the new supplier inquiry that owns the quotation.
     */
    public function supplierInquiry(): BelongsTo
    {
        return $this->belongsTo(SupplierInquiry::class);
    }

    /**
     * Get the supplier inquiry response that owns the quotation.
     */
    public function supplierInquiryResponse(): BelongsTo
    {
        return $this->belongsTo(SupplierInquiryResponse::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierQuotationItem::class, 'supplier_quotation_id');
    }

    // Helper Methods
    public static function generateQuotationNumber(): string
    {
        $lastQuotation = self::latest()->first();
        $lastNumber = $lastQuotation ? intval(substr($lastQuotation->quotation_number, -5)) : 0;
        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        return 'SQ-' . date('Y') . '-' . $nextNumber;
    }

    public function markAsAccepted(): void
    {
        $this->update(['status' => self::STATUS_ACCEPTED]);
    }

    public function markAsRejected(): void
    {
        $this->update(['status' => self::STATUS_REJECTED]);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Get the order that owns the quotation.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the related inquiry (either QuotationRequest or SupplierInquiry)
     */
    public function getInquiry()
    {
        if ($this->quotation_request_id) {
            return $this->quotationRequest;
        } elseif ($this->supplier_inquiry_id) {
            return $this->supplierInquiry;
        }
        return null;
    }

    /**
     * Approve the quotation
     */
    public function approve(): void
    {
        // Start transaction to ensure atomic operations
        DB::transaction(function () {
            $this->update([
                'status' => self::STATUS_ACCEPTED,
                'approved_at' => Carbon::now()
            ]);

            // Handle different inquiry types
            if ($this->order_id) {
                // Handle order-based quotations
                $order = $this->order()->lockForUpdate()->first();
                
                // Validate order state
                if (!in_array($order->status, [
                    Order::STATUS_AWAITING_QUOTATIONS,
                    Order::STATUS_QUOTATIONS_RECEIVED
                ])) {
                    throw new \InvalidArgumentException(
                        "Cannot approve quotation. Order is in invalid state: {$order->status}"
                    );
                }

                // Update order status
                $order->update([
                    'status' => Order::STATUS_APPROVED,
                    'quotation_status' => Order::QUOTATION_STATUS_APPROVED
                ]);

                // Reject all other quotations for this order
                static::where('order_id', $this->order_id)
                    ->where('id', '!=', $this->id)
                    ->update([
                        'status' => self::STATUS_REJECTED,
                        'rejected_at' => Carbon::now(),
                        'rejection_reason' => 'Another supplier quotation was approved'
                    ]);

                // Create purchase order
                $purchaseOrder = $this->createPurchaseOrder($order);
                
                // Send notification
                $this->supplier->notify(new QuotationApprovedNotification($this, $purchaseOrder));
            } elseif ($this->supplier_inquiry_id) {
                // Handle SupplierInquiry quotations
                $inquiry = $this->supplierInquiry;
                $inquiry->update(['status' => SupplierInquiry::STATUS_QUOTED]);
                
                // Update the supplier response status to 'accepted'
                if ($this->supplier_inquiry_response_id) {
                    $response = $this->supplierInquiryResponse;
                    if ($response) {
                        $response->update(['status' => SupplierInquiryResponse::STATUS_ACCEPTED]);
                    }
                }
                
                // Send notification without purchase order
                $this->supplier->notify(new QuotationApprovedNotification($this));
            } elseif ($this->quotation_request_id) {
                // Handle legacy QuotationRequest quotations
                $request = $this->quotationRequest;
                $request->update([
                    'status' => 'quote_created',
                    'generated_quote_id' => null // This would be set when customer quote is created
                ]);
                
                // Send notification
                $this->supplier->notify(new QuotationApprovedNotification($this));
            }
        });
    }

    /**
     * Create a purchase order from the approved quotation (for orders only)
     */
    private function createPurchaseOrder(Order $order): PurchaseOrder
    {
        // Get supplier information
        $supplier = $this->supplier;
        $supplierInfo = $supplier->supplierInformation;

        // Create the purchase order
        $purchaseOrder = PurchaseOrder::create([
            'order_id' => $order->id,
            'supplier_id' => $supplier->id,
            'supplier_quotation_id' => $this->id,
            'supplier_name' => $supplier->name,
            'supplier_email' => $supplier->email,
            'supplier_phone' => $supplierInfo->phone ?? null,
            'supplier_address' => $supplierInfo ? $this->formatSupplierAddress($supplierInfo) : null,
            'po_date' => Carbon::now()->toDateString(),
            'delivery_date_requested' => Carbon::now()->addDays(14)->toDateString(),
            'description' => "Purchase order for quotation #{$this->quotation_number} - Order #{$order->order_number}",
            'sub_total' => $this->total_amount ?? $this->total_price,
            'shipping_cost' => $this->shipping_cost ?? 0,
            'total_amount' => ($this->total_amount ?? $this->total_price) + ($this->shipping_cost ?? 0),
            'currency' => $this->currency,
            'status' => PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
            'sent_to_supplier_at' => Carbon::now(),
            'payment_status' => PurchaseOrder::PAYMENT_STATUS_PENDING,
            'payment_due_date' => Carbon::now()->addDays(30),
            'notes' => $this->notes,
            'terms_conditions' => $this->getDefaultTermsAndConditions(),
            'created_by' => auth()->id()
        ]);

        // Create purchase order items from order items
        foreach ($order->items as $orderItem) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $orderItem->product_id,
                'item_description' => $orderItem->product->name,
                'quantity' => $orderItem->quantity,
                'unit_price' => ($this->total_amount ?? $this->total_price) / $order->items->sum('quantity'),
                'line_total' => (($this->total_amount ?? $this->total_price) / $order->items->sum('quantity')) * $orderItem->quantity,
                'unit_of_measure' => 'pcs',
                'specifications' => $orderItem->product->specifications ?? 'Standard specifications',
                'sort_order' => $orderItem->id
            ]);
        }

        return $purchaseOrder;
    }

    /**
     * Format supplier address for purchase order
     */
    private function formatSupplierAddress($supplierInfo): string
    {
        $address = [];
        if ($supplierInfo->address) $address[] = $supplierInfo->address;
        if ($supplierInfo->city) $address[] = $supplierInfo->city;
        if ($supplierInfo->state) $address[] = $supplierInfo->state;
        if ($supplierInfo->postal_code) $address[] = $supplierInfo->postal_code;
        if ($supplierInfo->country) $address[] = $supplierInfo->country;
        
        return implode(', ', $address);
    }

    /**
     * Get default terms and conditions for purchase orders
     */
    private function getDefaultTermsAndConditions(): string
    {
        return "1. Payment Terms: Net 30 days from invoice date
2. Delivery: As per agreed delivery date
3. Quality: All products must meet specified requirements
4. Returns: Defective items may be returned within 7 days
5. Warranty: Standard manufacturer warranty applies
6. Compliance: All products must comply with UAE regulations";
    }

    /**
     * Reject the quotation
     */
    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => Carbon::now(),
            'rejection_reason' => $reason
        ]);

        // Notify supplier
        $this->supplier->notify(new QuotationRejectedNotification($this));
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_EXPIRED => 'Expired',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_SUBMITTED => 'bg-blue-100 text-blue-800',
            self::STATUS_ACCEPTED => 'bg-green-100 text-green-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_EXPIRED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}