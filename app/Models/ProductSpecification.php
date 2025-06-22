<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpecification extends Model
{
    protected $fillable = [
        'product_id',
        'specification_key',
        'specification_value',
        'unit',
        'category',
        'display_name',
        'description',
        'sort_order',
        'is_filterable',
        'is_searchable',
        'show_on_listing',
        'show_on_detail',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'show_on_listing' => 'boolean',
        'show_on_detail' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the product that owns the specification
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted specification value with unit
     */
    public function getFormattedValueAttribute(): string
    {
        $value = $this->specification_value;
        
        if ($this->unit) {
            return $value . ' ' . $this->unit;
        }
        
        return $value;
    }

    /**
     * Scope for filterable specifications
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope for specifications shown on listings
     */
    public function scopeForListing($query)
    {
        return $query->where('show_on_listing', true)->orderBy('sort_order');
    }

    /**
     * Scope for specifications shown on detail page
     */
    public function scopeForDetail($query)
    {
        return $query->where('show_on_detail', true)->orderBy('category', 'asc')->orderBy('sort_order', 'asc');
    }

    /**
     * Group specifications by category
     */
    public static function getSpecificationsByCategory($productId)
    {
        return self::where('product_id', $productId)
            ->forDetail()
            ->get()
            ->groupBy('category');
    }
}
