<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing LLM with Real Email ===\n\n";

$emlFile = __DIR__ . '/Request for Quotations - Ref _ SU. Ref _SU.PR- 10891 _ CE – I.eml';
$content = file_get_contents($emlFile);

// Extract From
preg_match('/^From:\s*(.+)$/m', $content, $fromMatch);
$from = trim($fromMatch[1] ?? '');

// Extract Subject
preg_match('/^Subject:\s*(.+)$/m', $content, $subjectMatch);
$subject = trim($subjectMatch[1] ?? '');
if (preg_match('/=\?utf-8\?B\?(.+?)\?=/s', $subject, $base64Match)) {
    $subject = base64_decode($base64Match[1]);
}

// Extract base64 body directly (lines 141-259)
$lines = file($emlFile);
$base64Content = '';
$inBase64 = false;
foreach ($lines as $line) {
    if (strpos($line, 'Content-Transfer-Encoding: base64') !== false) {
        $inBase64 = true;
        continue;
    }
    if ($inBase64 && trim($line) !== '' && strpos($line, '--_000_') === false && strpos($line, 'Content-Type:') === false) {
        $base64Content .= trim($line);
    }
    if ($inBase64 && (strpos($line, '--_000_') !== false || strpos($line, 'Content-Type: text/html') !== false)) {
        break;
    }
}

$body = base64_decode($base64Content);
if ($body === false) {
    $body = base64_decode(str_replace(["\n", "\r", " "], "", $base64Content));
}

echo "1. Email Details:\n";
echo "   From: {$from}\n";
echo "   Subject: {$subject}\n";
echo "   Body length: " . strlen($body) . " characters\n";
echo "   Body preview: " . substr($body, 0, 300) . "...\n\n";

// Extract email from From header
preg_match('/<([^>]+@[^>]+)>/', $from, $emailMatch);
$fromEmail = $emailMatch[1] ?? '';

// Combine for extraction
$text = trim($subject . "\n\n" . $body);

// Limit to first 3500 characters for LLM (leave room for prompt in 4096 context)
$textForLLM = substr($text, 0, 3500);
echo "   Text for LLM (first 3500 chars): " . strlen($textForLLM) . " characters\n";

echo "2. Testing Regex Parser...\n";
$parser = app(\App\Services\LeadTextParser::class);
$regexResult = $parser->parse($text);
echo "   Regex: Name=" . ($regexResult['first_name'] ?? 'null') . " " . ($regexResult['last_name'] ?? 'null') . ", Email=" . ($regexResult['email'] ?? 'null') . ", Company=" . ($regexResult['company_name'] ?? 'null') . "\n\n";

echo "3. Testing LLM Enricher...\n";
$enricher = app(\App\Services\LeadTextEnricher::class);
$enrichedResult = $enricher->extract($textForLLM);

echo "   LLM Enriched:\n";
echo "   - Name: " . ($enrichedResult['first_name'] ?? 'null') . " " . ($enrichedResult['last_name'] ?? 'null') . "\n";
echo "   - Email: " . ($enrichedResult['email'] ?? $fromEmail) . "\n";
echo "   - Phone: " . ($enrichedResult['mobile'] ?? 'null') . "\n";
echo "   - Company: " . ($enrichedResult['company_name'] ?? 'null') . "\n";
echo "   - Intent: " . ($enrichedResult['intent'] ?? 'null') . "\n";
echo "   - Urgency: " . ($enrichedResult['urgency'] ?? 'null') . "\n";
echo "   - Products: " . (isset($enrichedResult['products_interested']) && is_array($enrichedResult['products_interested']) ? implode(', ', array_slice($enrichedResult['products_interested'], 0, 5)) : 'null') . "\n\n";

echo "4. Full Results:\n";
echo "=== REGEX ===\n";
print_r($regexResult);
echo "\n=== LLM ENRICHED ===\n";
print_r($enrichedResult);

echo "\n=== Test Complete ===\n";

