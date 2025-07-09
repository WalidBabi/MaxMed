<?php

/**
 * Comprehensive Production Fix Script
 * Run this script to fix multiple potential production issues
 */

echo "=== Comprehensive Production Fix ===\n";

// Step 1: Clear all caches
echo "Step 1: Clearing all caches...\n";
system('php artisan config:clear');
system('php artisan cache:clear');
system('php artisan view:clear');
system('php artisan route:clear');
system('php artisan optimize:clear');

// Step 2: Check and fix session directory permissions
echo "Step 2: Checking session directory permissions...\n";
$sessionPath = storage_path('framework/sessions');
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0755, true);
    echo "Created sessions directory\n";
}
system('chmod -R 755 ' . storage_path('framework/sessions'));

// Step 3: Check and fix logs directory permissions
echo "Step 3: Checking logs directory permissions...\n";
$logsPath = storage_path('logs');
if (!is_dir($logsPath)) {
    mkdir($logsPath, 0755, true);
    echo "Created logs directory\n";
}
system('chmod -R 755 ' . storage_path('logs'));

// Step 4: Check and fix cache directory permissions
echo "Step 4: Checking cache directory permissions...\n";
$cachePath = storage_path('framework/cache');
if (!is_dir($cachePath)) {
    mkdir($cachePath, 0755, true);
    echo "Created cache directory\n";
}
system('chmod -R 755 ' . storage_path('framework/cache'));

// Step 5: Run migrations
echo "Step 5: Running migrations...\n";
system('php artisan migrate --force');

// Step 6: Check database connection
echo "Step 6: Checking database connection...\n";
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    $db = $app->make('Illuminate\Database\Connection');
    $db->getPdo();
    echo "Database connection successful\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

// Step 7: Check if sessions table exists and create if needed
echo "Step 7: Checking sessions table...\n";
try {
    $result = $db->select("SELECT 1 FROM sessions LIMIT 1");
    echo "Sessions table exists\n";
} catch (Exception $e) {
    echo "Sessions table missing, creating...\n";
    system('php artisan session:table');
    system('php artisan migrate --force');
}

// Step 8: Optimize for production
echo "Step 8: Optimizing for production...\n";
system('php artisan config:cache');
system('php artisan route:cache');
system('php artisan view:cache');

// Step 9: Check environment
echo "Step 9: Checking environment...\n";
echo "Environment: " . $app->environment() . "\n";
echo "Debug mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
echo "Session driver: " . config('session.driver') . "\n";

echo "=== Fix Complete ===\n";
echo "Please test the login functionality now.\n"; 