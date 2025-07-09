<?php

/**
 * Emergency Login Fix
 * This script temporarily modifies the authentication to bypass potential issues
 */

echo "=== Emergency Login Fix ===\n";

// Step 1: Temporarily change session driver to file
echo "Step 1: Changing session driver to file...\n";
$sessionConfig = file_get_contents('config/session.php');
$sessionConfig = str_replace("'driver' => env('SESSION_DRIVER', 'database')", "'driver' => env('SESSION_DRIVER', 'file')", $sessionConfig);
file_put_contents('config/session.php', $sessionConfig);

// Step 2: Clear all caches
echo "Step 2: Clearing all caches...\n";
system('php artisan config:clear');
system('php artisan cache:clear');
system('php artisan view:clear');
system('php artisan route:clear');
system('php artisan optimize:clear');

// Step 3: Create session directory if it doesn't exist
echo "Step 3: Creating session directory...\n";
$sessionDir = 'storage/framework/sessions';
if (!is_dir($sessionDir)) {
    mkdir($sessionDir, 0755, true);
    echo "Created session directory\n";
}

// Step 4: Set proper permissions
echo "Step 4: Setting permissions...\n";
system('chmod -R 755 storage/framework/sessions');
system('chmod -R 755 storage/logs');
system('chmod -R 755 storage/framework/cache');

// Step 5: Run migrations
echo "Step 5: Running migrations...\n";
system('php artisan migrate --force');

// Step 6: Optimize for production
echo "Step 6: Optimizing for production...\n";
system('php artisan config:cache');
system('php artisan route:cache');
system('php artisan view:cache');

echo "=== Emergency Fix Complete ===\n";
echo "Session driver changed to 'file' to avoid database session issues.\n";
echo "Please test the login functionality now.\n";
echo "To revert: Change SESSION_DRIVER back to 'database' in .env file.\n"; 