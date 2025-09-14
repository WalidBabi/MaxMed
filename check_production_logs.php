<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== PRODUCTION LOG ANALYSIS ===\n\n";

// Check the latest log entries
$logFile = storage_path('logs/laravel.log');

if (!file_exists($logFile)) {
    echo "❌ Log file not found at: {$logFile}\n";
    exit;
}

echo "📄 Log file found: {$logFile}\n";
echo "📅 File size: " . number_format(filesize($logFile)) . " bytes\n";
echo "🕒 Last modified: " . date('Y-m-d H:i:s', filemtime($logFile)) . "\n\n";

// Read the last 50 lines of the log
$lines = file($logFile);
$recentLines = array_slice($lines, -50);

echo "📋 RECENT LOG ENTRIES (Last 50 lines):\n";
echo str_repeat("=", 80) . "\n";

foreach ($recentLines as $line) {
    if (trim($line)) {
        echo $line;
    }
}

echo "\n" . str_repeat("=", 80) . "\n";

// Look for permission-related entries specifically
echo "\n🔍 PERMISSION-RELATED LOG ENTRIES:\n";
echo str_repeat("-", 80) . "\n";

foreach ($recentLines as $line) {
    if (stripos($line, 'permission') !== false || stripos($line, 'access control') !== false) {
        echo $line;
    }
}

echo "\n" . str_repeat("-", 80) . "\n";

// Check user permissions in production
echo "\n👤 USER PERMISSION CHECK:\n";
$user = User::where('email', 'wbabi@localhost.com')->first();

if ($user) {
    echo "✅ User found: {$user->name}\n";
    echo "📧 Email: {$user->email}\n";
    echo "👤 Role: " . ($user->role ? $user->role->name : 'No role') . "\n";
    
    if ($user->role) {
        echo "🔑 Role permissions count: " . $user->role->permissions()->count() . "\n";
        
        // Test specific permissions
        $testPermissions = ['dashboard.view', 'users.view', 'admin.access'];
        foreach ($testPermissions as $permission) {
            $hasPermission = $user->hasPermission($permission);
            $status = $hasPermission ? "✅" : "❌";
            echo "{$status} {$permission}: " . ($hasPermission ? "YES" : "NO") . "\n";
        }
    }
} else {
    echo "❌ User not found!\n";
}

echo "\n=== LOG ANALYSIS COMPLETE ===\n";
