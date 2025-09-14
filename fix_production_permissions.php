<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;
use App\Models\Permission;

echo "=== FIXING PRODUCTION PERMISSIONS ===\n\n";

// Check if we're in production
$isProduction = env('APP_ENV') === 'production';
echo "Environment: " . env('APP_ENV') . "\n";
echo "Production Mode: " . ($isProduction ? "YES" : "NO") . "\n\n";

// Define the correct permissions for super_admin role
$superAdminPermissions = [
    // Dashboard
    'dashboard.view', 'dashboard.analytics', 'dashboard.admin',
    
    // Users (full access)
    'users.view', 'users.create', 'users.edit', 'users.delete', 'users.export', 'users.impersonate',
    
    // Roles (full access)
    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
    
    // Permissions
    'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
    
    // Products (full access)
    'products.view', 'products.create', 'products.edit', 'products.delete', 
    'products.approve', 'products.manage_inventory', 'products.manage_specifications',
    
    // Categories
    'categories.view', 'categories.create', 'categories.edit', 'categories.delete', 'categories.manage_hierarchy',
    
    // Brands
    'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
    
    // Orders (full access)
    'orders.view_all', 'orders.view', 'orders.view_own', 'orders.create', 'orders.edit', 'orders.delete', 'orders.manage_status',
    
    // Customers
    'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.export',
    
    // Suppliers (full access)
    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete', 'suppliers.approve',
    'suppliers.manage_categories', 'suppliers.view_performance', 'suppliers.manage_contracts', 'suppliers.manage_payments',
    'supplier.dashboard', 'supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete',
    'supplier.orders.view', 'supplier.orders.manage',
    
    // Quotations (full access)
    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
    
    // Purchase Orders (full access)
    'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.delete', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
    
    // Invoices (full access)
    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.send', 'invoices.manage_payments',
    
    // Deliveries
    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete', 'deliveries.track', 'deliveries.confirm',
    
    // Feedback
    'feedback.view', 'feedback.respond', 'feedback.delete', 'feedback.export',
    
    // News
    'news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish',
    
    // CRM (full access)
    'crm.access', 'crm.leads.view', 'crm.leads.edit', 'crm.leads.delete', 'crm.contacts.view', 'crm.contacts.edit', 'crm.contacts.delete',
    'crm.opportunities.view', 'crm.opportunities.edit', 'crm.opportunities.delete', 'crm.tasks.view', 'crm.tasks.edit', 'crm.tasks.delete',
    'crm.reports.view', 'crm.reports.create', 'crm.reports.edit', 'crm.reports.delete', 'crm.reports.export',
    
    // Marketing
    'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit', 'marketing.campaigns.delete',
    'marketing.templates.view', 'marketing.templates.create', 'marketing.templates.edit', 'marketing.templates.delete',
    'marketing.contacts.view', 'marketing.contacts.create', 'marketing.contacts.edit', 'marketing.contacts.delete',
    'marketing.analytics.view',
    
    // Analytics
    'analytics.view', 'analytics.advanced', 'analytics.export', 'reports.view', 'reports.create', 'reports.schedule',
    
    // System (full access)
    'system.settings', 'system.maintenance', 'system.logs', 'system.backup', 'system.notifications',
    'system.performance', 'system.security', 'system.updates', 'system.configuration',
    
    // API (full access)
    'api.access', 'api.read', 'api.write', 'api.admin',
    
    // Blog
    'blog.create', 'blog.view', 'blog.edit', 'blog.delete',
    
    // Settings
    'settings.view', 'settings.edit',
    
    // Permissions
    'permissions.manage'
];

// Get all permissions from database (should be 238)
$allPermissions = Permission::where('is_active', true)->get();
echo "Total permissions in database: " . $allPermissions->count() . "\n";

// Update super_admin role with ALL permissions
$superAdminRole = Role::where('name', 'super_admin')->first();

if (!$superAdminRole) {
    echo "❌ super_admin role not found!\n";
    exit;
}

echo "Updating super_admin role with ALL permissions...\n";

// Assign ALL permissions to super_admin (this should be 238 permissions)
$superAdminRole->permissions()->sync($allPermissions->pluck('id'));

$updatedCount = $superAdminRole->permissions()->count();
echo "✅ super_admin role updated with {$updatedCount} permissions\n";

// Verify critical permissions
$criticalPermissions = ['dashboard.view', 'users.view', 'roles.view', 'permissions.view'];
echo "\n🔍 Verifying critical permissions:\n";
foreach ($criticalPermissions as $permission) {
    $hasPermission = $superAdminRole->permissions()->where('name', $permission)->exists();
    $status = $hasPermission ? "✅" : "❌";
    echo "{$status} {$permission}: " . ($hasPermission ? "YES" : "NO") . "\n";
}

echo "\n=== PRODUCTION PERMISSION FIX COMPLETE ===\n";
echo "Super admin should now have access to all functions.\n";
echo "Please test accessing the dashboard and user management.\n";
