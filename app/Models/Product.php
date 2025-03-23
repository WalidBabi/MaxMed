<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'price_aed', 'image_url', 'category_id'];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function inStock()
    {
        // If no inventory record exists, you could default to 0 or handle it otherwise.
        return $this->inventory && $this->inventory->quantity > 0;
    }
}
