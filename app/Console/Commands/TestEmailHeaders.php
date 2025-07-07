<?php

namespace App\Console\Commands;

use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\MarketingContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailHeaders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-headers {email? : Email address to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email headers to ensure they are set for important business communications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Email Headers for Important Business Communications...');
        $this->newLine();

        $testEmail = $this->argument('email');
        if (!$testEmail) {
            $testEmail = $this->ask('Please enter an email address to test with:');
        }

        if (!$testEmail) {
            $this->error('No email address provided. Exiting.');
            return 1;
        }

        $this->info("Testing email headers with: {$testEmail}");
        $this->newLine();

        try {
            // Create a test campaign
            $campaign = new Campaign([
                'name' => 'Test Important Business Communication',
                'subject' => 'Important Business Communication - Test',
                'html_content' => '<h1>Important Business Communication</h1><p>This is a test of important business communication headers.</p>',
                'text_content' => 'Important Business Communication - This is a test of important business communication headers.',
            ]);

            // Create a test contact
            $contact = new MarketingContact([
                'email' => $testEmail,
                'first_name' => 'Test',
                'last_name' => 'Business',
                'company' => 'Test Company',
            ]);

            $emailContent = [
                'html' => '<h1>Important Business Communication</h1><p>This is a test of important business communication headers.</p>',
                'text' => 'Important Business Communication - This is a test of important business communication headers.',
            ];

            // Send test email
            $this->info('Sending test email...');
            
            Mail::mailer('campaign')
                ->to($testEmail)
                ->send(new CampaignEmail(
                    $campaign,
                    $contact,
                    'Important Business Communication - Test',
                    $emailContent
                ));

            $this->info('✅ Test email sent successfully!');
            $this->newLine();
            
            $this->info('📧 Email Headers Applied:');
            $this->line('• X-Priority: 1 (High)');
            $this->line('• X-MSMail-Priority: High');
            $this->line('• Importance: High');
            $this->line('• X-Entity-Type: Business');
            $this->line('• X-Message-Type: Business-Important');
            $this->line('• X-Business-Type: Healthcare-Supplies');
            $this->line('• X-Auto-Category: business-important');
            $this->line('• X-Content-Type: business-notification');
            $this->line('• X-Importance: High');
            $this->line('• X-Business-Communication: true');
            $this->line('• X-Healthcare-Supplies: true');
            $this->newLine();
            
            $this->info('🎯 Expected Results:');
            $this->line('• Email should appear in INBOX instead of Promotions tab');
            $this->line('• Email should be marked as important');
            $this->line('• Email should be categorized as business communication');
            $this->newLine();
            
            $this->info('📋 Next Steps:');
            $this->line('1. Check your email inbox (not promotions tab)');
            $this->line('2. Verify the email appears as important');
            $this->line('3. Check email headers in your email client');
            $this->newLine();
            
            $this->info('💡 Tips for Production:');
            $this->line('• Ensure your sending domain has proper SPF/DKIM records');
            $this->line('• Use a consistent "from" address for business communications');
            $this->line('• Maintain good sender reputation');
            $this->line('• Monitor email deliverability metrics');
            
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            return 1;
        }
    }
} 