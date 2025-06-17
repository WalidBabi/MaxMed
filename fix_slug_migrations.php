<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

echo "Fixing slug migrations...\n\n";

$slugMigrations = [
    '2025_06_17_132837_add_slug_to_products_table',
    '2025_06_17_132838_add_slug_to_categories_table'
];

// Remove these migrations from the migrations table so they can run again
foreach ($slugMigrations as $migration) {
    $deleted = DB::table('migrations')->where('migration', $migration)->delete();
    if ($deleted) {
        echo "✓ Removed {$migration} from migrations table\n";
    } else {
        echo "⚠ {$migration} was not in migrations table\n";
    }
}

echo "\nNow you can run: php artisan migrate\n";
echo "This will properly add the slug columns and then you can populate them.\n\n";

// Check if slug columns exist
echo "Checking current slug column status:\n";
if (Schema::hasColumn('products', 'slug')) {
    echo "✓ Products table already has slug column\n";
} else {
    echo "⚠ Products table missing slug column - migration will add it\n";
}

if (Schema::hasColumn('categories', 'slug')) {
    echo "✓ Categories table already has slug column\n";
} else {
    echo "⚠ Categories table missing slug column - migration will add it\n";
}

echo "\nAfter running migrations, you may want to populate slugs for existing data:\n";
echo "php artisan tinker\n";
echo "// For products:\n";
echo "App\\Models\\Product::whereNull('slug')->get()->each(function(\$product) { \$product->update(['slug' => Str::slug(\$product->name)]); });\n";
echo "// For categories:\n";
echo "App\\Models\\Category::whereNull('slug')->get()->each(function(\$category) { \$category->update(['slug' => Str::slug(\$category->name)]); });\n"; 