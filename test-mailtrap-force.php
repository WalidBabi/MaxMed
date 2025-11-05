<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Forcing SMTP Mailer ===\n\n";

// Clear all caches
\Artisan::call('config:clear');
\Artisan::call('cache:clear');

// Force set mailer to smtp
config(['mail.default' => 'smtp']);

echo "1. Configuration:\n";
echo "   env('MAIL_MAILER'): " . env('MAIL_MAILER') . "\n";
echo "   config('mail.default'): " . config('mail.default') . "\n";
echo "   SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "   SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
echo "   SMTP Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "   SMTP Username: " . (config('mail.mailers.smtp.username') ? substr(config('mail.mailers.smtp.username'), 0, 10) . '...' : 'not set') . "\n\n";

echo "2. Sending test email via SMTP...\n";

try {
    // Use Mail facade with explicit mailer
    \Illuminate\Support\Facades\Mail::mailer('smtp')->raw('This is a test email from MaxMed to verify Mailtrap is working correctly.', function($message) {
        $message->to('test@example.com')
                ->subject('MaxMed Test Email - ' . now()->format('Y-m-d H:i:s'))
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "   ✅ Email sent successfully via SMTP!\n";
    echo "\n📧 Check your Mailtrap inbox:\n";
    echo "   https://mailtrap.io/inboxes\n";
    echo "   Look for email with timestamp: " . now()->format('Y-m-d H:i:s') . "\n\n";
    
} catch (\Swift_TransportException $e) {
    echo "   ❌ SMTP Connection Error: " . $e->getMessage() . "\n";
    echo "\n   Troubleshooting:\n";
    echo "   - Check Mailtrap credentials are correct\n";
    echo "   - Verify MAIL_ENCRYPTION=tls is set\n";
    echo "   - Check firewall allows port 2525\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Type: " . get_class($e) . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "=== Test Complete ===\n";



