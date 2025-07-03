<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'size',
        'quantity',
        'unit_price',
        'subtotal',
        'tax',
        'total',
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
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            // Calculate subtotal and total
            $item->subtotal = $item->quantity * $item->unit_price;
            $item->total = $item->subtotal + ($item->tax ?? 0);
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
        return number_format($this->total, 2);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAttribute()
    {
        return number_format($this->tax ?? 0, 2);
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

    public function getFormattedLineTotalAttribute()
    {
        // Use line_total if set, otherwise fallback to total
        $value = $this->line_total ?? $this->total ?? 0;
        return number_format($value, 2);
    }
} 