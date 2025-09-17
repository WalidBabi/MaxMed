<?php
/**
 * Verify Database Connection Script
 * 
 * This script will show exactly which database Laravel is connecting to
 * and compare it with what phpMyAdmin shows
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

echo "🔍 Database Connection Verification\n";
echo "==================================\n\n";

// Show database configuration
echo "📊 Database Configuration:\n";
echo "   Default Connection: " . Config::get('database.default') . "\n";
echo "   Host: " . Config::get('database.connections.' . Config::get('database.default') . '.host') . "\n";
echo "   Database: " . Config::get('database.connections.' . Config::get('database.default') . '.database') . "\n";
echo "   Username: " . Config::get('database.connections.' . Config::get('database.default') . '.username') . "\n\n";

// Test direct database query
echo "🔍 Direct Database Queries:\n";
try {
    // Raw SQL count
    $rawCount = DB::select("SELECT COUNT(*) as count FROM permissions")[0]->count;
    echo "   Raw SQL count: $rawCount\n";
    
    // Eloquent count
    $eloquentCount = Permission::count();
    echo "   Eloquent count: $eloquentCount\n";
    
    // Active permissions count
    $activeCount = Permission::where('is_active', true)->count();
    echo "   Active permissions: $activeCount\n";
    
    // Inactive permissions count
    $inactiveCount = Permission::where('is_active', false)->count();
    echo "   Inactive permissions: $inactiveCount\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ Database query failed: " . $e->getMessage() . "\n\n";
}

// Show database name from connection
echo "🗄️  Actual Database Information:\n";
try {
    $databaseName = DB::select("SELECT DATABASE() as db")[0]->db;
    echo "   Currently connected to database: $databaseName\n";
    
    // Show table info
    $tableInfo = DB::select("SHOW TABLE STATUS LIKE 'permissions'");
    if (!empty($tableInfo)) {
        $table = $tableInfo[0];
        echo "   Permissions table rows: " . $table->Rows . "\n";
        echo "   Table engine: " . $table->Engine . "\n";
        echo "   Table collation: " . $table->Collation . "\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Could not get database info: " . $e->getMessage() . "\n";
}

// Show some sample permissions to verify data
echo "\n📋 Sample Permissions (first 10):\n";
try {
    $samplePermissions = DB::select("SELECT id, name, category, is_active FROM permissions ORDER BY id LIMIT 10");
    foreach ($samplePermissions as $permission) {
        $status = $permission->is_active ? 'Active' : 'Inactive';
        echo "   ID: {$permission->id} | {$permission->name} | {$permission->category} | {$status}\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Could not fetch sample permissions: " . $e->getMessage() . "\n";
}

// Check for any permissions with specific categories
echo "\n🔍 Category Breakdown:\n";
try {
    $categories = DB::select("SELECT category, COUNT(*) as count FROM permissions GROUP BY category ORDER BY category");
    $total = 0;
    foreach ($categories as $cat) {
        echo "   {$cat->category}: {$cat->count}\n";
        $total += $cat->count;
    }
    echo "   Total: $total\n";
} catch (\Exception $e) {
    echo "   ❌ Could not get category breakdown: " . $e->getMessage() . "\n";
}

echo "\n💡 Analysis:\n";
echo "   If phpMyAdmin shows 232 but Laravel shows 297, then:\n";
echo "   1. Laravel might be connecting to a different database\n";
echo "   2. There might be multiple database connections\n";
echo "   3. phpMyAdmin might be showing a different table/database\n";
echo "   \n";
echo "   Compare the database name above with what you see in phpMyAdmin!\n";

echo "\nVerification complete!\n";
