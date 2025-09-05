<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role - Update if exists, create if not
        Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full administrative access to all system features',
                'permissions' => [
                    'dashboard.view',
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'products.view', 'products.create', 'products.edit', 'products.delete',
                    'orders.view', 'orders.create', 'orders.edit', 'orders.delete',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                    'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
                    'news.view', 'news.create', 'news.edit', 'news.delete',
                    'supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete'
                ],
                'is_active' => true,
            ]
        );

        // Manager Role
        Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Can manage products, orders, and customer relations',
                'permissions' => [
                    'dashboard.view',
                    'products.view', 'products.create', 'products.edit',
                    'orders.view', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit',
                    'categories.view', 'categories.create', 'categories.edit',
                    'brands.view', 'brands.create', 'brands.edit',
                    'news.view', 'news.create', 'news.edit',
                ],
                'is_active' => true,
            ]
        );

        // Sales Role
        Role::updateOrCreate(
            ['name' => 'sales'],
            [
                'display_name' => 'Sales Representative',
                'description' => 'Can manage orders and customer relations',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'orders.view', 'orders.create',
                    'customers.view', 'customers.create',
                    'deliveries.view',
                ],
                'is_active' => true,
            ]
        );

        // Support Role
        Role::updateOrCreate(
            ['name' => 'support'],
            [
                'display_name' => 'Support Agent',
                'description' => 'Can view orders and assist customers',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'orders.view',
                    'customers.view',
                    'deliveries.view',
                ],
                'is_active' => true,
            ]
        );

        // Supplier Role
        Role::updateOrCreate(
            ['name' => 'supplier'],
            [
                'display_name' => 'Supplier',
                'description' => 'Can manage their own products with limited access',
                'permissions' => [
                    'supplier.products.view',
                    'supplier.products.create', 
                    'supplier.products.edit',
                    'supplier.products.delete'
                ],
                'is_active' => true,
            ]
        );

        // Purchasing Role
        Role::updateOrCreate(
            ['name' => 'purchasing'],
            [
                'display_name' => 'Purchasing Manager',
                'description' => 'Manages purchase orders, supplier relationships, and procurement processes',
                'permissions' => [
                    'dashboard.view',
                    'dashboard.analytics',
                    
                    // Purchase Order Management
                    'purchase_orders.view',
                    'purchase_orders.create',
                    'purchase_orders.edit',
                    'purchase_orders.approve',
                    'purchase_orders.send_to_supplier',
                    'purchase_orders.manage_status',
                    'purchase_orders.view_financials',
                    'purchase_orders.manage_payments',
                    
                    // Supplier Management
                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.edit',
                    'suppliers.manage_contracts',
                    'suppliers.view_performance',
                    
                    // Quotation Management
                    'quotations.view',
                    'quotations.create',
                    'quotations.edit',
                    'quotations.approve',
                    'quotations.compare',
                    
                    // Product and Inventory
                    'products.view',
                    'products.manage_inventory',
                    'categories.view',
                    'brands.view',
                    
                    // Order Management (for procurement context)
                    'orders.view',
                    'orders.create',
                    'orders.edit',
                    'orders.manage_status',
                    
                    // Customer Management (for supplier relationships)
                    'customers.view',
                    'customers.create',
                    'customers.edit',
                    
                    // Procurement Analytics
                    'procurement.analytics',
                    'procurement.reports',
                    'procurement.budget_tracking',
                    
                    // Feedback and Communication
                    'feedback.view',
                    'feedback.respond',
                ],
                'is_active' => true,
            ]
        );

        // CRM Manager Role
        Role::updateOrCreate(
            ['name' => 'crm_manager'],
            [
                'display_name' => 'CRM Manager',
                'description' => 'Manages CRM operations, leads, deals, and sales processes',
                'permissions' => [
                    'dashboard.view',
                    'dashboard.analytics',
                    
                    // CRM Lead Management
                    'crm.leads.view',
                    'crm.leads.create',
                    'crm.leads.edit',
                    'crm.leads.assign',
                    'crm.leads.convert',
                    'crm.leads.export',
                    'crm.leads.import',
                    'crm.leads.merge',
                    'crm.leads.bulk_actions',
                    
                    // CRM Deal Management
                    'crm.deals.view',
                    'crm.deals.create',
                    'crm.deals.edit',
                    'crm.deals.assign',
                    'crm.deals.close',
                    'crm.deals.export',
                    'crm.deals.pipeline',
                    'crm.deals.forecast',
                    
                    // CRM Activity Management
                    'crm.activities.view',
                    'crm.activities.create',
                    'crm.activities.edit',
                    'crm.activities.complete',
                    'crm.activities.schedule',
                    'crm.activities.timeline',
                    
                    // CRM Contact Management
                    'crm.contacts.view',
                    'crm.contacts.create',
                    'crm.contacts.edit',
                    'crm.contacts.merge',
                    'crm.contacts.export',
                    'crm.contacts.import',
                    
                    // CRM Campaign Management
                    'crm.campaigns.view',
                    'crm.campaigns.create',
                    'crm.campaigns.edit',
                    'crm.campaigns.execute',
                    'crm.campaigns.track',
                    
                    // CRM Task Management
                    'crm.tasks.view',
                    'crm.tasks.create',
                    'crm.tasks.edit',
                    'crm.tasks.assign',
                    'crm.tasks.complete',
                    'crm.tasks.overdue',
                    
                    // CRM Reporting & Analytics
                    'crm.reports.view',
                    'crm.reports.create',
                    'crm.reports.export',
                    'crm.analytics.view',
                    'crm.analytics.dashboard',
                    'crm.analytics.performance',
                    
                    // CRM Communication
                    'crm.communication.email',
                    'crm.communication.sms',
                    'crm.communication.call',
                    'crm.communication.meeting',
                    'crm.communication.templates',
                    
                    // Basic CRM Administration
                    'crm.admin.settings',
                    'crm.admin.fields',
                ],
                'is_active' => true,
            ]
        );

        // Sales Representative Role
        Role::updateOrCreate(
            ['name' => 'sales_rep'],
            [
                'display_name' => 'Sales Representative',
                'description' => 'Handles sales activities, leads, and customer interactions',
                'permissions' => [
                    'dashboard.view',
                    
                    // CRM Lead Management (Limited)
                    'crm.leads.view',
                    'crm.leads.create',
                    'crm.leads.edit',
                    'crm.leads.convert',
                    
                    // CRM Deal Management (Limited)
                    'crm.deals.view',
                    'crm.deals.create',
                    'crm.deals.edit',
                    'crm.deals.close',
                    'crm.deals.pipeline',
                    
                    // CRM Activity Management
                    'crm.activities.view',
                    'crm.activities.create',
                    'crm.activities.edit',
                    'crm.activities.complete',
                    'crm.activities.schedule',
                    'crm.activities.timeline',
                    
                    // CRM Contact Management
                    'crm.contacts.view',
                    'crm.contacts.create',
                    'crm.contacts.edit',
                    
                    // CRM Task Management
                    'crm.tasks.view',
                    'crm.tasks.create',
                    'crm.tasks.edit',
                    'crm.tasks.complete',
                    
                    // CRM Reporting (Limited)
                    'crm.reports.view',
                    'crm.analytics.view',
                    'crm.analytics.dashboard',
                    
                    // CRM Communication
                    'crm.communication.email',
                    'crm.communication.call',
                    'crm.communication.meeting',
                    
                    // Product and Order Access
                    'products.view',
                    'orders.view',
                    'orders.create',
                    'customers.view',
                    'customers.create',
                    'customers.edit',
                ],
                'is_active' => true,
            ]
        );

        // CRM Administrator Role
        Role::updateOrCreate(
            ['name' => 'crm_admin'],
            [
                'display_name' => 'CRM Administrator',
                'description' => 'Full administrative access to CRM system and configurations',
                'permissions' => [
                    'dashboard.view',
                    'dashboard.analytics',
                    
                    // All CRM Lead Management
                    'crm.leads.view',
                    'crm.leads.create',
                    'crm.leads.edit',
                    'crm.leads.delete',
                    'crm.leads.assign',
                    'crm.leads.convert',
                    'crm.leads.export',
                    'crm.leads.import',
                    'crm.leads.merge',
                    'crm.leads.bulk_actions',
                    
                    // All CRM Deal Management
                    'crm.deals.view',
                    'crm.deals.create',
                    'crm.deals.edit',
                    'crm.deals.delete',
                    'crm.deals.assign',
                    'crm.deals.close',
                    'crm.deals.export',
                    'crm.deals.pipeline',
                    'crm.deals.forecast',
                    
                    // All CRM Activity Management
                    'crm.activities.view',
                    'crm.activities.create',
                    'crm.activities.edit',
                    'crm.activities.delete',
                    'crm.activities.complete',
                    'crm.activities.schedule',
                    'crm.activities.timeline',
                    
                    // All CRM Contact Management
                    'crm.contacts.view',
                    'crm.contacts.create',
                    'crm.contacts.edit',
                    'crm.contacts.delete',
                    'crm.contacts.merge',
                    'crm.contacts.export',
                    'crm.contacts.import',
                    
                    // All CRM Campaign Management
                    'crm.campaigns.view',
                    'crm.campaigns.create',
                    'crm.campaigns.edit',
                    'crm.campaigns.delete',
                    'crm.campaigns.execute',
                    'crm.campaigns.track',
                    
                    // All CRM Task Management
                    'crm.tasks.view',
                    'crm.tasks.create',
                    'crm.tasks.edit',
                    'crm.tasks.delete',
                    'crm.tasks.assign',
                    'crm.tasks.complete',
                    'crm.tasks.overdue',
                    
                    // All CRM Reporting & Analytics
                    'crm.reports.view',
                    'crm.reports.create',
                    'crm.reports.export',
                    'crm.analytics.view',
                    'crm.analytics.dashboard',
                    'crm.analytics.performance',
                    
                    // All CRM Communication
                    'crm.communication.email',
                    'crm.communication.sms',
                    'crm.communication.call',
                    'crm.communication.meeting',
                    'crm.communication.templates',
                    
                    // All CRM Integration & Automation
                    'crm.integration.webhooks',
                    'crm.integration.api',
                    'crm.automation.workflows',
                    'crm.automation.rules',
                    'crm.automation.triggers',
                    
                    // All CRM Administration
                    'crm.admin.settings',
                    'crm.admin.fields',
                    'crm.admin.workflows',
                    'crm.admin.integrations',
                    'crm.admin.backup',
                    'crm.admin.restore',
                ],
                'is_active' => true,
            ]
        );

        // Viewer Role
        Role::updateOrCreate(
            ['name' => 'viewer'],
            [
                'display_name' => 'Viewer',
                'description' => 'Read-only access to most system information',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'orders.view',
                    'customers.view',
                    'deliveries.view',
                    'categories.view',
                    'brands.view',
                    'news.view',
                ],
                'is_active' => true,
            ]
        );
    }
} 