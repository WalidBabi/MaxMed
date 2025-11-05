<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing LLM Integration ===\n\n";

// Test 1: Check if services can be resolved
echo "1. Testing service container resolution...\n";
try {
    $parser = $app->make(\App\Services\LeadTextParser::class);
    echo "   ✅ LeadTextParser resolved\n";
    
    $llmClient = $app->make(\App\Services\LeadLLMClient::class);
    echo "   ✅ LeadLLMClient resolved\n";
    
    $enricher = $app->make(\App\Services\LeadTextEnricher::class);
    echo "   ✅ LeadTextEnricher resolved\n";
    
    echo "   ✅ All services resolved successfully\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error resolving services: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n\n";
    exit(1);
}

// Test 2: Check LLM configuration
echo "2. Testing LLM configuration...\n";
$llmEnabled = filter_var(env('LEAD_LLM_ENABLED', false), FILTER_VALIDATE_BOOL);
$baseUrl = env('LEAD_LLM_BASE_URL', '');
$model = env('LEAD_LLM_MODEL', 'llama3.2:3b');

echo "   LLM Enabled: " . ($llmEnabled ? 'YES' : 'NO') . "\n";
echo "   Base URL: " . ($baseUrl ?: 'NOT SET') . "\n";
echo "   Model: " . $model . "\n";

if ($llmEnabled && $baseUrl) {
    echo "   ✅ LLM is configured\n\n";
} else {
    echo "   ⚠️  LLM is disabled or not configured (will use regex only)\n\n";
}

// Test 3: Test parser without LLM
echo "3. Testing regex parser (without LLM)...\n";
$sampleText = "Hi, I am Sarah Ali from Medix UAE. Email: sarah@medix-uae.com. Phone: +971 50 111 2222. Thanks!";
$regexResult = $parser->parse($sampleText);
echo "   Result: " . json_encode($regexResult, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Test enricher with LLM (if enabled)
echo "4. Testing enricher (with/without LLM)...\n";
$sampleEmail = "Subject: Need ENT catalog\n\nHi, please send ENT price list. My phone +971 50 111 2222. I work at Medix Medical Supplies LLC. Thanks, Sarah Ali";
$enrichedResult = $enricher->extract($sampleEmail);
echo "   Result: " . json_encode($enrichedResult, JSON_PRETTY_PRINT) . "\n";

if ($llmEnabled && $baseUrl) {
    echo "   ✅ LLM extraction attempted\n";
    
    // Test LLM connection
    echo "\n5. Testing LLM connection...\n";
    try {
        $llmResult = $llmClient->extract($sampleEmail);
        if (!empty($llmResult)) {
            echo "   ✅ LLM responded with data\n";
            echo "   LLM Result: " . json_encode($llmResult, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   ⚠️  LLM returned empty result (might be timeout or connection issue)\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ LLM connection failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️  LLM not enabled, using regex only\n";
}

echo "\n=== Test Complete ===\n";



