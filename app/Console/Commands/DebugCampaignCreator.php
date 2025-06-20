<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\User;

class DebugCampaignCreator extends Command
{
    protected $signature = 'debug:campaign-creator {campaign-id?}';
    protected $description = 'Debug campaign creator relationship';

    public function handle()
    {
        $campaignId = $this->argument('campaign-id');
        
        if ($campaignId) {
            $campaign = Campaign::with('creator')->find($campaignId);
            if (!$campaign) {
                $this->error("Campaign not found with ID: {$campaignId}");
                return 1;
            }
            
            $this->info("Campaign: {$campaign->name}");
            $this->info("Created by ID: {$campaign->created_by}");
            $this->info("Creator loaded: " . ($campaign->creator ? 'Yes' : 'No'));
            if ($campaign->creator) {
                $this->info("Creator name: {$campaign->creator->name}");
            } else {
                $this->warn("Creator is NULL!");
                
                // Check if user exists
                if ($campaign->created_by) {
                    $user = User::find($campaign->created_by);
                    if ($user) {
                        $this->info("User {$campaign->created_by} exists: {$user->name}");
                    } else {
                        $this->error("User {$campaign->created_by} has been deleted!");
                    }
                }
            }
        } else {
            // Check all campaigns
            $this->info("Checking all campaigns...");
            
            $campaigns = Campaign::with('creator')->get();
            $nullCreators = 0;
            
            foreach ($campaigns as $campaign) {
                if (!$campaign->creator) {
                    $nullCreators++;
                    $this->warn("Campaign ID {$campaign->id}: '{$campaign->name}' has null creator (created_by: {$campaign->created_by})");
                    
                    if ($campaign->created_by) {
                        $user = User::find($campaign->created_by);
                        if (!$user) {
                            $this->error("  -> User {$campaign->created_by} has been deleted!");
                        }
                    }
                }
            }
            
            $this->info("Total campaigns: " . $campaigns->count());
            $this->info("Campaigns with null creators: {$nullCreators}");
        }
        
        return 0;
    }
} 