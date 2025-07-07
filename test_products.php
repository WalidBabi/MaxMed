<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Total products: " . Product::count() . "\n";
echo "Products with specifications: " . Product::whereHas('specifications')->count() . "\n";
echo "Products with size options: " . Product::where('has_size_options', true)->count() . "\n";

// Test a product with size options
$productWithSize = Product::with(['specifications', 'brand', 'category'])->where('has_size_options', true)->first();

if ($productWithSize) {
    echo "\nSample product with size options:\n";
    echo "Name: " . $productWithSize->name . "\n";
    echo "Specifications count: " . $productWithSize->specifications->count() . "\n";
    echo "Has size options: " . ($productWithSize->has_size_options ? 'Yes' : 'No') . "\n";
    
    if ($productWithSize->has_size_options) {
        echo "Size options: " . json_encode($productWithSize->size_options) . "\n";
    }
    
    echo "Specifications:\n";
    foreach ($productWithSize->specifications as $spec) {
        echo "- " . ($spec->display_name ?: $spec->specification_key . ': ' . $spec->specification_value) . "\n";
    }
    
    // Test the mapping logic from the controller
    $mappedProduct = [
        'id' => $productWithSize->id,
        'name' => $productWithSize->name,
        'sku' => $productWithSize->sku,
        'price_aed' => $productWithSize->price_aed,
        'price_usd' => $productWithSize->procurement_price_usd,
        'has_size_options' => $productWithSize->has_size_options,
        'size_options' => $productWithSize->size_options,
        'specifications' => $productWithSize->specifications->map(function($spec) {
            return $spec->display_name ?: $spec->specification_key . ': ' . $spec->specification_value;
        })->toArray(),
        'brand_name' => $productWithSize->brand ? $productWithSize->brand->name : null,
        'category_name' => $productWithSize->category ? $productWithSize->category->name : null
    ];
    
    echo "\nMapped product data:\n";
    echo "Specifications JSON: " . json_encode($mappedProduct['specifications']) . "\n";
    echo "Size options JSON: " . json_encode($mappedProduct['size_options']) . "\n";
    echo "Has size options: " . ($mappedProduct['has_size_options'] ? 'true' : 'false') . "\n";
}

// Also test a product with both specifications and size options
$productWithBoth = Product::with(['specifications', 'brand', 'category'])
    ->whereHas('specifications')
    ->where('has_size_options', true)
    ->first();

if ($productWithBoth) {
    echo "\n\nSample product with BOTH specifications and size options:\n";
    echo "Name: " . $productWithBoth->name . "\n";
    echo "Specifications count: " . $productWithBoth->specifications->count() . "\n";
    echo "Has size options: " . ($productWithBoth->has_size_options ? 'Yes' : 'No') . "\n";
    
    if ($productWithBoth->has_size_options) {
        echo "Size options: " . json_encode($productWithBoth->size_options) . "\n";
    }
    
    echo "Specifications:\n";
    foreach ($productWithBoth->specifications as $spec) {
        echo "- " . ($spec->display_name ?: $spec->specification_key . ': ' . $spec->specification_value) . "\n";
    }
} 