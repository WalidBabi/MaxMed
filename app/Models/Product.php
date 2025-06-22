<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name', 
        'slug',
        'description', 
        'price', 
        'price_aed', 
        'image_url', 
        'category_id', 
        'brand_id',
        'supplier_id',
        'has_size_options',
        'size_options',
        'pdf_file',
        'average_rating',
        'review_count'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_size_options' => 'boolean',
        'size_options' => 'array',
        'average_rating' => 'decimal:1',
        'review_count' => 'integer'
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

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function getSpecificationsByCategory()
    {
        return $this->specifications()
            ->where('show_on_detail', true)
            ->orderBy('category', 'asc')
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('category');
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
     * Get the route key name for URL binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate a SEO-friendly slug
     */
    public function generateSlug()
    {
        $text = trim($this->name . ' ' . ($this->sku ?? ''));
        
        // Add brand name for better SEO
        if ($this->brand_id) {
            $brand = \App\Models\Brand::find($this->brand_id);
            if ($brand && !stripos($text, $brand->name)) {
                $text .= ' ' . $brand->name;
            }
        }

        // Add location for local SEO
        $text .= ' dubai uae';

        // Clean and create SEO-friendly slug
        $slug = \Illuminate\Support\Str::lower($text);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Limit length
        if (strlen($slug) > 100) {
            $slug = substr($slug, 0, 100);
            $slug = rtrim($slug, '-');
        }

        return $this->ensureUniqueSlug($slug);
    }

    /**
     * Ensure slug is unique
     */
    private function ensureUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $existing = static::where('slug', $slug);
            if ($this->exists) {
                $existing = $existing->where('id', '!=', $this->id);
            }
            
            if (!$existing->exists()) {
                return $slug;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;

            if ($counter > 100) {
                $slug = $baseSlug . '-' . time();
                break;
            }
        }

        return $slug;
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
            
            // Auto-generate slug when creating a new product (if not provided)
            if (empty($product->slug)) {
                $product->slug = $product->generateSlug();
            }
        });

        // Auto-update slug when updating a product name or brand
        static::updating(function ($product) {
            if ($product->isDirty(['name', 'sku', 'brand_id']) && empty($product->getOriginal('slug'))) {
                $product->slug = $product->generateSlug();
            }
        });
    }
}
