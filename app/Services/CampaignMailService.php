<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Mail\CampaignEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CampaignMailService
{
    /**
     * Send a campaign email to a specific contact
     */
    public function sendCampaignEmail(
        Campaign $campaign,
        MarketingContact $contact,
        string $subject,
        array $emailContent
    ): bool {
        try {
            // Enhance subject line for better deliverability
            $enhancedSubject = $this->enhanceSubjectForDeliverability($subject);
            
            // Enhance email content for better deliverability
            $enhancedContent = $this->enhanceContentForDeliverability($emailContent, $contact);
            
            // Send using the campaign mailer
            Mail::mailer('campaign')
                ->to($contact->email)
                ->send(new CampaignEmail(
                    $campaign,
                    $contact,
                    $enhancedSubject,
                    $enhancedContent
                ));

            Log::info('Campaign email sent successfully', [
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'mailer' => 'campaign',
                'original_subject' => $subject,
                'enhanced_subject' => $enhancedSubject
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send campaign email', [
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'error' => $e->getMessage(),
                'mailer' => 'campaign'
            ]);

            return false;
        }
    }

    /**
     * Enhance subject line for better deliverability
     * Avoids promotional language that triggers spam filters
     */
    private function enhanceSubjectForDeliverability(string $subject): string
    {
        // Remove common promotional words that trigger spam filters
        $promotionalWords = [
            'free', 'limited time', 'act now', 'don\'t miss', 'exclusive offer',
            'special offer', 'discount', 'sale', 'buy now', 'click here',
            'urgent', 'last chance', 'limited offer', 'one time only'
        ];
        
        $enhancedSubject = $subject;
        foreach ($promotionalWords as $word) {
            $enhancedSubject = str_ireplace($word, '', $enhancedSubject);
        }
        
        // Add business context if subject is too generic
        if (strlen($enhancedSubject) < 10) {
            $enhancedSubject = 'MaxMed Business Update: ' . $enhancedSubject;
        }
        
        // Ensure subject doesn't exceed recommended length
        if (strlen($enhancedSubject) > 60) {
            $enhancedSubject = substr($enhancedSubject, 0, 57) . '...';
        }
        
        return trim($enhancedSubject);
    }

    /**
     * Enhance email content for better deliverability
     * Improves content structure and removes promotional elements
     */
    private function enhanceContentForDeliverability(array $emailContent, MarketingContact $contact): array
    {
        $enhancedHtml = $emailContent['html'] ?? '';
        $enhancedText = $emailContent['text'] ?? '';
        
        // Add business communication header to HTML content
        if (!empty($enhancedHtml)) {
            $enhancedHtml = $this->addBusinessHeaderToHtml($enhancedHtml, $contact);
        }
        
        // Enhance text content
        if (!empty($enhancedText)) {
            $enhancedText = $this->addBusinessHeaderToText($enhancedText, $contact);
        }
        
        return [
            'html' => $enhancedHtml,
            'text' => $enhancedText,
        ];
    }

    /**
     * Add business communication header to HTML content
     */
    private function addBusinessHeaderToHtml(string $html, MarketingContact $contact): string
    {
        $businessHeader = '
        <div style="background-color: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <div style="font-weight: bold; color: #007bff; margin-bottom: 5px;">IMPORTANT BUSINESS COMMUNICATION</div>
            <div style="font-size: 14px; color: #6c757d;">
                This is a business communication from MaxMed regarding healthcare supplies and medical equipment 
                relevant to your business operations. This is not promotional marketing material.
            </div>
        </div>';
        
        // Insert header after opening body tag or at the beginning
        if (strpos($html, '<body') !== false) {
            $html = preg_replace('/<body[^>]*>/', '$0' . $businessHeader, $html);
        } else {
            $html = $businessHeader . $html;
        }
        
        return $html;
    }

    /**
     * Add business communication header to text content
     */
    private function addBusinessHeaderToText(string $text, MarketingContact $contact): string
    {
        $businessHeader = "IMPORTANT BUSINESS COMMUNICATION\n";
        $businessHeader .= "MaxMed Healthcare Supplies - Business Update\n";
        $businessHeader .= "This is a business communication regarding healthcare supplies and medical equipment.\n\n";
        
        return $businessHeader . $text;
    }

    /**
     * Get campaign mailer configuration
     */
    public function getCampaignMailerConfig(): array
    {
        return [
            'host' => config('mail.mailers.campaign.host'),
            'port' => config('mail.mailers.campaign.port'),
            'username' => config('mail.mailers.campaign.username'),
            'encryption' => config('mail.mailers.campaign.encryption'),
            'from_address' => config('mail.campaign_from.address'),
            'from_name' => config('mail.campaign_from.name'),
        ];
    }

    /**
     * Test campaign mailer configuration
     */
    public function testCampaignMailer(): bool
    {
        try {
            $config = $this->getCampaignMailerConfig();
            
            Log::info('Campaign mailer configuration', $config);
            
            // Test if campaign mailer is properly configured
            if (empty($config['username']) || empty($config['host'])) {
                Log::warning('Campaign mailer not fully configured', $config);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Campaign mailer test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get optimal send times to improve deliverability
     */
    public function getOptimalSendTime(): array
    {
        // Business hours in different time zones for better engagement
        return [
            'recommendations' => [
                'best_days' => ['Tuesday', 'Wednesday', 'Thursday'],
                'best_times' => ['10:00-11:00', '14:00-15:00'],
                'avoid_days' => ['Monday', 'Friday'],
                'avoid_times' => ['Before 9:00', 'After 17:00', 'Weekends']
            ],
            'engagement_tips' => [
                'Send during business hours',
                'Avoid holiday periods',
                'Consider recipient time zones',
                'Maintain consistent sending schedule'
            ]
        ];
    }

    /**
     * Validate email content for deliverability
     */
    public function validateEmailContent(string $subject, string $content): array
    {
        $issues = [];
        $score = 100;

        // Check subject line
        if (strlen($subject) > 60) {
            $issues[] = 'Subject line too long (>60 characters)';
            $score -= 10;
        }

        if (strlen($subject) < 10) {
            $issues[] = 'Subject line too short (<10 characters)';
            $score -= 15;
        }

        // Check for spam trigger words
        $spamWords = ['FREE', 'URGENT', 'LIMITED TIME', 'SALE', 'DISCOUNT', 'AMAZING', 'INCREDIBLE', 'WIN', 'WINNER', 'CONGRATULATIONS'];
        $spamCount = 0;
        foreach ($spamWords as $word) {
            if (stripos($subject . ' ' . $content, $word) !== false) {
                $spamCount++;
            }
        }

        if ($spamCount > 0) {
            $issues[] = "Contains {$spamCount} potential spam trigger words";
            $score -= ($spamCount * 5);
        }

        // Check content quality
        if (strlen(strip_tags($content)) < 50) {
            $issues[] = 'Content too short (may appear suspicious)';
            $score -= 20;
        }

        // Check for excessive links
        $linkCount = substr_count($content, 'http');
        if ($linkCount > 5) {
            $issues[] = 'Too many links in content';
            $score -= 15;
        }

        // Check for proper unsubscribe
        if (stripos($content, 'unsubscribe') === false) {
            $issues[] = 'Missing unsubscribe option';
            $score -= 25;
        }

        return [
            'score' => max(0, $score),
            'issues' => $issues,
            'recommendations' => [
                'Use business-focused language',
                'Include personalization',
                'Add company signature',
                'Ensure proper text-to-HTML ratio',
                'Include clear unsubscribe option'
            ]
        ];
    }
} 