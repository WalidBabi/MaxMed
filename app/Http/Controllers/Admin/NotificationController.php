<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the admin.
     */
    public function index()
    {
        // Exclude contact, quotation, and campaign notifications from admin bell
        $excludedTypes = [
            'App\\Notifications\\ContactSubmissionNotification',
            'App\\Notifications\\CampaignStatusNotification', 
            'App\\Notifications\\FeedbackNotification',
            'App\\Notifications\\QuotationRequestNotification'
        ];

        $notifications = Auth::user()->unreadNotifications()
            ->whereNotIn('type', $excludedTypes)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->whereNotIn('type', $excludedTypes)->count()
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Get notification count for header badge.
     */
    public function count()
    {
        // Exclude contact, quotation, and campaign notifications from admin bell
        $excludedTypes = [
            'App\\Notifications\\ContactSubmissionNotification',
            'App\\Notifications\\CampaignStatusNotification', 
            'App\\Notifications\\FeedbackNotification',
            'App\\Notifications\\QuotationRequestNotification'
        ];

        return response()->json([
            'count' => Auth::user()->unreadNotifications()->whereNotIn('type', $excludedTypes)->count()
        ]);
    }

    /**
     * Stream real-time notifications using Server-Sent Events.
     */
    public function stream()
    {
        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Disable Nginx buffering
        ];

        $callback = function () {
            $lastNotificationId = request()->input('last_id', 0);
            
            // Send initial connection message
            echo "data: " . json_encode([
                'type' => 'connected',
                'message' => 'Real-time notifications connected'
            ]) . "\n\n";
            
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            $lastCheck = time();
            
            while (true) {
                // Check for new notifications every 2 seconds
                if (time() - $lastCheck >= 2) {
                    // Exclude contact, quotation, and campaign notifications from admin bell
                    $excludedTypes = [
                        'App\\Notifications\\ContactSubmissionNotification',
                        'App\\Notifications\\CampaignStatusNotification', 
                        'App\\Notifications\\FeedbackNotification',
                        'App\\Notifications\\QuotationRequestNotification'
                    ];

                    $newNotifications = Auth::user()->unreadNotifications()
                        ->whereNotIn('type', $excludedTypes)
                        ->where('created_at', '>', now()->subMinutes(5)) // Only check recent notifications
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($newNotifications as $notification) {
                        if ($notification->id > $lastNotificationId) {
                            $eventData = [
                                'type' => 'new_notification',
                                'notification' => $notification,
                                'count' => Auth::user()->unreadNotifications()->whereNotIn('type', $excludedTypes)->count()
                            ];

                            echo "id: {$notification->id}\n";
                            echo "data: " . json_encode($eventData) . "\n\n";
                            
                            $lastNotificationId = $notification->id;
                            
                            if (ob_get_level()) {
                                ob_flush();
                            }
                            flush();
                        }
                    }
                    
                    $lastCheck = time();
                }
                
                // Break if client disconnected
                if (connection_aborted()) {
                    break;
                }
                
                usleep(1000000); // Sleep for 1 second
            }
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check for new notifications (AJAX endpoint for real-time updates)
     */
    public function checkNew()
    {
        $lastTimestamp = request()->input('last_timestamp', '1970-01-01 00:00:00');
        
        // Exclude contact, quotation, and campaign notifications from admin bell
        $excludedTypes = [
            'App\\Notifications\\ContactSubmissionNotification',
            'App\\Notifications\\CampaignStatusNotification', 
            'App\\Notifications\\FeedbackNotification',
            'App\\Notifications\\QuotationRequestNotification'
        ];
        
        try {
            $newNotifications = Auth::user()->unreadNotifications()
                ->whereNotIn('type', $excludedTypes)
                ->where('created_at', '>', $lastTimestamp)
                ->orderBy('created_at', 'desc')
                ->get();

            $allUnreadCount = Auth::user()->unreadNotifications()->whereNotIn('type', $excludedTypes)->count();
            $latestTimestamp = $newNotifications->isNotEmpty() ? 
                $newNotifications->first()->created_at->toISOString() : 
                $lastTimestamp;

            \Log::info('Admin checkNew response:', [
                'has_new' => $newNotifications->count() > 0,
                'new_notifications_count' => $newNotifications->count(),
                'total_unread_count' => $allUnreadCount,
                'latest_timestamp' => $latestTimestamp,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'has_new' => $newNotifications->count() > 0,
                'notifications' => $newNotifications,
                'count' => $allUnreadCount,
                'latest_timestamp' => $latestTimestamp
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Admin checkNew:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'has_new' => false,
                'notifications' => [],
                'count' => 0,
                'latest_timestamp' => $lastTimestamp
            ], 500);
        }
    }
} 