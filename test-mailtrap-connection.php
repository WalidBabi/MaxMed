<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Mailtrap Connection ===\n\n";

// Get current configuration
echo "1. Current Mail Configuration:\n";
echo "   MAIL_MAILER: " . env('MAIL_MAILER', 'not set') . "\n";
echo "   MAIL_HOST: " . env('MAIL_HOST', 'not set') . "\n";
echo "   MAIL_PORT: " . env('MAIL_PORT', 'not set') . "\n";
echo "   MAIL_USERNAME: " . (env('MAIL_USERNAME') ? substr(env('MAIL_USERNAME'), 0, 10) . '...' : 'not set') . "\n";
echo "   MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? '***SET***' : 'not set') . "\n";
echo "   MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION', 'not set') . "\n";
echo "   MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'not set') . "\n";
echo "   MAIL_FROM_NAME: " . env('MAIL_FROM_NAME', 'not set') . "\n\n";

// Check config cache
echo "2. Config Cache Check:\n";
$mailer = config('mail.default');
echo "   Config mailer (mail.default): {$mailer}\n";
if ($mailer === 'log') {
    echo "   ⚠️  WARNING: Mailer is set to 'log' - emails will be logged, not sent!\n";
    echo "   This means emails are saved to storage/logs/laravel.log instead of being sent to Mailtrap.\n\n";
} else {
    echo "   ✅ Mailer is set to '{$mailer}' - emails should be sent via SMTP.\n\n";
}

// Test SMTP connection directly
echo "3. Testing SMTP Connection...\n";
try {
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $username = config('mail.mailers.smtp.username');
    $password = config('mail.mailers.smtp.password');
    $encryption = config('mail.mailers.smtp.encryption');
    
    echo "   Host: {$host}\n";
    echo "   Port: {$port}\n";
    echo "   Encryption: {$encryption}\n";
    echo "   Username: " . ($username ? substr($username, 0, 10) . '...' : 'not set') . "\n";
    
    // Test connection
    $transport = new \Swift_SmtpTransport($host, $port, $encryption);
    $transport->setUsername($username);
    $transport->setPassword($password);
    $transport->setTimeout(10);
    
    $mailer = new \Swift_Mailer($transport);
    $mailer->getTransport()->start();
    
    echo "   ✅ SMTP connection successful!\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ SMTP connection failed: " . $e->getMessage() . "\n";
    echo "   Error details:\n";
    echo "   - " . get_class($e) . "\n";
    echo "   - File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

// Send test email
echo "4. Sending Test Email...\n";
try {
    $testEmail = 'test@example.com';
    $subject = 'MaxMed Test Email - ' . now()->format('Y-m-d H:i:s');
    
    \Illuminate\Support\Facades\Mail::raw('This is a test email from MaxMed to verify Mailtrap is working correctly.', function ($message) use ($testEmail, $subject) {
        $message->to($testEmail)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "   ✅ Email sent successfully!\n";
    echo "   📧 Check Mailtrap inbox: https://mailtrap.io/inboxes\n";
    echo "   📧 Subject: {$subject}\n";
    echo "   📧 To: {$testEmail}\n";
    echo "   📧 From: " . config('mail.from.name') . " <" . config('mail.from.address') . ">\n\n";
    
    // Check if it's actually being sent or logged
    if ($mailer === 'log') {
        echo "   ⚠️  WARNING: Email was logged, not sent to Mailtrap!\n";
        echo "   Check storage/logs/laravel.log for the email content.\n";
        echo "   To fix: Set MAIL_MAILER=smtp in .env and clear config cache.\n\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Error sending email: " . $e->getMessage() . "\n";
    echo "   Error type: " . get_class($e) . "\n";
    if ($e instanceof \Swift_TransportException) {
        echo "\n   SMTP Error Details:\n";
        echo "   - Check your Mailtrap credentials\n";
        echo "   - Verify MAIL_ENCRYPTION=tls is set\n";
        echo "   - Check firewall allows port 2525\n";
    }
    echo "\n";
}

// Check recent logs
echo "5. Checking Recent Logs...\n";
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -50); // Last 50 lines
    $mailLines = array_filter($recentLines, function($line) {
        return stripos($line, 'mail') !== false || stripos($line, 'smtp') !== false || stripos($line, 'email') !== false;
    });
    
    if (count($mailLines) > 0) {
        echo "   Found mail-related log entries:\n";
        foreach (array_slice($mailLines, -5) as $line) { // Show last 5
            echo "   - " . substr($line, 0, 100) . "...\n";
        }
    } else {
        echo "   ✅ No mail errors in recent logs\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "\nTroubleshooting:\n";
echo "1. If mailer is 'log', run: php artisan config:clear\n";
echo "2. Verify .env has MAIL_MAILER=smtp (not log)\n";
echo "3. Check Mailtrap inbox: https://mailtrap.io/inboxes\n";
echo "4. Make sure you're looking at the correct inbox\n";
echo "5. Check Mailtrap spam/junk folder\n";



