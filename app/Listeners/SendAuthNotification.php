<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\AuthNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendAuthNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            // Create a unique cache key for this user's login notification
            $cacheKey = 'login_notification_sent_' . $event->user->id;
            
            // Check if we've already sent a notification for this user in the last 5 minutes
            if (Cache::has($cacheKey)) {
                Log::info('Login notification skipped for user ' . $event->user->id . ' - cooldown period active');
                return;
            }
            
            // Use configured admin email if available, otherwise fallback to database admin
            $adminEmail = config('mail.admin_email');
            
            if ($adminEmail) {
                // Create a temporary admin object for notification
                $admin = new User();
                $admin->email = $adminEmail;
                $admin->name = 'Admin';
                $admin->id = 0;
            } else {
                $admin = User::where('is_admin', true)->whereNotNull('email')->first();
            }
            
            // Don't send notification if admin is logging in themselves or no admin found
            if (!$admin || $admin->id === $event->user->id) {
                return;
            }
            
            // Determine authentication method based on request
            $method = 'Email'; // Default
            if (request()->routeIs('login.google.callback')) {
                $method = 'Google OAuth';
            } elseif (request()->routeIs('login.google.one-tap')) {
                $method = 'Google One Tap';
            }
            
            // Send the notification
            Notification::send($admin, new AuthNotification($event->user, 'login', $method));
            
            // Set cache flag to prevent duplicate notifications for 5 minutes
            Cache::put($cacheKey, true, now()->addMinutes(5));
            
            Log::info('Login notification sent for user ' . $event->user->id . ' via ' . $method);
            
        } catch (\Exception $e) {
            Log::error('Failed to send auth notification: ' . $e->getMessage());
        }
    }
}
