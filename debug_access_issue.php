<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== ACCESS ISSUE DEBUG ===\n\n";

// Check the user
$user = User::where('email', 'wbabi@localhost.com')->first();

if ($user) {
    echo "✅ User found: {$user->name}\n";
    echo "📧 Email: {$user->email}\n";
    echo "👤 Role: " . ($user->role ? $user->role->name : 'No role') . "\n";
    echo "🆔 User ID: {$user->id}\n";
    
    // Test specific permissions
    echo "\n🔍 Permission Tests:\n";
    
    $permissions = [
        'admin.dashboard.access',
        'dashboard.view',
        'users.view',
        'admin.access'
    ];
    
    foreach ($permissions as $permission) {
        $hasPermission = $user->hasPermission($permission);
        $status = $hasPermission ? "✅" : "❌";
        echo "{$status} {$permission}: " . ($hasPermission ? "YES" : "NO") . "\n";
    }
    
    // Check if user can access admin routes
    echo "\n🛣️  Route Access Tests:\n";
    
    // Simulate the middleware check
    try {
        $canAccessDashboard = $user->can('admin.dashboard.access') || $user->can('dashboard.view');
        $canAccessUsers = $user->can('users.view');
        
        echo "Dashboard access: " . ($canAccessDashboard ? "✅ YES" : "❌ NO") . "\n";
        echo "Users access: " . ($canAccessUsers ? "✅ YES" : "❌ NO") . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error testing permissions: " . $e->getMessage() . "\n";
    }
    
    // Check authentication status
    echo "\n🔐 Authentication Status:\n";
    echo "Authenticated: " . (auth()->check() ? "✅ YES" : "❌ NO") . "\n";
    echo "Current user ID: " . (auth()->id() ?? 'Not authenticated') . "\n";
    
    // Check role permissions count
    if ($user->role) {
        echo "\n📊 Role Details:\n";
        echo "Role permissions count: " . $user->role->permissions()->count() . "\n";
        
        // Check if role has the specific permission
        $hasAdminDashboardAccess = $user->role->permissions()->where('name', 'admin.dashboard.access')->exists();
        echo "Role has admin.dashboard.access: " . ($hasAdminDashboardAccess ? "✅ YES" : "❌ NO") . "\n";
    }
    
} else {
    echo "❌ User not found!\n";
}

echo "\n=== DEBUG COMPLETE ===\n";

// Check if there are any view-specific issues
echo "\n🔍 Potential Issues:\n";
echo "1. Check if the view file has @can directives that might be blocking access\n";
echo "2. Check if there are any JavaScript errors in the browser console\n";
echo "3. Check if the route is properly defined\n";
echo "4. Check if there are any other middleware blocking access\n";

echo "\n💡 Next Steps:\n";
echo "1. Try accessing the dashboard directly: /admin/dashboard\n";
echo "2. Check browser console for JavaScript errors\n";
echo "3. Check if the view file exists and has proper @can directives\n";
echo "4. Try clearing browser cache\n";
