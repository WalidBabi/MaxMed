<?php

// Simple test script to identify production issues
// Place this in the public directory and access it via browser

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

echo "<h1>Production Connection Test</h1>";

try {
    // Test 1: Basic Laravel boot
    echo "<h2>Test 1: Laravel Boot</h2>";
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Laravel booted successfully<br>";
    
    // Test 2: Database connection
    echo "<h2>Test 2: Database Connection</h2>";
    $db = $app->make('Illuminate\Database\Connection');
    $db->getPdo();
    echo "✅ Database connection successful<br>";
    
    // Test 3: Session configuration
    echo "<h2>Test 3: Session Configuration</h2>";
    $session = $app->make('Illuminate\Session\SessionManager');
    echo "✅ Session manager created<br>";
    
    // Test 4: Auth configuration
    echo "<h2>Test 4: Auth Configuration</h2>";
    $auth = $app->make('Illuminate\Auth\AuthManager');
    echo "✅ Auth manager created<br>";
    
    // Test 5: Check if sessions table exists
    echo "<h2>Test 5: Sessions Table</h2>";
    try {
        $result = $db->select("SELECT 1 FROM sessions LIMIT 1");
        echo "✅ Sessions table exists and accessible<br>";
    } catch (Exception $e) {
        echo "❌ Sessions table error: " . $e->getMessage() . "<br>";
    }
    
    // Test 6: Check if users table exists
    echo "<h2>Test 6: Users Table</h2>";
    try {
        $result = $db->select("SELECT 1 FROM users LIMIT 1");
        echo "✅ Users table exists and accessible<br>";
    } catch (Exception $e) {
        echo "❌ Users table error: " . $e->getMessage() . "<br>";
    }
    
    // Test 7: Check if supplier_quotations table exists
    echo "<h2>Test 7: Supplier Quotations Table</h2>";
    try {
        $result = $db->select("SELECT 1 FROM supplier_quotations LIMIT 1");
        echo "✅ Supplier quotations table exists and accessible<br>";
    } catch (Exception $e) {
        echo "❌ Supplier quotations table error: " . $e->getMessage() . "<br>";
    }
    
    // Test 8: Check environment
    echo "<h2>Test 8: Environment</h2>";
    echo "Environment: " . $app->environment() . "<br>";
    echo "Debug mode: " . (config('app.debug') ? 'ON' : 'OFF') . "<br>";
    
    // Test 9: Check session driver
    echo "<h2>Test 9: Session Driver</h2>";
    echo "Session driver: " . config('session.driver') . "<br>";
    echo "Session table: " . config('session.table') . "<br>";
    
    echo "<h2>✅ All tests passed! The issue might be elsewhere.</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error Found:</h2>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<strong>Trace:</strong><br><pre>" . $e->getTraceAsString() . "</pre>";
} 