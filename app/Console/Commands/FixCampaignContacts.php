<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Jobs\SendCampaignJob;
use Illuminate\Console\Command;

class FixCampaignContacts extends Command
{
    protected $signature = 'fix:campaign-contacts {campaign-id}';
    protected $description = 'Fix campaign contact statuses and test sending';

    public function handle()
    {
        $campaignId = $this->argument('campaign-id');
        $campaign = Campaign::find($campaignId);
        
        if (!$campaign) {
            $this->error("Campaign not found with ID: {$campaignId}");
            return 1;
        }

        $this->info("Fixing campaign: {$campaign->name}");
        
        // Reset all contact statuses to pending
        $contactIds = $campaign->contacts()->pluck('marketing_contacts.id')->toArray();
        $campaign->contacts()->updateExistingPivot($contactIds, [
            'status' => 'pending',
            'sent_at' => null,
            'delivered_at' => null,
            'opened_at' => null,
            'clicked_at' => null,
            'bounce_reason' => null
        ]);
        
        $this->info("Reset " . count($contactIds) . " contacts to pending status");
        
        // Check current status
        $pendingCount = $campaign->contacts()->wherePivot('status', 'pending')->count();
        $this->info("Pending contacts: {$pendingCount}");
        
        if ($this->confirm('Do you want to send this campaign now?')) {
            try {
                $campaign->markAsSending();
                SendCampaignJob::dispatch($campaign);
                $this->info('Campaign job dispatched! Run: php artisan queue:work');
            } catch (\Exception $e) {
                $this->error('Failed to dispatch: ' . $e->getMessage());
            }
        }
        
        return 0;
    }
} 