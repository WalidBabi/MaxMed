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
        
        if ($admin) {
            Notification::send($admin, new AuthNotification($event->user, 'login'));
        }
    }
}
