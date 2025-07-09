<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        Log::info('=== LOGIN PROCESS STARTED ===', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'environment' => app()->environment(),
            'timestamp' => now()->toISOString()
        ]);

        try {
            Log::info('Step 1: Starting login validation', [
                'email' => $request->email,
                'has_password' => !empty($request->password)
            ]);

            // Check database connection
            try {
                Log::info('Step 2: Testing database connection');
                DB::connection()->getPdo();
                Log::info('Step 2: Database connection successful');
            } catch (\Exception $e) {
                Log::error('Step 2: Database connection failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                if (app()->environment('production')) {
                    return back()->withErrors([
                        'email' => 'Service temporarily unavailable. Please try again later.'
                    ]);
                }
                
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }

            Log::info('Step 3: Starting authentication process');
            $request->authenticate();
            Log::info('Step 3: Authentication successful', ['email' => $request->email]);

            // Check session configuration
            try {
                Log::info('Step 4: Testing session regeneration');
                $request->session()->regenerate();
                Log::info('Step 4: Session regenerated successfully');
            } catch (\Exception $e) {
                Log::error('Step 4: Session regeneration failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                if (app()->environment('production')) {
                    return back()->withErrors([
                        'email' => 'Session error. Please try again.'
                    ]);
                }
                
                throw new \Exception('Session regeneration failed: ' . $e->getMessage());
            }
            
            Log::info('Step 5: Clearing problematic intended URLs');
            // Clear any problematic intended URLs that might redirect to API endpoints
            $this->clearProblematicIntendedUrls($request);
            
            // Set a session flag to show orders hint
            $request->session()->put('show_orders_hint', true);
            
            Log::info('Step 6: Getting authenticated user');
            $user = Auth::user();
            
            // Validate user and role
            if (!$user) {
                Log::error('Step 6: User not found after authentication');
                
                if (app()->environment('production')) {
                    return back()->withErrors([
                        'email' => 'Authentication failed. Please try again.'
                    ]);
                }
                
                throw new \Exception('User not found after authentication');
            }

            Log::info('Step 6: User retrieved successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'has_role' => $user->role ? 'yes' : 'no',
                'role_name' => $user->role ? $user->role->name : 'no role'
            ]);

            // Check if user has a role
            if (!$user->role) {
                Log::warning('Step 6: User has no role assigned', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            Log::info('Step 7: Checking user permissions', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role ? $user->role->name : 'no role',
                'is_admin' => $user->isAdmin(),
                'is_supplier' => $user->isSupplier()
            ]);
            
            // Clear the intended URL if it's an API endpoint or notification check
            $intendedUrl = session()->get('url.intended');
            $shouldClearIntended = $intendedUrl && (
                str_contains($intendedUrl, '/notifications/check-new') ||
                str_contains($intendedUrl, '/api/') ||
                str_contains($intendedUrl, '.json') ||
                str_contains($intendedUrl, '/count') ||
                str_contains($intendedUrl, '/stream')
            );
            
            if ($shouldClearIntended) {
                session()->forget('url.intended');
                Log::info('Step 7: Cleared problematic intended URL', ['intended_url' => $intendedUrl]);
            }
            
            Log::info('Step 8: Determining redirect route', [
                'user_id' => $user->id,
                'is_admin' => $user->isAdmin(),
                'is_supplier' => $user->isSupplier(),
                'should_clear_intended' => $shouldClearIntended
            ]);
            
            if ($user->isAdmin()) {
                $route = $shouldClearIntended ? 'admin.dashboard' : 'admin.dashboard';
                Log::info('Step 8: Redirecting admin user', ['route' => $route, 'user_id' => $user->id]);
                
                try {
                    $redirect = $shouldClearIntended ? 
                        redirect()->route('admin.dashboard') : 
                        redirect()->intended(route('admin.dashboard'));
                    
                    Log::info('Step 8: Admin redirect created successfully', [
                        'route' => $route,
                        'user_id' => $user->id
                    ]);
                    
                    return $redirect;
                } catch (\Exception $e) {
                    Log::error('Step 8: Admin redirect failed', [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    throw $e;
                }
            }

            // Check if user is a supplier
            if ($user->isSupplier()) {
                $route = $shouldClearIntended ? 'supplier.dashboard' : 'supplier.dashboard';
                Log::info('Step 8: Redirecting supplier user', ['route' => $route, 'user_id' => $user->id]);
                
                try {
                    $redirect = $shouldClearIntended ? 
                        redirect()->route('supplier.dashboard') : 
                        redirect()->intended(route('supplier.dashboard'));
                    
                    Log::info('Step 8: Supplier redirect created successfully', [
                        'route' => $route,
                        'user_id' => $user->id
                    ]);
                    
                    return $redirect;
                } catch (\Exception $e) {
                    Log::error('Step 8: Supplier redirect failed', [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    throw $e;
                }
            }

            $route = $shouldClearIntended ? 'dashboard' : 'dashboard';
            Log::info('Step 8: Redirecting regular user', ['route' => $route, 'user_id' => $user->id]);
            
            try {
                $redirect = $shouldClearIntended ? 
                    redirect()->route('dashboard') : 
                    redirect()->intended(route('dashboard'));
                
                Log::info('Step 8: Regular user redirect created successfully', [
                    'route' => $route,
                    'user_id' => $user->id
                ]);
                
                return $redirect;
            } catch (\Exception $e) {
                Log::error('Step 8: Regular user redirect failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw $e;
            }
                
        } catch (\Exception $e) {
            Log::error('=== LOGIN PROCESS FAILED ===', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'environment' => app()->environment()
            ]);
            
            // In production, don't expose detailed error messages
            if (app()->environment('production')) {
                return back()->withErrors([
                    'email' => 'Login failed. Please check your credentials and try again.'
                ]);
            }
            
            throw $e; // Re-throw the exception to maintain the original error handling
        }
    }

    /**
     * Clear problematic intended URLs from session
     */
    private function clearProblematicIntendedUrls(Request $request): void
    {
        $problematicPatterns = [
            'notifications/check-new',
            'notifications/count',
            'notifications/stream',
            '/api/',
            '.json',
            '.xml'
        ];
        
        $intendedUrl = $request->session()->get('url.intended');
        
        if ($intendedUrl) {
            foreach ($problematicPatterns as $pattern) {
                if (str_contains($intendedUrl, $pattern)) {
                    $request->session()->forget('url.intended');
                    break;
                }
            }
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
