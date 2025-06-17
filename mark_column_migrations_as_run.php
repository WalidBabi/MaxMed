<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking existing columns and marking migrations as run...\n\n";

// List of column-adding migrations
$columnMigrations = [
    [
        'migration' => '2025_01_10_000002_add_role_id_to_users_table',
        'table' => 'users',
        'column' => 'role_id'
    ],
    [
        'migration' => '2025_01_14_000000_add_customer_id_to_orders_table',
        'table' => 'orders',
        'column' => 'customer_id'
    ],
    [
        'migration' => '2025_01_17_000001_add_seo_slugs_to_categories_table',
        'table' => 'categories',
        'column' => 'slug'
    ],
    [
        'migration' => '2025_01_18_000000_modify_price_aed_column_in_products_table',
        'table' => 'products',
        'column' => 'price_aed'
    ],
    [
        'migration' => '2025_01_20_000000_add_sku_to_products_table',
        'table' => 'products',
        'column' => 'sku'
    ],
    [
        'migration' => '2025_04_12_082659_add_specifications_to_products_table',
        'table' => 'products',
        'column' => 'specifications'
    ],
    [
        'migration' => '2025_06_09_065800_add_signature_fields_to_deliveries_table',
        'table' => 'deliveries',
        'column' => 'signature_image'
    ],
    [
        'migration' => '2025_06_13_082805_add_supplier_id_to_products_table',
        'table' => 'products',
        'column' => 'supplier_id'
    ],
    [
        'migration' => '2025_06_15_210524_add_supplier_fields_to_deliveries_table',
        'table' => 'deliveries',
        'column' => 'supplier_id'
    ],
    [
        'migration' => '2025_06_17_001109_add_delivery_timeline_to_quotation_requests_table',
        'table' => 'quotation_requests',
        'column' => 'delivery_timeline'
    ]
];

// Get current batch number
$currentBatch = DB::table('migrations')->max('batch') ?: 0;
$currentBatch++;

$markedCount = 0;
$alreadyMarkedCount = 0;

foreach ($columnMigrations as $migration) {
    $migrationName = $migration['migration'];
    $tableName = $migration['table'];
    $columnName = $migration['column'];
    
    // Check if migration is already marked as run
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    
    if ($exists) {
        echo "✓ Migration {$migrationName} already marked as run\n";
        $alreadyMarkedCount++;
        continue;
    }
    
    // Check if the table and column exist
    if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $columnName)) {
        // Insert the migration record
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $currentBatch
        ]);
        
        echo "✓ Marked {$migrationName} as run (column '{$columnName}' exists in '{$tableName}')\n";
        $markedCount++;
    } else {
        echo "⚠ Column '{$columnName}' does not exist in table '{$tableName}', skipping {$migrationName}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Column migrations marked as run: {$markedCount}\n";
echo "Already marked: {$alreadyMarkedCount}\n";
echo "Batch number used: {$currentBatch}\n\n";

echo "You can now run 'php artisan migrate' again to run any remaining new migrations.\n"; 