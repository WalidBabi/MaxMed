<?php

/**
 * Fix Log Permissions Script
 * 
 * This script ensures that the storage/logs directory has proper permissions
 * and creates necessary log files with correct permissions.
 */

// Ensure storage/logs directory exists and has proper permissions
$logsDir = __DIR__ . '/storage/logs';

if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    echo "Created storage/logs directory\n";
}

// Set proper permissions for the logs directory
chmod($logsDir, 0755);

// Create empty log files with proper permissions if they don't exist
$logFiles = [
    'laravel.log',
    'custom.log',
    'test.log',
    'production-debug.log',
    'critical-errors.log'
];

foreach ($logFiles as $logFile) {
    $logPath = $logsDir . '/' . $logFile;
    
    if (!file_exists($logPath)) {
        touch($logPath);
        chmod($logPath, 0664);
        echo "Created {$logFile} with proper permissions\n";
    } else {
        chmod($logPath, 0664);
        echo "Updated permissions for {$logFile}\n";
    }
}

echo "Log permissions fixed successfully!\n";
echo "Make sure to run this script as the web server user in production.\n"; 