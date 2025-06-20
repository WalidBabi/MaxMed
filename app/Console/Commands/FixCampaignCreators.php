<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\User;

class FixCampaignCreators extends Command
{
    protected $signature = 'campaign:fix-creators {--dry-run : Only show what would be fixed}';
    
    protected $description = 'Fix campaigns with missing or invalid creators';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('=== Checking Campaign Creators ===');
        
        // Get campaigns with null creators
        $nullCreators = Campaign::whereNull('created_by')->get();
        $this->info("Campaigns with null creator: {$nullCreators->count()}");
        
        // Get campaigns with invalid user IDs
        $validUserIds = User::pluck('id')->toArray();
        $invalidCreators = Campaign::whereNotNull('created_by')
                                 ->whereNotIn('created_by', $validUserIds)
                                 ->get();
        $this->info("Campaigns with invalid creator ID: {$invalidCreators->count()}");
        
        $problemCampaigns = $nullCreators->merge($invalidCreators);
        
        if ($problemCampaigns->isEmpty()) {
            $this->info('✅ All campaigns have valid creators!');
            return 0;
        }
        
        $this->info("Found {$problemCampaigns->count()} campaigns with creator issues:");
        
        foreach ($problemCampaigns as $campaign) {
            $creatorInfo = $campaign->created_by ? "Invalid ID: {$campaign->created_by}" : "NULL";
            $this->line("- Campaign #{$campaign->id}: {$campaign->name} (Creator: {$creatorInfo})");
        }
        
        if ($isDryRun) {
            $this->info("\n--dry-run mode: No changes made");
            $this->info("Run without --dry-run to fix these campaigns");
            return 0;
        }
        
        // Fix the campaigns by assigning them to the first admin user
        $adminUser = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$adminUser) {
            $adminUser = User::first();
        }
        
        if (!$adminUser) {
            $this->error('No users found to assign as creator!');
            return 1;
        }
        
        $this->info("\nAssigning campaigns to user: {$adminUser->name} (ID: {$adminUser->id})");
        
        foreach ($problemCampaigns as $campaign) {
            $campaign->update(['created_by' => $adminUser->id]);
            $this->info("✅ Fixed Campaign #{$campaign->id}: {$campaign->name}");
        }
        
        $this->info("\n🎉 Fixed {$problemCampaigns->count()} campaigns!");
        
        return 0;
    }
} 