<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "🔧 FIXING PERMISSION NAME MISMATCH\n\n";

// Check if both permissions exist
$dashboardView = Permission::where('name', 'dashboard.view')->first();
$adminDashboardAccess = Permission::where('name', 'admin.dashboard.access')->first();

echo "📊 Permission Check:\n";
echo "dashboard.view: " . ($dashboardView ? "✅ EXISTS" : "❌ MISSING") . "\n";
echo "admin.dashboard.access: " . ($adminDashboardAccess ? "✅ EXISTS" : "❌ MISSING") . "\n";

// If admin.dashboard.access doesn't exist, create it
if (!$adminDashboardAccess) {
    echo "\n🔧 Creating admin.dashboard.access permission...\n";
    
    $adminDashboardAccess = Permission::create([
        'name' => 'admin.dashboard.access',
        'display_name' => 'Admin Dashboard Access',
        'description' => 'Access to admin dashboard',
        'category' => 'dashboard',
        'is_active' => true
    ]);
    
    echo "✅ Created admin.dashboard.access permission\n";
}

// Assign admin.dashboard.access to super_admin role
$superAdminRole = Role::where('name', 'super_admin')->first();
if ($superAdminRole) {
    $hasPermission = $superAdminRole->permissions()->where('name', 'admin.dashboard.access')->exists();
    
    if (!$hasPermission) {
        echo "\n🔧 Assigning admin.dashboard.access to super_admin...\n";
        $superAdminRole->permissions()->attach($adminDashboardAccess->id);
        echo "✅ Assigned admin.dashboard.access to super_admin\n";
    } else {
        echo "✅ super_admin already has admin.dashboard.access\n";
    }
}

// Also ensure dashboard.view is assigned
if ($dashboardView) {
    $hasDashboardView = $superAdminRole->permissions()->where('name', 'dashboard.view')->exists();
    
    if (!$hasDashboardView) {
        echo "\n🔧 Assigning dashboard.view to super_admin...\n";
        $superAdminRole->permissions()->attach($dashboardView->id);
        echo "✅ Assigned dashboard.view to super_admin\n";
    } else {
        echo "✅ super_admin already has dashboard.view\n";
    }
}

echo "\n🎯 PERMISSION MISMATCH FIX COMPLETE!\n";
echo "Both dashboard.view and admin.dashboard.access should now work.\n";
