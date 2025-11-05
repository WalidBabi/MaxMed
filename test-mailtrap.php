<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Mailtrap Email Configuration ===\n\n";

// Check .env configuration
echo "1. Checking Mail Configuration...\n";
$mailer = config('mail.default');
$host = config('mail.mailers.smtp.host');
$port = config('mail.mailers.smtp.port');
$username = config('mail.mailers.smtp.username');
$password = config('mail.mailers.smtp.password');
$fromAddress = config('mail.from.address');
$fromName = config('mail.from.name');

echo "   Mailer: {$mailer}\n";
echo "   Host: {$host}\n";
echo "   Port: {$port}\n";
echo "   Username: " . ($username ? substr($username, 0, 10) . '...' : 'NOT SET') . "\n";
echo "   Password: " . ($password ? '***SET***' : 'NOT SET') . "\n";
echo "   From: {$fromName} <{$fromAddress}>\n";

if (empty($username) || empty($password)) {
    echo "\n   ❌ ERROR: MAIL_USERNAME or MAIL_PASSWORD not set in .env\n";
    echo "   Please check your .env file (lines 59-68)\n";
    exit(1);
}

// Check encryption settings
$encryption = config('mail.mailers.smtp.encryption');
echo "   Encryption: " . ($encryption ?: 'NOT SET (should be tls for Mailtrap)') . "\n";

if (!$encryption) {
    echo "\n   ⚠️  WARNING: MAIL_ENCRYPTION not set. Mailtrap requires 'tls' or 'ssl'\n";
}

echo "\n2. Testing Mailtrap Connection...\n";

try {
    // Create a test email
    $testEmail = 'test@example.com';
    $subject = 'Test Email from MaxMed - ' . now()->format('Y-m-d H:i:s');
    
    \Illuminate\Support\Facades\Mail::raw('This is a test email from MaxMed to verify Mailtrap configuration.', function ($message) use ($testEmail, $subject, $fromAddress, $fromName) {
        $message->to($testEmail)
                ->subject($subject)
                ->from($fromAddress, $fromName);
    });
    
    echo "   ✅ Email sent successfully!\n";
    echo "   Check your Mailtrap inbox at: https://mailtrap.io/inboxes\n";
    echo "   Look for email with subject: {$subject}\n";
    
} catch (\Swift_TransportException $e) {
    echo "   ❌ SMTP Connection Error: " . $e->getMessage() . "\n";
    echo "\n   Common issues:\n";
    echo "   - Wrong username/password\n";
    echo "   - Wrong port (should be 2525 for Mailtrap)\n";
    echo "   - Missing encryption (should be 'tls')\n";
    echo "   - Firewall blocking port 2525\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n3. Checking Mail Logs...\n";
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    if (strpos($logContent, 'Mail') !== false || strpos($logContent, 'SMTP') !== false) {
        echo "   ⚠️  Found mail-related entries in logs\n";
        echo "   Check storage/logs/laravel.log for details\n";
    } else {
        echo "   ✅ No mail errors in recent logs\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "\nNext steps:\n";
echo "1. Check Mailtrap inbox: https://mailtrap.io/inboxes\n";
echo "2. Verify .env has these settings:\n";
echo "   MAIL_MAILER=smtp\n";
echo "   MAIL_HOST=smtp.mailtrap.io\n";
echo "   MAIL_PORT=2525\n";
echo "   MAIL_USERNAME=your_mailtrap_username\n";
echo "   MAIL_PASSWORD=your_mailtrap_password\n";
echo "   MAIL_ENCRYPTION=tls\n";
echo "   MAIL_FROM_ADDRESS=noreply@maxmed.ae\n";
echo "   MAIL_FROM_NAME=\"MaxMed UAE\"\n";



