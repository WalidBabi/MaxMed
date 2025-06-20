<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\EmailLog;
use App\Jobs\SendCampaignJob;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignEmail;

class TestBounceHandling extends Command
{
    protected $signature = 'campaign:test-bounce';
    
    protected $description = 'Test bounce handling with invalid email addresses';

    public function handle()
    {
        $this->info('=== Testing Bounce Handling ===');
        
        // Create a test contact with an invalid email
        $invalidEmail = 'test-bounce-' . time() . '@nonexistent-domain-12345.com';
        
        $contact = MarketingContact::create([
            'email' => $invalidEmail,
            'first_name' => 'Test',
            'last_name' => 'Bounce',
            'status' => 'active'
        ]);
        
        $this->info("Created test contact: {$contact->email} (ID: {$contact->id})");
        
        // Create a simple test campaign
        $campaign = Campaign::create([
            'name' => 'Bounce Test Campaign - ' . now()->format('Y-m-d H:i:s'),
            'subject' => 'Test Email for Bounce Testing',
            'text_content' => 'This is a test email to check bounce handling.',
            'html_content' => '<p>This is a test email to check bounce handling.</p>',
            'type' => 'one_time',
            'status' => 'draft',
            'created_by' => 1,
            'total_recipients' => 1
        ]);
        
        $this->info("Created test campaign: {$campaign->name} (ID: {$campaign->id})");
        
        // Attach contact to campaign
        $campaign->contacts()->attach($contact->id, [
            'status' => 'pending'
        ]);
        
        // Create email log
        $emailLog = EmailLog::create([
            'campaign_id' => $campaign->id,
            'marketing_contact_id' => $contact->id,
            'email' => $contact->email,
            'subject' => $campaign->subject,
            'type' => 'campaign',
            'status' => 'pending'
        ]);
        
        $this->info("Created email log: ID {$emailLog->id}");
        
        // Try to send the email and see what happens
        $this->info("Attempting to send email to invalid address...");
        
        try {
            // Prepare email content
            $emailContent = [
                'html' => $campaign->html_content,
                'text' => $campaign->text_content
            ];
            
            // Try to send email
            Mail::to($contact->email)->send(new CampaignEmail(
                $campaign,
                $contact,
                $campaign->subject,
                $emailContent
            ));
            
            $this->info("✅ Email sent successfully (no immediate error)");
            $emailLog->markAsSent();
            
            // In development, we might not get immediate bounce feedback
            $this->warn("⚠️  In development (XAMPP), bounces may not be detected automatically");
            $this->info("The email might appear as 'sent' but could bounce later");
            
        } catch (\Exception $e) {
            $this->error("❌ Email send failed: " . $e->getMessage());
            $emailLog->markAsFailed($e->getMessage());
            
            // Check if this is a bounce-like error
            $errorMessage = strtolower($e->getMessage());
            if (strpos($errorMessage, 'bounce') !== false || 
                strpos($errorMessage, 'invalid') !== false ||
                strpos($errorMessage, 'not found') !== false ||
                strpos($errorMessage, 'does not exist') !== false) {
                
                $this->info("This looks like a bounce error, marking as bounced...");
                $emailLog->markAsBounced($e->getMessage());
            }
        }
        
        // Update campaign statistics
        $campaign->updateStatistics();
        
        // Show results
        $this->line('');
        $this->info('=== Results ===');
        $this->info("Email Log Status: {$emailLog->fresh()->status}");
        $this->info("Campaign Statistics:");
        $this->info("  - Recipients: {$campaign->fresh()->total_recipients}");
        $this->info("  - Sent: {$campaign->fresh()->sent_count}");
        $this->info("  - Delivered: {$campaign->fresh()->delivered_count}");
        $this->info("  - Bounced: {$campaign->fresh()->bounced_count}");
        $this->info("  - Failed: " . ($campaign->fresh()->total_recipients - $campaign->fresh()->sent_count - $campaign->fresh()->bounced_count));
        
        if ($emailLog->fresh()->isBounced()) {
            $this->info("✅ Email was properly marked as bounced!");
            $this->info("Bounce reason: " . $emailLog->fresh()->bounce_reason);
        } elseif ($emailLog->fresh()->isFailed()) {
            $this->warn("⚠️  Email was marked as failed (not bounced)");
            $this->info("Error: " . $emailLog->fresh()->error_message);
        } else {
            $this->warn("⚠️  Email appears to have been sent successfully");
            $this->info("This is normal in development - real bounces happen later");
        }
        
        $this->line('');
        $this->info('=== Next Steps ===');
        $this->info('1. In production, set up email provider webhooks for real bounce detection');
        $this->info('2. You can manually test bounce tracking with:');
        $this->info("   php artisan campaign:test-tracking --simulate-bounce={$emailLog->id}");
        
        return 0;
    }
} 