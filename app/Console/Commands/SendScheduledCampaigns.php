<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Jobs\SendCampaignJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledCampaigns extends Command
{
    protected $signature = 'campaigns:send-scheduled';
    
    protected $description = 'Send all scheduled campaigns that are ready to be sent';

    public function handle()
    {
        $this->info('Checking for scheduled campaigns...');

        $campaigns = Campaign::readyToSend()->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns ready to send.');
            return 0;
        }

        $this->info("Found {$campaigns->count()} campaigns ready to send.");

        foreach ($campaigns as $campaign) {
            try {
                $this->info("Sending campaign: {$campaign->name}");
                
                // Mark as sending
                $campaign->markAsSending();
                
                // Dispatch job
                SendCampaignJob::dispatch($campaign);
                
                $this->info("Campaign {$campaign->name} dispatched successfully.");
                
            } catch (\Exception $e) {
                $this->error("Failed to dispatch campaign {$campaign->name}: {$e->getMessage()}");
                Log::error("Failed to dispatch scheduled campaign", [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('Scheduled campaigns processing completed.');
        return 0;
    }
} 