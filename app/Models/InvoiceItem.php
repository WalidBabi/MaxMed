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
        'line_total' => 'decimal:2',
        'sort_order' => 'integer'
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
    public function getFormattedLineTotalAttribute()
    {
        return number_format($this->line_total, 2);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2);
    }
} 