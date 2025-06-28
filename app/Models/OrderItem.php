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
        'discount_amount'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCalculatedDiscountAmountAttribute()
    {
        if ($this->discount_amount > 0) {
            return $this->discount_amount;
        }

        if ($this->discount_percentage > 0) {
            $subtotal = $this->price * $this->quantity;
            return $subtotal * ($this->discount_percentage / 100);
        }

        return 0;
    }

    public function getLineSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getLineTotalAttribute()
    {
        $subtotal = $this->line_subtotal;
        $discount = $this->calculated_discount_amount;
        return $subtotal - $discount;
    }
}
