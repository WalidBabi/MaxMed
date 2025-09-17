<?php

require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CrmLead;

$lead = CrmLead::first();

// Create a simple HTML page to test the output
$sanitized = \App\Helpers\HtmlSanitizer::sanitizeRichContent($lead->notes);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Formatting</title>
    <style>
    .formatted-content {
        line-height: 1.6;
        max-width: 100%;
        word-wrap: break-word;
    }
    .formatted-content table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
    }
    .formatted-content th,
    .formatted-content td {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        text-align: left;
        vertical-align: top;
    }
    .formatted-content th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
    }
    </style>
</head>
<body>
    <h1>Test Formatting Output</h1>
    <div class="formatted-content">
        ' . $sanitized . '
    </div>
    
    <hr>
    <h2>Raw HTML Source:</h2>
    <pre>' . htmlspecialchars($sanitized) . '</pre>
</body>
</html>';

file_put_contents('test_formatting.html', $html);

echo "✅ Test HTML file created: test_formatting.html\n";
echo "🌐 Open this file in your browser to see the actual formatting\n";
echo "📝 The file shows both the rendered output and the raw HTML source\n\n";

echo "First 500 characters of sanitized HTML:\n";
echo "=======================================\n";
echo substr($sanitized, 0, 500) . "...\n\n";

echo "🔍 Check if the following are preserved:\n";
echo "- Tables: " . (strpos($sanitized, '<table') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Colors: " . (strpos($sanitized, 'color:') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Backgrounds: " . (strpos($sanitized, 'background-color:') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Font styles: " . (strpos($sanitized, 'font-') !== false ? '✅ YES' : '❌ NO') . "\n";

echo "\n=== Test Complete ===\n";

