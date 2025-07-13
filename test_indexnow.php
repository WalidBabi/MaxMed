<?php

/**
 * IndexNow Integration Test Script
 * 
 * This script tests the IndexNow integration for MaxMed UAE
 * Run this script to verify everything is working correctly
 */

require_once 'vendor/autoload.php';

use App\Services\IndexNowService;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 IndexNow Integration Test for MaxMed UAE\n";
echo "=============================================\n\n";

// Test 1: Check if key file exists
echo "1. Testing IndexNow Key File...\n";
$keyFile = 'public/cb4a3e27410c45f09a6a107fbecd69ff.txt';
if (file_exists($keyFile)) {
    $content = file_get_contents($keyFile);
    if (trim($content) === 'cb4a3e27410c45f09a6a107fbecd69ff') {
        echo "   ✅ Key file exists and content is correct\n";
    } else {
        echo "   ❌ Key file exists but content is incorrect\n";
        echo "   Expected: cb4a3e27410c45f09a6a107fbecd69ff\n";
        echo "   Found: " . trim($content) . "\n";
    }
} else {
    echo "   ❌ Key file does not exist\n";
}

// Test 2: Check if service class exists
echo "\n2. Testing IndexNow Service...\n";
if (class_exists('App\Services\IndexNowService')) {
    echo "   ✅ IndexNowService class exists\n";
    
    // Test service instantiation
    try {
        $service = new IndexNowService();
        echo "   ✅ IndexNowService can be instantiated\n";
        
        // Test configuration
        $config = $service->getConfig();
        echo "   ✅ Configuration retrieved successfully\n";
        echo "   API Key: " . $config['api_key'] . "\n";
        echo "   Host: " . $config['host'] . "\n";
        
    } catch (Exception $e) {
        echo "   ❌ Error instantiating service: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ IndexNowService class does not exist\n";
}

// Test 3: Check if command exists
echo "\n3. Testing IndexNow Command...\n";
if (class_exists('App\Console\Commands\IndexNowCommand')) {
    echo "   ✅ IndexNowCommand class exists\n";
} else {
    echo "   ❌ IndexNowCommand class does not exist\n";
}

// Test 4: Check if observer exists
echo "\n4. Testing IndexNow Observer...\n";
if (class_exists('App\Observers\IndexNowObserver')) {
    echo "   ✅ IndexNowObserver class exists\n";
} else {
    echo "   ❌ IndexNowObserver class does not exist\n";
}

// Test 5: Check if AppServiceProvider is updated
echo "\n5. Testing AppServiceProvider Integration...\n";
$appServiceProvider = 'app/Providers/AppServiceProvider.php';
if (file_exists($appServiceProvider)) {
    $content = file_get_contents($appServiceProvider);
    if (strpos($content, 'IndexNowObserver') !== false) {
        echo "   ✅ AppServiceProvider includes IndexNowObserver\n";
    } else {
        echo "   ❌ AppServiceProvider does not include IndexNowObserver\n";
    }
} else {
    echo "   ❌ AppServiceProvider file does not exist\n";
}

echo "\n=============================================\n";
echo "🎯 Next Steps:\n";
echo "1. Deploy files to production server\n";
echo "2. Run: php artisan indexnow:submit --validate\n";
echo "3. Run: php artisan indexnow:submit --all\n";
echo "4. Check logs: tail -f storage/logs/laravel.log\n";
echo "5. Verify in Bing Webmaster Tools\n";
echo "=============================================\n"; 