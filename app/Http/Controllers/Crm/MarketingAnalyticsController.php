<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\ContactList;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $dateRange = $request->get('range', 30);
        $startDate = now()->subDays($dateRange);
        
        // Get comprehensive analytics data
        $data = [
            'total_contacts' => MarketingContact::count(),
            'active_contacts' => MarketingContact::where('status', 'active')->count(),
            'campaigns_sent' => Campaign::where('status', 'sent')->count(),
            'active_campaigns' => Campaign::whereIn('status', ['active', 'running', 'scheduled'])->count(),
        ];

        // Calculate average open and click rates
        $campaignStats = Campaign::where('status', 'sent')
            ->where('sent_at', '>=', $startDate)
            ->selectRaw('
                AVG(CASE WHEN delivered_count > 0 THEN (opened_count / delivered_count) * 100 ELSE 0 END) as avg_open_rate,
                AVG(CASE WHEN opened_count > 0 THEN (clicked_count / opened_count) * 100 ELSE 0 END) as avg_click_rate
            ')
            ->first();

        $data['avg_open_rate'] = round($campaignStats->avg_open_rate ?? 0, 1);
        $data['avg_click_rate'] = round($campaignStats->avg_click_rate ?? 0, 1);

        // Get top performing campaigns
        $data['top_campaigns'] = Campaign::where('status', 'sent')
            ->where('sent_at', '>=', $startDate)
            ->where('delivered_count', '>', 0)
            ->selectRaw('
                id, name, sent_count, delivered_count, opened_count, clicked_count,
                ROUND((opened_count / delivered_count) * 100, 1) as open_rate,
                ROUND(CASE WHEN opened_count > 0 THEN (clicked_count / opened_count) * 100 ELSE 0 END, 1) as click_rate
            ')
            ->orderByDesc('open_rate')
            ->limit(5)
            ->get()
            ->toArray();

        // Get contact list performance
        $data['contact_lists'] = ContactList::withCount('contacts')
            ->get()
            ->map(function ($list) use ($startDate) {
                // Calculate engagement rate based on recent campaign participation
                $engagementRate = 0;
                if ($list->contacts_count > 0) {
                    $engagedContacts = DB::table('campaign_contacts')
                        ->join('campaigns', 'campaign_contacts.campaign_id', '=', 'campaigns.id')
                        ->join('contact_list_contacts', 'campaign_contacts.marketing_contact_id', '=', 'contact_list_contacts.marketing_contact_id')
                        ->where('contact_list_contacts.contact_list_id', $list->id)
                        ->where('campaigns.sent_at', '>=', $startDate)
                        ->where('campaign_contacts.opened_at', '!=', null)
                        ->distinct('campaign_contacts.marketing_contact_id')
                        ->count();
                    
                    $engagementRate = round(($engagedContacts / $list->contacts_count) * 100, 1);
                }

                return [
                    'name' => $list->name,
                    'type' => $list->type,
                    'contacts_count' => $list->contacts_count,
                    'engagement_rate' => $engagementRate,
                ];
            })
            ->sortByDesc('engagement_rate')
            ->take(10)
            ->values()
            ->toArray();

        return view('crm.marketing.analytics.index', compact('data'));
    }
    
    public function campaigns(Request $request)
    {
        $dateRange = $request->get('range', 30);
        $startDate = now()->subDays($dateRange);
        
        // Get detailed campaign analytics
        $campaigns = Campaign::with(['creator', 'emailTemplate'])
            ->where('sent_at', '>=', $startDate)
            ->whereIn('status', ['sent', 'sending', 'scheduled'])
            ->orderByDesc('sent_at')
            ->paginate(25);

        // Calculate overall campaign metrics
        $metrics = Campaign::where('sent_at', '>=', $startDate)
            ->where('status', 'sent')
            ->selectRaw('
                COUNT(*) as total_campaigns,
                SUM(total_recipients) as total_recipients,
                SUM(sent_count) as total_sent,
                SUM(delivered_count) as total_delivered,
                SUM(opened_count) as total_opened,
                SUM(clicked_count) as total_clicked,
                SUM(bounced_count) as total_bounced,
                SUM(unsubscribed_count) as total_unsubscribed,
                AVG(CASE WHEN delivered_count > 0 THEN (opened_count / delivered_count) * 100 ELSE 0 END) as avg_open_rate,
                AVG(CASE WHEN opened_count > 0 THEN (clicked_count / opened_count) * 100 ELSE 0 END) as avg_click_rate,
                AVG(CASE WHEN sent_count > 0 THEN (delivered_count / sent_count) * 100 ELSE 0 END) as avg_delivery_rate
            ')
            ->first();

        return view('crm.marketing.analytics.campaigns', compact('campaigns', 'metrics', 'dateRange'));
    }
    
    public function contacts(Request $request)
    {
        $dateRange = $request->get('range', 30);
        $startDate = now()->subDays($dateRange);
        
        // Get contact analytics
        $contactStats = [
            'total_contacts' => MarketingContact::count(),
            'new_contacts' => MarketingContact::where('created_at', '>=', $startDate)->count(),
            'active_contacts' => MarketingContact::where('status', 'active')->count(),
            'unsubscribed_contacts' => MarketingContact::where('status', 'unsubscribed')->count(),
            'bounced_contacts' => MarketingContact::where('status', 'bounced')->count(),
        ];

        // Get contact growth over time
        $contactGrowth = DB::table('marketing_contacts')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get most engaged contacts
        $topContacts = DB::table('marketing_contacts')
            ->leftJoin('campaign_contacts', 'marketing_contacts.id', '=', 'campaign_contacts.marketing_contact_id')
            ->selectRaw('
                marketing_contacts.id,
                marketing_contacts.first_name,
                marketing_contacts.last_name,
                marketing_contacts.email,
                marketing_contacts.company,
                COUNT(campaign_contacts.id) as total_campaigns,
                SUM(campaign_contacts.open_count) as total_opens,
                SUM(campaign_contacts.click_count) as total_clicks
            ')
            ->groupBy('marketing_contacts.id', 'marketing_contacts.first_name', 'marketing_contacts.last_name', 'marketing_contacts.email', 'marketing_contacts.company')
            ->having('total_campaigns', '>', 0)
            ->orderByDesc('total_opens')
            ->limit(20)
            ->get();

        // Get contact source breakdown
        $contactSources = MarketingContact::select('source', DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        // Get industry breakdown
        $industryBreakdown = MarketingContact::select('industry', DB::raw('COUNT(*) as count'))
            ->whereNotNull('industry')
            ->where('industry', '!=', '')
            ->groupBy('industry')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('crm.marketing.analytics.contacts', compact(
            'contactStats', 
            'contactGrowth', 
            'topContacts', 
            'contactSources', 
            'industryBreakdown',
            'dateRange'
        ));
    }
}
