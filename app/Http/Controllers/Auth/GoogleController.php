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
            Auth::login($user);

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function handleOneTap(Request $request)
    {
        try {
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
            Auth::login($user);
            
            return response()->json([
                'redirect' => route('dashboard')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Google One Tap Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to authenticate with Google. Please try again.'
            ], 401);
        }
    }
    
    protected function findOrCreateUser($googleUser)
    {
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]);
        }
        
        return $user;
    }
    

}
