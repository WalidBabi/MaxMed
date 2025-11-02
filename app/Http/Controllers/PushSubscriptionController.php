<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;
use App\Models\PushNotificationToken;
use Illuminate\Support\Str;

class PushSubscriptionController extends Controller
{
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    /**
     * Generate a push notification token for authenticated users
     */
    public function generateToken(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Generate a unique token for this device
        $deviceName = $request->input('device_name', $request->userAgent());
        $result = PushNotificationToken::generateToken($user, $deviceName);

        return response()->json([
            'token' => $result['token'],
            'expires_at' => $result['model']->expires_at->toISOString(),
            'device_name' => $deviceName,
        ]);
    }

    /**
     * Subscribe to push notifications using API token
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            // This will work with either Auth::check() OR the push.token middleware
            $user = $request->user();
            
            if (!$user) {
                \Log::warning('Push subscription attempt without authentication', [
                    'endpoint' => $request->input('endpoint'),
                    'user_agent' => $request->userAgent(),
                ]);
                return response()->json(['error' => 'Unauthorized', 'message' => 'Authentication required'], 401);
            }

            $data = $request->validate([
                'endpoint' => 'required|string|max:500',
                'keys.auth' => 'required|string|max:255',
                'keys.p256dh' => 'required|string|max:255',
            ]);

            // Log the subscription attempt
            \Log::info('Push subscription attempt', [
                'user_id' => $user->id,
                'endpoint' => substr($data['endpoint'], 0, 100),
                'p256dh_length' => strlen($data['keys']['p256dh']),
                'auth_length' => strlen($data['keys']['auth']),
            ]);

            // Check if keys are within length limits
            if (strlen($data['keys']['p256dh']) > 255) {
                \Log::error('Push subscription: p256dh key too long', [
                    'length' => strlen($data['keys']['p256dh']),
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'error' => 'Invalid subscription',
                    'message' => 'p256dh key is too long (max 255 characters)',
                ], 400);
            }

            if (strlen($data['keys']['auth']) > 255) {
                \Log::error('Push subscription: auth key too long', [
                    'length' => strlen($data['keys']['auth']),
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'error' => 'Invalid subscription',
                    'message' => 'auth key is too long (max 255 characters)',
                ], 400);
            }

            DB::table('push_subscriptions')->upsert([
                'endpoint' => $data['endpoint'],
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
                'user_id' => $user->id,
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'updated_at' => now(),
                'created_at' => now(),
            ], ['endpoint'], ['p256dh', 'auth', 'user_id', 'user_agent', 'updated_at']);

            \Log::info('Push subscription saved successfully', [
                'user_id' => $user->id,
                'endpoint' => substr($data['endpoint'], 0, 100),
            ]);

            return response()->json(['status' => 'subscribed']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Push subscription validation error', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Invalid subscription data',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Push subscription error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()?->id,
                'endpoint' => $request->input('endpoint'),
            ]);
            return response()->json([
                'error' => 'Server error',
                'message' => 'Failed to save subscription: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        DB::table('push_subscriptions')->where('endpoint', $validated['endpoint'])->delete();

        return response()->json(['status' => 'unsubscribed']);
    }

    public function testPage()
    {
        $subscriptionCount = DB::table('push_subscriptions')
            ->when(Auth::id(), fn($q) => $q->where('user_id', Auth::id()))
            ->count();

        return view('push.test', [
            'subscriptionCount' => $subscriptionCount,
            'userId' => Auth::id(),
        ]);
    }

    public function test(Request $request): JsonResponse
    {
        $title = (string) ($request->input('title') ?? 'MaxMed');
        $body = (string) ($request->input('body') ?? 'Test notification');
        $url = (string) ($request->input('url') ?? '/');

        $query = DB::table('push_subscriptions');
        if (Auth::id()) {
            $query->where('user_id', Auth::id());
        }
        $subscriptions = $query->limit(100)->get();

        if ($subscriptions->isEmpty()) {
            return response()->json(['sent' => 0, 'message' => 'No subscriptions'], 404);
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);

        $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

        $sent = 0;
        foreach ($subscriptions as $sub) {
            $subscription = WebPushSubscription::create([
                'endpoint' => $sub->endpoint,
                'keys' => [
                    'p256dh' => $sub->p256dh,
                    'auth' => $sub->auth,
                ],
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

        return response()->json(['sent' => $sent]);
    }
}


