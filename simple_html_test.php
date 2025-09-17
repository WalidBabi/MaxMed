<?php

// Test HTML output without Laravel
require_once 'app/Helpers/HtmlSanitizer.php';

use App\Helpers\HtmlSanitizer;

// Sample rich content similar to what's in the database
$richContent = '
<div style="font-family: Calibri, sans-serif; font-size: 11pt;">
    <p style="margin: 0; color: #1f497d; font-size: 14pt; font-weight: bold;">
        🏥 Al Dhannah Hospital - Equipment Procurement Request
    </p>
    
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; margin: 10px 0;">
        <thead>
            <tr style="background-color: #4472c4; color: white;">
                <th style="text-align: left; font-weight: bold;">Equipment</th>
                <th style="text-align: center; font-weight: bold;">Quantity</th>
                <th style="text-align: center; font-weight: bold;">Budget (AED)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f2f2f2;">
                <td style="color: #1f4e79; font-weight: bold;">Apo B Testing System</td>
                <td style="text-align: center; color: #70ad47; font-weight: bold;">2 units</td>
                <td style="text-align: center; color: #c55a11; font-weight: bold;">65,000 - 85,000</td>
            </tr>
        </tbody>
    </table>
    
    <ul style="color: #595959; line-height: 1.6;">
        <li><span style="color: #e74c3c; font-weight: bold;">FDA 510(k) Clearance</span> - Mandatory</li>
        <li><span style="color: #f39c12; font-weight: bold;">CE Marking</span> - European compliance</li>
    </ul>
</div>';

// Sanitize the content
$sanitized = HtmlSanitizer::sanitizeRichContent($richContent);

// Create HTML test file
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Notes Formatting Test</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; margin: 20px; line-height: 1.6; }
        .test-container { max-width: 800px; margin: 0 auto; }
        .formatted-content { 
            line-height: 1.6; 
            max-width: 100%; 
            word-wrap: break-word; 
            border: 1px solid #ddd; 
            padding: 20px; 
            margin: 20px 0;
            background: white;
        }
        .formatted-content table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 1rem; 
            border: 1px solid #d1d5db; 
            background-color: #ffffff; 
        }
        .formatted-content th, .formatted-content td { 
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
        .raw-html { background: #f5f5f5; padding: 15px; border: 1px solid #ccc; white-space: pre-wrap; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 CRM Notes Formatting Test</h1>
        
        <h2>📊 Rendered Output (How it should appear in CRM)</h2>
        <div class="formatted-content">
            ' . $sanitized . '
        </div>
        
        <h2>🔍 Raw HTML Source</h2>
        <div class="raw-html">' . htmlspecialchars($sanitized) . '</div>
        
        <h2>✅ Formatting Check</h2>
        <ul>
            <li>Tables: ' . (strpos($sanitized, '<table') !== false ? '✅ Preserved' : '❌ Missing') . '</li>
            <li>Colors: ' . (strpos($sanitized, 'color:') !== false ? '✅ Preserved' : '❌ Missing') . '</li>
            <li>Backgrounds: ' . (strpos($sanitized, 'background-color:') !== false ? '✅ Preserved' : '❌ Missing') . '</li>
            <li>Font Styles: ' . (strpos($sanitized, 'font-') !== false ? '✅ Preserved' : '❌ Missing') . '</li>
            <li>Style Attributes: ' . (substr_count($sanitized, 'style=') . ' found') . '</li>
        </ul>
    </div>
</body>
</html>';

file_put_contents('crm_formatting_test.html', $html);

echo "✅ Test file created: crm_formatting_test.html\n";
echo "🌐 Open this file in your browser to see the formatting\n";
echo "📝 This shows exactly how the content should appear in the CRM\n\n";

echo "Summary:\n";
echo "========\n";
echo "- Tables: " . (strpos($sanitized, '<table') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Colors: " . (strpos($sanitized, 'color:') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Backgrounds: " . (strpos($sanitized, 'background-color:') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Style attributes: " . substr_count($sanitized, 'style=') . " found\n";

echo "\nIf the HTML file shows proper formatting but the CRM doesn't,\n";
echo "the issue is likely browser caching or a CSP (Content Security Policy) issue.\n";
echo "\n💡 Try hard refresh (Ctrl+F5) in your browser when viewing the CRM page.\n";

echo "\n=== Test Complete ===\n";

