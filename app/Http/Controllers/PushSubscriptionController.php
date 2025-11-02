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
        $userId = Auth::id();
        $subscriptionCount = DB::table('push_subscriptions')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->where('is_enabled', true)
            ->when($userId, function($q) use ($userId) {
                $muted = DB::table('users')->where('id', $userId)->value('push_muted');
                if ($muted) {
                    $q->whereRaw('1=0');
                }
            })
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

        $query = DB::table('push_subscriptions')->where('is_enabled', true);
        if (Auth::id()) {
            $muted = DB::table('users')->where('id', Auth::id())->value('push_muted');
            if ($muted) {
                return response()->json(['sent' => 0, 'message' => 'Notifications are muted for this user'], 403);
            }
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

    public function broadcastSelected(Request $request): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'isAdmin') && $user->isAdmin(), 403);

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'title' => 'nullable|string',
            'body' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        $subs = DB::table('push_subscriptions')
            ->leftJoin('users', 'users.id', '=', 'push_subscriptions.user_id')
            ->whereIn('push_subscriptions.id', $data['ids'])
            ->where('push_subscriptions.is_enabled', true)
            ->where(function($q){ $q->whereNull('users.push_muted')->orWhere('users.push_muted', false); })
            ->select('push_subscriptions.*')
            ->get();

        if ($subs->isEmpty()) {
            return response()->json(['sent' => 0, 'message' => 'No eligible subscriptions selected'], 400);
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);

        $payload = json_encode([
            'title' => (string) ($data['title'] ?? 'MaxMed'),
            'body' => (string) ($data['body'] ?? ''),
            'url' => (string) ($data['url'] ?? '/'),
        ]);

        $sent = 0;
        foreach ($subs as $sub) {
            $subscription = WebPushSubscription::create([
                'endpoint' => $sub->endpoint,
                'keys' => [ 'p256dh' => $sub->p256dh, 'auth' => $sub->auth ],
            ]);
            $report = $webPush->sendOneNotification($subscription, $payload);
            if ($report->isSuccess()) {
                $sent++;
            } else if (in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                DB::table('push_subscriptions')->where('id', $sub->id)->delete();
            }
        }

        return response()->json(['sent' => $sent, 'selected' => count($data['ids'])]);
    }

    public function toggleUserMute(Request $request, int $userId): JsonResponse
    {
        $admin = Auth::user();
        abort_unless($admin && method_exists($admin, 'isAdmin') && $admin->isAdmin(), 403);

        $target = DB::table('users')->where('id', $userId)->first();
        if (!$target) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $new = !((bool) ($target->push_muted ?? false));
        DB::table('users')->where('id', $userId)->update(['push_muted' => $new, 'updated_at' => now()]);
        return response()->json(['ok' => true, 'push_muted' => $new]);
    }

    public function listUser()
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $subs = DB::table('push_subscriptions')
            ->where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->get();

        return view('push.manage', [ 'subs' => $subs ]);
    }

    public function listAdmin(Request $request)
    {
        abort_unless(Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin(), 403);

        $query = DB::table('push_subscriptions')
            ->leftJoin('users', 'users.id', '=', 'push_subscriptions.user_id')
            ->select('push_subscriptions.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('push_subscriptions.updated_at');
        if ($request->filled('user_id')) {
            $query->where('push_subscriptions.user_id', (int) $request->input('user_id'));
        }
        $subs = $query->paginate(25)->appends($request->only('user_id'));

        return view('admin.push.manage', [ 'subs' => $subs ]);
    }

    public function toggle(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $sub = DB::table('push_subscriptions')->where('id', $id)->first();
        if (!$sub) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();
        if (!$isAdmin && (int) $sub->user_id !== (int) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        DB::table('push_subscriptions')->where('id', $id)->update([
            'is_enabled' => !$sub->is_enabled,
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true, 'is_enabled' => !$sub->is_enabled]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $sub = DB::table('push_subscriptions')->where('id', $id)->first();
        if (!$sub) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();
        if (!$isAdmin && (int) $sub->user_id !== (int) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        DB::table('push_subscriptions')->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    }

    public function testSingle(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $sub = DB::table('push_subscriptions')->where('id', $id)->first();
        if (!$sub) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();
        if (!$isAdmin && (int) $sub->user_id !== (int) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);

        $payload = json_encode([
            'title' => (string) ($request->input('title') ?? 'MaxMed'),
            'body' => (string) ($request->input('body') ?? 'Test notification'),
            'url' => (string) ($request->input('url') ?? '/'),
        ]);

        $subscription = WebPushSubscription::create([
            'endpoint' => $sub->endpoint,
            'keys' => [ 'p256dh' => $sub->p256dh, 'auth' => $sub->auth ],
        ]);

        $report = $webPush->sendOneNotification($subscription, $payload);
        $ok = $report->isSuccess();
        if (!$ok && in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
            DB::table('push_subscriptions')->where('id', $id)->delete();
        }

        return response()->json(['ok' => $ok]);
    }
}


