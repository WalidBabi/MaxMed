<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AuthNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'g-recaptcha-response' => [
                    // Only require reCAPTCHA in production environment
                    app()->environment('production') ? 'required' : 'nullable',
                    function ($attribute, $value, $fail) {
                        // Skip reCAPTCHA validation in development environment
                        if (!app()->environment('production')) {
                            Log::info('Skipping reCAPTCHA validation in development environment');
                            return;
                        }
                        
                        if (empty($value)) {
                            $fail('The reCAPTCHA verification is required.');
                            return;
                        }
                        
                        try {
                            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                                'secret' => config('services.recaptcha.secret_key'),
                                'response' => $value,
                                'remoteip' => request()->ip(),
                            ]);
                            
                            Log::info('reCAPTCHA response', ['success' => $response->json('success')]);
                            
                            if (!$response->json('success')) {
                                $fail('The reCAPTCHA verification failed. Please try again.');
                            }
                        } catch (\Exception $e) {
                            Log::error('reCAPTCHA verification failed', [
                                'error' => $e->getMessage(),
                                'environment' => app()->environment()
                            ]);
                            
                            if (app()->environment('production')) {
                                $fail('The reCAPTCHA verification is temporarily unavailable. Please try again.');
                            }
                        }
                    }
                ],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Log for debugging duplicate emails
            Log::info('Customer registered, firing Registered event', ['user_id' => $user->id, 'email' => $user->email]);
            
            // Send verification email manually instead of using event to avoid duplicates
            try {
                $user->sendEmailVerificationNotification();
                Log::info('Email verification notification sent successfully', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send email verification notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'environment' => app()->environment()
                ]);
                // Don't fail registration if email fails
            }
            
            // Note: Commenting out event firing to prevent duplicate emails
            // event(new Registered($user));
            
            // Send notification to admin
            try {
                $adminEmail = config('mail.admin_email');
                
                if ($adminEmail) {
                    // Create a temporary admin object for notification
                    $admin = new User();
                    $admin->email = $adminEmail;
                    $admin->name = 'Admin';
                    $admin->id = 0;
                } else {
                    $admin = User::where('is_admin', true)
                        ->whereNotNull('email')
                        ->whereDoesntHave('role', function($query) {
                            $query->where('name', 'supplier');
                        })
                        ->first();
                }
                
                if ($admin) {
                    Notification::send($admin, new AuthNotification($user, 'registered', 'Email'));
                    Log::info('Admin notification sent successfully', ['user_id' => $user->id]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send registration notification: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'environment' => app()->environment()
                ]);
                // Don't fail registration if admin notification fails
            }

            Auth::login($user);

            // Set a session flag to show orders hint
            session()->put('show_orders_hint', true);

            // Redirect to email verification notice if email is not verified
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('message', 'Please verify your email address to complete your registration.');
            }

            return redirect(route('dashboard', absolute: false));
            
        } catch (\Exception $e) {
            Log::error('Registration error occurred', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            
            // In production, don't expose detailed error messages
            if (app()->environment('production')) {
                return back()->withErrors([
                    'email' => 'Registration failed. Please try again.'
                ])->withInput($request->except('password'));
            }
            
            throw $e; // Re-throw the exception to maintain the original error handling
        }
    }
}
