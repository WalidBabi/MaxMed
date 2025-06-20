<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class FixCampaignStatistics extends Command
{
    protected $signature = 'campaign:fix-stats {--campaign-id=}';
    protected $description = 'Fix campaign statistics inconsistencies';

    public function handle()
    {
        $campaignId = $this->option('campaign-id');
        
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if (!$campaign) {
                $this->error("Campaign not found with ID: {$campaignId}");
                return 1;
            }
            $campaigns = collect([$campaign]);
        } else {
            $campaigns = Campaign::all();
        }

        foreach ($campaigns as $campaign) {
            $this->info("Fixing campaign: {$campaign->name} (ID: {$campaign->id})");
            
            // Get correct counts from related tables
            $stats = DB::table('campaign_contacts')
                ->where('campaign_id', $campaign->id)
                ->selectRaw('
                    COUNT(*) as total_recipients,
                    SUM(CASE WHEN status IN ("sent", "delivered") THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_count,
                    SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_count,
                    SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked_count,
                    SUM(CASE WHEN status = "bounced" THEN 1 ELSE 0 END) as bounced_count
                ')
                ->first();

            // Get unsubscribe count from marketing_contacts
            $unsubscribedCount = DB::table('campaign_contacts')
                ->join('marketing_contacts', 'campaign_contacts.marketing_contact_id', '=', 'marketing_contacts.id')
                ->where('campaign_contacts.campaign_id', $campaign->id)
                ->where('marketing_contacts.status', 'unsubscribed')
                ->count();

            // Update campaign with correct statistics
            $campaign->update([
                'total_recipients' => $stats->total_recipients ?? 0,
                'sent_count' => $stats->sent_count ?? 0,
                'delivered_count' => $stats->delivered_count ?? 0,
                'opened_count' => $stats->opened_count ?? 0,
                'clicked_count' => $stats->clicked_count ?? 0,
                'bounced_count' => $stats->bounced_count ?? 0,
                'unsubscribed_count' => $unsubscribedCount,
            ]);

            $this->info("Updated statistics:");
            $this->info("- Recipients: {$campaign->total_recipients}");
            $this->info("- Sent: {$campaign->sent_count}");
            $this->info("- Delivered: {$campaign->delivered_count}");
            $this->info("- Opens: {$campaign->opened_count}");
            $this->info("- Clicks: {$campaign->clicked_count}");
            $this->info("- Bounced: {$campaign->bounced_count}");
            $this->info("- Unsubscribed: {$campaign->unsubscribed_count}");
            $this->line('');
        }

        $this->info('Campaign statistics fixed!');
        return 0;
    }
} 