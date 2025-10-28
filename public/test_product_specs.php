<?php
// Quick diagnostic for product specifications
// Access via: http://127.0.0.1:8000/test_product_specs.php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\Product;

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Product Specifications Test</h1>";
echo "<style>body{font-family:sans-serif;margin:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f2f2f2;}</style>";

// Get product by ID or SKU
$productId = $_GET['id'] ?? 617;
$product = Product::with(['specifications', 'brand'])->find($productId);

if (!$product) {
    echo "<p style='color:red;'>Product ID {$productId} not found!</p>";
    exit;
}

echo "<h2>Product: {$product->name}</h2>";
echo "<p><strong>ID:</strong> {$product->id} | <strong>SKU:</strong> {$product->sku}</p>";

// Check specifications
$specs = $product->specifications;
echo "<h3>Specifications ({$specs->count()})</h3>";

if ($specs->count() > 0) {
    echo "<table>";
    echo "<tr><th>Key</th><th>Display Name</th><th>Value</th><th>show_on_detail</th></tr>";
    foreach ($specs as $spec) {
        $value = htmlspecialchars($spec->specification_value);
        $showOn = $spec->show_on_detail ? 'true' : 'false';
        echo "<tr>";
        echo "<td>{$spec->specification_key}</td>";
        echo "<td>{$spec->display_name}</td>";
        echo "<td><pre style='max-height:100px;overflow:auto;'>{$value}</pre></td>";
        echo "<td>{$showOn}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show what will be in JSON
    echo "<h3>JSON for Quote Form (data-specifications)</h3>";
    $specsArray = $specs->map(function($spec) {
        return $spec->display_name . ': ' . $spec->formatted_value;
    })->toArray();
    echo "<pre>" . json_encode($specsArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
} else {
    echo "<p style='color:orange;'>No specifications found for this product.</p>";
}

echo "<hr>";
echo "<p><a href='/admin/quotes/create'>Go to Create Quote</a></p>";
echo "<p><a href='?id=617'>Test Product 617</a> | <a href='?id=61'>Test Product 61</a> | <a href='?id=64'>Test Product 64</a></p>";

