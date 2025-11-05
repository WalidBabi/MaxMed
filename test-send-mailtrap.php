<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Mailtrap Email Sending ===\n\n";

// Force clear all caches
\Artisan::call('config:clear');
\Artisan::call('cache:clear');
\Artisan::call('view:clear');

echo "1. Environment Variables:\n";
echo "   MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "   MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "   MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "   MAIL_USERNAME: " . (env('MAIL_USERNAME') ? substr(env('MAIL_USERNAME'), 0, 10) . '...' : 'not set') . "\n";
echo "   MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n\n";

// Force reload config
config(['mail.default' => env('MAIL_MAILER', 'smtp')]);

echo "2. Config Values (after reload):\n";
echo "   Config mailer: " . config('mail.default') . "\n";
echo "   SMTP host: " . config('mail.mailers.smtp.host') . "\n";
echo "   SMTP port: " . config('mail.mailers.smtp.port') . "\n";
echo "   SMTP encryption: " . config('mail.mailers.smtp.encryption') . "\n\n";

// Send test email using Mail facade
echo "3. Sending Test Email...\n";
try {
    $testEmail = 'test@example.com';
    $subject = 'MaxMed Test - ' . now()->format('Y-m-d H:i:s');
    
    // Use Mail::to() which respects the mailer configuration
    \Illuminate\Support\Facades\Mail::to($testEmail)
        ->send(new \Illuminate\Mail\Message(function($message) use ($subject) {
            $message->subject($subject)
                   ->from(config('mail.from.address'), config('mail.from.name'))
                   ->text('This is a test email from MaxMed to verify Mailtrap is working correctly.');
        }));
    
    echo "   ✅ Email sent successfully!\n";
    echo "   📧 Check Mailtrap: https://mailtrap.io/inboxes\n";
    echo "   📧 Subject: {$subject}\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Type: " . get_class($e) . "\n";
    
    // Try alternative method
    echo "\n   Trying alternative send method...\n";
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from MaxMed', function($message) use ($testEmail, $subject) {
            $message->to($testEmail)
                   ->subject($subject)
                   ->from(config('mail.from.address'), config('mail.from.name'));
        });
        echo "   ✅ Email sent using alternative method!\n";
    } catch (\Exception $e2) {
        echo "   ❌ Alternative method also failed: " . $e2->getMessage() . "\n";
    }
}

echo "\n=== Test Complete ===\n";



