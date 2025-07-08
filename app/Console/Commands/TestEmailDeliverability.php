<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Services\CampaignMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestEmailDeliverability extends Command
{
    protected $signature = 'email:test-deliverability {email} {--campaign-id=} {--subject=}';
    
    protected $description = 'Test email deliverability to check if emails go to inbox or promotions tab';

    public function handle()
    {
        $testEmail = $this->argument('email');
        $campaignId = $this->option('campaign-id');
        $customSubject = $this->option('subject');

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        $this->info('Testing email deliverability...');
        $this->info("Target email: {$testEmail}");

        try {
            // Create or find a test contact
            $contact = MarketingContact::where('email', $testEmail)->first();
            if (!$contact) {
                $contact = MarketingContact::create([
                    'email' => $testEmail,
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'company' => 'Test Company',
                    'job_title' => 'Test Position',
                    'industry' => 'Healthcare',
                    'country' => 'UAE',
                    'city' => 'Dubai',
                    'status' => 'active'
                ]);
                $this->info('Created test contact.');
            }

            // Get or create a test campaign
            $campaign = null;
            if ($campaignId) {
                $campaign = Campaign::find($campaignId);
                if (!$campaign) {
                    $this->error("Campaign with ID {$campaignId} not found.");
                    return 1;
                }
            } else {
                $campaign = Campaign::where('name', 'like', '%test%')->first();
                if (!$campaign) {
                    $campaign = Campaign::create([
                        'name' => 'Email Deliverability Test Campaign',
                        'subject' => $customSubject ?: 'MaxMed Business Update - Important Information',
                        'html_content' => $this->getTestHtmlContent(),
                        'text_content' => $this->getTestTextContent(),
                        'type' => 'business_update',
                        'status' => 'draft',
                        'created_by' => 1
                    ]);
                    $this->info('Created test campaign.');
                }
            }

            // Prepare email content
            $emailContent = [
                'html' => $campaign->html_content ?: $this->getTestHtmlContent(),
                'text' => $campaign->text_content ?: $this->getTestTextContent(),
            ];

            $subject = $customSubject ?: ($campaign->subject ?: 'MaxMed Business Update - Important Information');

            // Send test email
            $campaignMailService = new CampaignMailService();
            $sent = $campaignMailService->sendCampaignEmail(
                $campaign,
                $contact,
                $subject,
                $emailContent
            );

            if ($sent) {
                $this->info('✅ Test email sent successfully!');
                $this->info('');
                $this->info('📧 Email Details:');
                $this->info("   Subject: {$subject}");
                $this->info("   From: " . config('mail.campaign_from.name') . ' <' . config('mail.campaign_from.address') . '>');
                $this->info("   To: {$testEmail}");
                $this->info('');
                $this->info('🔍 Deliverability Tips:');
                $this->info('   1. Check your inbox (not promotions tab)');
                $this->info('   2. If in promotions, move to inbox and mark as "Not Spam"');
                $this->info('   3. Add ' . config('mail.campaign_from.address') . ' to your contacts');
                $this->info('   4. Reply to the email to improve future deliverability');
                $this->info('');
                $this->info('📊 The email includes enhanced headers to improve inbox placement.');
                
                Log::info('Email deliverability test completed', [
                    'email' => $testEmail,
                    'campaign_id' => $campaign->id,
                    'subject' => $subject,
                    'sent' => $sent
                ]);
            } else {
                $this->error('❌ Failed to send test email.');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Error during deliverability test: ' . $e->getMessage());
            Log::error('Email deliverability test failed', [
                'email' => $testEmail,
                'error' => $e->getMessage()
            ]);
            return 1;
        }

        return 0;
    }

    private function getTestHtmlContent(): string
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background-color: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <div style="font-weight: bold; color: #007bff; margin-bottom: 5px;">IMPORTANT BUSINESS COMMUNICATION</div>
                <div style="font-size: 14px; color: #6c757d;">
                    This is a business communication from MaxMed regarding healthcare supplies and medical equipment 
                    relevant to your business operations. This is not promotional marketing material.
                </div>
            </div>
            
            <h2 style="color: #333;">MaxMed Healthcare Supplies - Business Update</h2>
            
            <p>Dear Valued Business Partner,</p>
            
            <p>This is a test email to verify our business communication delivery system. We want to ensure that 
            important business updates regarding healthcare supplies and medical equipment reach your inbox.</p>
            
            <p>Our system has been enhanced with improved deliverability features to ensure business communications 
            are properly categorized and delivered to your primary inbox.</p>
            
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #0056b3;">Key Business Information:</h3>
                <ul style="margin-bottom: 0;">
                    <li>Healthcare supplies availability updates</li>
                    <li>Medical equipment specifications</li>
                    <li>Business partnership opportunities</li>
                    <li>Technical support and consultation</li>
                </ul>
            </div>
            
            <p>If you have any questions about our healthcare supplies or medical equipment, please don\'t hesitate 
            to contact our business development team.</p>
            
            <p>Best regards,<br>
            <strong>MaxMed Team</strong><br>
            Business Communication Department<br>
            Healthcare Supplies Division</p>
            
            <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
            
            <div style="font-size: 12px; color: #666; text-align: center;">
                <p><strong>BUSINESS COMMUNICATION NOTICE:</strong><br>
                This is an important business communication from MaxMed regarding healthcare supplies and medical equipment 
                relevant to your business operations. This communication is sent to business contacts and is not promotional marketing material.</p>
                
                <p>MaxMed Healthcare Supplies & Medical Equipment<br>
                For business inquiries: ' . config('mail.campaign_from.address') . '</p>
            </div>
        </div>';
    }

    private function getTestTextContent(): string
    {
        return "IMPORTANT BUSINESS COMMUNICATION
MaxMed Healthcare Supplies - Business Update
This is a business communication regarding healthcare supplies and medical equipment.

Dear Valued Business Partner,

This is a test email to verify our business communication delivery system. We want to ensure that 
important business updates regarding healthcare supplies and medical equipment reach your inbox.

Our system has been enhanced with improved deliverability features to ensure business communications 
are properly categorized and delivered to your primary inbox.

Key Business Information:
- Healthcare supplies availability updates
- Medical equipment specifications
- Business partnership opportunities
- Technical support and consultation

If you have any questions about our healthcare supplies or medical equipment, please don't hesitate 
to contact our business development team.

Best regards,
MaxMed Team
Business Communication Department
Healthcare Supplies Division

---
BUSINESS COMMUNICATION NOTICE:
This is an important business communication from MaxMed regarding healthcare supplies and medical equipment 
relevant to your business operations. This communication is sent to business contacts and is not promotional marketing material.

MaxMed Healthcare Supplies & Medical Equipment
For business inquiries: " . config('mail.campaign_from.address');
    }
} 