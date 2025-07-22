<?php

require_once 'vendor/autoload.php';

use App\Services\ProductSpecificationService;
use App\Models\Category;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Corrected Category Names...\n\n";

$service = new ProductSpecificationService();

// Test the corrected category names (matching your actual database)
$rapidTestCategories = [
    'Women\'s Health',
    'Infectious Disease', 
    'Drugs of Abuse',
    'Tumor Markers',
    'Cardiac Markers',
    'Other Rapid Tests'
];

foreach ($rapidTestCategories as $categoryName) {
    echo "Testing category: {$categoryName}\n";
    
    $category = Category::where('name', $categoryName)->first();
    
    if ($category) {
        echo "  ✓ Found category (ID: {$category->id})\n";
        
        $templates = $service->getCategorySpecificationTemplates($category->id);
        
        if (!empty($templates)) {
            echo "  ✓ Templates loaded: " . count($templates) . " groups\n";
            
            foreach ($templates as $groupName => $specs) {
                echo "    - {$groupName}: " . count($specs) . " specifications\n";
            }
            echo "\n";
        } else {
            echo "  ✗ No templates found\n\n";
        }
    } else {
        echo "  ✗ Category not found in database\n\n";
    }
}

echo "Test completed.\n"; 