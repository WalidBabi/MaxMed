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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log for debugging duplicate emails
        Log::info('Customer registered, firing Registered event', ['user_id' => $user->id, 'email' => $user->email]);
        
        // Send verification email manually instead of using event to avoid duplicates
        $user->sendEmailVerificationNotification();
        
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
            }
        } catch (\Exception $e) {
            Log::error('Failed to send registration notification: ' . $e->getMessage());
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
    }
}
