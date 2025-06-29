<?php

namespace App\Console\Commands;

use App\Services\CampaignMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCampaignMailer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-campaign {email? : Email address to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the campaign mailer configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Campaign Mailer Configuration...');
        $this->newLine();

        $campaignMailService = new CampaignMailService();

        // Test configuration
        $this->info('1. Checking configuration...');
        $config = $campaignMailService->getCampaignMailerConfig();
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Host', $config['host'] ?? 'Not set'],
                ['Port', $config['port'] ?? 'Not set'],
                ['Username', $config['username'] ? 'Set' : 'Not set'],
                ['Encryption', $config['encryption'] ?? 'Not set'],
                ['From Address', $config['from_address'] ?? 'Not set'],
                ['From Name', $config['from_name'] ?? 'Not set'],
            ]
        );

        if (empty($config['username']) || empty($config['host'])) {
            $this->error('❌ Campaign mailer is not properly configured!');
            $this->warn('Please set the following environment variables:');
            $this->warn('- MAIL_CAMPAIGN_HOST');
            $this->warn('- MAIL_CAMPAIGN_USERNAME');
            $this->warn('- MAIL_CAMPAIGN_PASSWORD');
            return 1;
        }

        $this->info('✅ Configuration looks good!');
        $this->newLine();

        // Test mailer
        $this->info('2. Testing mailer connection...');
        if ($campaignMailService->testCampaignMailer()) {
            $this->info('✅ Campaign mailer test passed!');
        } else {
            $this->error('❌ Campaign mailer test failed!');
            $this->warn('Check your Mailtrap bulk stream credentials.');
            return 1;
        }

        $this->newLine();

        // Test sending if email provided
        $testEmail = $this->argument('email');
        if ($testEmail) {
            $this->info("3. Testing email sending to: {$testEmail}");
            
            if ($this->confirm('Do you want to send a test email?')) {
                try {
                    // Create a test campaign and contact
                    $campaign = new \App\Models\Campaign([
                        'name' => 'Test Campaign',
                        'subject' => 'Test Campaign Email',
                        'html_content' => '<h1>Test Campaign</h1><p>This is a test email from the campaign mailer.</p>',
                        'text_content' => 'Test Campaign - This is a test email from the campaign mailer.',
                    ]);

                    $contact = new \App\Models\MarketingContact([
                        'email' => $testEmail,
                        'first_name' => 'Test',
                        'last_name' => 'User',
                    ]);

                    $emailContent = [
                        'html' => '<h1>Test Campaign</h1><p>This is a test email from the campaign mailer.</p>',
                        'text' => 'Test Campaign - This is a test email from the campaign mailer.',
                    ];

                    $sent = $campaignMailService->sendCampaignEmail(
                        $campaign,
                        $contact,
                        'Test Campaign Email',
                        $emailContent
                    );

                    if ($sent) {
                        $this->info('✅ Test email sent successfully!');
                        $this->info('Check your Mailtrap inbox for the test email.');
                    } else {
                        $this->error('❌ Failed to send test email!');
                        return 1;
                    }

                } catch (\Exception $e) {
                    $this->error('❌ Error sending test email: ' . $e->getMessage());
                    return 1;
                }
            }
        } else {
            $this->info('3. Skipping email test (no email provided)');
            $this->warn('To test email sending, run: php artisan mail:test-campaign your-email@example.com');
        }

        $this->newLine();
        $this->info('🎉 Campaign mailer setup complete!');
        $this->info('Your campaign system is now configured to use Mailtrap bulk stream.');
        
        return 0;
    }
} 