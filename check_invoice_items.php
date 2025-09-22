<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check invoice items table structure
$columns = \DB::select('DESCRIBE invoice_items');
echo "Invoice Items table columns:\n";
foreach($columns as $col) {
    echo "- " . $col->Field . " (" . $col->Type . ")\n";
}

echo "\n";

// Check if there are any invoice items with data
$invoiceItem = \App\Models\InvoiceItem::first();
if ($invoiceItem) {
    echo "Sample InvoiceItem data:\n";
    echo "- ID: {$invoiceItem->id}\n";
    echo "- Invoice ID: {$invoiceItem->invoice_id}\n";
    echo "- Product ID: {$invoiceItem->product_id}\n";
    echo "- Quantity: {$invoiceItem->quantity}\n";
    echo "- Price: {$invoiceItem->price}\n";
    echo "- Size: " . ($invoiceItem->size ?? 'NULL') . "\n";
    echo "- Specifications: " . ($invoiceItem->specifications ?? 'NULL') . "\n";
    
    // Get all attributes
    echo "\nAll InvoiceItem attributes:\n";
    $attributes = $invoiceItem->getAttributes();
    foreach($attributes as $key => $value) {
        echo "- {$key}: " . ($value ?? 'NULL') . "\n";
    }
}
?>
