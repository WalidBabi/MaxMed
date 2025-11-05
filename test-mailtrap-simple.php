<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Mailtrap Email Test ===\n\n";

// Clear config cache
\Artisan::call('config:clear');

echo "MAIL_MAILER from .env: " . env('MAIL_MAILER') . "\n";
echo "Config mailer: " . config('mail.default') . "\n\n";

// Force use SMTP mailer
echo "Sending test email via Mailtrap...\n";

try {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from MaxMed to verify Mailtrap configuration.', function($message) {
        $message->to('test@example.com')
                ->subject('MaxMed Test Email - ' . now()->format('Y-m-d H:i:s'))
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "✅ Email sent successfully!\n";
    echo "\n📧 Check your Mailtrap inbox:\n";
    echo "   https://mailtrap.io/inboxes\n";
    echo "   Look for email with subject: MaxMed Test Email\n\n";
    
    // Check if it was actually sent or logged
    $mailer = config('mail.default');
    if ($mailer === 'log') {
        echo "⚠️  WARNING: Mailer is set to 'log' - email was logged, not sent!\n";
        echo "   Check: storage/logs/laravel.log\n";
        echo "   Fix: Set MAIL_MAILER=smtp in .env and clear config cache\n";
    } else {
        echo "✅ Mailer is set to '{$mailer}' - email should appear in Mailtrap\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Type: " . get_class($e) . "\n";
}



