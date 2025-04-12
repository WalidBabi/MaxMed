<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_url',
        'is_featured',
        'sort_order'
    ];

    /**
     * Get all products for the brand
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
