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
     * Get the permissions that belong to this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
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
            // First check the new permission system
            if ($this->permissions()->where('name', $permission)->where('is_active', true)->exists()) {
                return true;
            }
            
            // Fallback to legacy permissions array for backwards compatibility
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
     * Check if role has any of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if role has all of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assign a permission to this role.
     *
     * @param string|Permission $permission
     * @return void
     */
    public function givePermissionTo($permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Remove a permission from this role.
     *
     * @param string|Permission $permission
     * @return void
     */
    public function revokePermissionTo($permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        
        $this->permissions()->detach($permission->id);
    }

    /**
     * Sync permissions for this role.
     *
     * @param array $permissions
     * @return void
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permissionModel = Permission::where('name', $permission)->first();
                if ($permissionModel) {
                    $permissionIds[] = $permissionModel->id;
                }
            } elseif ($permission instanceof Permission) {
                $permissionIds[] = $permission->id;
            } elseif (is_numeric($permission)) {
                $permissionIds[] = $permission;
            }
        }
        
        $this->permissions()->sync($permissionIds);
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
            
            // Purchase Order Management
            'purchase_orders.view' => 'View Purchase Orders',
            'purchase_orders.create' => 'Create Purchase Orders',
            'purchase_orders.edit' => 'Edit Purchase Orders',
            'purchase_orders.delete' => 'Delete Purchase Orders',
            'purchase_orders.approve' => 'Approve Purchase Orders',
            'purchase_orders.send_to_supplier' => 'Send Purchase Orders to Suppliers',
            'purchase_orders.manage_status' => 'Manage Purchase Order Status',
            'purchase_orders.view_financials' => 'View Purchase Order Financial Information',
            'purchase_orders.manage_payments' => 'Manage Purchase Order Payments',
            
            // Supplier Management
            'suppliers.view' => 'View Suppliers',
            'suppliers.create' => 'Create Suppliers',
            'suppliers.edit' => 'Edit Suppliers',
            'suppliers.delete' => 'Delete Suppliers',
            'suppliers.manage_contracts' => 'Manage Supplier Contracts',
            'suppliers.view_performance' => 'View Supplier Performance',
            
            // Quotation Management
            'quotations.view' => 'View Quotations',
            'quotations.create' => 'Create Quotations',
            'quotations.edit' => 'Edit Quotations',
            'quotations.delete' => 'Delete Quotations',
            'quotations.approve' => 'Approve Quotations',
            'quotations.compare' => 'Compare Quotations',
            
            // Procurement Analytics
            'procurement.analytics' => 'View Procurement Analytics',
            'procurement.reports' => 'Generate Procurement Reports',
            'procurement.budget_tracking' => 'Track Procurement Budget',
            
            // CRM Lead Management
            'crm.leads.view' => 'View CRM Leads',
            'crm.leads.create' => 'Create CRM Leads',
            'crm.leads.edit' => 'Edit CRM Leads',
            'crm.leads.delete' => 'Delete CRM Leads',
            'crm.leads.assign' => 'Assign Leads to Users',
            'crm.leads.convert' => 'Convert Leads to Deals',
            'crm.leads.export' => 'Export Lead Data',
            'crm.leads.import' => 'Import Lead Data',
            'crm.leads.merge' => 'Merge Duplicate Leads',
            'crm.leads.bulk_actions' => 'Perform Bulk Actions on Leads',
            
            // CRM Deal Management
            'crm.deals.view' => 'View CRM Deals',
            'crm.deals.create' => 'Create CRM Deals',
            'crm.deals.edit' => 'Edit CRM Deals',
            'crm.deals.delete' => 'Delete CRM Deals',
            'crm.deals.assign' => 'Assign Deals to Users',
            'crm.deals.close' => 'Close Deals (Won/Lost)',
            'crm.deals.export' => 'Export Deal Data',
            'crm.deals.pipeline' => 'View Sales Pipeline',
            'crm.deals.forecast' => 'View Sales Forecast',
            
            // CRM Activity Management
            'crm.activities.view' => 'View CRM Activities',
            'crm.activities.create' => 'Create CRM Activities',
            'crm.activities.edit' => 'Edit CRM Activities',
            'crm.activities.delete' => 'Delete CRM Activities',
            'crm.activities.complete' => 'Mark Activities as Complete',
            'crm.activities.schedule' => 'Schedule Activities',
            'crm.activities.timeline' => 'View Activity Timeline',
            
            // CRM Contact Management
            'crm.contacts.view' => 'View CRM Contacts',
            'crm.contacts.create' => 'Create CRM Contacts',
            'crm.contacts.edit' => 'Edit CRM Contacts',
            'crm.contacts.delete' => 'Delete CRM Contacts',
            'crm.contacts.merge' => 'Merge Duplicate Contacts',
            'crm.contacts.export' => 'Export Contact Data',
            'crm.contacts.import' => 'Import Contact Data',
            
            // CRM Campaign Management
            'crm.campaigns.view' => 'View Marketing Campaigns',
            'crm.campaigns.create' => 'Create Marketing Campaigns',
            'crm.campaigns.edit' => 'Edit Marketing Campaigns',
            'crm.campaigns.delete' => 'Delete Marketing Campaigns',
            'crm.campaigns.execute' => 'Execute Campaigns',
            'crm.campaigns.track' => 'Track Campaign Performance',
            
            // CRM Task Management
            'crm.tasks.view' => 'View CRM Tasks',
            'crm.tasks.create' => 'Create CRM Tasks',
            'crm.tasks.edit' => 'Edit CRM Tasks',
            'crm.tasks.delete' => 'Delete CRM Tasks',
            'crm.tasks.assign' => 'Assign Tasks to Users',
            'crm.tasks.complete' => 'Mark Tasks as Complete',
            'crm.tasks.overdue' => 'View Overdue Tasks',
            
            // CRM Reporting & Analytics
            'crm.reports.view' => 'View CRM Reports',
            'crm.reports.create' => 'Create Custom Reports',
            'crm.reports.export' => 'Export CRM Reports',
            'crm.analytics.view' => 'View CRM Analytics',
            'crm.analytics.dashboard' => 'View CRM Dashboard',
            'crm.analytics.performance' => 'View Performance Metrics',
            
            // CRM Communication
            'crm.communication.email' => 'Send CRM Emails',
            'crm.communication.sms' => 'Send SMS Messages',
            'crm.communication.call' => 'Log Phone Calls',
            'crm.communication.meeting' => 'Schedule Meetings',
            'crm.communication.templates' => 'Manage Email Templates',
            
            // CRM Integration & Automation
            'crm.integration.webhooks' => 'Manage Webhooks',
            'crm.integration.api' => 'Access CRM API',
            'crm.automation.workflows' => 'Manage Workflows',
            'crm.automation.rules' => 'Manage Automation Rules',
            'crm.automation.triggers' => 'Manage Triggers',
            
            // CRM Administration
            'crm.admin.settings' => 'Manage CRM Settings',
            'crm.admin.fields' => 'Manage Custom Fields',
            'crm.admin.workflows' => 'Manage System Workflows',
            'crm.admin.integrations' => 'Manage Integrations',
            'crm.admin.backup' => 'Backup CRM Data',
            'crm.admin.restore' => 'Restore CRM Data',
        ];
    }

    /**
     * Get all available permissions with descriptions (alias for getAllPermissions)
     */
    public static function getAvailablePermissions(): array
    {
        return self::getAllPermissions();
    }
} 