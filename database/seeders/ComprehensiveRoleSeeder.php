<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class ComprehensiveRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create all permissions
        Permission::createDefaultPermissions();

        // Define comprehensive business roles for MaxMed
        $roles = [
            // Super Administrator - Full system access
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Complete system administrator with all permissions',
                'permissions' => 'all', // Special flag to assign all permissions
            ],

            // System Administrator - Technical administration
            [
                'name' => 'system_admin',
                'display_name' => 'System Administrator',
                'description' => 'Technical system administrator with system management capabilities',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics', 'dashboard.admin',
                    'system.settings', 'system.maintenance', 'system.logs', 'system.backup', 'system.notifications',
                    'users.view', 'users.create', 'users.edit', 'users.delete', 'users.export',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'permissions.manage',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export', 'reports.schedule',
                    'api.access', 'api.read', 'api.write', 'api.admin',
                ],
            ],

            // Business Administrator - Business operations
            [
                'name' => 'business_admin',
                'display_name' => 'Business Administrator',
                'description' => 'Business administrator with operational oversight',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics', 'dashboard.admin',
                    'users.view', 'users.create', 'users.edit', 'users.export',
                    'roles.view',
                    'products.view', 'products.create', 'products.edit', 'products.delete', 'products.approve', 'products.manage_inventory', 'products.manage_pricing', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.delete', 'categories.manage_hierarchy',
                    'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.process', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.view_sensitive', 'customers.export',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.approve', 'suppliers.manage_categories', 'suppliers.view_performance', 'suppliers.manage_contracts',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.manage_payments',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.track', 'deliveries.confirm',
                    'feedback.view', 'feedback.respond', 'feedback.export',
                    'news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export',
                ],
            ],

            // Operations Manager - Day-to-day operations
            [
                'name' => 'operations_manager',
                'display_name' => 'Operations Manager',
                'description' => 'Manages daily operations, orders, and inventory',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.create', 'products.edit', 'products.manage_inventory', 'products.manage_pricing', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit',
                    'brands.view', 'brands.create', 'brands.edit',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.process', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.export',
                    'suppliers.view', 'suppliers.edit', 'suppliers.manage_categories', 'suppliers.view_performance',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.track', 'deliveries.confirm',
                    'feedback.view', 'feedback.respond',
                    'analytics.view', 'reports.generate',
                ],
            ],

            // Sales Manager - Sales team leadership
            [
                'name' => 'sales_manager',
                'display_name' => 'Sales Manager',
                'description' => 'Manages sales team and sales processes',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.manage_pricing',
                    'categories.view', 'brands.view',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.view_sensitive', 'customers.export',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
                    'feedback.view', 'feedback.respond',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.assign', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.send', 'marketing.analytics',
                    'analytics.view', 'reports.generate', 'reports.export',
                ],
            ],

            // Sales Representative - Individual sales
            [
                'name' => 'sales_rep',
                'display_name' => 'Sales Representative',
                'description' => 'Individual sales representative with customer interaction capabilities',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_own', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'invoices.view', 'invoices.create', 'invoices.edit',
                    'feedback.view',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit',
                    'analytics.view',
                ],
            ],

            // Purchasing Manager - Procurement and supplier management
            [
                'name' => 'purchasing_manager',
                'display_name' => 'Purchasing Manager',
                'description' => 'Manages procurement, suppliers, and purchase orders',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.manage_inventory',
                    'categories.view', 'brands.view',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.approve', 'suppliers.manage_categories', 'suppliers.view_performance', 'suppliers.manage_contracts', 'suppliers.manage_payments',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.compare',
                    'orders.view_all', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'feedback.view', 'feedback.respond',
                    'analytics.view', 'reports.generate',
                ],
            ],

            // Purchasing Assistant - Procurement support
            [
                'name' => 'purchasing_assistant',
                'display_name' => 'Purchasing Assistant',
                'description' => 'Assists with procurement processes and supplier coordination',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'categories.view', 'brands.view',
                    'suppliers.view', 'suppliers.edit', 'suppliers.view_performance',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.send', 'purchase_orders.manage_status',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.compare',
                    'orders.view_all', 'orders.create', 'orders.edit',
                    'customers.view',
                    'feedback.view',
                ],
            ],

            // Inventory Manager - Stock and warehouse management
            [
                'name' => 'inventory_manager',
                'display_name' => 'Inventory Manager',
                'description' => 'Manages inventory, stock levels, and deliveries',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.create', 'products.edit', 'products.manage_inventory', 'products.manage_specifications', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit',
                    'brands.view', 'brands.create', 'brands.edit',
                    'orders.view_all', 'orders.edit', 'orders.manage_status', 'orders.process',
                    'purchase_orders.view', 'purchase_orders.edit', 'purchase_orders.manage_status',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.track', 'deliveries.confirm',
                    'suppliers.view', 'suppliers.view_performance',
                    'analytics.view', 'reports.generate',
                ],
            ],

            // Customer Service Manager - Customer support leadership
            [
                'name' => 'customer_service_manager',
                'display_name' => 'Customer Service Manager',
                'description' => 'Manages customer service team and customer relations',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.view_sensitive', 'customers.export',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
                    'deliveries.view', 'deliveries.track',
                    'feedback.view', 'feedback.respond', 'feedback.export',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'analytics.view', 'reports.generate',
                ],
            ],

            // Customer Service Representative - Front-line support
            [
                'name' => 'customer_service_rep',
                'display_name' => 'Customer Service Representative',
                'description' => 'Provides customer support and handles inquiries',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_all', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'quotations.view', 'quotations.create', 'quotations.edit',
                    'invoices.view',
                    'deliveries.view', 'deliveries.track',
                    'feedback.view', 'feedback.respond',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit',
                ],
            ],

            // Marketing Manager - Marketing and campaigns
            [
                'name' => 'marketing_manager',
                'display_name' => 'Marketing Manager',
                'description' => 'Manages marketing campaigns and promotional activities',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view',
                    'categories.view', 'brands.view', 'brands.create', 'brands.edit',
                    'customers.view', 'customers.export',
                    'news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.assign',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.delete', 'marketing.campaigns.send',
                    'marketing.templates.manage', 'marketing.analytics',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export',
                ],
            ],

            // Content Manager - Content and news management
            [
                'name' => 'content_manager',
                'display_name' => 'Content Manager',
                'description' => 'Manages website content, news, and product information',
                'permissions' => [
                    'dashboard.view',
                    'products.view', 'products.create', 'products.edit', 'products.manage_specifications',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.manage_hierarchy',
                    'brands.view', 'brands.create', 'brands.edit',
                    'news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish',
                    'marketing.templates.manage',
                ],
            ],

            // Financial Manager - Financial oversight
            [
                'name' => 'financial_manager',
                'display_name' => 'Financial Manager',
                'description' => 'Manages financial aspects, invoicing, and payments',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.manage_pricing',
                    'orders.view_all', 'orders.export',
                    'customers.view', 'customers.view_sensitive', 'customers.export',
                    'suppliers.view', 'suppliers.manage_payments',
                    'quotations.view', 'quotations.approve',
                    'purchase_orders.view', 'purchase_orders.approve', 'purchase_orders.view_financials',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.manage_payments',
                    'feedback.view',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export', 'reports.schedule',
                ],
            ],

            // Supplier - External supplier access
            [
                'name' => 'supplier',
                'display_name' => 'Supplier',
                'description' => 'External supplier with limited access to manage their products and orders',
                'permissions' => [
                    'supplier.dashboard',
                    'supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete',
                    'supplier.orders.view', 'supplier.orders.manage',
                    'supplier.inquiries.view', 'supplier.inquiries.respond',
                    'supplier.feedback.create',
                    'products.manage_specifications', // For their own products
                    'categories.view', 'brands.view',
                ],
            ],

            // Viewer - Read-only access
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to system information',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_own',
                    'customers.view',
                    'suppliers.view',
                    'quotations.view',
                    'invoices.view',
                    'deliveries.view',
                    'feedback.view',
                    'news.view',
                ],
            ],

            // API User - For system integrations
            [
                'name' => 'api_user',
                'display_name' => 'API User',
                'description' => 'System integration user with API access',
                'permissions' => [
                    'api.access', 'api.read', 'api.write',
                    'products.view', 'products.create', 'products.edit',
                    'categories.view',
                    'orders.view_all', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_active' => true,
                ]
            );

            // Assign permissions
            if ($roleData['permissions'] === 'all') {
                // Assign all permissions for super admin
                $allPermissions = Permission::where('is_active', true)->get();
                $role->permissions()->sync($allPermissions->pluck('id')->toArray());
            } else {
                // Assign specific permissions
                $permissionIds = [];
                foreach ($roleData['permissions'] as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission) {
                        $permissionIds[] = $permission->id;
                    }
                }
                $role->permissions()->sync($permissionIds);
            }

            $this->command->info("Created/Updated role: {$roleData['display_name']} with " . count($roleData['permissions'] === 'all' ? Permission::all() : $roleData['permissions']) . " permissions");
        }

        $this->command->info('Comprehensive role system created successfully!');
    }
}
