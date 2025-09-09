<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
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
        'category',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get all available permission categories
     */
    public static function getCategories(): array
    {
        return [
            'dashboard' => 'Dashboard & Analytics',
            'users' => 'User Management',
            'roles' => 'Role & Permission Management',
            'products' => 'Product Management',
            'categories' => 'Category Management',
            'brands' => 'Brand Management',
            'orders' => 'Order Management',
            'customers' => 'Customer Management',
            'suppliers' => 'Supplier Management',
            'quotations' => 'Quotation Management',
            'purchase_orders' => 'Purchase Order Management',
            'invoices' => 'Invoice Management',
            'deliveries' => 'Delivery Management',
            'feedback' => 'Feedback Management',
            'news' => 'News Management',
            'crm' => 'CRM System',
            'marketing' => 'Marketing & Campaigns',
            'analytics' => 'Analytics & Reports',
            'system' => 'System Administration',
            'api' => 'API Access',
        ];
    }

    /**
     * Get permissions by category
     */
    public static function getByCategory(string $category)
    {
        return self::where('category', $category)
                   ->where('is_active', true)
                   ->orderBy('display_name')
                   ->get();
    }

    /**
     * Create default permissions for the system
     */
    public static function createDefaultPermissions(): void
    {
        $permissions = [
            // Dashboard & Analytics
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Access main dashboard', 'category' => 'dashboard'],
            ['name' => 'dashboard.analytics', 'display_name' => 'View Analytics', 'description' => 'Access analytics and reports', 'category' => 'dashboard'],
            ['name' => 'dashboard.admin', 'display_name' => 'Admin Dashboard', 'description' => 'Access admin-specific dashboard features', 'category' => 'dashboard'],
            
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'View user profiles and lists', 'category' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Create new user accounts', 'category' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Modify user profiles and settings', 'category' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Remove user accounts', 'category' => 'users'],
            ['name' => 'users.impersonate', 'display_name' => 'Impersonate Users', 'description' => 'Login as other users', 'category' => 'users'],
            ['name' => 'users.export', 'display_name' => 'Export Users', 'description' => 'Export user data', 'category' => 'users'],
            
            // Role & Permission Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'description' => 'View roles and permissions', 'category' => 'roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'description' => 'Create new roles', 'category' => 'roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'description' => 'Modify roles and permissions', 'category' => 'roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'description' => 'Remove roles', 'category' => 'roles'],
            ['name' => 'permissions.manage', 'display_name' => 'Manage Permissions', 'description' => 'Create and modify permissions', 'category' => 'roles'],
            
            // Product Management
            ['name' => 'products.view', 'display_name' => 'View Products', 'description' => 'View product catalog', 'category' => 'products'],
            ['name' => 'products.create', 'display_name' => 'Create Products', 'description' => 'Add new products', 'category' => 'products'],
            ['name' => 'products.edit', 'display_name' => 'Edit Products', 'description' => 'Modify product details', 'category' => 'products'],
            ['name' => 'products.delete', 'display_name' => 'Delete Products', 'description' => 'Remove products', 'category' => 'products'],
            ['name' => 'products.approve', 'display_name' => 'Approve Products', 'description' => 'Approve product submissions', 'category' => 'products'],
            ['name' => 'products.manage_inventory', 'display_name' => 'Manage Inventory', 'description' => 'Update stock levels', 'category' => 'products'],
            ['name' => 'products.manage_pricing', 'display_name' => 'Manage Pricing', 'description' => 'Set and modify product prices', 'category' => 'products'],
            ['name' => 'products.manage_specifications', 'display_name' => 'Manage Specifications', 'description' => 'Manage technical specifications', 'category' => 'products'],
            ['name' => 'products.export', 'display_name' => 'Export Products', 'description' => 'Export product data', 'category' => 'products'],
            
            // Category Management
            ['name' => 'categories.view', 'display_name' => 'View Categories', 'description' => 'View product categories', 'category' => 'categories'],
            ['name' => 'categories.create', 'display_name' => 'Create Categories', 'description' => 'Add new categories', 'category' => 'categories'],
            ['name' => 'categories.edit', 'display_name' => 'Edit Categories', 'description' => 'Modify category details', 'category' => 'categories'],
            ['name' => 'categories.delete', 'display_name' => 'Delete Categories', 'description' => 'Remove categories', 'category' => 'categories'],
            ['name' => 'categories.manage_hierarchy', 'display_name' => 'Manage Hierarchy', 'description' => 'Organize category structure', 'category' => 'categories'],
            
            // Brand Management
            ['name' => 'brands.view', 'display_name' => 'View Brands', 'description' => 'View brand information', 'category' => 'brands'],
            ['name' => 'brands.create', 'display_name' => 'Create Brands', 'description' => 'Add new brands', 'category' => 'brands'],
            ['name' => 'brands.edit', 'display_name' => 'Edit Brands', 'description' => 'Modify brand details', 'category' => 'brands'],
            ['name' => 'brands.delete', 'display_name' => 'Delete Brands', 'description' => 'Remove brands', 'category' => 'brands'],
            
            // Order Management
            ['name' => 'orders.view', 'display_name' => 'View Orders', 'description' => 'View order information', 'category' => 'orders'],
            ['name' => 'orders.view_all', 'display_name' => 'View All Orders', 'description' => 'View all system orders', 'category' => 'orders'],
            ['name' => 'orders.view_own', 'display_name' => 'View Own Orders', 'description' => 'View only own orders', 'category' => 'orders'],
            ['name' => 'orders.create', 'display_name' => 'Create Orders', 'description' => 'Place new orders', 'category' => 'orders'],
            ['name' => 'orders.edit', 'display_name' => 'Edit Orders', 'description' => 'Modify order details', 'category' => 'orders'],
            ['name' => 'orders.delete', 'display_name' => 'Delete Orders', 'description' => 'Cancel/remove orders', 'category' => 'orders'],
            ['name' => 'orders.manage_status', 'display_name' => 'Manage Order Status', 'description' => 'Update order statuses', 'category' => 'orders'],
            ['name' => 'orders.process', 'display_name' => 'Process Orders', 'description' => 'Process and fulfill orders', 'category' => 'orders'],
            ['name' => 'orders.export', 'display_name' => 'Export Orders', 'description' => 'Export order data', 'category' => 'orders'],
            
            // Customer Management
            ['name' => 'customers.view', 'display_name' => 'View Customers', 'description' => 'View customer information', 'category' => 'customers'],
            ['name' => 'customers.create', 'display_name' => 'Create Customers', 'description' => 'Add new customers', 'category' => 'customers'],
            ['name' => 'customers.edit', 'display_name' => 'Edit Customers', 'description' => 'Modify customer details', 'category' => 'customers'],
            ['name' => 'customers.delete', 'display_name' => 'Delete Customers', 'description' => 'Remove customer records', 'category' => 'customers'],
            ['name' => 'customers.view_sensitive', 'display_name' => 'View Sensitive Data', 'description' => 'Access sensitive customer information', 'category' => 'customers'],
            ['name' => 'customers.export', 'display_name' => 'Export Customers', 'description' => 'Export customer data', 'category' => 'customers'],
            
            // Supplier Management
            ['name' => 'suppliers.view', 'display_name' => 'View Suppliers', 'description' => 'View supplier information', 'category' => 'suppliers'],
            ['name' => 'suppliers.create', 'display_name' => 'Create Suppliers', 'description' => 'Add new suppliers', 'category' => 'suppliers'],
            ['name' => 'suppliers.edit', 'display_name' => 'Edit Suppliers', 'description' => 'Modify supplier details', 'category' => 'suppliers'],
            ['name' => 'suppliers.delete', 'display_name' => 'Delete Suppliers', 'description' => 'Remove suppliers', 'category' => 'suppliers'],
            ['name' => 'suppliers.approve', 'display_name' => 'Approve Suppliers', 'description' => 'Approve supplier applications', 'category' => 'suppliers'],
            ['name' => 'suppliers.manage_categories', 'display_name' => 'Manage Categories', 'description' => 'Assign suppliers to categories', 'category' => 'suppliers'],
            ['name' => 'suppliers.view_performance', 'display_name' => 'View Performance', 'description' => 'View supplier performance metrics', 'category' => 'suppliers'],
            ['name' => 'suppliers.manage_contracts', 'display_name' => 'Manage Contracts', 'description' => 'Manage supplier contracts', 'category' => 'suppliers'],
            ['name' => 'suppliers.manage_payments', 'display_name' => 'Manage Payments', 'description' => 'Process supplier payments', 'category' => 'suppliers'],
            
            // Supplier Self-Service
            ['name' => 'supplier.dashboard', 'display_name' => 'Supplier Dashboard', 'description' => 'Access supplier dashboard', 'category' => 'suppliers'],
            ['name' => 'supplier.products.view', 'display_name' => 'View Own Products', 'description' => 'View own products as supplier', 'category' => 'suppliers'],
            ['name' => 'supplier.products.create', 'display_name' => 'Create Products', 'description' => 'Add products as supplier', 'category' => 'suppliers'],
            ['name' => 'supplier.products.edit', 'display_name' => 'Edit Own Products', 'description' => 'Edit own products as supplier', 'category' => 'suppliers'],
            ['name' => 'supplier.products.delete', 'display_name' => 'Delete Own Products', 'description' => 'Remove own products as supplier', 'category' => 'suppliers'],
            ['name' => 'supplier.orders.view', 'display_name' => 'View Assigned Orders', 'description' => 'View orders assigned to supplier', 'category' => 'suppliers'],
            ['name' => 'supplier.orders.manage', 'display_name' => 'Manage Orders', 'description' => 'Update order status and details', 'category' => 'suppliers'],
            ['name' => 'supplier.inquiries.view', 'display_name' => 'View Inquiries', 'description' => 'View customer inquiries', 'category' => 'suppliers'],
            ['name' => 'supplier.inquiries.respond', 'display_name' => 'Respond to Inquiries', 'description' => 'Respond to customer inquiries', 'category' => 'suppliers'],
            ['name' => 'supplier.feedback.create', 'display_name' => 'Submit Feedback', 'description' => 'Submit system feedback', 'category' => 'suppliers'],
            
            // Quotation Management
            ['name' => 'quotations.view', 'display_name' => 'View Quotations', 'description' => 'View quotation requests', 'category' => 'quotations'],
            ['name' => 'quotations.create', 'display_name' => 'Create Quotations', 'description' => 'Create new quotations', 'category' => 'quotations'],
            ['name' => 'quotations.edit', 'display_name' => 'Edit Quotations', 'description' => 'Modify quotations', 'category' => 'quotations'],
            ['name' => 'quotations.delete', 'display_name' => 'Delete Quotations', 'description' => 'Remove quotations', 'category' => 'quotations'],
            ['name' => 'quotations.approve', 'display_name' => 'Approve Quotations', 'description' => 'Approve quotations before sending', 'category' => 'quotations'],
            ['name' => 'quotations.send', 'display_name' => 'Send Quotations', 'description' => 'Send quotations to customers', 'category' => 'quotations'],
            ['name' => 'quotations.compare', 'display_name' => 'Compare Quotations', 'description' => 'Compare multiple quotations', 'category' => 'quotations'],
            ['name' => 'quotations.convert', 'display_name' => 'Convert to Order', 'description' => 'Convert quotations to orders', 'category' => 'quotations'],
            
            // Purchase Order Management
            ['name' => 'purchase_orders.view', 'display_name' => 'View Purchase Orders', 'description' => 'View purchase orders', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.create', 'display_name' => 'Create Purchase Orders', 'description' => 'Create new purchase orders', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.edit', 'display_name' => 'Edit Purchase Orders', 'description' => 'Modify purchase orders', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.delete', 'display_name' => 'Delete Purchase Orders', 'description' => 'Remove purchase orders', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.approve', 'display_name' => 'Approve Purchase Orders', 'description' => 'Approve purchase orders', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.send', 'display_name' => 'Send to Suppliers', 'description' => 'Send purchase orders to suppliers', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.manage_status', 'display_name' => 'Manage Status', 'description' => 'Update purchase order status', 'category' => 'purchase_orders'],
            ['name' => 'purchase_orders.view_financials', 'display_name' => 'View Financial Info', 'description' => 'View purchase order costs', 'category' => 'purchase_orders'],
            
            // Invoice Management
            ['name' => 'invoices.view', 'display_name' => 'View Invoices', 'description' => 'View invoice information', 'category' => 'invoices'],
            ['name' => 'invoices.create', 'display_name' => 'Create Invoices', 'description' => 'Generate new invoices', 'category' => 'invoices'],
            ['name' => 'invoices.edit', 'display_name' => 'Edit Invoices', 'description' => 'Modify invoice details', 'category' => 'invoices'],
            ['name' => 'invoices.delete', 'display_name' => 'Delete Invoices', 'description' => 'Remove invoices', 'category' => 'invoices'],
            ['name' => 'invoices.send', 'display_name' => 'Send Invoices', 'description' => 'Email invoices to customers', 'category' => 'invoices'],
            ['name' => 'invoices.manage_payments', 'display_name' => 'Manage Payments', 'description' => 'Track and process payments', 'category' => 'invoices'],
            
            // Delivery Management
            ['name' => 'deliveries.view', 'display_name' => 'View Deliveries', 'description' => 'View delivery information', 'category' => 'deliveries'],
            ['name' => 'deliveries.create', 'display_name' => 'Create Deliveries', 'description' => 'Schedule new deliveries', 'category' => 'deliveries'],
            ['name' => 'deliveries.edit', 'display_name' => 'Edit Deliveries', 'description' => 'Modify delivery details', 'category' => 'deliveries'],
            ['name' => 'deliveries.delete', 'display_name' => 'Delete Deliveries', 'description' => 'Cancel deliveries', 'category' => 'deliveries'],
            ['name' => 'deliveries.track', 'display_name' => 'Track Deliveries', 'description' => 'Monitor delivery status', 'category' => 'deliveries'],
            ['name' => 'deliveries.confirm', 'display_name' => 'Confirm Deliveries', 'description' => 'Confirm delivery completion', 'category' => 'deliveries'],
            
            // Feedback Management
            ['name' => 'feedback.view', 'display_name' => 'View Feedback', 'description' => 'View customer feedback', 'category' => 'feedback'],
            ['name' => 'feedback.respond', 'display_name' => 'Respond to Feedback', 'description' => 'Reply to customer feedback', 'category' => 'feedback'],
            ['name' => 'feedback.delete', 'display_name' => 'Delete Feedback', 'description' => 'Remove feedback entries', 'category' => 'feedback'],
            ['name' => 'feedback.export', 'display_name' => 'Export Feedback', 'description' => 'Export feedback data', 'category' => 'feedback'],
            
            // News Management
            ['name' => 'news.view', 'display_name' => 'View News', 'description' => 'View news articles', 'category' => 'news'],
            ['name' => 'news.create', 'display_name' => 'Create News', 'description' => 'Write new articles', 'category' => 'news'],
            ['name' => 'news.edit', 'display_name' => 'Edit News', 'description' => 'Modify news articles', 'category' => 'news'],
            ['name' => 'news.delete', 'display_name' => 'Delete News', 'description' => 'Remove news articles', 'category' => 'news'],
            ['name' => 'news.publish', 'display_name' => 'Publish News', 'description' => 'Publish news articles', 'category' => 'news'],
            
            // CRM System
            ['name' => 'crm.access', 'display_name' => 'Access CRM', 'description' => 'Access CRM system', 'category' => 'crm'],
            ['name' => 'crm.leads.view', 'display_name' => 'View Leads', 'description' => 'View CRM leads', 'category' => 'crm'],
            ['name' => 'crm.leads.create', 'display_name' => 'Create Leads', 'description' => 'Add new leads', 'category' => 'crm'],
            ['name' => 'crm.leads.edit', 'display_name' => 'Edit Leads', 'description' => 'Modify lead information', 'category' => 'crm'],
            ['name' => 'crm.leads.delete', 'display_name' => 'Delete Leads', 'description' => 'Remove leads', 'category' => 'crm'],
            ['name' => 'crm.leads.assign', 'display_name' => 'Assign Leads', 'description' => 'Assign leads to users', 'category' => 'crm'],
            ['name' => 'crm.leads.convert', 'display_name' => 'Convert Leads', 'description' => 'Convert leads to customers', 'category' => 'crm'],
            ['name' => 'crm.contacts.view', 'display_name' => 'View Contacts', 'description' => 'View CRM contacts', 'category' => 'crm'],
            ['name' => 'crm.contacts.create', 'display_name' => 'Create Contacts', 'description' => 'Add new contacts', 'category' => 'crm'],
            ['name' => 'crm.contacts.edit', 'display_name' => 'Edit Contacts', 'description' => 'Modify contact information', 'category' => 'crm'],
            ['name' => 'crm.contacts.delete', 'display_name' => 'Delete Contacts', 'description' => 'Remove contacts', 'category' => 'crm'],
            ['name' => 'crm.activities.view', 'display_name' => 'View Activities', 'description' => 'View CRM activities', 'category' => 'crm'],
            ['name' => 'crm.activities.create', 'display_name' => 'Create Activities', 'description' => 'Log new activities', 'category' => 'crm'],
            ['name' => 'crm.activities.edit', 'display_name' => 'Edit Activities', 'description' => 'Modify activities', 'category' => 'crm'],
            ['name' => 'crm.tasks.view', 'display_name' => 'View Tasks', 'description' => 'View CRM tasks', 'category' => 'crm'],
            ['name' => 'crm.tasks.create', 'display_name' => 'Create Tasks', 'description' => 'Create new tasks', 'category' => 'crm'],
            ['name' => 'crm.tasks.edit', 'display_name' => 'Edit Tasks', 'description' => 'Modify tasks', 'category' => 'crm'],
            ['name' => 'crm.tasks.assign', 'display_name' => 'Assign Tasks', 'description' => 'Assign tasks to users', 'category' => 'crm'],
            
            // Marketing & Campaigns
            ['name' => 'marketing.access', 'display_name' => 'Access Marketing', 'description' => 'Access marketing features', 'category' => 'marketing'],
            ['name' => 'marketing.campaigns.view', 'display_name' => 'View Campaigns', 'description' => 'View marketing campaigns', 'category' => 'marketing'],
            ['name' => 'marketing.campaigns.create', 'display_name' => 'Create Campaigns', 'description' => 'Create marketing campaigns', 'category' => 'marketing'],
            ['name' => 'marketing.campaigns.edit', 'display_name' => 'Edit Campaigns', 'description' => 'Modify campaigns', 'category' => 'marketing'],
            ['name' => 'marketing.campaigns.delete', 'display_name' => 'Delete Campaigns', 'description' => 'Remove campaigns', 'category' => 'marketing'],
            ['name' => 'marketing.campaigns.send', 'display_name' => 'Send Campaigns', 'description' => 'Execute marketing campaigns', 'category' => 'marketing'],
            ['name' => 'marketing.templates.manage', 'display_name' => 'Manage Templates', 'description' => 'Create and edit email templates', 'category' => 'marketing'],
            ['name' => 'marketing.analytics', 'display_name' => 'Marketing Analytics', 'description' => 'View marketing performance', 'category' => 'marketing'],
            
            // Analytics & Reports
            ['name' => 'analytics.view', 'display_name' => 'View Analytics', 'description' => 'Access analytics dashboard', 'category' => 'analytics'],
            ['name' => 'analytics.advanced', 'display_name' => 'Advanced Analytics', 'description' => 'Access detailed analytics', 'category' => 'analytics'],
            ['name' => 'reports.generate', 'display_name' => 'Generate Reports', 'description' => 'Create custom reports', 'category' => 'analytics'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'description' => 'Export report data', 'category' => 'analytics'],
            ['name' => 'reports.schedule', 'display_name' => 'Schedule Reports', 'description' => 'Schedule automated reports', 'category' => 'analytics'],
            
            // System Administration
            ['name' => 'system.settings', 'display_name' => 'System Settings', 'description' => 'Manage system configuration', 'category' => 'system'],
            ['name' => 'system.maintenance', 'display_name' => 'System Maintenance', 'description' => 'Perform system maintenance', 'category' => 'system'],
            ['name' => 'system.logs', 'display_name' => 'View System Logs', 'description' => 'Access system logs', 'category' => 'system'],
            ['name' => 'system.backup', 'display_name' => 'System Backup', 'description' => 'Create and restore backups', 'category' => 'system'],
            ['name' => 'system.notifications', 'display_name' => 'Manage Notifications', 'description' => 'Configure system notifications', 'category' => 'system'],
            
            // API Access
            ['name' => 'api.access', 'display_name' => 'API Access', 'description' => 'Access API endpoints', 'category' => 'api'],
            ['name' => 'api.read', 'display_name' => 'API Read', 'description' => 'Read data via API', 'category' => 'api'],
            ['name' => 'api.write', 'display_name' => 'API Write', 'description' => 'Write data via API', 'category' => 'api'],
            ['name' => 'api.admin', 'display_name' => 'API Admin', 'description' => 'Full API administrative access', 'category' => 'api'],
        ];

        foreach ($permissions as $permission) {
            self::updateOrCreate(
                ['name' => $permission['name']],
                array_merge($permission, ['is_active' => true])
            );
        }
    }
}
