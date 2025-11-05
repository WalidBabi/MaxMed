<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing LLM Extraction with Real Email ===\n\n";

// Read the .eml file
$emlFile = __DIR__ . '/Request for Quotations - Ref _ SU. Ref _SU.PR- 10891 _ CE – I.eml';
if (!file_exists($emlFile)) {
    die("❌ Email file not found: {$emlFile}\n");
}

echo "1. Parsing .eml file...\n";
$content = file_get_contents($emlFile);

// Extract From header
preg_match('/^From:\s*(.+)$/m', $content, $fromMatch);
$from = $fromMatch[1] ?? '';

// Extract Subject header (handle base64 encoded)
preg_match('/^Subject:\s*(.+)$/m', $content, $subjectMatch);
$subject = $subjectMatch[1] ?? '';
if (preg_match('/=\?utf-8\?B\?(.+)\?=/', $subject, $base64Match)) {
    $subject = base64_decode($base64Match[1]);
}

// Extract body text (look for Content-Type: text/plain)
$body = '';
if (preg_match('/Content-Type: text\/plain[^]*?Content-Transfer-Encoding: base64[^]*?\n\n([A-Za-z0-9+\/=\s]+)/s', $content, $bodyMatch)) {
    $body = base64_decode($bodyMatch[1]);
} else {
    // Fallback: try to find text after headers
    $parts = explode("\n\n", $content, 2);
    if (isset($parts[1])) {
        $body = $parts[1];
        // Clean up - remove base64 encoded parts
        $body = preg_replace('/[A-Za-z0-9+\/=\s]{100,}/', '', $body);
    }
}

echo "   From: {$from}\n";
echo "   Subject: {$subject}\n";
echo "   Body length: " . strlen($body) . " characters\n\n";

// Combine subject and body for extraction
$text = trim($subject . "\n\n" . $body);

// Limit text length for testing (first 2000 chars)
$text = substr($text, 0, 2000);

echo "2. Testing Regex Parser (without LLM)...\n";
$parser = app(\App\Services\LeadTextParser::class);
$regexResult = $parser->parse($text);
echo "   Regex Result:\n";
echo "   - Name: " . ($regexResult['first_name'] ?? 'null') . " " . ($regexResult['last_name'] ?? 'null') . "\n";
echo "   - Email: " . ($regexResult['email'] ?? 'null') . "\n";
echo "   - Phone: " . ($regexResult['mobile'] ?? 'null') . "\n";
echo "   - Company: " . ($regexResult['company_name'] ?? 'null') . "\n\n";

echo "3. Testing LLM Enricher (with LLM)...\n";
$enricher = app(\App\Services\LeadTextEnricher::class);
$enrichedResult = $enricher->extract($text);
echo "   Enriched Result:\n";
echo "   - Name: " . ($enrichedResult['first_name'] ?? 'null') . " " . ($enrichedResult['last_name'] ?? 'null') . "\n";
echo "   - Email: " . ($enrichedResult['email'] ?? 'null') . "\n";
echo "   - Phone: " . ($enrichedResult['mobile'] ?? 'null') . "\n";
echo "   - Company: " . ($enrichedResult['company_name'] ?? 'null') . "\n";
echo "   - Intent: " . ($enrichedResult['intent'] ?? 'null') . "\n";
echo "   - Urgency: " . ($enrichedResult['urgency'] ?? 'null') . "\n";
echo "   - Products Interested: " . (isset($enrichedResult['products_interested']) ? implode(', ', $enrichedResult['products_interested']) : 'null') . "\n\n";

echo "4. Full Extraction Results:\n";
echo "=== REGEX ONLY ===\n";
print_r($regexResult);
echo "\n=== LLM ENRICHED ===\n";
print_r($enrichedResult);

echo "\n=== Test Complete ===\n";
echo "📧 Email From: {$from}\n";
echo "📧 Email Subject: {$subject}\n";



