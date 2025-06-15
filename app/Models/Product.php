<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
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

    /**
     * Generate a unique SKU for the product
     */
    public static function generateSku($productId = null, $brandId = null)
    {
        // Get the appropriate prefix based on brand
        $prefix = static::getSkuPrefix($brandId);
        
        // Find the next sequential number for this brand prefix
        $lastProduct = static::where('sku', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(sku, ' . (strlen($prefix) + 1) . ', 4) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastProduct && $lastProduct->sku) {
            // Extract the number part from the SKU
            $numberPart = substr($lastProduct->sku, strlen($prefix), 4);
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
            }
        }
        
        $sku = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Check if SKU already exists (safety check)
        $counter = 0;
        while (static::where('sku', $sku)->exists() && $counter < 1000) {
            $nextNumber++;
            $sku = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $sku;
    }

    /**
     * Get SKU prefix based on brand ID
     */
    public static function getSkuPrefix($brandId = null)
    {
        if (!$brandId) {
            return 'MM-'; // Default for products without brand
        }

        $brand = \App\Models\Brand::find($brandId);
        if (!$brand) {
            return 'MM-'; // Default if brand not found
        }

        $brandName = strtolower($brand->name);
        
        if (str_contains($brandName, 'maxtest')) {
            return 'MT-';
        } elseif (str_contains($brandName, 'maxware')) {
            return 'MW-';
        } else {
            return 'MM-'; // Default for other brands
        }
    }

    /**
     * Boot the model and set up event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate SKU when creating a new product (if not provided)
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = static::generateSku(null, $product->brand_id);
            }
        });
    }
}
