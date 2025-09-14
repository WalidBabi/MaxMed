<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:marketing.access')->only(['dashboard', 'index']);
        $this->middleware('permission:marketing.campaigns.view')->only(['campaigns']);
        $this->middleware('permission:marketing.contacts.view')->only(['contacts']);
        $this->middleware('permission:marketing.templates.view')->only(['templates']);
        $this->middleware('permission:marketing.analytics.view')->only(['analytics']);
    }

    public function dashboard()
    {
        // Get real marketing data from database
        $totalContacts = MarketingContact::count();
        $activeContacts = MarketingContact::where('status', 'active')->count();
        
        // Get campaign statistics
        $activeCampaigns = Campaign::whereIn('status', ['active', 'running', 'scheduled'])->count();
        $totalCampaigns = Campaign::count();
        $sentCampaigns = Campaign::where('status', 'sent')->count();
        
        // Get email template count
        $totalTemplates = EmailTemplate::count();
        $activeTemplates = EmailTemplate::where('is_active', true)->count();
        
        // Get recent activities from email logs and campaigns
        $recentActivities = collect();
        
        // Add recent campaign activities
        $recentCampaigns = Campaign::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($campaign) {
                return [
                    'type' => 'campaign',
                    'title' => "Campaign '{$campaign->name}' was {$campaign->status}",
                    'description' => $campaign->description ?? "Campaign targeting {$campaign->total_recipients} recipients",
                    'date' => $campaign->created_at,
                    'user' => $campaign->creator->name ?? 'System',
                    'status' => $campaign->status,
                ];
            });
        
        // Add recent contact activities
        $recentContacts = MarketingContact::orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($contact) {
                return [
                    'type' => 'contact',
                    'title' => "New contact added: {$contact->full_name}",
                    'description' => "{$contact->email} from {$contact->company}",
                    'date' => $contact->created_at,
                    'user' => 'System',
                    'status' => $contact->status,
                ];
            });
        
        $recentActivities = $recentCampaigns->concat($recentContacts)
            ->sortByDesc('date')
            ->take(8)
            ->values();
        
        // Get email performance metrics
        $emailStats = DB::table('email_logs')
            ->select([
                DB::raw('COUNT(*) as total_emails'),
                DB::raw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent_emails'),
                DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_emails'),
                DB::raw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_emails'),
                DB::raw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked_emails'),
                DB::raw('SUM(CASE WHEN status = "bounced" THEN 1 ELSE 0 END) as bounced_emails'),
            ])
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->first();
        
        $data = [
            'total_contacts' => $totalContacts,
            'active_contacts' => $activeContacts,
            'active_campaigns' => $activeCampaigns,
            'total_campaigns' => $totalCampaigns,
            'sent_campaigns' => $sentCampaigns,
            'total_templates' => $totalTemplates,
            'active_templates' => $activeTemplates,
            'recent_activities' => $recentActivities->toArray(),
            'email_stats' => [
                'total_emails' => $emailStats->total_emails ?? 0,
                'sent_emails' => $emailStats->sent_emails ?? 0,
                'delivered_emails' => $emailStats->delivered_emails ?? 0,
                'opened_emails' => $emailStats->opened_emails ?? 0,
                'clicked_emails' => $emailStats->clicked_emails ?? 0,
                'bounced_emails' => $emailStats->bounced_emails ?? 0,
                'delivery_rate' => $emailStats->sent_emails > 0 ? 
                    round(($emailStats->delivered_emails / $emailStats->sent_emails) * 100, 1) : 0,
                'open_rate' => $emailStats->delivered_emails > 0 ? 
                    round(($emailStats->opened_emails / $emailStats->delivered_emails) * 100, 1) : 0,
                'click_rate' => $emailStats->opened_emails > 0 ? 
                    round(($emailStats->clicked_emails / $emailStats->opened_emails) * 100, 1) : 0,
            ],
        ];

        return view('crm.marketing.dashboard', compact('data'));
    }
} 