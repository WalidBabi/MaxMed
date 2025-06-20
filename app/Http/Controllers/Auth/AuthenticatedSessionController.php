<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->authenticate();

        $request->session()->regenerate();
        
        // Clear any problematic intended URLs that might redirect to API endpoints
        $this->clearProblematicIntendedUrls($request);
        
        // Set a session flag to show orders hint
        $request->session()->put('show_orders_hint', true);
        
        $user = Auth::user();
        
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
        }
        
        if ($user->is_admin == 1) {
            return $shouldClearIntended ? 
                redirect()->route('admin.dashboard') : 
                redirect()->intended(route('admin.dashboard'));
        }

        // Check if user is a supplier
        if ($user->role && $user->role->name === 'supplier') {
            return $shouldClearIntended ? 
                redirect()->route('supplier.dashboard') : 
                redirect()->intended(route('supplier.dashboard'));
        }

        return $shouldClearIntended ? 
            redirect()->route('dashboard') : 
            redirect()->intended(route('dashboard'));
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
