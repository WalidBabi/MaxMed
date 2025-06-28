<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'item_description',
        'size',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'line_total',
        'unit_of_measure',
        'specifications',
        'sort_order'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            // Calculate line total
            $subtotal = $item->quantity * $item->unit_price;
            $discount = $item->discount_amount ?: ($subtotal * $item->discount_percentage / 100);
            $item->line_total = $subtotal - $discount;
        });

        static::saved(function ($item) {
            // Recalculate invoice totals when item is saved
            if ($item->invoice) {
                $item->invoice->calculateTotals();
            }
        });

        static::deleted(function ($item) {
            // Recalculate invoice totals when item is deleted
            if ($item->invoice) {
                $item->invoice->calculateTotals();
            }
        });
    }

    /**
     * Relationships
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->line_total, 2);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedLineTotalAttribute()
    {
        return number_format($this->line_total, 2);
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return number_format($this->discount_amount, 2);
    }

    public function getCalculatedDiscountAmountAttribute()
    {
        if ($this->discount_amount > 0) {
            return $this->discount_amount;
        }
        
        if ($this->discount_percentage > 0) {
            $subtotal = $this->quantity * $this->unit_price;
            return $subtotal * ($this->discount_percentage / 100);
        }
        
        return 0;
    }
} 