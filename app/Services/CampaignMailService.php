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
} 