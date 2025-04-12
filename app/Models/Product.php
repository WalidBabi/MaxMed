<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'price_aed', 'image_url', 'category_id', 'brand_id', 'application'];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function inStock()
    {
        // If no inventory record exists, you could default to 0 or handle it otherwise.
        return $this->inventory && $this->inventory->quantity > 0;
    }
}
