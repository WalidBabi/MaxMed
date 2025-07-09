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
        try {
            Log::info('Login attempt started', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'environment' => app()->environment()
            ]);

            // Check database connection
            try {
                DB::connection()->getPdo();
                Log::info('Database connection successful');
            } catch (\Exception $e) {
                Log::error('Database connection failed', [
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

            $request->authenticate();
            Log::info('Authentication successful', ['email' => $request->email]);

            // Check session configuration
            try {
                $request->session()->regenerate();
                Log::info('Session regenerated successfully');
            } catch (\Exception $e) {
                Log::error('Session regeneration failed', [
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
            
            // Clear any problematic intended URLs that might redirect to API endpoints
            $this->clearProblematicIntendedUrls($request);
            
            // Set a session flag to show orders hint
            $request->session()->put('show_orders_hint', true);
            
            $user = Auth::user();
            
            // Validate user and role
            if (!$user) {
                Log::error('User not found after authentication');
                
                if (app()->environment('production')) {
                    return back()->withErrors([
                        'email' => 'Authentication failed. Please try again.'
                    ]);
                }
                
                throw new \Exception('User not found after authentication');
            }

            // Check if user has a role
            if (!$user->role) {
                Log::warning('User has no role assigned', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            Log::info('User retrieved from Auth', [
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
                Log::info('Cleared problematic intended URL', ['intended_url' => $intendedUrl]);
            }
            
            Log::info('Determining redirect route', [
                'user_id' => $user->id,
                'is_admin' => $user->isAdmin(),
                'is_supplier' => $user->isSupplier(),
                'should_clear_intended' => $shouldClearIntended
            ]);
            
            if ($user->isAdmin()) {
                $route = $shouldClearIntended ? 'admin.dashboard' : 'admin.dashboard';
                Log::info('Redirecting admin user', ['route' => $route, 'user_id' => $user->id]);
                return $shouldClearIntended ? 
                    redirect()->route('admin.dashboard') : 
                    redirect()->intended(route('admin.dashboard'));
            }

            // Check if user is a supplier
            if ($user->isSupplier()) {
                $route = $shouldClearIntended ? 'supplier.dashboard' : 'supplier.dashboard';
                Log::info('Redirecting supplier user', ['route' => $route, 'user_id' => $user->id]);
                return $shouldClearIntended ? 
                    redirect()->route('supplier.dashboard') : 
                    redirect()->intended(route('supplier.dashboard'));
            }

            $route = $shouldClearIntended ? 'dashboard' : 'dashboard';
            Log::info('Redirecting regular user', ['route' => $route, 'user_id' => $user->id]);
            return $shouldClearIntended ? 
                redirect()->route('dashboard') : 
                redirect()->intended(route('dashboard'));
                
        } catch (\Exception $e) {
            Log::error('Login error occurred', [
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
