<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quote;
use App\Models\Invoice;

echo "=== Testing Quote to Invoice Conversion ===\n";

// Find a quote that hasn't been converted yet
$quote = Quote::with('items')->where('status', '!=', 'invoiced')->first();
if (!$quote) {
    echo "No unconverted quotes found. Creating a test scenario...\n";
    
    // Let's check the most recent quote regardless of status
    $quote = Quote::with('items')->latest()->first();
    if (!$quote) {
        echo "No quotes found at all!\n";
        exit;
    }
    
    // Reset its status to draft so we can test conversion
    $quote->update(['status' => 'draft']);
    
    // Delete any existing invoices for this quote to allow re-conversion
    Invoice::where('quote_id', $quote->id)->delete();
}

echo "Using Quote ID: {$quote->id}, Number: {$quote->quote_number}\n";
echo "Quote Items: " . $quote->items->count() . "\n";

foreach ($quote->items as $item) {
    echo "- Item {$item->id}: Product ID = " . ($item->product_id ?: 'NULL') . ", Details: {$item->item_details}\n";
}

echo "\n=== Simulating Conversion ===\n";

// Simulate the conversion process
try {
    \Illuminate\Support\Facades\DB::beginTransaction();

    // Create proforma invoice from quote (similar to the controller method)
    $invoice = Invoice::create([
        'type' => 'proforma',
        'quote_id' => $quote->id,
        'customer_name' => $quote->customer_name,
        'billing_address' => 'Test Address',
        'invoice_date' => now(),
        'due_date' => now()->addDays(30),
        'description' => $quote->subject ?: 'Test Proforma Invoice',
        'terms_conditions' => $quote->terms_conditions,
        'notes' => $quote->customer_notes,
        'sub_total' => $quote->sub_total,
        'total_amount' => $quote->total_amount,
        'payment_terms' => 'advance_50',
        'status' => 'draft',
        'is_proforma' => true,
        'requires_advance_payment' => true,
        'reference_number' => $quote->reference_number,
        'created_by' => 1
    ]);

    echo "Created Invoice ID: {$invoice->id}\n";

    // Copy quote items to invoice items
    foreach ($quote->items as $index => $quoteItem) {
        echo "Processing quote item {$quoteItem->id} with product_id: {$quoteItem->product_id}\n";
        
        $subtotal = $quoteItem->quantity * $quoteItem->rate;
        $discountAmount = ($subtotal * $quoteItem->discount) / 100;
        $lineTotal = $subtotal - $discountAmount;
        
        $invoiceItemData = [
            'invoice_id' => $invoice->id,
            'product_id' => $quoteItem->product_id,
            'item_description' => $quoteItem->item_details,
            'quantity' => $quoteItem->quantity,
            'unit_price' => $quoteItem->rate,
            'discount_percentage' => $quoteItem->discount,
            'discount_amount' => $discountAmount,
            'line_total' => $lineTotal,
            'sort_order' => $index + 1
        ];
        
        echo "Creating invoice item with product_id: {$invoiceItemData['product_id']}\n";
        
        $invoiceItem = \App\Models\InvoiceItem::create($invoiceItemData);
        
        echo "Created invoice item {$invoiceItem->id} with product_id: " . ($invoiceItem->product_id ?: 'NULL') . "\n";
        
        // Check again after refresh
        $invoiceItem->refresh();
        echo "After refresh: product_id = " . ($invoiceItem->product_id ?: 'NULL') . "\n";
    }

    \Illuminate\Support\Facades\DB::commit();
    echo "Conversion completed successfully!\n";

} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollback();
    echo "Error during conversion: " . $e->getMessage() . "\n";
}

echo "\n=== Final Check ===\n";
$createdInvoice = Invoice::with('items')->where('quote_id', $quote->id)->latest()->first();
if ($createdInvoice) {
    echo "Invoice {$createdInvoice->id} has " . $createdInvoice->items->count() . " items:\n";
    foreach ($createdInvoice->items as $item) {
        echo "- Item {$item->id}: Product ID = " . ($item->product_id ?: 'NULL') . "\n";
    }
}

echo "\nDone!\n"; 