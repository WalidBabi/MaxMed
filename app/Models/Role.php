<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get all available permissions.
     *
     * @return array
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'dashboard.view' => 'View Dashboard',
            'users.view' => 'View Users',
            'users.create' => 'Create Users',
            'users.edit' => 'Edit Users',
            'users.delete' => 'Delete Users',
            'roles.view' => 'View Roles',
            'roles.create' => 'Create Roles',
            'roles.edit' => 'Edit Roles',
            'roles.delete' => 'Delete Roles',
            'products.view' => 'View Products',
            'products.create' => 'Create Products',
            'products.edit' => 'Edit Products',
            'products.delete' => 'Delete Products',
            'orders.view' => 'View Orders',
            'orders.create' => 'Create Orders',
            'orders.edit' => 'Edit Orders',
            'orders.delete' => 'Delete Orders',
            'customers.view' => 'View Customers',
            'customers.create' => 'Create Customers',
            'customers.edit' => 'Edit Customers',
            'customers.delete' => 'Delete Customers',
            'deliveries.view' => 'View Deliveries',
            'deliveries.create' => 'Create Deliveries',
            'deliveries.edit' => 'Edit Deliveries',
            'deliveries.delete' => 'Delete Deliveries',
            'categories.view' => 'View Categories',
            'categories.create' => 'Create Categories',
            'categories.edit' => 'Edit Categories',
            'categories.delete' => 'Delete Categories',
            'brands.view' => 'View Brands',
            'brands.create' => 'Create Brands',
            'brands.edit' => 'Edit Brands',
            'brands.delete' => 'Delete Brands',
            'news.view' => 'View News',
            'news.create' => 'Create News',
            'news.edit' => 'Edit News',
            'news.delete' => 'Delete News',
        ];
    }
} 