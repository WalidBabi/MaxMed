<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\SupplierInformation;
use App\Models\SupplierInvitation;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Carbon\Carbon;
use Illuminate\View\View as ViewContract;
use App\Notifications\SupplierAuthNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class SupplierRegistrationController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): ViewContract
    {
        $token = $request->query('token');
        $invitation = null;

        if ($token) {
            // Invitation-based registration
            $invitation = SupplierInvitation::where('token', $token)
                ->where('status', SupplierInvitation::STATUS_PENDING)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$invitation) {
                abort(403, 'Invalid or expired invitation token.');
            }
        }
        // Public registration (no token required)

        return View::make('auth.supplier-register', compact('invitation'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            Log::info('Supplier registration attempt', $request->all());

            // Validate based on whether this is invitation-based or public registration
            $validationRules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'company_name' => ['required', 'string', 'max:255'],
                'business_address' => ['required', 'string', 'max:500'],
                'phone_primary' => ['required', 'string', 'max:20'],
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
            ];

            $invitation = null;
            $isInvitationBased = false;

            // Check if this is invitation-based registration
            if ($request->has('token') && $request->token) {
                $validationRules['token'] = ['required', 'string'];
                $isInvitationBased = true;
                
                // Verify invitation token
                $invitation = SupplierInvitation::where('token', $request->token)
                    ->where('status', SupplierInvitation::STATUS_PENDING)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();

                if (!$invitation) {
                    return back()->withErrors(['error' => 'Invalid or expired invitation token.']);
                }

                // For invitation-based registration, verify email matches invitation
                if ($invitation->email !== $request->email) {
                    return back()->withErrors(['error' => 'The email address does not match the invitation.']);
                }
            }

            $request->validate($validationRules);

            // Get supplier role
            $supplierRole = Role::where('name', 'supplier')->first();
            if (!$supplierRole) {
                Log::error('Supplier role not found');
                return back()->withErrors(['error' => 'Supplier role not found. Please contact administrator.']);
            }

            try {
                // Start transaction
                DB::beginTransaction();

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id' => $supplierRole->id,
                ]);

                // Create supplier information
                SupplierInformation::create([
                    'user_id' => $user->id,
                    'company_name' => $request->company_name,
                    'business_address' => $request->business_address,
                    'phone_primary' => $request->phone_primary,
                    'status' => 'pending_approval',
                ]);

                // Update invitation status only if this is invitation-based registration
                if ($isInvitationBased && $invitation) {
                    $invitation->accept($user);
                }

                // Send notification to admin
                try {
                    $adminEmail = Config::get('mail.admin_email');
                    
                    if ($adminEmail) {
                        // Create a temporary admin object for notification
                        $admin = new User([
                            'email' => $adminEmail,
                            'name' => 'Admin',
                            'id' => 0
                        ]);
                    } else {
                        $admin = User::where('is_admin', true)
                            ->whereNotNull('email')
                            ->whereDoesntHave('role', function($query) {
                                $query->where('name', 'supplier');
                            })
                            ->first();
                    }
                    
                    if ($admin) {
                        Notification::send($admin, new SupplierAuthNotification($user, 'registered', 'Email'));
                        Log::info('Admin supplier notification sent successfully', ['user_id' => $user->id]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send supplier registration notification: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'environment' => app()->environment()
                    ]);
                    // Don't fail registration if admin notification fails
                }

                // Commit transaction
                DB::commit();

                // Log for debugging duplicate emails
                Log::info('Supplier registered, firing Registered event', ['user_id' => $user->id, 'email' => $user->email, 'type' => $isInvitationBased ? 'invitation' : 'public']);

                // Send verification email manually instead of using event to avoid duplicates
                try {
                    $user->sendEmailVerificationNotification();
                    Log::info('Supplier email verification notification sent successfully', ['user_id' => $user->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to send supplier email verification notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'environment' => app()->environment()
                    ]);
                    // Don't fail registration if email fails
                }
                
                // Note: Commenting out event firing to prevent duplicate emails
                // event(new Registered($user));

                Auth::login($user);
                
                // Set a session flag to show orders hint
                session()->put('show_orders_hint', true);

                // Redirect to email verification notice if email is not verified
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice')
                        ->with('message', 'Please verify your email address to complete your registration.');
                }

                return redirect(route('supplier.dashboard', absolute: false));
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Supplier registration database error', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'environment' => app()->environment()
                ]);
                
                if (app()->environment('production')) {
                    return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput($request->except('password'));
                }
                
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Supplier registration error occurred', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            
            // In production, don't expose detailed error messages
            if (app()->environment('production')) {
                return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput($request->except('password'));
            }
            
            throw $e; // Re-throw the exception to maintain the original error handling
        }
    }

    /**
     * Handle direct onboarding for invited suppliers.
     * This method allows invited suppliers to go directly to onboarding without registration.
     */
    public function invitationOnboarding(string $token): RedirectResponse|ViewContract
    {
        // Verify invitation token
        $invitation = SupplierInvitation::where('token', $token)
            ->where('status', SupplierInvitation::STATUS_PENDING)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$invitation) {
            abort(403, 'Invalid or expired invitation token.');
        }

        // Check if user already exists with this email
        $existingUser = User::where('email', $invitation->email)->first();
        
        if ($existingUser) {
            // User already exists, log them in and redirect to onboarding
            Auth::login($existingUser);
            
            // Regenerate session to ensure authentication is properly set
            request()->session()->regenerate();
            
            // Update invitation status
            $invitation->accept($existingUser);
            
            return redirect()->route('supplier.onboarding.company')
                ->with('success', 'Welcome back! Please complete your supplier profile.');
        }

        // User doesn't exist, create account automatically and redirect to onboarding
        try {
            DB::beginTransaction();

            // Get supplier role
            $supplierRole = Role::where('name', 'supplier')->first();
            if (!$supplierRole) {
                Log::error('Supplier role not found');
                return back()->withErrors(['error' => 'Supplier role not found. Please contact administrator.']);
            }

            // Create user with temporary password
            $tempPassword = Str::random(12);
            $user = User::create([
                'name' => $invitation->contact_name,
                'email' => $invitation->email,
                'password' => Hash::make($tempPassword),
                'role_id' => $supplierRole->id,
            ]);

            // Create supplier information
            SupplierInformation::create([
                'user_id' => $user->id,
                'company_name' => $invitation->company_name ?? '',
                'business_address' => '',
                'phone_primary' => '',
                'status' => 'pending_approval',
            ]);

            // Update invitation status
            $invitation->accept($user);

            // Send notification to admin
            try {
                $adminEmail = Config::get('mail.admin_email');
                
                if ($adminEmail) {
                    $admin = new User([
                        'email' => $adminEmail,
                        'name' => 'Admin',
                        'id' => 0
                    ]);
                } else {
                    $admin = User::where('is_admin', true)
                        ->whereNotNull('email')
                        ->whereDoesntHave('role', function($query) {
                            $query->where('name', 'supplier');
                        })
                        ->first();
                }
                
                if ($admin) {
                    Notification::send($admin, new SupplierAuthNotification($user, 'invited', 'Email'));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send supplier invitation notification: ' . $e->getMessage());
            }

            DB::commit();

            // Log in the user
            Auth::login($user);
            
            // Regenerate session to ensure authentication is properly set
            request()->session()->regenerate();

            // Send email verification notification instead of password reset
            $user->sendEmailVerificationNotification();

            // Send password reset email so supplier can set their own password
            Password::sendResetLink(['email' => $user->email]);

            Log::info('Supplier invited and auto-registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'invitation_id' => $invitation->id
            ]);

            return redirect()->route('supplier.onboarding.company')
                ->with('success', 'Welcome! Your account has been created. Please complete your supplier profile, verify your email address, and check your email for password setup instructions.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error during supplier invitation onboarding', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'invitation_id' => $invitation->id
            ]);
            
            return back()->withErrors(['error' => 'An error occurred while setting up your account. Please try again.']);
        }
    }
} 