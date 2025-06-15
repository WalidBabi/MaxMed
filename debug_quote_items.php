<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\QuoteItem;
use App\Models\InvoiceItem;

echo "=== Checking Quote Items ===\n";
$quoteItems = QuoteItem::with('product')->latest()->take(5)->get();

foreach ($quoteItems as $item) {
    echo "Quote Item ID: {$item->id}\n";
    echo "Product ID: " . ($item->product_id ?: 'NULL') . "\n";
    echo "Product Name: " . ($item->product ? $item->product->name : 'N/A') . "\n";
    echo "Item Details: {$item->item_details}\n";
    echo "---\n";
}

echo "\n=== Checking Invoice Items ===\n";
$invoiceItems = InvoiceItem::with('product')->latest()->take(5)->get();

foreach ($invoiceItems as $item) {
    echo "Invoice Item ID: {$item->id}\n";
    echo "Product ID: " . ($item->product_id ?: 'NULL') . "\n";
    echo "Product Name: " . ($item->product ? $item->product->name : 'N/A') . "\n";
    echo "Item Description: {$item->item_description}\n";
    echo "---\n";
} 