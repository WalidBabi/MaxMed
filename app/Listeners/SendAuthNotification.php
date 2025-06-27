<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\AuthNotification;
use App\Notifications\SupplierAuthNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

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
            
            // Check if notification was already sent recently (prevent duplicates)
            if (Cache::has($cacheKey)) {
                Log::info('Login notification skipped for user ' . $event->user->id . ' - cooldown period active');
                return;
            }
            
            // Use configured admin email if available, otherwise fallback to database admin
            $adminEmail = Config::get('mail.admin_email');
            
            if ($adminEmail) {
                // Create a temporary admin object for notification
                $admin = new User([
                    'email' => $adminEmail,
                    'name' => 'Admin',
                    'id' => 0
                ]);
            } else {
                $admin = User::where('is_admin', true)
                    ->whereNotNull('email')
                    ->whereDoesntHave('role', function($query) {
                        $query->where('name', 'supplier');
                    })
                    ->first();
            }
            
            // Don't send notification if admin is logging in themselves or no admin found
            if (!$admin || $admin->id === $event->user->id) {
                return;
            }
            
            // Determine authentication method based on request
            $method = 'Email'; // Default
            if (Request::routeIs('login.google.callback')) {
                $method = 'Google OAuth';
            } elseif (Request::routeIs('login.google-one-tap.callback')) {
                $method = 'Google One Tap';
            }
            
            // Check if the user is a supplier
            $isSupplier = $event->user->role && $event->user->role->name === 'supplier';
            
            // Send the appropriate notification
            if ($isSupplier) {
                Notification::send($admin, new SupplierAuthNotification($event->user, 'login', $method));
                Log::info('Supplier login notification sent for user ' . $event->user->id . ' via ' . $method);
            } else {
                Notification::send($admin, new AuthNotification($event->user, 'login', $method));
                Log::info('Login notification sent for user ' . $event->user->id . ' via ' . $method);
            }
            
            // Set cache flag to prevent duplicate notifications for 1 minute
            Cache::put($cacheKey, true, now()->addMinutes(1));
            
        } catch (\Exception $e) {
            Log::error('Failed to send auth notification: ' . $e->getMessage());
        }
    }
}
