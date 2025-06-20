<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Services\EmailTrackingService;

class DebugCampaignTracking extends Command
{
    protected $signature = 'campaign:debug-tracking {campaign-id}';
    protected $description = 'Debug campaign tracking for a specific campaign';

    public function handle()
    {
        $campaignId = $this->argument('campaign-id');
        $campaign = Campaign::with(['contacts', 'emailLogs'])->find($campaignId);
        
        if (!$campaign) {
            $this->error("Campaign not found with ID: {$campaignId}");
            return 1;
        }
        
        $this->info("=== Campaign Debug Info ===");
        $this->info("Campaign: {$campaign->name} (ID: {$campaign->id})");
        $this->info("Status: {$campaign->status}");
        $this->line('');
        
        $this->info("=== Statistics ===");
        $this->table(['Metric', 'Value'], [
            ['Recipients', $campaign->total_recipients],
            ['Sent', $campaign->sent_count],
            ['Delivered', $campaign->delivered_count],
            ['Opens', $campaign->opened_count],
            ['Clicks', $campaign->clicked_count],
            ['Bounced', $campaign->bounced_count],
            ['Unsubscribed', $campaign->unsubscribed_count],
        ]);
        
        $this->info("=== Email Logs ===");
        $emailLogs = $campaign->emailLogs()->latest()->take(5)->get();
        
        if ($emailLogs->count() > 0) {
            $logData = [];
            foreach ($emailLogs as $log) {
                $logData[] = [
                    $log->id,
                    $log->email,
                    $log->status,
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '-',
                    $log->delivered_at ? $log->delivered_at->format('Y-m-d H:i:s') : '-',
                    $log->opened_at ? $log->opened_at->format('Y-m-d H:i:s') : '-',
                    $log->clicked_at ? $log->clicked_at->format('Y-m-d H:i:s') : '-',
                ];
            }
            
            $this->table([
                'ID', 'Email', 'Status', 'Sent At', 'Delivered At', 'Opened At', 'Clicked At'
            ], $logData);
        } else {
            $this->warn('No email logs found for this campaign');
        }
        
        $this->info("=== Campaign Contacts ===");
        $contacts = $campaign->contacts()->take(5)->get();
        
        if ($contacts->count() > 0) {
            $contactData = [];
            foreach ($contacts as $contact) {
                $pivot = $contact->pivot;
                $contactData[] = [
                    $contact->id,
                    $contact->email,
                    $pivot->status,
                    $pivot->sent_at ? $pivot->sent_at->format('Y-m-d H:i:s') : '-',
                    $pivot->delivered_at ? $pivot->delivered_at->format('Y-m-d H:i:s') : '-',
                    $pivot->opened_at ? $pivot->opened_at->format('Y-m-d H:i:s') : '-',
                    $pivot->clicked_at ? $pivot->clicked_at->format('Y-m-d H:i:s') : '-',
                ];
            }
            
            $this->table([
                'ID', 'Email', 'Status', 'Sent At', 'Delivered At', 'Opened At', 'Clicked At'
            ], $contactData);
        } else {
            $this->warn('No contacts found for this campaign');
        }
        
        // Generate sample tracking URLs for testing
        $this->info("=== Sample Tracking URLs ===");
        if ($contacts->count() > 0 && $emailLogs->count() > 0) {
            $contact = $contacts->first();
            $emailLog = $emailLogs->first();
            
            $trackingService = new EmailTrackingService();
            
            $openTrackingUrl = $trackingService->generateTrackingPixelUrl($campaign, $contact, $emailLog);
            $clickTrackingUrl = $trackingService->generateClickTrackingUrl($campaign, $contact, $emailLog, 'https://example.com');
            $unsubscribeUrl = $trackingService->generateUnsubscribeUrl($contact, $campaign);
            
            $this->info("Open Tracking URL: {$openTrackingUrl}");
            $this->info("Click Tracking URL: {$clickTrackingUrl}");
            $this->info("Unsubscribe URL: {$unsubscribeUrl}");
            
            $this->line('');
            $this->info("=== Test Tracking ===");
            $this->info("You can test these URLs manually:");
            $this->info("1. Visit the open tracking URL to simulate an email open");
            $this->info("2. Visit the click tracking URL to simulate a click");
            $this->info("3. Run this command again to see updated statistics");
        }
        
        // Check if HTML content has tracking
        $this->info("=== HTML Content Analysis ===");
        if ($campaign->html_content) {
            $hasTrackingPixel = strpos($campaign->html_content, 'email/track/open') !== false;
            $hasTrackableLinks = strpos($campaign->html_content, 'email/track/click') !== false;
            
            $this->info("Has tracking pixel: " . ($hasTrackingPixel ? 'YES' : 'NO'));
            $this->info("Has trackable links: " . ($hasTrackableLinks ? 'YES' : 'NO'));
            
            if (!$hasTrackingPixel && !$hasTrackableLinks) {
                $this->warn('HTML content does not contain tracking elements. This might be why tracking is not working.');
                $this->info('The tracking should be added automatically when the email is sent.');
            }
        } else {
            $this->warn('Campaign has no HTML content');
        }
        
        return 0;
    }
} 