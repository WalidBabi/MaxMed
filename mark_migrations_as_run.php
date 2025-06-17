<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking existing tables and marking migrations as run...\n\n";

// List of migrations that should be marked as run if their corresponding tables exist
$migrationTableMap = [
    'feedback' => '2024_03_21_000000_create_feedback_table',
    'roles' => '2025_01_10_000001_create_roles_table',
    'users' => '2025_01_10_000002_add_role_id_to_users_table', // This modifies users table
    'orders' => '2025_01_14_000000_add_customer_id_to_orders_table', // This modifies orders table
    'purchase_orders' => '2025_01_15_000001_create_purchase_orders_table',
    'purchase_order_items' => '2025_01_15_000002_create_purchase_order_items_table',
    'supplier_payments' => '2025_01_15_000003_create_supplier_payments_table',
    'categories' => '2025_01_17_000001_add_seo_slugs_to_categories_table', // This modifies categories table
    'products' => '2025_01_18_000000_modify_price_aed_column_in_products_table', // This modifies products table
    'products' => '2025_01_20_000000_add_sku_to_products_table', // This modifies products table
    'crm_leads' => '2025_01_20_000001_create_crm_leads_table',
    'crm_activities' => '2025_01_20_000002_create_crm_activities_table',
    'crm_deals' => '2025_01_20_000003_create_crm_deals_table',
    'quotation_requests' => '2025_01_20_000004_enhance_quotation_requests_table', // This modifies quotation_requests table
    'supplier_quotations' => '2025_01_20_000005_create_supplier_quotations_table',
    'contact_submissions' => '2025_01_21_000001_create_contact_submissions_table',
    'supplier_categories' => '2025_01_21_000001_create_supplier_categories_table',
    'cache_locks' => '2025_03_24_180858_create_cache_locks_table',
    'cache' => '2025_03_24_180858_create_cache_table',
    'categories' => '2025_03_24_180858_create_categories_table',
    'failed_jobs' => '2025_03_24_180858_create_failed_jobs_table',
    'inventories' => '2025_03_24_180858_create_inventories_table',
    'job_batches' => '2025_03_24_180858_create_job_batches_table',
    'jobs' => '2025_03_24_180858_create_jobs_table',
    'news' => '2025_03_24_180858_create_news_table',
    'order_items' => '2025_03_24_180858_create_order_items_table',
    'orders' => '2025_03_24_180858_create_orders_table',
    'password_reset_tokens' => '2025_03_24_180858_create_password_reset_tokens_table',
    'product_reservations' => '2025_03_24_180858_create_product_reservations_table',
    'products' => '2025_03_24_180858_create_products_table',
    'sessions' => '2025_03_24_180858_create_sessions_table',
    'transactions' => '2025_03_24_180858_create_transactions_table',
    'users' => '2025_03_24_180858_create_users_table',
    'quotation_requests' => '2025_03_24_181256_create_quotation_requests_table',
    'product_filters' => '2025_04_05_120417_create_product_filters_table',
    'product_images' => '2025_04_11_104451_create_product_images_table',
    'brands' => '2025_04_11_193653_create_brands_table',
    'products' => '2025_04_12_082659_add_specifications_to_products_table', // This modifies products table
    'product_specifications' => '2025_04_12_082917_create_product_specifications_table',
    'deliveries' => '2025_06_09_022540_create_deliveries_table',
    'customers' => '2025_06_09_031300_create_customers_table',
    'quotes' => '2025_06_09_031325_create_quotes_table',
    'invoices' => '2025_06_09_031331_create_invoices_table',
    'quote_items' => '2025_06_09_031349_create_quote_items_table',
    'invoice_items' => '2025_06_09_031354_create_invoice_items_table',
    'deliveries' => '2025_06_09_065800_add_signature_fields_to_deliveries_table', // This modifies deliveries table
    'products' => '2025_06_13_082805_add_supplier_id_to_products_table', // This modifies products table
    'system_feedback' => '2025_06_13_112836_create_system_feedback_table',
    'quote_items' => '2025_06_14_140626_add_product_id_to_quote_items_table', // This modifies quote_items table
    'order_items' => '2025_06_14_182951_add_variation_to_order_items_table', // This modifies order_items table
    'payments' => '2025_06_15_000000_create_payments_table',
    'users' => '2025_06_15_120000_add_profile_photo_to_users_table', // This modifies users table
    'deliveries' => '2025_06_15_210524_add_supplier_fields_to_deliveries_table', // This modifies deliveries table
    'deliveries' => '2025_06_15_232734_add_delivery_number_to_deliveries_table', // This modifies deliveries table
    'quotation_requests' => '2025_06_17_001109_add_delivery_timeline_to_quotation_requests_table', // This modifies quotation_requests table
    'contact_submissions' => '2025_06_17_001552_add_lead_potential_to_contact_submissions_table', // This modifies contact_submissions table
    'quotation_requests' => '2025_06_17_041449_enhance_quotation_requests_table_complete', // This modifies quotation_requests table
    'quotation_requests' => '2025_06_17_041938_add_quotation_requests_foreign_keys', // This modifies quotation_requests table
    'products' => '2025_06_17_132837_add_slug_to_products_table', // This modifies products table
    'categories' => '2025_06_17_132838_add_slug_to_categories_table', // This modifies categories table
];

// Get current batch number (or start with 1)
$currentBatch = DB::table('migrations')->max('batch') ?: 0;
$currentBatch++;

$markedCount = 0;
$alreadyMarkedCount = 0;

foreach ($migrationTableMap as $tableName => $migrationName) {
    // Check if migration is already marked as run
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    
    if ($exists) {
        echo "✓ Migration {$migrationName} already marked as run\n";
        $alreadyMarkedCount++;
        continue;
    }
    
    // Check if the table exists
    if (Schema::hasTable($tableName)) {
        // Insert the migration record
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $currentBatch
        ]);
        
        echo "✓ Marked {$migrationName} as run (table '{$tableName}' exists)\n";
        $markedCount++;
    } else {
        echo "⚠ Table '{$tableName}' does not exist, skipping {$migrationName}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Migrations marked as run: {$markedCount}\n";
echo "Already marked: {$alreadyMarkedCount}\n";
echo "Batch number used: {$currentBatch}\n\n";

echo "You can now run 'php artisan migrate' to run any remaining new migrations.\n"; 