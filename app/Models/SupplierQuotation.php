<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierQuotation extends Model
{
    protected $fillable = [
        'quotation_request_id',
        'supplier_id',
        'product_id',
        'quotation_number',
        'unit_price',
        'currency',
        'minimum_quantity',
        'lead_time_days',
        'valid_until',
        'size',
        'specifications',
        'description',
        'supplier_notes',
        'terms_conditions',
        'status',
        'submitted_at',
        'attachments',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'submitted_at' => 'datetime',
        'specifications' => 'array',
        'attachments' => 'array',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Boot the model and set up event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate quotation number when creating
        static::creating(function ($quotation) {
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = static::generateQuotationNumber();
            }
        });
    }

    /**
     * Generate unique quotation number
     */
    public static function generateQuotationNumber(): string
    {
        $lastQuotation = static::orderBy('id', 'desc')->first();
        $number = $lastQuotation ? intval(substr($lastQuotation->quotation_number, 3)) + 1 : 1;
        return 'SQ-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the quotation request
     */
    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    /**
     * Get the supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if quotation is submitted
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if quotation is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Scope to get submitted quotations
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope to get quotations by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Calculate total price based on quantity
     */
    public function calculateTotalPrice(int $quantity): float
    {
        return $this->unit_price * max($quantity, $this->minimum_quantity);
    }
} 