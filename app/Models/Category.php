<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'parent_id', 'order', 'image_url'];
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Recursive relationship to get subcategories
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Alias for subcategories to maintain compatibility
    public function children()
    {
        return $this->subcategories();
    }

    // Get parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Get suppliers assigned to this category
    public function suppliers()
    {
        return $this->belongsToMany(User::class, 'supplier_categories', 'category_id', 'supplier_id')
                    ->withPivot([
                        'status', 'minimum_order_value', 'lead_time_days', 'notes',
                        'commission_rate', 'avg_response_time_hours', 'quotation_win_rate',
                        'total_quotations', 'won_quotations', 'avg_customer_rating',
                        'assigned_by', 'assigned_at', 'last_quotation_at'
                    ])
                    ->withTimestamps();
    }

    // Get active suppliers for this category
    public function activeSuppliers()
    {
        return $this->suppliers()->wherePivot('status', 'active');
    }

    // Check if category has subcategories
    public function hasSubcategories()
    {
        return $this->subcategories()->exists();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate a SEO-friendly slug
     */
    public function generateSlug()
    {
        $text = $this->name;
        
        // For subcategories, include parent for better SEO structure
        if ($this->parent_id) {
            $parent = static::find($this->parent_id);
            if ($parent) {
                $text = $parent->name . ' ' . $text;
            }
        }

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

        // Auto-generate slug when creating a new category (if not provided)
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateSlug();
            }
        });

        // Auto-update slug when updating a category name
        static::updating(function ($category) {
            if ($category->isDirty(['name', 'parent_id']) && empty($category->getOriginal('slug'))) {
                $category->slug = $category->generateSlug();
            }
        });
    }
} 