<?php
/**
 * Comprehensive Production Permission Fix Script
 * This script ensures all permissions are properly set up and caches are cleared
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "=== COMPREHENSIVE PRODUCTION PERMISSION FIX ===\n";
echo "Timestamp: " . now() . "\n\n";

try {
    DB::beginTransaction();
    
    // Step 1: Ensure all required permissions exist
    echo "Step 1: Ensuring all required permissions exist...\n";
    
    $requiredPermissions = [
        'users.view', 'users.create', 'users.edit', 'users.delete',
        'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
        'dashboard.view', 'products.view', 'products.create', 'products.edit', 'products.delete',
        'orders.view_all', 'orders.create', 'orders.edit', 'orders.delete',
        'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
        'inquiries.view', 'inquiries.create', 'inquiries.edit', 'inquiries.delete',
        'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete',
        'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.delete',
        'cash_receipts.view', 'cash_receipts.create', 'cash_receipts.edit', 'cash_receipts.delete',
        'sales_targets.view', 'sales_targets.create', 'sales_targets.edit', 'sales_targets.delete',
        'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete',
        'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete',
        'marketing.access', 'marketing.campaigns.view', 'marketing.campaigns.create', 'marketing.campaigns.edit',
        'system.settings', 'system.maintenance', 'system.logs', 'system.backup'
    ];
    
    $createdCount = 0;
    $existingCount = 0;
    
    foreach ($requiredPermissions as $permissionName) {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionName],
            [
                'display_name' => ucwords(str_replace(['.', '_'], [' ', ' '], $permissionName)),
                'description' => "Permission to {$permissionName}",
                'category' => getCategoryForPermission($permissionName),
                'is_active' => true
            ]
        );
        
        if ($permission->wasRecentlyCreated) {
            $createdCount++;
        } else {
            $existingCount++;
        }
    }
    
    echo "✅ Created {$createdCount} new permissions\n";
    echo "✅ Found {$existingCount} existing permissions\n";
    
    // Step 2: Ensure super_admin role exists and has all permissions
    echo "\nStep 2: Ensuring super_admin role has all permissions...\n";
    
    $superAdminRole = Role::firstOrCreate(
        ['name' => 'super_admin'],
        [
            'display_name' => 'Super Administrator',
            'description' => 'Full system access with all permissions',
            'is_active' => true
        ]
    );
    
    echo "✅ Super admin role: {$superAdminRole->name} (ID: {$superAdminRole->id})\n";
    
    // Get all active permissions
    $allPermissions = Permission::where('is_active', true)->get();
    echo "✅ Total active permissions: " . $allPermissions->count() . "\n";
    
    // Sync all permissions to super_admin role
    $superAdminRole->permissions()->sync($allPermissions->pluck('id')->toArray());
    echo "✅ Synced all permissions to super_admin role\n";
    
    // Step 3: Ensure user has super_admin role
    echo "\nStep 3: Ensuring user has super_admin role...\n";
    
    $user = User::where('email', 'wbabi@localhost.com')->first();
    if (!$user) {
        throw new \Exception("User wbabi@localhost.com not found!");
    }
    
    echo "✅ User found: {$user->name} (ID: {$user->id})\n";
    
    if ($user->role_id != $superAdminRole->id) {
        $user->update(['role_id' => $superAdminRole->id]);
        echo "✅ Assigned super_admin role to user\n";
    } else {
        echo "✅ User already has super_admin role\n";
    }
    
    // Step 4: Verify permissions
    echo "\nStep 4: Verifying permissions...\n";
    
    $user->refresh();
    $user->load('role.permissions');
    
    $testPermissions = ['users.view', 'roles.view', 'dashboard.view', 'products.view'];
    foreach ($testPermissions as $permission) {
        $hasPermission = $user->hasPermission($permission);
        echo "User has {$permission}: " . ($hasPermission ? 'YES' : 'NO') . "\n";
    }
    
    // Step 5: Clear all caches
    echo "\nStep 5: Clearing all caches...\n";
    
    // Clear Laravel caches
    exec('php artisan cache:clear', $output, $returnCode);
    echo "✅ Application cache cleared\n";
    
    exec('php artisan config:clear', $output, $returnCode);
    echo "✅ Configuration cache cleared\n";
    
    exec('php artisan route:clear', $output, $returnCode);
    echo "✅ Route cache cleared\n";
    
    exec('php artisan view:clear', $output, $returnCode);
    echo "✅ View cache cleared\n";
    
    // Clear application cache manually
    Cache::flush();
    echo "✅ Application cache flushed\n";
    
    DB::commit();
    
    echo "\n=== FIX COMPLETED SUCCESSFULLY ===\n";
    echo "All permissions have been set up correctly.\n";
    echo "All caches have been cleared.\n";
    echo "\nNext steps:\n";
    echo "1. Log out and log back in to refresh your session\n";
    echo "2. Try accessing the admin panel again\n";
    echo "3. The 403 errors should be resolved\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

function getCategoryForPermission($permissionName) {
    $categories = [
        'users' => 'User Management',
        'roles' => 'Role & Permission Management',
        'dashboard' => 'Dashboard & Analytics',
        'products' => 'Product Management',
        'orders' => 'Order Management',
        'suppliers' => 'Supplier Management',
        'inquiries' => 'Inquiry Management',
        'quotations' => 'Quotation Management',
        'purchase_orders' => 'Purchase Order Management',
        'cash_receipts' => 'Cash Receipt Management',
        'sales_targets' => 'Sales Target Management',
        'crm' => 'CRM System',
        'marketing' => 'Marketing & Campaigns',
        'system' => 'System Administration'
    ];
    
    $prefix = explode('.', $permissionName)[0];
    return $categories[$prefix] ?? 'General';
}
