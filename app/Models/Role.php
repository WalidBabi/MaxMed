<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        try {
            if (!$this->permissions) {
                return false;
            }
            
            return in_array($permission, $this->permissions ?? []);
            
        } catch (\Exception $e) {
            // Return false as fallback
            return false;
        }
    }

    /**
     * Get all available permissions with descriptions
     */
    public static function getAllPermissions(): array
    {
        return [
            // Dashboard Permissions
            'dashboard.view' => 'View Dashboard',
            'dashboard.analytics' => 'View Dashboard Analytics',
            
            // Product Management
            'products.view' => 'View All Products',
            'products.create' => 'Create Products',
            'products.edit' => 'Edit All Products',
            'products.delete' => 'Delete Products',
            'products.manage_inventory' => 'Manage Product Inventory',
            
            // Order Management
            'orders.view' => 'View All Orders',
            'orders.create' => 'Create Orders',
            'orders.edit' => 'Edit Orders',
            'orders.delete' => 'Delete Orders',
            'orders.manage_status' => 'Manage Order Status',
            
            // Customer Management
            'customers.view' => 'View Customers',
            'customers.create' => 'Create Customers',
            'customers.edit' => 'Edit Customers',
            'customers.delete' => 'Delete Customers',
            
            // Delivery Management
            'deliveries.view' => 'View Deliveries',
            'deliveries.create' => 'Create Deliveries',
            'deliveries.edit' => 'Edit Deliveries',
            'deliveries.delete' => 'Delete Deliveries',
            
            // Category Management
            'categories.view' => 'View Categories',
            'categories.create' => 'Create Categories',
            'categories.edit' => 'Edit Categories',
            'categories.delete' => 'Delete Categories',
            
            // Brand Management
            'brands.view' => 'View Brands',
            'brands.create' => 'Create Brands',
            'brands.edit' => 'Edit Brands',
            'brands.delete' => 'Delete Brands',
            
            // News Management
            'news.view' => 'View News',
            'news.create' => 'Create News',
            'news.edit' => 'Edit News',
            'news.delete' => 'Delete News',
            
            // Feedback Management
            'feedback.view' => 'View Feedback',
            'feedback.respond' => 'Respond to Feedback',
            'feedback.delete' => 'Delete Feedback',
            
            // User Management
            'users.view' => 'View Users',
            'users.create' => 'Create Users',
            'users.edit' => 'Edit Users',
            'users.delete' => 'Delete Users',
            
            // Role Management
            'roles.view' => 'View Roles',
            'roles.create' => 'Create Roles',
            'roles.edit' => 'Edit Roles',
            'roles.delete' => 'Delete Roles',
            
            // Supplier Permissions (Category-Specific)
            'supplier.products.view' => 'View Own Products (Supplier)',
            'supplier.products.create' => 'Create Products (Supplier)',
            'supplier.products.edit' => 'Edit Own Products (Supplier)',
            'supplier.products.delete' => 'Delete Own Products (Supplier)',
            'supplier.products.specifications' => 'Manage Product Specifications (Supplier)',
            'supplier.categories.view' => 'View Assigned Categories (Supplier)',
            'supplier.categories.products' => 'Manage Products in Assigned Categories (Supplier)',
            'supplier.orders.view' => 'View Own Orders (Supplier)',
            'supplier.orders.manage' => 'Manage Own Orders (Supplier)',
            'supplier.feedback.create' => 'Submit Feedback (Supplier)',
            'supplier.inquiries.view' => 'View Assigned Inquiries (Supplier)',
            'supplier.inquiries.respond' => 'Respond to Inquiries (Supplier)',
            
            // CRM Permissions
            'crm.leads.view' => 'View CRM Leads',
            'crm.leads.create' => 'Create CRM Leads',
            'crm.leads.edit' => 'Edit CRM Leads',
            'crm.leads.delete' => 'Delete CRM Leads',
            'crm.contacts.view' => 'View CRM Contacts',
            'crm.contacts.create' => 'Create CRM Contacts',
            'crm.contacts.edit' => 'Edit CRM Contacts',
            'crm.contacts.delete' => 'Delete CRM Contacts',
            'crm.campaigns.view' => 'View Marketing Campaigns',
            'crm.campaigns.create' => 'Create Marketing Campaigns',
            'crm.campaigns.edit' => 'Edit Marketing Campaigns',
            'crm.campaigns.delete' => 'Delete Marketing Campaigns',
        ];
    }
} 