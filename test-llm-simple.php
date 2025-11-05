<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing LLM Connection ===\n\n";

$llmClient = app(\App\Services\LeadLLMClient::class);

echo "LLM Enabled: " . ($llmClient->enabled() ? 'YES' : 'NO') . "\n";
echo "Base URL: " . env('LEAD_LLM_BASE_URL') . "\n";
echo "Model: " . env('LEAD_LLM_MODEL') . "\n\n";

if (!$llmClient->enabled()) {
    echo "❌ LLM is not enabled\n";
    exit(1);
}

// Test with simple text
$testText = "Hi, I need a quote for medical equipment. My name is John Smith from Acme Medical LLC. Email john@acme.com, phone +971 50 123 4567.";
echo "1. Testing with simple text:\n";
echo "   Text: {$testText}\n\n";

$result = $llmClient->extract($testText);

echo "2. LLM Result:\n";
if (empty($result)) {
    echo "   ❌ LLM returned empty result\n";
    echo "   This could mean:\n";
    echo "   - LM Studio is not running\n";
    echo "   - Connection timeout\n";
    echo "   - LLM endpoint not accessible\n";
} else {
    echo "   ✅ LLM responded!\n";
    echo "   Result:\n";
    print_r($result);
}

echo "\n=== Test Complete ===\n";



