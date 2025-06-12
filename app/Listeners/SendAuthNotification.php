<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\AuthNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;

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
        $admin = User::where('is_admin', true)->first();
        
        // Don't send notification if admin is logging in themselves
        if ($admin && $admin->id !== $event->user->id) {
            // Determine authentication method based on request
            $method = 'Email'; // Default
            if (request()->routeIs('login.google.callback')) {
                $method = 'Google OAuth';
            } elseif (request()->routeIs('login.google.one-tap')) {
                $method = 'Google One Tap';
            }
            
            Notification::send($admin, new AuthNotification($event->user, 'login', $method));
        }
    }
}
