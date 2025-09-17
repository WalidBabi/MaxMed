<?php
/**
 * Production Permission Diagnostic Script
 * 
 * Run this script on your production server to diagnose permission issues
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "🔍 Production Permission Diagnostic\n";
echo "==================================\n\n";

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✅ Database connection: OK\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Get permission counts
$totalPermissions = Permission::count();
$activePermissions = Permission::where('is_active', true)->count();
$inactivePermissions = Permission::where('is_active', false)->count();

echo "📊 Permission Counts:\n";
echo "   Total permissions: $totalPermissions\n";
echo "   Active permissions: $activePermissions\n";
echo "   Inactive permissions: $inactivePermissions\n\n";

// Check permissions by category
echo "📋 Permissions by Category:\n";
$categories = Permission::select('category', DB::raw('count(*) as count'))
    ->groupBy('category')
    ->orderBy('category')
    ->get();

$totalFromCategories = 0;
foreach ($categories as $category) {
    echo sprintf("   %-20s: %d\n", $category->category, $category->count);
    $totalFromCategories += $category->count;
}

echo "\n   Total from categories: $totalFromCategories\n\n";

// Check for specific missing permissions that should exist
echo "🔍 Checking for key permissions:\n";
$keyPermissions = [
    'dashboard.view', 'users.view', 'roles.view', 'products.view',
    'crm.access', 'crm.leads.view', 'permissions.view', 'blog.view',
    'settings.view', 'marketing.access', 'analytics.view'
];

$missingPermissions = [];
foreach ($keyPermissions as $permissionName) {
    $exists = Permission::where('name', $permissionName)->exists();
    if ($exists) {
        echo "   ✅ $permissionName\n";
    } else {
        echo "   ❌ $permissionName (MISSING)\n";
        $missingPermissions[] = $permissionName;
    }
}

if (count($missingPermissions) > 0) {
    echo "\n⚠️  Missing " . count($missingPermissions) . " key permissions!\n";
} else {
    echo "\n✅ All key permissions found\n";
}

// Check cache status
echo "\n🗂️  Cache Information:\n";
try {
    $cacheDriver = config('cache.default');
    echo "   Cache driver: $cacheDriver\n";
    
    // Try to clear cache
    \Artisan::call('cache:clear');
    echo "   ✅ Cache cleared\n";
} catch (\Exception $e) {
    echo "   ⚠️  Cache clear failed: " . $e->getMessage() . "\n";
}

// Check if we can create a test permission
echo "\n🧪 Testing permission creation:\n";
try {
    $testPermission = Permission::updateOrCreate(
        ['name' => 'test.diagnostic'],
        [
            'display_name' => 'Test Diagnostic',
            'description' => 'Test permission for diagnostic',
            'category' => 'system',
            'is_active' => true
        ]
    );
    
    if ($testPermission->wasRecentlyCreated) {
        echo "   ✅ Can create new permissions\n";
    } else {
        echo "   ✅ Can update existing permissions\n";
    }
    
    // Clean up test permission
    $testPermission->delete();
    echo "   ✅ Test permission cleaned up\n";
    
} catch (\Exception $e) {
    echo "   ❌ Permission creation failed: " . $e->getMessage() . "\n";
}

// Environment check
echo "\n🌍 Environment Information:\n";
echo "   Environment: " . app()->environment() . "\n";
echo "   Laravel Version: " . app()->version() . "\n";
echo "   PHP Version: " . PHP_VERSION . "\n";

// Final recommendation
echo "\n💡 Recommendations:\n";
if ($totalPermissions < 297) {
    echo "   1. Run the permission sync script: php production_permission_sync.php\n";
    echo "   2. Clear all caches: php artisan optimize:clear\n";
    echo "   3. Check database migrations are up to date\n";
} else {
    echo "   ✅ Permission count looks correct ($totalPermissions permissions)\n";
    echo "   If role edit page still shows wrong count, try:\n";
    echo "   1. Clear browser cache\n";
    echo "   2. Check for JavaScript caching issues\n";
    echo "   3. Verify the role edit page is using latest code\n";
}

echo "\n🎯 Next Steps:\n";
echo "   1. If permissions are missing, run: php production_permission_sync.php\n";
echo "   2. Clear caches: php artisan optimize:clear\n";
echo "   3. Test role edit page again\n";
echo "   4. Check browser developer tools for any JavaScript errors\n";

echo "\nDiagnostic complete!\n";
