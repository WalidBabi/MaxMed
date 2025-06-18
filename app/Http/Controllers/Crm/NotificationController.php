<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the CRM user.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get CRM-specific notifications (marketing, leads, contact submissions, etc.)
        $notifications = $user->unreadNotifications()
            ->whereIn('type', [
                'App\\Notifications\\LeadCreatedNotification',
                'App\\Notifications\\ContactSubmissionNotification', 
                'App\\Notifications\\QuotationRequestNotification',
                'App\\Notifications\\CampaignStatusNotification',
                'App\\Notifications\\MarketingActivityNotification',
                'App\\Notifications\\CrmGeneralNotification'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()
                ->whereIn('type', [
                    'App\\Notifications\\LeadCreatedNotification',
                    'App\\Notifications\\ContactSubmissionNotification', 
                    'App\\Notifications\\QuotationRequestNotification',
                    'App\\Notifications\\CampaignStatusNotification',
                    'App\\Notifications\\MarketingActivityNotification',
                    'App\\Notifications\\CrmGeneralNotification'
                ])
                ->count()
        ]);
    }

    /**
     * Check for new CRM notifications since a given timestamp.
     */
    public function checkNew(Request $request)
    {
        $user = Auth::user();
        $since = $request->get('since', '1970-01-01T00:00:00Z');
        
        $sinceDate = Carbon::parse($since);
        
        $newNotifications = $user->unreadNotifications()
            ->whereIn('type', [
                'App\\Notifications\\LeadCreatedNotification',
                'App\\Notifications\\ContactSubmissionNotification', 
                'App\\Notifications\\QuotationRequestNotification',
                'App\\Notifications\\CampaignStatusNotification',
                'App\\Notifications\\MarketingActivityNotification',
                'App\\Notifications\\CrmGeneralNotification'
            ])
            ->where('created_at', '>', $sinceDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalUnread = $user->unreadNotifications()
            ->whereIn('type', [
                'App\\Notifications\\LeadCreatedNotification',
                'App\\Notifications\\ContactSubmissionNotification', 
                'App\\Notifications\\QuotationRequestNotification',
                'App\\Notifications\\CampaignStatusNotification',
                'App\\Notifications\\MarketingActivityNotification',
                'App\\Notifications\\CrmGeneralNotification'
            ])
            ->count();

        $latestTimestamp = $newNotifications->isNotEmpty() ? 
            $newNotifications->first()->created_at->toISOString() : 
            $since;

        return response()->json([
            'has_new' => $newNotifications->isNotEmpty(),
            'notifications' => $newNotifications,
            'count' => $totalUnread,
            'latest_timestamp' => $latestTimestamp
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all CRM notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->unreadNotifications()
            ->whereIn('type', [
                'App\\Notifications\\LeadCreatedNotification',
                'App\\Notifications\\ContactSubmissionNotification', 
                'App\\Notifications\\QuotationRequestNotification',
                'App\\Notifications\\CampaignStatusNotification',
                'App\\Notifications\\MarketingActivityNotification',
                'App\\Notifications\\CrmGeneralNotification'
            ])
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Get notification count for header badge.
     */
    public function count()
    {
        $user = Auth::user();
        
        return response()->json([
            'count' => $user->unreadNotifications()
                ->whereIn('type', [
                    'App\\Notifications\\LeadCreatedNotification',
                    'App\\Notifications\\ContactSubmissionNotification', 
                    'App\\Notifications\\QuotationRequestNotification',
                    'App\\Notifications\\CampaignStatusNotification',
                    'App\\Notifications\\MarketingActivityNotification',
                    'App\\Notifications\\CrmGeneralNotification'
                ])
                ->count()
        ]);
    }
} 