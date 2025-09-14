<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Role;
use App\Models\Permission;

echo "🚨 QUICK PRODUCTION FIX - SUPERVISOR ADMIN ACCESS\n\n";

// Get super_admin role
$superAdminRole = Role::where('name', 'super_admin')->first();

if (!$superAdminRole) {
    echo "❌ super_admin role not found!\n";
    exit;
}

echo "✅ Found super_admin role\n";

// Get all active permissions
$allPermissions = Permission::where('is_active', true)->get();
echo "📊 Total permissions available: " . $allPermissions->count() . "\n";

// Check current permissions
$currentCount = $superAdminRole->permissions()->count();
echo "🔑 Current super_admin permissions: {$currentCount}\n";

if ($currentCount < 200) {
    echo "⚠️  Super admin has insufficient permissions. Fixing...\n";
    
    // Assign ALL permissions to super_admin
    $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
    
    $newCount = $superAdminRole->permissions()->count();
    echo "✅ Super admin now has {$newCount} permissions\n";
} else {
    echo "✅ Super admin already has sufficient permissions\n";
}

// Test critical permissions
$criticalPermissions = [
    'dashboard.view',
    'users.view', 
    'roles.view',
    'permissions.view',
    'admin.access'
];

echo "\n🔍 Testing critical permissions:\n";
foreach ($criticalPermissions as $permission) {
    $exists = Permission::where('name', $permission)->exists();
    $assigned = $superAdminRole->permissions()->where('name', $permission)->exists();
    
    $status = $exists && $assigned ? "✅" : "❌";
    echo "{$status} {$permission}: " . ($exists ? "EXISTS" : "MISSING") . " " . ($assigned ? "ASSIGNED" : "NOT ASSIGNED") . "\n";
}

echo "\n🎯 QUICK FIX COMPLETE!\n";
echo "Please clear caches and test access:\n";
echo "php artisan config:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "php artisan cache:clear\n";
