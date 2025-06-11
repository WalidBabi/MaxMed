<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
            
            // Get Google's public keys
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'verify' => true,
            ]);

            $response = $client->get('https://www.googleapis.com/oauth2/v3/certs');
            $keys = json_decode($response->getBody()->getContents(), true);
            
            // Decode the JWT header to get the key ID
            $jwtParts = explode('.', $credential);
            if (count($jwtParts) !== 3) {
                throw new \Exception('Invalid JWT format');
            }

            $header = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $jwtParts[0])), true);
            if (!$header || !isset($header['kid'])) {
                throw new \Exception('Invalid JWT header');
            }
            $keyId = $header['kid'];
            
            // Find the matching key
            $publicKey = null;
            foreach ($keys['keys'] as $key) {
                if (isset($key['kid']) && $key['kid'] === $keyId) {
                    $publicKey = $key;
                    break;
                }
            }
            
            if (!$publicKey) {
                throw new \Exception('Public key not found');
            }
            
            // Verify the JWT token
            $jwt = JWT::decode($credential, new Key(
                $this->convertKeyToPem($publicKey),
                'RS256'
            ));

            // Verify the token audience
            if ($jwt->aud !== config('services.google.client_id')) {
                throw new \Exception('Invalid audience');
            }

            // Verify the token issuer
            if ($jwt->iss !== 'https://accounts.google.com' && $jwt->iss !== 'accounts.google.com') {
                throw new \Exception('Invalid issuer');
            }
            
            // The token is valid, proceed with login/registration
            $googleUser = (object) [
                'name' => $jwt->name ?? $jwt->email,
                'email' => $jwt->email,
                'id' => $jwt->sub,
                'avatar' => $jwt->picture ?? null,
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
                'error' => $e->getMessage()
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
    
    protected function convertKeyToPem($key)
    {
        if (!isset($key['n']) || !isset($key['e'])) {
            throw new \Exception('Invalid key format: missing n or e parameters');
        }

        // Convert the key components to PEM format
        $modulus = $this->base64urlToBase64($key['n']);
        $exponent = $this->base64urlToBase64($key['e']);
        
        // Create the public key in PEM format
        $pem = "-----BEGIN PUBLIC KEY-----\n";
        $pem .= "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A\n";
        $pem .= chunk_split($modulus, 64, "\n");
        $pem .= $exponent . "\n";
        $pem .= "-----END PUBLIC KEY-----";
        
        return $pem;
    }
    
    protected function base64urlToBase64($input) 
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return strtr($input, [
            '-' => '+',
            '_' => '/',
            '~' => '='
        ]);
    }
}
