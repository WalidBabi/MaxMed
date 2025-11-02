<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PushNotificationToken;
use Illuminate\Support\Facades\Log;

class AuthenticatePushToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-Push-Token');
        
        if (!$token) {
            Log::warning('Push notification request without token', [
                'ip' => $request->ip(),
                'url' => $request->url(),
            ]);
            return response()->json(['error' => 'Missing push notification token'], 401);
        }

        // Hash the token to match stored hash
        $hashedToken = hash('sha256', $token);
        
        $pushToken = PushNotificationToken::where('token', $hashedToken)->first();
        
        if (!$pushToken) {
            Log::warning('Invalid push notification token', [
                'ip' => $request->ip(),
                'url' => $request->url(),
            ]);
            return response()->json(['error' => 'Invalid push notification token'], 401);
        }

        if ($pushToken->isExpired()) {
            Log::warning('Expired push notification token', [
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_id' => $pushToken->user_id,
            ]);
            return response()->json(['error' => 'Push notification token has expired'], 401);
        }

        // Touch last used
        $pushToken->touchLastUsed();

        // Attach user to request for easy access
        $request->setUserResolver(function () use ($pushToken) {
            return $pushToken->user;
        });

        return $next($request);
    }
}
