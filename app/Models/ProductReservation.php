<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReservation extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'session_id',
        'expires_at',
        'status'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 