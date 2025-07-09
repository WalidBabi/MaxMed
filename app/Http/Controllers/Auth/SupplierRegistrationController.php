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
        Log::info('=== SUPPLIER REGISTRATION PROCESS STARTED ===', [
            'email' => $request->email ?? 'unknown',
            'name' => $request->name ?? 'unknown',
            'company_name' => $request->company_name ?? 'unknown',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'environment' => app()->environment(),
            'timestamp' => now()->toISOString()
        ]);

        try {
            Log::info('Step 1: Starting supplier registration validation');
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
                            Log::info('Step 1.1: Validating reCAPTCHA for supplier registration');
                            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                                'secret' => config('services.recaptcha.secret_key'),
                                'response' => $value,
                                'remoteip' => request()->ip(),
                            ]);
                            
                            Log::info('Step 1.1: reCAPTCHA response for supplier', ['success' => $response->json('success')]);
                            
                            if (!$response->json('success')) {
                                $fail('The reCAPTCHA verification failed. Please try again.');
                            }
                        } catch (\Exception $e) {
                            Log::error('Step 1.1: reCAPTCHA verification failed for supplier', [
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

            Log::info('Step 1.2: Checking for invitation-based registration');
            // Check if this is invitation-based registration
            if ($request->has('token') && $request->token) {
                Log::info('Step 1.2: Invitation-based registration detected', ['token' => $request->token]);
                $validationRules['token'] = ['required', 'string'];
                $isInvitationBased = true;
                
                // Verify invitation token
                $invitation = SupplierInvitation::where('token', $request->token)
                    ->where('status', SupplierInvitation::STATUS_PENDING)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();

                if (!$invitation) {
                    Log::error('Step 1.2: Invalid or expired invitation token', ['token' => $request->token]);
                    return back()->withErrors(['error' => 'Invalid or expired invitation token.']);
                }

                Log::info('Step 1.2: Valid invitation found', ['invitation_id' => $invitation->id]);

                // For invitation-based registration, verify email matches invitation
                if ($invitation->email !== $request->email) {
                    Log::error('Step 1.2: Email does not match invitation', [
                        'invitation_email' => $invitation->email,
                        'provided_email' => $request->email
                    ]);
                    return back()->withErrors(['error' => 'The email address does not match the invitation.']);
                }
            } else {
                Log::info('Step 1.2: Public registration (no invitation)');
            }

            Log::info('Step 1.3: Running validation');
            $request->validate($validationRules);
            Log::info('Step 1.3: Validation passed');

            Log::info('Step 2: Getting supplier role');
            // Get supplier role
            $supplierRole = Role::where('name', 'supplier')->first();
            if (!$supplierRole) {
                Log::error('Step 2: Supplier role not found');
                return back()->withErrors(['error' => 'Supplier role not found. Please contact administrator.']);
            }
            Log::info('Step 2: Supplier role found', ['role_id' => $supplierRole->id]);

            try {
                Log::info('Step 3: Starting database transaction');
                // Start transaction
                DB::beginTransaction();

                Log::info('Step 3.1: Creating user');
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id' => $supplierRole->id,
                ]);
                Log::info('Step 3.1: User created successfully', ['user_id' => $user->id]);

                Log::info('Step 3.2: Creating supplier information');
                // Create supplier information
                SupplierInformation::create([
                    'user_id' => $user->id,
                    'company_name' => $request->company_name,
                    'business_address' => $request->business_address,
                    'phone_primary' => $request->phone_primary,
                    'status' => 'pending_approval',
                ]);
                Log::info('Step 3.2: Supplier information created successfully', ['user_id' => $user->id]);

                // Update invitation status only if this is invitation-based registration
                if ($isInvitationBased && $invitation) {
                    Log::info('Step 3.3: Updating invitation status', ['invitation_id' => $invitation->id]);
                    $invitation->accept($user);
                    Log::info('Step 3.3: Invitation accepted successfully');
                }

                Log::info('Step 4: Sending admin notification');
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
                        Log::info('Step 4: Admin supplier notification sent successfully', ['user_id' => $user->id]);
                    } else {
                        Log::warning('Step 4: No admin found to send supplier notification to', ['user_id' => $user->id]);
                    }
                } catch (\Exception $e) {
                    Log::error('Step 4: Failed to send supplier registration notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'environment' => app()->environment()
                    ]);
                    // Don't fail registration if admin notification fails
                }

                Log::info('Step 5: Committing database transaction');
                // Commit transaction
                DB::commit();
                Log::info('Step 5: Database transaction committed successfully');

                // Log for debugging duplicate emails
                Log::info('Step 6: Sending email verification notification', [
                    'user_id' => $user->id, 
                    'email' => $user->email, 
                    'type' => $isInvitationBased ? 'invitation' : 'public'
                ]);

                // Send verification email manually instead of using event to avoid duplicates
                try {
                    $user->sendEmailVerificationNotification();
                    Log::info('Step 6: Supplier email verification notification sent successfully', ['user_id' => $user->id]);
                } catch (\Exception $e) {
                    Log::error('Step 6: Failed to send supplier email verification notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'environment' => app()->environment()
                    ]);
                    // Don't fail registration if email fails
                }
                
                // Note: Commenting out event firing to prevent duplicate emails
                // event(new Registered($user));

                Log::info('Step 7: Logging in supplier user');
                Auth::login($user);
                Log::info('Step 7: Supplier user logged in successfully', ['user_id' => $user->id]);
                
                // Set a session flag to show orders hint
                session()->put('show_orders_hint', true);
                Log::info('Step 7: Session flag set');

                Log::info('Step 8: Checking email verification status');
                // Redirect to email verification notice if email is not verified
                if (!$user->hasVerifiedEmail()) {
                    Log::info('Step 8: Supplier email not verified, redirecting to verification notice', ['user_id' => $user->id]);
                    return redirect()->route('verification.notice')
                        ->with('message', 'Please verify your email address to complete your registration.');
                }

                Log::info('Step 8: Supplier email verified, redirecting to supplier dashboard', ['user_id' => $user->id]);
                return redirect(route('supplier.dashboard', absolute: false));
                
            } catch (\Exception $e) {
                Log::error('Step 3-8: Database transaction failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'environment' => app()->environment()
                ]);
                
                DB::rollBack();
                Log::info('Database transaction rolled back');
                
                if (app()->environment('production')) {
                    return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput($request->except('password'));
                }
                
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('=== SUPPLIER REGISTRATION PROCESS FAILED ===', [
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