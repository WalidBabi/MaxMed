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

    // Get parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
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