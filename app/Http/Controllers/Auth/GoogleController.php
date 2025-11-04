<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Oauth2;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AuthNotification;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = $this->findOrCreateUser($googleUser);
            session()->put('login_notification_intent', true);
            Auth::login($user);

            // Set a session flag to show orders hint
            session()->put('show_orders_hint', true);

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function handleOneTap(Request $request)
    {
        try {
            // Add development logging
            if (app()->environment('local')) {
                Log::info('Google One Tap attempt in development', [
                    'request_data' => $request->all(),
                    'user_agent' => $request->userAgent(),
                    'ip' => $request->ip()
                ]);
            }
            
            // Set headers to prevent indexing for all responses
            $headers = [
                'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ];
            
            // Verify CSRF token
            $request->validate([
                'g_csrf_token' => 'required|string',
                'credential' => 'required|string',
            ]);

            if ($request->g_csrf_token !== csrf_token()) {
                throw new \Exception('Invalid CSRF token');
            }

            $credential = $request->input('credential');
            
            // Initialize Google Client
            $client = new Google_Client([
                'client_id' => config('services.google.client_id'),
            ]);
            
            // Get the ID token from the credential
            $payload = $client->verifyIdToken($credential);
            
            if (!$payload) {
                throw new \Exception('Invalid ID token');
            }
            
            // The token is valid, proceed with login/registration
            $googleUser = (object) [
                'name' => $payload['name'] ?? $payload['email'],
                'email' => $payload['email'],
                'id' => $payload['sub'],
                'avatar' => $payload['picture'] ?? null,
            ];
            
            $user = $this->findOrCreateUser($googleUser);
            session()->put('login_notification_intent', true);
            Auth::login($user);
            
            // Set a session flag to show orders hint
            session()->put('show_orders_hint', true);
            
            return response()->json([
                'redirect' => route('dashboard')
            ])->withHeaders($headers);
            
        } catch (\Exception $e) {
            Log::error('Google One Tap Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to authenticate with Google. Please try again.'
            ], 401)->withHeaders($headers);
        }
    }

    public function handleOneTapGet()
    {
        // Set proper headers to prevent indexing
        return response()->json([
            'error' => 'This endpoint only accepts POST requests for Google One Tap authentication.',
            'message' => 'Please use the POST method with proper authentication credentials.'
        ], 405)->withHeaders([
            'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
    
    protected function findOrCreateUser($googleUser)
    {
        $user = User::where('email', $googleUser->email)->first();
        $isNewUser = false;

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]);
            $isNewUser = true;
        }
        
        // Send registration notification to admin for new users
        if ($isNewUser) {
            $admin = User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->first();
            if ($admin) {
                Notification::send($admin, new AuthNotification($user, 'registered', 'Google OAuth'));
            }
        }
        
        return $user;
    }
    

}
