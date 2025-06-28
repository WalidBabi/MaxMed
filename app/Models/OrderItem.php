<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'variation',
        'discount_percentage',
        'discount_amount',
        'line_total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
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
            $subtotal = $item->quantity * $item->price;
            $discount = $item->discount_amount ?: ($subtotal * $item->discount_percentage / 100);
            $item->line_total = $subtotal - $discount;
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the formatted line total
     */
    public function getFormattedLineTotalAttribute()
    {
        return number_format($this->line_total, 2);
    }

    /**
     * Get the calculated discount amount
     */
    public function getCalculatedDiscountAmountAttribute()
    {
        if ($this->discount_amount > 0) {
            return $this->discount_amount;
        }
        
        if ($this->discount_percentage > 0) {
            $subtotal = $this->quantity * $this->price;
            return $subtotal * ($this->discount_percentage / 100);
        }
        
        return 0;
    }
}
