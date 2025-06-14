<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'product_id',
        'item_details',
        'quantity',
        'rate',
        'discount',
        'amount',
        'sort_order'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'discount' => 'decimal:2',
        'amount' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            $item->calculateAmount();
        });
    }

    /**
     * Calculate the amount based on quantity, rate, and discount
     */
    public function calculateAmount()
    {
        $subtotal = $this->quantity * $this->rate;
        $discountAmount = ($subtotal * $this->discount) / 100;
        $this->amount = $subtotal - $discountAmount;
    }

    /**
     * Get the quote that owns this item
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the product associated with this item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
} 