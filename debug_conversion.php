<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;

echo "=== Testing Conversion Process ===\n";

// Find a quote with items
$quote = Quote::with('items')->latest()->first();
if (!$quote) {
    echo "No quotes found in database\n";
    exit;
}

echo "Quote ID: {$quote->id}\n";
echo "Quote Number: {$quote->quote_number}\n";
echo "Items Count: " . $quote->items->count() . "\n";

echo "\n=== Quote Items ===\n";
foreach ($quote->items as $item) {
    echo "Item ID: {$item->id}, Product ID: " . ($item->product_id ?: 'NULL') . ", Details: {$item->item_details}\n";
}

// Test creating a single invoice item manually
echo "\n=== Testing Manual Invoice Item Creation ===\n";
if ($quote->items->count() > 0) {
    $testItem = $quote->items->first();
    echo "Using quote item ID: {$testItem->id} with product_id: {$testItem->product_id}\n";
    
    // Create a test invoice first
    $testInvoice = Invoice::create([
        'type' => 'proforma',
        'quote_id' => $quote->id,
        'customer_name' => 'Test Customer',
        'billing_address' => 'Test Address',
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'total_amount' => 100.00,
        'payment_terms' => 'advance_50',
        'status' => 'draft',
        'is_proforma' => true,
        'created_by' => 1
    ]);
    
    echo "Created test invoice ID: {$testInvoice->id}\n";
    
    // Calculate values for invoice item
    $subtotal = $testItem->quantity * $testItem->rate;
    $discountAmount = ($subtotal * $testItem->discount) / 100;
    $lineTotal = $subtotal - $discountAmount;
    
    echo "Calculated values: subtotal={$subtotal}, discount={$discountAmount}, lineTotal={$lineTotal}\n";
    
    // Now test creating an invoice item - with explicit values to avoid boot() method interference
    $testInvoiceItem = InvoiceItem::create([
        'invoice_id' => $testInvoice->id,
        'product_id' => $testItem->product_id,
        'item_description' => $testItem->item_details,
        'quantity' => $testItem->quantity,
        'unit_price' => $testItem->rate,
        'discount_percentage' => $testItem->discount,
        'discount_amount' => $discountAmount,
        'line_total' => $lineTotal,
        'sort_order' => 1
    ]);
    
    echo "Created test invoice item ID: {$testInvoiceItem->id}\n";
    echo "Test invoice item product_id: " . ($testInvoiceItem->product_id ?: 'NULL') . "\n";
    
    // Refresh from database to see what was actually saved
    $testInvoiceItem->refresh();
    echo "After refresh - product_id: " . ($testInvoiceItem->product_id ?: 'NULL') . "\n";
    
    // Clean up
    $testInvoiceItem->delete();
    $testInvoice->delete();
} else {
    echo "No quote items found to test with\n";
}

echo "\nDone!\n"; 