<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\EmailLog;
use App\Services\EmailTrackingService;
use App\Jobs\SendCampaignJob;

class DebugEmailContent extends Command
{
    protected $signature = 'campaign:debug-email {campaign-id}';
    protected $description = 'Debug email content to see if tracking is applied';

    public function handle()
    {
        $campaignId = $this->argument('campaign-id');
        $campaign = Campaign::with(['contacts', 'emailLogs'])->find($campaignId);
        
        if (!$campaign) {
            $this->error("Campaign not found with ID: {$campaignId}");
            return 1;
        }
        
        $contact = $campaign->contacts()->first();
        if (!$contact) {
            $this->error("No contacts found for this campaign");
            return 1;
        }
        
        $this->info("=== Debugging Email Content for Campaign: {$campaign->name} ===");
        $this->info("Contact: {$contact->email}");
        $this->line('');
        
        // Create a test email log
        $emailLog = EmailLog::create([
            'campaign_id' => $campaign->id,
            'marketing_contact_id' => $contact->id,
            'email' => $contact->email,
            'subject' => $campaign->subject ?: 'Test Subject',
            'type' => 'campaign',
            'status' => 'pending'
        ]);
        
        $this->info("Created test email log ID: {$emailLog->id}");
        
        // Generate email content with tracking (simulate what SendCampaignJob does)
        $emailContent = $this->buildEmailContentWithTracking($campaign, $contact, $emailLog);
        
        $this->info("=== Original HTML Content ===");
        $this->line($campaign->html_content ?: 'No HTML content');
        $this->line('');
        
        $this->info("=== Processed HTML Content (with tracking) ===");
        $this->line($emailContent['html'] ?: 'No HTML content');
        $this->line('');
        
        // Check for tracking elements
        $htmlContent = $emailContent['html'] ?? '';
        $hasTrackingPixel = strpos($htmlContent, '/email/track/open/') !== false;
        $hasTrackableLinks = strpos($htmlContent, '/email/track/click/') !== false;
        
        $this->info("=== Tracking Analysis ===");
        $this->info("Has tracking pixel: " . ($hasTrackingPixel ? 'YES ✓' : 'NO ✗'));
        $this->info("Has trackable links: " . ($hasTrackableLinks ? 'YES ✓' : 'NO ✗'));
        
        if ($hasTrackingPixel) {
            $this->info("✓ Tracking pixel found - opens should be tracked");
        } else {
            $this->warn("✗ No tracking pixel found - opens will not be tracked");
        }
        
        if ($hasTrackableLinks) {
            $this->info("✓ Trackable links found - clicks should be tracked");
        } else {
            $this->warn("✗ No trackable links found - clicks will not be tracked");
        }
        
        // Generate tracking URLs for testing
        $trackingService = new EmailTrackingService();
        $openUrl = $trackingService->generateTrackingPixelUrl($campaign, $contact, $emailLog);
        $clickUrl = $trackingService->generateClickTrackingUrl($campaign, $contact, $emailLog, 'https://example.com');
        
        $this->line('');
        $this->info("=== Test URLs ===");
        $this->info("Open tracking: {$openUrl}");
        $this->info("Click tracking: {$clickUrl}");
        
        // Clean up test email log
        $emailLog->delete();
        $this->info("Cleaned up test email log");
        
        return 0;
    }
    
    private function buildEmailContentWithTracking(Campaign $campaign, MarketingContact $contact, EmailLog $emailLog): array
    {
        // Prepare personalization data
        $data = [
            'contact' => $contact,
            'campaign' => $campaign,
            'company_name' => config('app.name'),
            'current_date' => now()->format('Y-m-d'),
            'current_year' => now()->year,
            'unsubscribe_url' => route('marketing.unsubscribe', ['token' => base64_encode($contact->id . '|' . $contact->email . '|' . time())]),
        ];

        // Render email content
        $htmlContent = '';
        $textContent = '';
        
        if ($campaign->emailTemplate) {
            $htmlContent = $campaign->emailTemplate->renderHtmlContent($data);
            $textContent = $campaign->emailTemplate->renderTextContent($data);
        } elseif ($campaign->html_content) {
            // Simple template rendering for campaign HTML content
            $htmlContent = $campaign->html_content;
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                }
            }
            $textContent = $campaign->text_content ?: strip_tags($htmlContent);
        }

        // Add tracking to HTML content
        if ($htmlContent) {
            $trackingService = new EmailTrackingService();
            $htmlContent = $trackingService->processHtmlContentForTracking(
                $htmlContent, 
                $campaign, 
                $contact, 
                $emailLog
            );
        }

        return [
            'html' => $htmlContent,
            'text' => $textContent,
        ];
    }
} 