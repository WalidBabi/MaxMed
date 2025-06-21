<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ProductSpecificationService;
use App\Models\Category;

echo "Testing ProductSpecificationService...\n\n";

try {
    $service = new ProductSpecificationService();
    
    // Get all categories
    $categories = Category::all();
    echo "Available categories:\n";
    foreach ($categories as $category) {
        echo "- ID: {$category->id}, Name: {$category->name}\n";
    }
    
    echo "\n\nTesting specifications for each category:\n";
    foreach ($categories as $category) {
        echo "\n--- Category {$category->id}: {$category->name} ---\n";
        $templates = $service->getCategorySpecificationTemplates($category->id);
        if (!empty($templates)) {
            foreach ($templates as $categoryName => $specs) {
                echo "  {$categoryName}:\n";
                foreach ($specs as $spec) {
                    echo "    - {$spec['name']} ({$spec['type']})";
                    if ($spec['required']) echo " [Required]";
                    if ($spec['unit']) echo " [Unit: {$spec['unit']}]";
                    echo "\n";
                }
            }
        } else {
            echo "  No specifications available\n";
        }
    }
    
    echo "\n\nTest completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 