<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SupplierQuotation;

echo "Testing SupplierQuotation attachments processing...\n\n";

// Get a quotation with attachments
$quotation = SupplierQuotation::whereNotNull('attachments')->first();

if (!$quotation) {
    echo "No quotations with attachments found.\n";
    exit;
}

echo "Quotation ID: " . $quotation->id . "\n";
echo "Raw attachments from DB: " . $quotation->getRawOriginal('attachments') . "\n\n";

// Test the accessor
$attachments = $quotation->attachments;
echo "Processed attachments count: " . count($attachments) . "\n";
echo "Processed attachments: " . json_encode($attachments, JSON_PRETTY_PRINT) . "\n\n";

// Test file paths
foreach ($attachments as $index => $attachment) {
    echo "Attachment " . ($index + 1) . ":\n";
    echo "  Name: " . ($attachment['name'] ?? 'N/A') . "\n";
    echo "  Path: " . ($attachment['path'] ?? 'N/A') . "\n";
    echo "  File exists: " . (file_exists(public_path('storage/' . $attachment['path'])) ? 'YES' : 'NO') . "\n\n";
}

echo "Test completed.\n"; 