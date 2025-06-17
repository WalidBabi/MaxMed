<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

echo "Marking all pending migrations as run...\n\n";

// Get all pending migrations
$pendingMigrations = [
    '2025_01_10_000002_add_role_id_to_users_table',
    '2025_01_14_000000_add_customer_id_to_orders_table',
    '2025_01_17_000001_add_seo_slugs_to_categories_table',
    '2025_01_18_000000_modify_price_aed_column_in_products_table',
    '2025_01_20_000000_add_sku_to_products_table',
    '2025_01_20_000004_enhance_quotation_requests_table',
    '2025_01_21_000001_create_contact_submissions_table',
    '2025_03_24_180858_create_categories_table',
    '2025_03_24_180858_create_order_items_table',
    '2025_03_24_180858_create_products_table',
    '2025_03_24_180858_create_users_table',
    '2025_03_24_181256_create_quotation_requests_table',
    '2025_04_05_120417_create_product_filters_table',
    '2025_04_12_082659_add_specifications_to_products_table',
    '2025_04_12_082917_create_product_specifications_table',
    '2025_06_09_022540_create_deliveries_table',
    '2025_06_09_031349_create_quote_items_table',
    '2025_06_09_065800_add_signature_fields_to_deliveries_table',
    '2025_06_13_082805_add_supplier_id_to_products_table',
    '2025_06_15_210524_add_supplier_fields_to_deliveries_table',
    '2025_06_17_001109_add_delivery_timeline_to_quotation_requests_table',
    '2025_06_17_041449_enhance_quotation_requests_table_complete'
];

// Get current batch number
$currentBatch = DB::table('migrations')->max('batch') ?: 0;
$currentBatch++;

$markedCount = 0;
$alreadyMarkedCount = 0;

foreach ($pendingMigrations as $migrationName) {
    // Check if migration is already marked as run
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    
    if ($exists) {
        echo "✓ Migration {$migrationName} already marked as run\n";
        $alreadyMarkedCount++;
        continue;
    }
    
    // Insert the migration record
    DB::table('migrations')->insert([
        'migration' => $migrationName,
        'batch' => $currentBatch
    ]);
    
    echo "✓ Marked {$migrationName} as run\n";
    $markedCount++;
}

echo "\n=== Summary ===\n";
echo "Migrations marked as run: {$markedCount}\n";
echo "Already marked: {$alreadyMarkedCount}\n";
echo "Batch number used: {$currentBatch}\n\n";

echo "All pending migrations have been marked as run!\n";
echo "You can now run 'php artisan migrate:status' to verify.\n"; 