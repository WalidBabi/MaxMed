<?php

require_once 'vendor/autoload.php';

use App\Services\ProductSpecificationService;
use App\Models\Category;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing HPLC specifications...\n";

$service = new ProductSpecificationService();

// Find the HPLC category
$category = Category::where('name', 'LIKE', '%HPLC%')->first();

if ($category) {
    echo "Found category: {$category->name} (ID: {$category->id})\n";
    
    // Get the specification templates
    $templates = $service->getCategorySpecificationTemplates($category->id);
    
    echo "Templates found:\n";
    foreach ($templates as $categoryName => $specs) {
        echo "  Category: {$categoryName}\n";
        foreach ($specs as $spec) {
            echo "    - {$spec['name']} ({$spec['key']}) - {$spec['type']}";
            if (isset($spec['unit'])) {
                echo " - {$spec['unit']}";
            }
            echo "\n";
        }
    }
    
    // Show total count
    $totalSpecs = 0;
    foreach ($templates as $specs) {
        $totalSpecs += count($specs);
    }
    echo "\nTotal specifications: {$totalSpecs}\n";
    
} else {
    echo "HPLC category not found\n";
    
    // List all categories to help debug
    echo "Available categories:\n";
    $categories = Category::all();
    foreach ($categories as $cat) {
        echo "  - {$cat->name} (ID: {$cat->id})\n";
    }
}

echo "Test completed.\n"; 