<?php

/**
 * Test Logging Script
 * 
 * This script tests if logging is working properly after the permissions fix.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Log;

echo "=== Testing Laravel Logging ===\n";

try {
    // Test basic logging
    Log::info('Test log message from test script');
    echo "✓ Basic logging test passed\n";
    
    // Test error logging
    Log::error('Test error message from test script');
    echo "✓ Error logging test passed\n";
    
    // Test debug logging
    Log::debug('Test debug message from test script');
    echo "✓ Debug logging test passed\n";
    
    // Check if log file exists and is writable
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        echo "✓ Log file exists: {$logPath}\n";
        
        if (is_writable($logPath)) {
            echo "✓ Log file is writable\n";
        } else {
            echo "✗ Log file is not writable\n";
        }
        
        $fileSize = filesize($logPath);
        echo "✓ Log file size: {$fileSize} bytes\n";
    } else {
        echo "✗ Log file does not exist\n";
    }
    
    echo "\n=== Logging test completed successfully ===\n";
    
} catch (Exception $e) {
    echo "✗ Logging test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 