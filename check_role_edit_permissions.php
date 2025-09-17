<?php
/**
 * Check Role Edit Permissions
 * 
 * This script mimics exactly what the role edit page sees
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "🔍 Role Edit Permission Check\n";
echo "============================\n\n";

// This mimics exactly what the RoleController->edit() method does
$permissionCategories = Permission::getCategories();

// Get permissions ordered consistently (same as controller)
$allPermissions = Permission::where('is_active', true)
    ->orderBy('category')
    ->orderBy('display_name')
    ->orderBy('name')
    ->get();

echo "📊 What the role edit page sees:\n";
echo "   Total active permissions: " . $allPermissions->count() . "\n\n";

// Group permissions by category (same as controller)
$permissions = collect();
$categoryOrder = array_keys($permissionCategories);

foreach ($categoryOrder as $category) {
    $categoryPermissions = $allPermissions->where('category', $category);
    if ($categoryPermissions->isNotEmpty()) {
        $permissions->put($category, $categoryPermissions->values());
    }
}

// Add any categories not in the predefined list
$remainingCategories = $allPermissions->pluck('category')->unique()->diff($categoryOrder);
foreach ($remainingCategories as $category) {
    $categoryPermissions = $allPermissions->where('category', $category);
    if ($categoryPermissions->isNotEmpty()) {
        $permissions->put($category, $categoryPermissions->values());
    }
}

echo "📋 Categories and counts (as seen by role edit page):\n";
$totalDisplayed = 0;
foreach ($permissions as $categoryKey => $categoryPermissions) {
    $categoryName = $permissionCategories[$categoryKey] ?? ucfirst(str_replace('_', ' ', $categoryKey));
    $count = $categoryPermissions->count();
    echo sprintf("   %-30s: %d permissions\n", $categoryName, $count);
    $totalDisplayed += $count;
}

echo "\n📊 Summary:\n";
echo "   Total permissions displayed: $totalDisplayed\n";
echo "   Categories displayed: " . $permissions->count() . "\n";

if ($totalDisplayed != $allPermissions->count()) {
    echo "\n⚠️  WARNING: Mismatch between total active permissions and displayed permissions!\n";
    echo "   This suggests some permissions are not being categorized properly.\n";
} else {
    echo "\n✅ All active permissions are properly categorized and displayed.\n";
}

// Check for uncategorized permissions
$uncategorizedPermissions = $allPermissions->whereNotIn('category', array_keys($permissionCategories));
if ($uncategorizedPermissions->count() > 0) {
    echo "\n⚠️  Uncategorized permissions found:\n";
    foreach ($uncategorizedPermissions as $permission) {
        echo "   - {$permission->name} (category: {$permission->category})\n";
    }
}

echo "\nThis is exactly what the role edit page should display.\n";
