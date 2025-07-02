<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing automatic invoice conversion and cash receipt creation...\n";

try {
    // Find an existing delivery that's in_transit
    $delivery = \App\Models\Delivery::with(['order.proformaInvoice', 'order.invoices'])
        ->where('status', 'in_transit')
        ->first();
    
    if (!$delivery) {
        echo "❌ No delivery found with 'in_transit' status\n";
        echo "Creating a test delivery...\n";
        
        // Create a test order
        $order = \App\Models\Order::create([
            'user_id' => 1,
            'customer_id' => 1,
            'total_amount' => 1000.00,
            'shipping_address' => 'Test Address 123',
            'shipping_city' => 'Dubai',
            'shipping_state' => 'Dubai',
            'shipping_zipcode' => '12345',
            'shipping_phone' => '+971501234567',
            'notes' => 'Test order for delivery conversion',
            'status' => 'processing'
        ]);
        
        // Create a proforma invoice
        $proformaInvoice = \App\Models\Invoice::create([
            'order_id' => $order->id,
            'type' => 'proforma',
            'status' => 'sent',
            'payment_terms' => 'advance_50',
            'total_amount' => 1000.00,
            'paid_amount' => 500.00,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'created_by' => 1
        ]);
        
        // Create delivery
        $delivery = \App\Models\Delivery::create([
            'order_id' => $order->id,
            'tracking_number' => 'TRK' . time(),
            'status' => 'in_transit',
            'shipping_address' => 'Test Address 123',
            'shipping_city' => 'Dubai',
            'shipping_state' => 'Dubai',
            'shipping_zipcode' => '12345',
            'shipping_phone' => '+971501234567'
        ]);
        
        echo "✅ Created test delivery: {$delivery->tracking_number}\n";
    }
    
    echo "📦 Found delivery: {$delivery->tracking_number}\n";
    echo "   Order: {$delivery->order->order_number}\n";
    echo "   Status: {$delivery->status}\n";
    
    // Check for proforma invoice
    $proformaInvoice = $delivery->order->proformaInvoice;
    if ($proformaInvoice) {
        echo "📄 Found proforma invoice: {$proformaInvoice->invoice_number}\n";
        echo "   Status: {$proformaInvoice->status}\n";
        echo "   Type: {$proformaInvoice->type}\n";
        echo "   Payment Terms: {$proformaInvoice->payment_terms}\n";
        echo "   Total Amount: {$proformaInvoice->total_amount}\n";
        echo "   Paid Amount: {$proformaInvoice->paid_amount}\n";
        echo "   Can Convert: " . ($proformaInvoice->canConvertToFinalInvoice() ? 'YES' : 'NO') . "\n";
    } else {
        echo "❌ No proforma invoice found for order\n";
        
        // Check all invoices
        $allInvoices = $delivery->order->invoices;
        echo "All invoices for this order:\n";
        foreach ($allInvoices as $invoice) {
            echo "  - {$invoice->invoice_number}: type={$invoice->type}, status={$invoice->status}\n";
        }
    }
    
    // Simulate customer signature
    echo "\n🖊️  Simulating customer signature...\n";
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
        'customer_name' => 'Test Customer',
        'delivery_conditions' => 'Good condition'
    ]);
    
    $controller = new \App\Http\Controllers\DeliveryController();
    $response = $controller->processSignature($request, $delivery->tracking_number);
    
    if ($response->getStatusCode() === 200) {
        echo "✅ Customer signature processed successfully!\n";
        
        // Refresh delivery data
        $delivery->refresh();
        echo "📦 Delivery status updated to: {$delivery->status}\n";
        
        // Check if final invoice was created
        $finalInvoice = \App\Models\Invoice::where('order_id', $delivery->order_id)
            ->where('type', 'final')
            ->first();
            
        if ($finalInvoice) {
            echo "📄 Final invoice created: {$finalInvoice->invoice_number}\n";
            echo "   Amount: {$finalInvoice->total_amount}\n";
            echo "   Status: {$finalInvoice->status}\n";
        } else {
            echo "❌ No final invoice created\n";
        }
        
        // Check if cash receipt was created
        $cashReceipt = \App\Models\CashReceipt::where('order_id', $delivery->order_id)
            ->first();
            
        if ($cashReceipt) {
            echo "💰 Cash receipt created: {$cashReceipt->receipt_number}\n";
            echo "   Amount: {$cashReceipt->amount} {$cashReceipt->currency}\n";
            echo "   Status: {$cashReceipt->status}\n";
        } else {
            echo "❌ No cash receipt created\n";
        }
        
    } else {
        echo "❌ Failed to process signature\n";
        $responseData = json_decode($response->getContent(), true);
        echo "   Error: " . ($responseData['error'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n"; 