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
    public function create(Request $request): View
    {
        // Ensure a fresh session and CSRF token are created and sent to the browser
        // This helps avoid rare first-attempt 419 issues when the session cookie was not yet set
        $request->session()->put('login_page_viewed', true);
        $request->session()->regenerateToken();

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
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
            'app_debug' => config('app.debug'),
            'app_env' => config('app.env')
        ]);

        try {
            Log::info('Step 1: Starting login validation', [
                'email' => $request->email,
                'has_password' => !empty($request->password),
                'request_method' => $request->method(),
                'request_path' => $request->path()
            ]);

            // Check database connection
            try {
                Log::info('Step 2: Testing database connection');
                $pdo = DB::connection()->getPdo();
                Log::info('Step 2: Database connection successful', [
                    'connection_name' => DB::connection()->getName(),
                    'database_name' => DB::connection()->getDatabaseName()
                ]);
            } catch (\Exception $e) {
                Log::error('Step 2: Database connection failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if (app()->environment('production')) {
                    return back()->withErrors([
                        'email' => 'Service temporarily unavailable. Please try again later.'
                    ]);
                }
                
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }

            Log::info('Step 3: Starting authentication process');
            
            // Add pre-authentication logging
            Log::info('Step 3a: Before calling authenticate method', [
                'email' => $request->email,
                'auth_guard' => config('auth.defaults.guard'),
                'auth_provider' => config('auth.guards.web.provider'),
                'user_model' => config('auth.providers.users.model')
            ]);
            
            $request->authenticate();
            
            Log::info('Step 3b: Authentication successful', [
                'email' => $request->email,
                'auth_id' => Auth::id(),
                'auth_check' => Auth::check()
            ]);

            // Check session configuration
            try {
                Log::info('Step 4: Testing session regeneration', [
                    'session_driver' => config('session.driver'),
                    'session_lifetime' => config('session.lifetime'),
                    'session_id_before' => session()->getId()
                ]);
                
                $request->session()->regenerate();
                
                Log::info('Step 4: Session regenerated successfully', [
                    'session_id_after' => session()->getId()
                ]);
            } catch (\Exception $e) {
                Log::error('Step 4: Session regeneration failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
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
                Log::error('Step 6: User not found after authentication', [
                    'auth_id' => Auth::id(),
                    'auth_check' => Auth::check()
                ]);
                
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
                'user_role_id' => $user->role_id,
                'has_role' => $user->role ? 'yes' : 'no',
                'role_name' => $user->role ? $user->role->name : 'no role'
            ]);

            // Check if user has a role
            if (!$user->role) {
                Log::warning('Step 6: User has no role assigned', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role_id' => $user->role_id
                ]);
            }

            // Test role methods before using them
            try {
                Log::info('Step 7: Testing role methods', [
                    'user_id' => $user->id,
                    'testing_isAdmin' => 'about to call isAdmin()'
                ]);
                
                $isAdmin = $user->isAdmin();
                
                Log::info('Step 7: isAdmin() method completed', [
                    'user_id' => $user->id,
                    'is_admin' => $isAdmin,
                    'testing_isSupplier' => 'about to call isSupplier()'
                ]);
                
                $isSupplier = $user->isSupplier();
                
                Log::info('Step 7: isSupplier() method completed', [
                    'user_id' => $user->id,
                    'is_supplier' => $isSupplier
                ]);
                
            } catch (\Exception $e) {
                Log::error('Step 7: Role method test failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            Log::info('Step 7: Checking user permissions', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role ? $user->role->name : 'no role',
                'is_admin' => $isAdmin,
                'is_supplier' => $isSupplier
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
                'is_admin' => $isAdmin,
                'is_supplier' => $isSupplier,
                'should_clear_intended' => $shouldClearIntended
            ]);
            
            if ($isAdmin) {
                $route = $shouldClearIntended ? 'admin.dashboard' : 'admin.dashboard';
                Log::info('Step 8: Redirecting admin user', ['route' => $route, 'user_id' => $user->id]);
                
                try {
                    $redirect = $shouldClearIntended ? 
                        redirect()->route('admin.dashboard') : 
                        redirect()->intended(route('admin.dashboard'));
                    
                    Log::info('Step 8: Admin redirect created successfully', [
                        'route' => $route,
                        'user_id' => $user->id,
                        'redirect_url' => $redirect->getTargetUrl()
                    ]);
                    
                    return $redirect;
                } catch (\Exception $e) {
                    Log::error('Step 8: Admin redirect failed', [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            // Check if user is a supplier
            if ($isSupplier) {
                $route = $shouldClearIntended ? 'supplier.dashboard' : 'supplier.dashboard';
                Log::info('Step 8: Redirecting supplier user', ['route' => $route, 'user_id' => $user->id]);
                
                try {
                    $redirect = $shouldClearIntended ? 
                        redirect()->route('supplier.dashboard') : 
                        redirect()->intended(route('supplier.dashboard'));
                    
                    Log::info('Step 8: Supplier redirect created successfully', [
                        'route' => $route,
                        'user_id' => $user->id,
                        'redirect_url' => $redirect->getTargetUrl()
                    ]);
                    
                    return $redirect;
                } catch (\Exception $e) {
                    Log::error('Step 8: Supplier redirect failed', [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
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
                    'user_id' => $user->id,
                    'redirect_url' => $redirect->getTargetUrl()
                ]);
                
                return $redirect;
            } catch (\Exception $e) {
                Log::error('Step 8: Regular user redirect failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
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
                'environment' => app()->environment(),
                'session_id' => session()->getId(),
                'auth_check' => Auth::check(),
                'auth_id' => Auth::id()
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
     * Debug method to test authentication flow
     */
    public function debug(Request $request)
    {
        Log::info('=== AUTHENTICATION DEBUG START ===');
        
        try {
            // Test 1: Check if user is authenticated
            Log::info('Debug Step 1: Authentication check', [
                'auth_check' => Auth::check(),
                'auth_id' => Auth::id()
            ]);
            
            if (!Auth::check()) {
                return response()->json(['error' => 'User not authenticated']);
            }
            
            // Test 2: Get user and role
            $user = Auth::user();
            Log::info('Debug Step 2: User retrieval', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role_id' => $user->role_id
            ]);
            
            // Test 3: Check role relationship
            Log::info('Debug Step 3: Role relationship check', [
                'has_role' => $user->role ? 'yes' : 'no',
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->name : 'no role'
            ]);
            
            // Test 4: Test role methods
            try {
                $isAdmin = $user->isAdmin();
                Log::info('Debug Step 4a: isAdmin() method', [
                    'result' => $isAdmin,
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Debug Step 4a: isAdmin() failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'isAdmin() method failed: ' . $e->getMessage()]);
            }
            
            try {
                $isSupplier = $user->isSupplier();
                Log::info('Debug Step 4b: isSupplier() method', [
                    'result' => $isSupplier,
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Debug Step 4b: isSupplier() failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'isSupplier() method failed: ' . $e->getMessage()]);
            }
            
            // Test 5: Check route existence
            Log::info('Debug Step 5: Route existence check');
            
            $routes = [];
            try {
                $routes['dashboard'] = route('dashboard');
                Log::info('Debug Step 5a: dashboard route exists', ['url' => $routes['dashboard']]);
            } catch (\Exception $e) {
                Log::error('Debug Step 5a: dashboard route missing', ['error' => $e->getMessage()]);
                $routes['dashboard'] = 'MISSING';
            }
            
            try {
                $routes['admin.dashboard'] = route('admin.dashboard');
                Log::info('Debug Step 5b: admin.dashboard route exists', ['url' => $routes['admin.dashboard']]);
            } catch (\Exception $e) {
                Log::error('Debug Step 5b: admin.dashboard route missing', ['error' => $e->getMessage()]);
                $routes['admin.dashboard'] = 'MISSING';
            }
            
            try {
                $routes['supplier.dashboard'] = route('supplier.dashboard');
                Log::info('Debug Step 5c: supplier.dashboard route exists', ['url' => $routes['supplier.dashboard']]);
            } catch (\Exception $e) {
                Log::error('Debug Step 5c: supplier.dashboard route missing', ['error' => $e->getMessage()]);
                $routes['supplier.dashboard'] = 'MISSING';
            }
            
            // Test 6: Test redirect logic
            Log::info('Debug Step 6: Redirect logic test', [
                'is_admin' => $isAdmin,
                'is_supplier' => $isSupplier,
                'expected_route' => $isAdmin ? 'admin.dashboard' : ($isSupplier ? 'supplier.dashboard' : 'dashboard')
            ]);
            
            Log::info('=== AUTHENTICATION DEBUG END ===');
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role ? $user->role->name : 'no role'
                ],
                'permissions' => [
                    'is_admin' => $isAdmin,
                    'is_supplier' => $isSupplier
                ],
                'routes' => $routes
            ]);
            
        } catch (\Exception $e) {
            Log::error('=== AUTHENTICATION DEBUG FAILED ===', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Debug failed: ' . $e->getMessage()]);
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
