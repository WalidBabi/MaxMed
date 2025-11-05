<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Sending Test Email to Mailtrap ===\n\n";

try {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from MaxMed to verify Mailtrap is working correctly.', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Email from MaxMed - ' . now()->format('Y-m-d H:i:s'))
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "✅ Email sent successfully!\n";
    echo "📧 Check your Mailtrap inbox at: https://mailtrap.io/inboxes\n";
    echo "   Look for the test email in your sandbox inbox\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error sending email:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    if ($e instanceof \Swift_TransportException) {
        echo "\n   SMTP Connection Error - Check:\n";
        echo "   - MAIL_MAILER=smtp in .env\n";
        echo "   - MAIL_USERNAME and MAIL_PASSWORD are correct\n";
        echo "   - MAIL_ENCRYPTION=tls is set\n";
        echo "   - Firewall allows port 2525\n";
    }
}

echo "Current mail configuration:\n";
echo "  Mailer: " . config('mail.default') . "\n";
echo "  Host: " . config('mail.mailers.smtp.host') . "\n";
echo "  Port: " . config('mail.mailers.smtp.port') . "\n";
echo "  Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "  From: " . config('mail.from.name') . " <" . config('mail.from.address') . ">\n";



