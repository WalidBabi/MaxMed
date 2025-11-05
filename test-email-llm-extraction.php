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
$from = trim($fromMatch[1] ?? '');

// Extract Subject header (decode base64 if needed)
preg_match('/^Subject:\s*(.+)$/m', $content, $subjectMatch);
$subject = trim($subjectMatch[1] ?? '');
if (preg_match('/=\?utf-8\?B\?(.+?)\?=/s', $subject, $base64Match)) {
    $subject = base64_decode($base64Match[1]);
}

// Extract body - find text/plain section
$body = '';
// Find the text/plain section - look for Content-Type: text/plain followed by base64
$textPlainStart = strpos($content, 'Content-Type: text/plain');
if ($textPlainStart !== false) {
    // Find the base64 section after Content-Transfer-Encoding: base64
    $base64Start = strpos($content, 'Content-Transfer-Encoding: base64', $textPlainStart);
    if ($base64Start !== false) {
        // Find the start of base64 content (after two newlines)
        $contentStart = strpos($content, "\n\n", $base64Start) + 2;
        // Find the end (before next boundary or end of line sequence)
        $contentEnd = strpos($content, "\n--_000_", $contentStart);
        if ($contentEnd === false) {
            $contentEnd = strpos($content, "\n\nContent-Type:", $contentStart);
        }
        if ($contentEnd !== false) {
            $bodyEncoded = substr($content, $contentStart, $contentEnd - $contentStart);
            // Remove all whitespace for base64 decode
            $bodyEncoded = preg_replace('/\s+/', '', $bodyEncoded);
            $body = base64_decode($bodyEncoded, true);
        }
    }
}

// If body is empty, try to extract from HTML
if (empty($body) && preg_match('/Content-Type: text\/html.*?Content-Transfer-Encoding: base64.*?\n\n([A-Za-z0-9+\/=\s\n]+)/s', $content, $htmlMatch)) {
    $htmlEncoded = trim($htmlMatch[1]);
    $html = base64_decode($htmlEncoded);
    $body = strip_tags($html);
    $body = html_entity_decode($body);
}

// Clean up body
$body = preg_replace('/\s+/', ' ', $body);
$body = trim($body);

echo "   From: {$from}\n";
echo "   Subject: {$subject}\n";
echo "   Body length: " . strlen($body) . " characters\n";
echo "   Body preview: " . substr($body, 0, 200) . "...\n\n";

// Combine for extraction
$text = trim($subject . "\n\n" . $body);

// Limit to first 3000 characters for testing
$text = substr($text, 0, 3000);

echo "2. Testing Regex Parser (without LLM)...\n";
$parser = app(\App\Services\LeadTextParser::class);
$regexResult = $parser->parse($text);
echo "   Regex Results:\n";
echo "   - Name: " . ($regexResult['first_name'] ?? 'null') . " " . ($regexResult['last_name'] ?? 'null') . "\n";
echo "   - Email: " . ($regexResult['email'] ?? 'null') . "\n";
echo "   - Phone: " . ($regexResult['mobile'] ?? 'null') . "\n";
echo "   - Company: " . ($regexResult['company_name'] ?? 'null') . "\n\n";

echo "3. Testing LLM Enricher (with LLM)...\n";
$enricher = app(\App\Services\LeadTextEnricher::class);
$enrichedResult = $enricher->extract($text);
echo "   LLM Enriched Results:\n";
echo "   - Name: " . ($enrichedResult['first_name'] ?? 'null') . " " . ($enrichedResult['last_name'] ?? 'null') . "\n";
echo "   - Email: " . ($enrichedResult['email'] ?? 'null') . "\n";
echo "   - Phone: " . ($enrichedResult['mobile'] ?? 'null') . "\n";
echo "   - Company: " . ($enrichedResult['company_name'] ?? 'null') . "\n";
echo "   - Intent: " . ($enrichedResult['intent'] ?? 'null') . "\n";
echo "   - Urgency: " . ($enrichedResult['urgency'] ?? 'null') . "\n";
echo "   - Products Interested: " . (isset($enrichedResult['products_interested']) && is_array($enrichedResult['products_interested']) ? implode(', ', $enrichedResult['products_interested']) : 'null') . "\n\n";

echo "4. Comparison:\n";
echo "=== REGEX ONLY ===\n";
echo json_encode($regexResult, JSON_PRETTY_PRINT);
echo "\n\n=== LLM ENRICHED ===\n";
echo json_encode($enrichedResult, JSON_PRETTY_PRINT);

echo "\n\n=== Email Details ===\n";
echo "From: {$from}\n";
echo "Subject: {$subject}\n";

// Extract email from From header
if (preg_match('/<([^>]+@[^>]+)>/', $from, $emailMatch)) {
    echo "From Email: {$emailMatch[1]}\n";
}

echo "\n=== Test Complete ===\n";

