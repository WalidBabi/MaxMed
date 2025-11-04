<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Auth\Events\Login;
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
            // Only send when the login was triggered intentionally by the user
            $shouldNotify = session()->pull('login_notification_intent', false);

            if (!$shouldNotify) {
                Log::info('Login notification skipped for user ' . $event->user->id . ' - no intent flag present');
                return;
            }

            // Create a unique cache key for this user's login notification
            $cacheKey = 'login_notification_sent_' . $event->user->id;
            
            // Check if notification was already sent recently (prevent duplicates)
            if (Cache::has($cacheKey)) {
                Log::info('Login notification skipped for user ' . $event->user->id . ' - cooldown period active');
                return;
            }
            
            $loginUser = $event->user;

            $adminRecipients = collect();

            // Attempt to use configured admin email to find recipient
            if ($adminEmail = Config::get('mail.admin_email')) {
                $adminRecipients = User::query()
                    ->where('email', $adminEmail)
                    ->where('id', '!=', $loginUser->id)
                    ->get();
            }

            if ($adminRecipients->isEmpty()) {
                $adminRecipients = User::query()
                    ->where('id', '!=', $loginUser->id)
                    ->where(function ($q) {
                        $q->whereHas('role', function ($roleQuery) {
                            $roleQuery->whereIn('name', ['admin', 'super_admin', 'superadmin', 'super-administrator']);
                        })
                        ->orWhereHas('roles', function ($roleQuery) {
                            $roleQuery->whereIn('name', ['admin', 'super_admin', 'superadmin', 'super-administrator']);
                        });
                    })
                    ->get();
            }

            if ($adminRecipients->isEmpty()) {
                Log::info('Login notification skipped - no admin recipients found for push notification.');
                return;
            }
            
            // Determine authentication method based on request
            // Use current route name safely to avoid session issues
            $method = 'Email'; // Default
            try {
                $currentRoute = Request::route() ? Request::route()->getName() : null;
                if ($currentRoute === 'login.google.callback') {
                    $method = 'Google OAuth';
                } elseif ($currentRoute === 'login.google-one-tap.callback') {
                    $method = 'Google One Tap';
                }
            } catch (\Exception $e) {
                // If we can't get route info, default to Email
                Log::debug('Could not determine login method, defaulting to Email', ['error' => $e->getMessage()]);
            }
            
            $title = 'User login: ' . ($loginUser->name ?: $loginUser->email);
            $bodyParts = [
                $loginUser->name ?: null,
                $loginUser->email,
                'Method: ' . $method,
                'Time: ' . now()->toDateTimeString(),
            ];
            $body = implode("\n", array_filter($bodyParts));

            $url = url('/admin/users/' . $loginUser->id);

            /** @var PushNotificationService $pushService */
            $pushService = app(PushNotificationService::class);

            $sentTotal = 0;
            foreach ($adminRecipients as $recipient) {
                try {
                    $sentTotal += $pushService->sendToUser((int) $recipient->id, $title, $body, $url);
                } catch (\Throwable $exception) {
                    Log::warning('Failed sending login push notification', [
                        'recipient_id' => $recipient->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            Log::info('Login push notification dispatched', [
                'login_user_id' => $loginUser->id,
                'recipients' => $adminRecipients->pluck('id')->all(),
                'method' => $method,
                'sent_total' => $sentTotal,
            ]);
            
            // Set cache flag to prevent duplicate notifications for 1 minute
            Cache::put($cacheKey, true, now()->addMinutes(1));
            
        } catch (\Exception $e) {
            Log::error('Failed to send auth notification: ' . $e->getMessage());
        }
    }
}
