<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private function webPush(): WebPush
    {
        return new WebPush([
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);
    }

    public function sendToUser(int $userId, string $title, string $body = '', string $url = '/'): int
    {
        $subs = DB::table('push_subscriptions')->where('user_id', $userId)->get();
        if ($subs->isEmpty()) {
            return 0;
        }

        $webPush = $this->webPush();
        $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);
        $sent = 0;
        foreach ($subs as $sub) {
            $subscription = WebPushSubscription::create([
                'endpoint' => $sub->endpoint,
                'keys' => [ 'p256dh' => $sub->p256dh, 'auth' => $sub->auth ],
            ]);
            $report = $webPush->sendOneNotification($subscription, $payload);
            if ($report->isSuccess()) {
                $sent++;
            } else {
                if (in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                    DB::table('push_subscriptions')->where('endpoint', $sub->endpoint)->delete();
                }
            }
        }
        return $sent;
    }

    public function broadcast(string $title, string $body = '', string $url = '/'): int
    {
        $subs = DB::table('push_subscriptions')->limit(1000)->get();
        if ($subs->isEmpty()) {
            return 0;
        }
        $webPush = $this->webPush();
        $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);
        $sent = 0;
        foreach ($subs as $sub) {
            $subscription = WebPushSubscription::create([
                'endpoint' => $sub->endpoint,
                'keys' => [ 'p256dh' => $sub->p256dh, 'auth' => $sub->auth ],
            ]);
            $report = $webPush->sendOneNotification($subscription, $payload);
            if ($report->isSuccess()) {
                $sent++;
            } else {
                if (in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                    DB::table('push_subscriptions')->where('endpoint', $sub->endpoint)->delete();
                }
            }
        }
        return $sent;
    }
}


