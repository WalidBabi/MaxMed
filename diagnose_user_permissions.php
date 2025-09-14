<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== USER PERMISSION DIAGNOSIS ===\n\n";

// Check super admin user
$user = User::where('email', 'wbabi@localhost.com')->first();

if (!$user) {
    echo "❌ User with email 'wbabi@localhost.com' not found!\n";
    
    // List all users to help identify the correct one
    echo "\nAvailable users:\n";
    User::all()->each(function($u) {
        echo "- {$u->name} ({$u->email}) - Role: " . ($u->role ? $u->role->name : 'No role') . "\n";
    });
    exit;
}

echo "✅ User found: {$user->name}\n";
echo "📧 Email: {$user->email}\n";
echo "👤 Role: " . ($user->role ? $user->role->name : 'No role') . "\n";

if ($user->role) {
    echo "🔑 Role permissions count: " . $user->role->permissions()->count() . "\n";
    
    // Check specific permissions
    $criticalPermissions = [
        'dashboard.view',
        'users.view',
        'roles.view',
        'permissions.view'
    ];
    
    echo "\n🔍 Critical Permission Check:\n";
    foreach ($criticalPermissions as $permission) {
        $hasPermission = $user->hasPermission($permission);
        $status = $hasPermission ? "✅" : "❌";
        echo "{$status} {$permission}: " . ($hasPermission ? "YES" : "NO") . "\n";
    }
    
    // Check if permissions exist in database
    echo "\n📊 Permission Database Check:\n";
    foreach ($criticalPermissions as $permission) {
        $perm = Permission::where('name', $permission)->first();
        $exists = $perm ? "✅" : "❌";
        $active = $perm && $perm->is_active ? "ACTIVE" : "INACTIVE";
        echo "{$exists} {$permission}: " . ($perm ? "EXISTS ({$active})" : "NOT FOUND") . "\n";
    }
    
    // Check role-permission relationship
    echo "\n🔗 Role-Permission Relationship Check:\n";
    $rolePermissions = $user->role->permissions()->pluck('name')->toArray();
    foreach ($criticalPermissions as $permission) {
        $inRole = in_array($permission, $rolePermissions);
        $status = $inRole ? "✅" : "❌";
        echo "{$status} {$permission} in role: " . ($inRole ? "YES" : "NO") . "\n";
    }
    
} else {
    echo "❌ User has no role assigned!\n";
}

echo "\n=== ALL ROLES AND THEIR PERMISSIONS ===\n";
Role::all()->each(function($role) {
    $count = $role->permissions()->count();
    echo "{$role->name}: {$count} permissions\n";
    
    if ($role->name === 'super_admin') {
        echo "  Super Admin permissions:\n";
        $role->permissions()->take(10)->get()->each(function($perm) {
            echo "    - {$perm->name}\n";
        });
        if ($role->permissions()->count() > 10) {
            echo "    ... and " . ($role->permissions()->count() - 10) . " more\n";
        }
    }
});

echo "\n=== TOTAL PERMISSIONS IN SYSTEM ===\n";
echo "Total permissions: " . Permission::count() . "\n";
echo "Active permissions: " . Permission::where('is_active', true)->count() . "\n";

echo "\n=== DIAGNOSIS COMPLETE ===\n";
