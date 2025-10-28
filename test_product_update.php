<?php
// Test script to check product update
// Access via: http://your-domain.com/test_product_update.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\Product;
use App\Models\ProductSpecification;

echo "<h1>Product Update Test</h1>";

// Check if product 61 exists
$product = Product::find(61);

if (!$product) {
    echo "<p style='color: red;'>Product 61 not found!</p>";
    exit;
}

echo "<h2>Product Information</h2>";
echo "<p><strong>ID:</strong> {$product->id}</p>";
echo "<p><strong>Name:</strong> {$product->name}</p>";
echo "<p><strong>Category ID:</strong> {$product->category_id}</p>";

// Check specifications
$specs = ProductSpecification::where('product_id', $product->id)->get();

echo "<h2>Current Specifications ({$specs->count()})</h2>";

if ($specs->count() > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Key</th><th>Display Name</th><th>Value (truncated)</th><th>show_on_detail</th></tr>";
    foreach ($specs as $spec) {
        $value = substr($spec->specification_value, 0, 50);
        $showOn = $spec->show_on_detail ? 'true' : 'false';
        echo "<tr>";
        echo "<td>{$spec->specification_key}</td>";
        echo "<td>{$spec->display_name}</td>";
        echo "<td>{$value}...</td>";
        echo "<td>{$showOn}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No specifications found.</p>";
}

// Check validation rules
echo "<h2>Validation Test</h2>";

$testNotes = "Package Includes:\n•\tDPCD100T Meter\n•\tD201G Refillable glass pH probe";

$validator = Validator::make([
    'notes' => $testNotes,
    'notes_show_on_website' => true
], [
    'notes' => 'nullable|string',
    'notes_show_on_website' => 'nullable|boolean'
]);

if ($validator->fails()) {
    echo "<p style='color: red;'>Validation FAILED!</p>";
    echo "<pre>" . print_r($validator->errors()->all(), true) . "</pre>";
} else {
    echo "<p style='color: green;'>Validation PASSED!</p>";
    echo "<p>Notes length: " . strlen($testNotes) . " characters</p>";
}

// Test creating a notes specification
echo "<h2>Test Creating Notes Specification</h2>";

try {
    // Delete existing notes
    ProductSpecification::where('product_id', $product->id)
        ->where('specification_key', 'notes')
        ->delete();
    
    // Create new notes
    $newNotes = ProductSpecification::create([
        'product_id' => $product->id,
        'specification_key' => 'notes',
        'specification_value' => $testNotes,
        'unit' => null,
        'category' => 'General',
        'display_name' => 'Notes',
        'description' => null,
        'sort_order' => 999,
        'is_filterable' => false,
        'is_searchable' => false,
        'show_on_listing' => false,
        'show_on_detail' => true,
    ]);
    
    echo "<p style='color: green;'>Notes specification created successfully!</p>";
    echo "<p>ID: {$newNotes->id}</p>";
    echo "<p>Value: " . substr($newNotes->specification_value, 0, 100) . "...</p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>Error creating notes: {$e->getMessage()}</p>";
}

echo "<hr>";
echo "<p><a href='/admin/products/{$product->id}/edit'>Go to Edit Product Page</a></p>";

