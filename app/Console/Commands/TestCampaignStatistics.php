<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\EmailLog;
use App\Jobs\SendCampaignJob;
use Illuminate\Support\Facades\Queue;

class TestCampaignStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:test-stats {--campaign-id=} {--create-test-data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test campaign statistics functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaignId = $this->option('campaign-id');
        $createTestData = $this->option('create-test-data');
        
        if ($createTestData) {
            $this->createTestData();
            return 0;
        }
        
        if (!$campaignId) {
            $this->error('Please provide a campaign ID with --campaign-id option');
            return 1;
        }
        
        $campaign = Campaign::find($campaignId);
        if (!$campaign) {
            $this->error("Campaign not found with ID: {$campaignId}");
            return 1;
        }
        
        $this->info("Testing campaign: {$campaign->name} (ID: {$campaign->id})");
        $this->info('Current statistics:');
        $this->displayCampaignStats($campaign);
        
        // Test sending campaign if it has contacts
        if ($campaign->contacts()->count() > 0) {
            $this->info("\nSending campaign to test statistics...");
            
            // Dispatch the send job
            SendCampaignJob::dispatch($campaign);
            
            $this->info('Campaign send job dispatched. Process the queue with: php artisan queue:work');
            
            // Wait a moment and check stats again
            sleep(2);
            $campaign->refresh();
            
            $this->info("\nUpdated statistics:");
            $this->displayCampaignStats($campaign);
        } else {
            $this->warn('Campaign has no contacts. Add contacts first.');
        }
        
        return 0;
    }
    
    private function createTestData()
    {
        $this->info('Creating test campaign and contacts...');
        
        // Create test contact
        $contact = MarketingContact::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'status' => 'active',
            'source' => 'test'
        ]);
        
        $this->info("Created test contact: {$contact->email}");
        
        // Create test campaign
        $campaign = Campaign::create([
            'name' => 'Test Campaign - ' . now()->format('Y-m-d H:i:s'),
            'subject' => 'Test Campaign Subject',
            'html_content' => '<h1>Test Campaign</h1><p>Hello {{first_name}},</p><p>This is a test campaign email.</p><p><a href="https://example.com">Visit our website</a></p>',
            'text_content' => 'Hello {{first_name}}, This is a test campaign email.',
            'status' => 'draft',
            'created_by' => 1,
            'total_recipients' => 1
        ]);
        
        $this->info("Created test campaign: {$campaign->name} (ID: {$campaign->id})");
        
        // Attach contact to campaign
        $campaign->contacts()->attach($contact->id, [
            'status' => 'pending'
        ]);
        
        $this->info('Attached contact to campaign');
        $this->info("Run: php artisan campaign:test-stats --campaign-id={$campaign->id}");
    }
    
    private function displayCampaignStats(Campaign $campaign)
    {
        $this->table([
            'Metric', 'Count', 'Rate'
        ], [
            ['Recipients', $campaign->total_recipients, '-'],
            ['Sent', $campaign->sent_count, '-'],
            ['Delivered', $campaign->delivered_count, $campaign->delivery_rate . '%'],
            ['Opens', $campaign->opened_count, $campaign->open_rate . '%'],
            ['Clicks', $campaign->clicked_count, $campaign->click_rate . '%'],
            ['Bounced', $campaign->bounced_count, $campaign->bounce_rate . '%'],
            ['Unsubscribed', $campaign->unsubscribed_count, $campaign->unsubscribe_rate . '%'],
        ]);
    }
} 