<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SupplierInquiry;
use App\Models\SupplierInquiryResponse;
use App\Models\User;

echo "=== Supplier Inquiry Check ===\n\n";

// Check recent inquiries
$inquiries = SupplierInquiry::with(['supplierResponses.supplier', 'product'])->latest()->take(5)->get();

if ($inquiries->isEmpty()) {
    echo "No supplier inquiries found in database.\n";
} else {
    echo "Recent Supplier Inquiries:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($inquiries as $inquiry) {
        echo "ID: {$inquiry->id}\n";
        echo "Reference: {$inquiry->reference_number}\n";
        echo "Status: {$inquiry->status}\n";
        echo "Broadcast: " . ($inquiry->broadcast_at ? $inquiry->broadcast_at->format('Y-m-d H:i') : 'Not broadcast') . "\n";
        echo "Product: " . ($inquiry->product ? $inquiry->product->name : ($inquiry->product_name ?: 'N/A')) . "\n";
        echo "Responses: {$inquiry->supplierResponses->count()}\n";
        
        if ($inquiry->supplierResponses->count() > 0) {
            echo "Supplier Responses:\n";
            foreach ($inquiry->supplierResponses as $response) {
                echo "  - {$response->supplier->name} ({$response->supplier->email}): {$response->status}\n";
                if ($response->viewed_at) {
                    echo "    Viewed: {$response->viewed_at->format('Y-m-d H:i')}\n";
                }
                if ($response->email_sent_successfully === false) {
                    echo "    Email failed: {$response->email_error}\n";
                }
                if ($response->notes) {
                    echo "    Notes: {$response->notes}\n";
                }
            }
        }
        echo str_repeat("-", 80) . "\n";
    }
}

// Check suppliers
echo "\n=== Supplier Check ===\n";
$suppliers = User::whereHas('role', function($q) {
    $q->where('name', 'supplier');
})->with('activeSupplierCategories')->get();

echo "Total suppliers: {$suppliers->count()}\n";
foreach ($suppliers as $supplier) {
    echo "Supplier: {$supplier->name} ({$supplier->email})\n";
    echo "Categories: " . $supplier->activeSupplierCategories->pluck('category.name')->implode(', ') . "\n";
    echo "---\n";
}

// Check specific inquiry responses
echo "\n=== Detailed Response Check ===\n";
$latestInquiry = SupplierInquiry::with(['supplierResponses.supplier', 'supplierResponses.quotation'])->latest()->first();
if ($latestInquiry) {
    echo "Latest Inquiry: {$latestInquiry->reference_number}\n";
    echo "Product: " . ($latestInquiry->product ? $latestInquiry->product->name : ($latestInquiry->product_name ?: 'N/A')) . "\n";
    echo "Status: {$latestInquiry->status}\n";
    echo "Broadcast at: " . ($latestInquiry->broadcast_at ? $latestInquiry->broadcast_at->format('Y-m-d H:i:s') : 'Not broadcast') . "\n";
    
    foreach ($latestInquiry->supplierResponses as $response) {
        echo "\nSupplier: {$response->supplier->name} ({$response->supplier->email})\n";
        echo "Response Status: {$response->status}\n";
        echo "Email Sent: " . ($response->email_sent_successfully ? 'Yes' : 'No') . "\n";
        if ($response->email_sent_at) {
            echo "Email Sent At: {$response->email_sent_at->format('Y-m-d H:i:s')}\n";
        }
        if ($response->viewed_at) {
            echo "Viewed At: {$response->viewed_at->format('Y-m-d H:i:s')}\n";
        }
        if ($response->email_error) {
            echo "Email Error: {$response->email_error}\n";
        }
        if ($response->quotation) {
            echo "Has Quotation: Yes (Status: {$response->quotation->status})\n";
        } else {
            echo "Has Quotation: No\n";
        }
    }
} 