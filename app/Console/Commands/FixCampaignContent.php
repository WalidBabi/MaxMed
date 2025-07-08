<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;

class FixCampaignContent extends Command
{
    protected $signature = 'campaign:fix-content {campaign-id} {--yes : Automatically add sample content}';
    protected $description = 'Check and fix campaign content issues';

    public function handle()
    {
        $campaignId = $this->argument('campaign-id');
        $campaign = Campaign::find($campaignId);
        
        if (!$campaign) {
            $this->error("Campaign not found with ID: {$campaignId}");
            return 1;
        }

        $this->info("=== Campaign Details ===");
        $this->info("ID: {$campaign->id}");
        $this->info("Name: {$campaign->name}");
        $this->info("Status: {$campaign->status}");
        $this->info("Subject: " . ($campaign->subject ?: 'NULL'));
        $this->info("Text content length: " . strlen($campaign->text_content ?: ''));
        $this->info("HTML content length: " . strlen($campaign->html_content ?: ''));
        $this->info("Email template ID: " . ($campaign->email_template_id ?: 'NULL'));
        $this->line('');

        // Check if campaign has content
        $hasContent = !empty($campaign->text_content) || 
                     !empty($campaign->html_content) || 
                     $campaign->emailTemplate;

        if (!$hasContent) {
            $this->error("❌ Campaign has no content!");
            $this->line('');
            
            if ($this->option('yes') || $this->confirm('Do you want to add sample content to this campaign?')) {
                $this->addSampleContent($campaign);
                $this->info("✅ Sample content added successfully!");
            } else {
                $this->info("Please add content manually through the admin interface.");
            }
        } else {
            $this->info("✅ Campaign has content and should work properly.");
        }

        return 0;
    }

    private function addSampleContent(Campaign $campaign)
    {
        $sampleTextContent = "Dear {{ first_name }},

I hope this email finds you well. My name is Walid, and I am reaching out on behalf of MaxMed, a trusted provider of advanced scientific solutions tailored to life science and research professionals.

We specialize in:
- Laboratory Equipment
- Medical Consumables  
- Analytical Chemistry Tools
- Research Supplies

I would love to discuss how we can support your research and laboratory needs. Our team is available to provide personalized consultation and technical support.

Best regards,
Walid Babi
MaxMed Team
Business Development";

        $sampleSubject = "MaxMed Business Update - Healthcare Supplies & Laboratory Equipment";

        $campaign->update([
            'subject' => $sampleSubject,
            'text_content' => $sampleTextContent,
            'html_content' => $this->generateHtmlFromText($sampleTextContent),
            'status' => 'draft'
        ]);
    }

    private function generateHtmlFromText(string $textContent): string
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
            
            <p>' . nl2br($textContent) . '</p>
            
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
} 