<?php
/**
 * Fix All Roles Navigation Script
 * This script ensures all roles have proper navigation permissions
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== Fixing All Roles Navigation ===\n\n";

try {
    DB::beginTransaction();
    
    // Get all roles
    $roles = Role::with('permissions')->get();
    echo "Found " . $roles->count() . " roles to process\n\n";
    
    // Define navigation permissions that should be available
    $navigationPermissions = [
        'dashboard.view',
        'crm.access',
        'crm.leads.view',
        'crm.contacts.view',
        'products.view',
        'suppliers.view',
        'inquiries.view',
        'quotations.view',
        'orders.view',
        'purchase_orders.view',
        'cash_receipts.view',
        'sales_targets.view',
        'users.view',
        'roles.view',
        'permissions.view'
    ];
    
    // Ensure all navigation permissions exist
    echo "Ensuring navigation permissions exist...\n";
    foreach ($navigationPermissions as $permissionName) {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionName],
            [
                'display_name' => ucwords(str_replace(['.', '_'], [' ', ' '], $permissionName)),
                'description' => "Navigation permission for {$permissionName}",
                'category' => 'navigation',
                'is_active' => true
            ]
        );
    }
    echo "✅ Navigation permissions ensured\n\n";
    
    // Process each role
    foreach ($roles as $role) {
        echo "Processing role: {$role->name}\n";
        
        $currentPermissions = $role->permissions->pluck('name')->toArray();
        $missingPermissions = array_diff($navigationPermissions, $currentPermissions);
        
        if (!empty($missingPermissions)) {
            echo "  Adding " . count($missingPermissions) . " missing navigation permissions\n";
            
            $permissionIds = Permission::whereIn('name', $missingPermissions)
                ->pluck('id')
                ->toArray();
            
            $role->permissions()->syncWithoutDetaching($permissionIds);
            
            foreach ($missingPermissions as $permission) {
                echo "    ✅ Added: {$permission}\n";
            }
        } else {
            echo "  ✅ All navigation permissions already assigned\n";
        }
        
        echo "\n";
    }
    
    // Special handling for viewer role - ensure it has view permissions only
    $viewerRole = Role::where('name', 'viewer')->first();
    if ($viewerRole) {
        echo "Special handling for viewer role...\n";
        
        // Remove any non-view permissions
        $nonViewPermissions = Permission::where('name', 'like', '%.create')
            ->orWhere('name', 'like', '%.edit')
            ->orWhere('name', 'like', '%.delete')
            ->orWhere('name', 'like', '%.approve')
            ->orWhere('name', 'like', '%.manage')
            ->pluck('id')
            ->toArray();
        
        $viewerRole->permissions()->detach($nonViewPermissions);
        echo "✅ Removed non-view permissions from viewer role\n";
    }
    
    // Special handling for super_admin role - ensure it has all permissions
    $superAdminRole = Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
        echo "Special handling for super_admin role...\n";
        
        $allPermissions = Permission::where('is_active', true)->pluck('id')->toArray();
        $superAdminRole->permissions()->sync($allPermissions);
        echo "✅ Assigned all permissions to super_admin role\n";
    }
    
    DB::commit();
    
    echo "\n=== SUMMARY ===\n";
    echo "✅ All roles processed successfully\n";
    echo "✅ Navigation permissions ensured\n";
    echo "✅ Role-specific permissions applied\n";
    
    // Show final role summary
    echo "\n--- Final Role Summary ---\n";
    foreach ($roles as $role) {
        $permissionCount = $role->permissions()->count();
        echo "{$role->name}: {$permissionCount} permissions\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✅ All roles navigation fixed successfully!\n";
