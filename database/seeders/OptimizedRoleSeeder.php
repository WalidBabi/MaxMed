<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class OptimizedRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Optimizing Role Permissions for MaxMed Business Operations');
        
        // Define optimized role-permission mappings based on actual business needs
        $optimizedRoles = [
            
            // EXECUTIVE & ADMINISTRATIVE ROLES
            'super_admin' => [
                'display_name' => 'Super Administrator',
                'description' => 'Complete system administrator with all permissions',
                'permissions' => 'all' // Gets all permissions
            ],
            
            'admin' => [
                'display_name' => 'Administrator', 
                'description' => 'System administrator with comprehensive access',
                'permissions' => [
                    // Dashboard & Analytics
                    'dashboard.view', 'dashboard.analytics', 'dashboard.admin',
                    
                    // User Management
                    'users.view', 'users.create', 'users.edit', 'users.delete', 'users.export',
                    
                    // Role Management
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'permissions.manage',
                    
                    // Product Management
                    'products.view', 'products.create', 'products.edit', 'products.delete', 'products.approve', 
                    'products.manage_inventory', 'products.manage_pricing', 'products.manage_specifications', 'products.export',
                    
                    // Category & Brand Management
                    'categories.view', 'categories.create', 'categories.edit', 'categories.delete', 'categories.manage_hierarchy',
                    'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
                    
                    // Order Management
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.delete', 'orders.manage_status', 'orders.process', 'orders.export',
                    
                    // Customer Management
                    'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.view_sensitive', 'customers.export',
                    
                    // Supplier Management
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete', 'suppliers.approve', 
                    'suppliers.manage_categories', 'suppliers.view_performance', 'suppliers.manage_contracts', 'suppliers.manage_payments',
                    
                    // Financial & Business Operations
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.delete', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.send', 'invoices.manage_payments',
                    
                    // Operations
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete', 'deliveries.track', 'deliveries.confirm',
                    'feedback.view', 'feedback.respond', 'feedback.delete', 'feedback.export',
                    'news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish',
                    
                    // CRM & Marketing
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete', 'crm.leads.assign', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.delete', 'marketing.campaigns.send',
                    'marketing.templates.manage', 'marketing.analytics',
                    
                    // Analytics & Reports
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export', 'reports.schedule',
                    
                    // System Administration
                    'system.settings', 'system.maintenance', 'system.logs', 'system.backup', 'system.notifications',
                ]
            ],

            // BUSINESS MANAGEMENT ROLES
            'business_admin' => [
                'display_name' => 'Business Administrator',
                'description' => 'Business operations administrator with oversight capabilities',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'users.view', 'users.create', 'users.edit', 'users.export',
                    'products.view', 'products.create', 'products.edit', 'products.approve', 'products.manage_inventory', 'products.manage_pricing', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.manage_hierarchy',
                    'brands.view', 'brands.create', 'brands.edit',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.process', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.view_sensitive', 'customers.export',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.approve', 'suppliers.manage_categories', 'suppliers.view_performance',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.manage_payments',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.track', 'deliveries.confirm',
                    'feedback.view', 'feedback.respond', 'feedback.export',
                    'news.view', 'news.create', 'news.edit', 'news.publish',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export',
                ]
            ],

            'manager' => [
                'display_name' => 'Operations Manager',
                'description' => 'Manages daily operations, inventory, and team coordination',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.create', 'products.edit', 'products.manage_inventory', 'products.manage_pricing', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit',
                    'brands.view', 'brands.create', 'brands.edit',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.process', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.export',
                    'suppliers.view', 'suppliers.edit', 'suppliers.view_performance',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.send', 'purchase_orders.manage_status',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.track', 'deliveries.confirm',
                    'feedback.view', 'feedback.respond',
                    'analytics.view', 'reports.generate',
                ]
            ],

            // SALES & CUSTOMER MANAGEMENT ROLES
            'sales_manager' => [
                'display_name' => 'Sales Manager',
                'description' => 'Manages sales team, customer relationships, and sales processes',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.manage_pricing',
                    'categories.view', 'brands.view',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.view_sensitive', 'customers.export',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.manage_payments',
                    'feedback.view', 'feedback.respond',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.assign', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.send', 'marketing.analytics',
                    'analytics.view', 'reports.generate', 'reports.export',
                ]
            ],

            'sales_rep' => [
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
                    'deliveries.view', 'deliveries.track',
                    'feedback.view',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit',
                    'analytics.view',
                ]
            ],

            'sales-rep' => [
                'display_name' => 'Sales Representative',
                'description' => 'Individual sales representative (legacy role name)',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_own', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'invoices.view', 'invoices.create', 'invoices.edit',
                    'feedback.view',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'analytics.view',
                ]
            ],

            // PURCHASING & PROCUREMENT ROLES
            'purchasing' => [
                'display_name' => 'Purchasing Manager',
                'description' => 'Manages procurement, suppliers, and purchase orders',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.manage_inventory', 'products.manage_pricing',
                    'categories.view', 'brands.view',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.approve', 'suppliers.manage_categories', 'suppliers.view_performance', 'suppliers.manage_contracts', 'suppliers.manage_payments',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.compare',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status',
                    'customers.view', 'customers.create', 'customers.edit',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.manage_payments',
                    'deliveries.view', 'deliveries.track',
                    'feedback.view', 'feedback.respond',
                    'analytics.view', 'reports.generate',
                ]
            ],

            'purchasing_manager' => [
                'display_name' => 'Purchasing Manager',
                'description' => 'Senior purchasing role with full procurement authority',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.create', 'products.edit', 'products.manage_inventory', 'products.manage_pricing',
                    'categories.view', 'brands.view',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.approve', 'suppliers.manage_categories', 'suppliers.view_performance', 'suppliers.manage_contracts', 'suppliers.manage_payments',
                    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.approve', 'quotations.compare', 'quotations.convert',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status',
                    'customers.view', 'customers.create', 'customers.edit',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.manage_payments',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.track',
                    'feedback.view', 'feedback.respond',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export',
                ]
            ],

            // CUSTOMER SERVICE ROLES
            'support' => [
                'display_name' => 'Customer Support Agent',
                'description' => 'Provides customer support and handles inquiries',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status',
                    'customers.view', 'customers.create', 'customers.edit',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'invoices.view',
                    'deliveries.view', 'deliveries.track',
                    'feedback.view', 'feedback.respond',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit',
                ]
            ],

            'customer_service_manager' => [
                'display_name' => 'Customer Service Manager',
                'description' => 'Manages customer service team and customer relations',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view',
                    'categories.view', 'brands.view',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.manage_status', 'orders.export',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.view_sensitive', 'customers.export',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
                    'deliveries.view', 'deliveries.track', 'deliveries.confirm',
                    'feedback.view', 'feedback.respond', 'feedback.export',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.assign',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'analytics.view', 'reports.generate',
                ]
            ],

            // CRM & MARKETING ROLES
            'crm_manager' => [
                'display_name' => 'CRM Manager',
                'description' => 'Manages CRM operations, leads, and sales processes',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.export',
                    'orders.view_all', 'orders.create', 'orders.edit',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete', 'crm.leads.assign', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.send',
                    'marketing.templates.manage', 'marketing.analytics',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export',
                ]
            ],

            'crm-administrator' => [
                'display_name' => 'CRM Administrator',
                'description' => 'Full CRM system administration and configuration',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'users.view', 'users.create', 'users.edit',
                    'products.view',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.view_sensitive', 'customers.export',
                    'orders.view_all', 'orders.create', 'orders.edit', 'orders.export',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                    'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete', 'crm.leads.assign', 'crm.leads.convert',
                    'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete',
                    'crm.activities.view', 'crm.activities.create', 'crm.activities.edit',
                    'crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.assign',
                    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.delete', 'marketing.campaigns.send',
                    'marketing.templates.manage', 'marketing.analytics',
                    'analytics.view', 'analytics.advanced', 'reports.generate', 'reports.export',
                ]
            ],

            // CONTENT & MARKETING ROLES
            'content-editor' => [
                'display_name' => 'Content Manager',
                'description' => 'Manages website content, news, and product information',
                'permissions' => [
                    'dashboard.view',
                    'products.view', 'products.create', 'products.edit', 'products.manage_specifications',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.manage_hierarchy',
                    'brands.view', 'brands.create', 'brands.edit',
                    'news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish',
                    'marketing.templates.manage',
                    'customers.view',
                    'feedback.view',
                ]
            ],

            'marketing_manager' => [
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
                ]
            ],

            // SUPPLIER ROLES
            'supplier' => [
                'display_name' => 'Supplier',
                'description' => 'External supplier with product and order management capabilities',
                'permissions' => [
                    'supplier.dashboard',
                    'supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete',
                    'supplier.orders.view', 'supplier.orders.manage',
                    'supplier.inquiries.view', 'supplier.inquiries.respond',
                    'supplier.feedback.create',
                    'products.manage_specifications', // For their own products
                    'categories.view', 'brands.view',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send', // Supplier quotations
                    'deliveries.view', 'deliveries.track', // Track their shipments
                    'customers.view', // View their customers
                ]
            ],

            // ACCESS CONTROL ROLES
            'viewer' => [
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
                ]
            ],

            // SPECIALIZED ROLES
            'financial_manager' => [
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
                ]
            ],

            'inventory_manager' => [
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
                ]
            ],

            'api_user' => [
                'display_name' => 'API User',
                'description' => 'System integration user with API access',
                'permissions' => [
                    'api.access', 'api.read', 'api.write',
                    'products.view', 'products.create', 'products.edit',
                    'categories.view',
                    'orders.view_all', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'suppliers.view',
                    'quotations.view', 'quotations.create',
                    'invoices.view', 'invoices.create',
                ]
            ],
        ];

        // Update each role with optimized permissions
        foreach ($optimizedRoles as $roleName => $roleData) {
            $role = Role::where('name', $roleName)->first();
            
            if (!$role) {
                $this->command->warn("⚠️  Role '{$roleName}' not found, creating...");
                $role = Role::create([
                    'name' => $roleName,
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_active' => true,
                ]);
            } else {
                $role->update([
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                ]);
            }

            // Assign permissions
            if ($roleData['permissions'] === 'all') {
                // Assign all permissions for super admin
                $allPermissions = Permission::where('is_active', true)->get();
                $role->permissions()->sync($allPermissions->pluck('id')->toArray());
                $this->command->info("✅ Updated {$roleData['display_name']} with ALL permissions ({$allPermissions->count()})");
            } else {
                // Assign specific permissions
                $permissionIds = [];
                $foundPermissions = 0;
                $missingPermissions = [];
                
                foreach ($roleData['permissions'] as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission) {
                        $permissionIds[] = $permission->id;
                        $foundPermissions++;
                    } else {
                        $missingPermissions[] = $permissionName;
                    }
                }
                
                $role->permissions()->sync($permissionIds);
                
                if (count($missingPermissions) > 0) {
                    $this->command->warn("✅ Updated {$roleData['display_name']} with {$foundPermissions} permissions (⚠️  {count($missingPermissions)} missing)");
                } else {
                    $this->command->info("✅ Updated {$roleData['display_name']} with {$foundPermissions} permissions");
                }
            }
        }

        $this->command->info('🎉 Role optimization completed successfully!');
    }
}
