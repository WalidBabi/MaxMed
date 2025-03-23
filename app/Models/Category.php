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
        return 'id'; // or 'slug' if you are using slugs
    }
} 