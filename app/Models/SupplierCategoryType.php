<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupplierCategoryType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the suppliers assigned to this category type
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'supplier_category_type_user')
            ->whereHas('role', function($query) {
                $query->where('name', 'supplier');
            })
            ->withTimestamps();
    }

    /**
     * Get the active suppliers assigned to this category type
     */
    public function activeSuppliers(): BelongsToMany
    {
        return $this->suppliers();
    }

    /**
     * Scope to get only active category types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get categories with supplier count
     */
    public function scopeWithSupplierCount($query)
    {
        return $query->withCount('suppliers');
    }

    /**
     * Scope to get categories with active supplier count
     */
    public function scopeWithActiveSupplierCount($query)
    {
        return $query->withCount(['suppliers as active_suppliers_count']);
    }

    /**
     * Get the route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate a unique slug
     */
    public function generateSlug()
    {
        $slug = \Str::slug($this->name);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();
        
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        
        return $slug;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });
    }
} 