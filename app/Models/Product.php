<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'price_aed', 
        'image_url', 
        'category_id', 
        'brand_id',
        'supplier_id',
        'has_size_options',
        'size_options',
        'pdf_file'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_size_options' => 'boolean',
        'size_options' => 'array'
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

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function inStock()
    {
        // If no inventory record exists, you could default to 0 or handle it otherwise.
        return $this->inventory && $this->inventory->quantity > 0;
    }
}
