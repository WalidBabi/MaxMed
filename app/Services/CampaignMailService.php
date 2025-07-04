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
            // Send using the campaign mailer
            Mail::mailer('campaign')
                ->to($contact->email)
                ->send(new CampaignEmail(
                    $campaign,
                    $contact,
                    $subject,
                    $emailContent
                ));

            Log::info('Campaign email sent successfully', [
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'mailer' => 'campaign'
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