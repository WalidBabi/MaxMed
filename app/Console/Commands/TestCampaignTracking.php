<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\EmailLog;
use App\Services\EmailTrackingService;
use Illuminate\Support\Facades\DB;

class TestCampaignTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:test-tracking 
                           {--create-data : Create test campaign with trackable content}
                           {--campaign-id= : Test specific campaign ID}
                           {--simulate-click= : Simulate click for email log ID}
                           {--simulate-bounce= : Simulate bounce for email log ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and debug campaign click tracking functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('create-data')) {
            return $this->createTestData();
        }
        
        if ($this->option('campaign-id')) {
            return $this->testCampaignTracking($this->option('campaign-id'));
        }
        
        if ($this->option('simulate-click')) {
            return $this->simulateClick($this->option('simulate-click'));
        }
        
        if ($this->option('simulate-bounce')) {
            return $this->simulateBounce($this->option('simulate-bounce'));
        }
        
        $this->info('Available options:');
        $this->info('--create-data: Create test campaign with trackable links');
        $this->info('--campaign-id=X: Test tracking for specific campaign');
        $this->info('--simulate-click=X: Simulate click for email log ID');
        $this->info('--simulate-bounce=X: Simulate bounce for email log ID');
        
        return 0;
    }
    
    private function createTestData()
    {
        $this->info('=== Creating Test Campaign with Trackable Content ===');
        
        // Create or find test contact
        $contact = MarketingContact::firstOrCreate([
            'email' => 'test-tracking@example.com'
        ], [
            'first_name' => 'Test',
            'last_name' => 'Tracking',
            'status' => 'active'
        ]);
        
        $this->info("Test contact: {$contact->email} (ID: {$contact->id})");
        
        // Create campaign with HTML content containing links
        $htmlContent = '
<!DOCTYPE html>
<html>
<head>
    <title>Test Campaign</title>
</head>
<body>
    <h1>Test Email Campaign</h1>
    <p>Hello {{contact.first_name}},</p>
    <p>This is a test email with trackable links:</p>
    <ul>
        <li><a href="https://www.example.com/product1">Product 1</a></li>
        <li><a href="https://www.example.com/product2">Product 2</a></li>
        <li><a href="https://www.maxmed.ae">Visit MaxMed</a></li>
    </ul>
    <p>Best regards,<br>MaxMed Team</p>
    <p><a href="{{unsubscribe_url}}">Unsubscribe</a></p>
</body>
</html>';
        
        $campaign = Campaign::create([
            'name' => 'Test Tracking Campaign - ' . now()->format('Y-m-d H:i:s'),
            'subject' => 'Test Email with Trackable Links',
            'html_content' => $htmlContent,
            'text_content' => 'Test email with links: https://www.example.com/product1, https://www.maxmed.ae',
            'type' => 'one_time',
            'status' => 'sent',
            'created_by' => 1,
            'total_recipients' => 1,
            'sent_count' => 1,
            'delivered_count' => 1
        ]);
        
        $this->info("Test campaign created: {$campaign->name} (ID: {$campaign->id})");
        
        // Attach contact to campaign
        $campaign->contacts()->attach($contact->id, [
            'status' => 'delivered',
            'sent_at' => now(),
            'delivered_at' => now()
        ]);
        
        // Create email log
        $emailLog = EmailLog::create([
            'campaign_id' => $campaign->id,
            'marketing_contact_id' => $contact->id,
            'email' => $contact->email,
            'subject' => $campaign->subject,
            'type' => 'campaign',
            'status' => 'delivered',
            'sent_at' => now(),
            'delivered_at' => now()
        ]);
        
        $this->info("Email log created: ID {$emailLog->id}");
        
        // Generate tracking URLs
        $trackingService = new EmailTrackingService();
        $openUrl = $trackingService->generateTrackingPixelUrl($campaign, $contact, $emailLog);
        $clickUrl1 = $trackingService->generateClickTrackingUrl($campaign, $contact, $emailLog, 'https://www.example.com/product1');
        $clickUrl2 = $trackingService->generateClickTrackingUrl($campaign, $contact, $emailLog, 'https://www.maxmed.ae');
        
        $this->line('');
        $this->info('=== Test URLs Generated ===');
        $this->info("Open tracking: {$openUrl}");
        $this->info("Click URL 1: {$clickUrl1}");
        $this->info("Click URL 2: {$clickUrl2}");
        
        $this->line('');
        $this->info('=== Next Steps ===');
        $this->info("1. Test open tracking: visit {$openUrl}");
        $this->info("2. Test click tracking: visit {$clickUrl1}");
        $this->info("3. Check statistics: php artisan campaign:test-tracking --campaign-id={$campaign->id}");
        
        return 0;
    }
    
    private function testCampaignTracking($campaignId)
    {
        $campaign = Campaign::with(['contacts', 'emailLogs'])->find($campaignId);
        
        if (!$campaign) {
            $this->error("Campaign not found: {$campaignId}");
            return 1;
        }
        
        $this->info("=== Campaign Tracking Test: {$campaign->name} ===");
        
        // Update statistics first
        $campaign->updateStatistics();
        
        // Display statistics
        $this->table(['Metric', 'Count', 'Rate'], [
            ['Recipients', $campaign->total_recipients, '-'],
            ['Sent', $campaign->sent_count, '-'],
            ['Delivered', $campaign->delivered_count, $campaign->delivery_rate . '%'],
            ['Opens', $campaign->opened_count, $campaign->open_rate . '%'],
            ['Clicks', $campaign->clicked_count, $campaign->click_rate . '%'],
            ['Bounced', $campaign->bounced_count, $campaign->bounce_rate . '%'],
            ['Unsubscribed', $campaign->unsubscribed_count, $campaign->unsubscribe_rate . '%'],
        ]);
        
        // Show email logs
        $emailLogs = $campaign->emailLogs()->get();
        if ($emailLogs->count() > 0) {
            $this->line('');
            $this->info('=== Email Logs ===');
            foreach ($emailLogs as $log) {
                $this->info("Log ID {$log->id}: {$log->email}");
                $this->info("  Status: {$log->status}");
                $this->info("  Opened: " . ($log->opened_at ? $log->opened_at->format('Y-m-d H:i:s') : 'No'));
                $this->info("  Clicked: " . ($log->clicked_at ? $log->clicked_at->format('Y-m-d H:i:s') : 'No'));
            }
        }
        
        // Show campaign contacts
        $contacts = $campaign->contacts()->get();
        if ($contacts->count() > 0) {
            $this->line('');
            $this->info('=== Campaign Contacts ===');
            foreach ($contacts as $contact) {
                $pivot = $contact->pivot;
                $this->info("Contact {$contact->id}: {$contact->email}");
                $this->info("  Status: {$pivot->status}");
                $this->info("  Opened: " . ($pivot->opened_at ? $pivot->opened_at->format('Y-m-d H:i:s') : 'No'));
                $this->info("  Clicked: " . ($pivot->clicked_at ? $pivot->clicked_at->format('Y-m-d H:i:s') : 'No'));
                $this->info("  Click Count: {$pivot->click_count}");
            }
        }
        
        return 0;
    }
    
    private function simulateClick($emailLogId)
    {
        $emailLog = EmailLog::find($emailLogId);
        
        if (!$emailLog) {
            $this->error("Email log not found: {$emailLogId}");
            return 1;
        }
        
        $this->info("=== Simulating Click for Email Log {$emailLogId} ===");
        $this->info("Email: {$emailLog->email}");
        $this->info("Campaign: {$emailLog->campaign_id}");
        
        // Simulate click
        $emailLog->markAsClicked('127.0.0.1', 'Test User Agent');
        
        $this->info('Click simulated successfully!');
        
        // Show updated statistics
        $campaign = $emailLog->campaign;
        if ($campaign) {
            $campaign->updateStatistics();
            $this->info("Updated campaign statistics:");
            $this->info("  Clicks: {$campaign->clicked_count}");
            $this->info("  Click Rate: {$campaign->click_rate}%");
        }
        
        return 0;
    }
    
    private function simulateBounce($emailLogId)
    {
        $emailLog = EmailLog::find($emailLogId);
        
        if (!$emailLog) {
            $this->error("Email log not found: {$emailLogId}");
            return 1;
        }
        
        $this->info("=== Simulating Bounce for Email Log {$emailLogId} ===");
        $this->info("Email: {$emailLog->email}");
        $this->info("Campaign: {$emailLog->campaign_id}");
        
        // Simulate bounce
        $emailLog->markAsBounced('Test bounce - Mailbox not found');
        
        $this->info('Bounce simulated successfully!');
        
        // Show updated statistics
        $campaign = $emailLog->campaign;
        if ($campaign) {
            $campaign->updateStatistics();
            $this->info("Updated campaign statistics:");
            $this->info("  Bounced: {$campaign->bounced_count}");
            $this->info("  Bounce Rate: {$campaign->bounce_rate}%");
        }
        
        return 0;
    }
}
