<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::debug('AdminMiddleware::handle() - Starting admin access check', [
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'is_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);

        if (!auth()->check()) {
            Log::warning('AdminMiddleware::handle() - User not authenticated', [
                'url' => $request->url(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            // Don't store API endpoints or notification checks as intended URL
            if (!$this->shouldStoreIntendedUrl($request)) {
                return redirect()->route('login');
            }
            
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        Log::debug('AdminMiddleware::handle() - Checking user admin status', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'has_role' => $user->role ? true : false,
            'role_name' => $user->role ? $user->role->name : 'no role',
            'is_admin' => $user->isAdmin(),
            'timestamp' => now()->toISOString()
        ]);

        if (!$user->isAdmin()) {
            Log::warning('AdminMiddleware::handle() - Access denied - User is not admin', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'url' => $request->url(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            abort(403, 'Unauthorized access to admin area.');
        }

        Log::info('AdminMiddleware::handle() - Admin access granted', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'url' => $request->url(),
            'timestamp' => now()->toISOString()
        ]);

        return $next($request);
    }
    
    /**
     * Determine if we should store the current URL as intended URL
     */
    private function shouldStoreIntendedUrl(Request $request): bool
    {
        $url = $request->url();
        $path = $request->path();
        
        // Don't store these URLs as intended URLs
        $excludePatterns = [
            '/notifications/check-new',
            '/notifications/count',
            '/notifications/stream',
            '/api/',
            '.json',
            '.xml',
            '.txt'
        ];
        
        foreach ($excludePatterns as $pattern) {
            if (str_contains($url, $pattern) || str_contains($path, $pattern)) {
                Log::debug('AdminMiddleware::shouldStoreIntendedUrl() - Excluding URL pattern', [
                    'url' => $url,
                    'pattern' => $pattern,
                    'timestamp' => now()->toISOString()
                ]);
                return false;
            }
        }
        
        // Don't store AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            Log::debug('AdminMiddleware::shouldStoreIntendedUrl() - Excluding AJAX request', [
                'url' => $url,
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'timestamp' => now()->toISOString()
            ]);
            return false;
        }
        
        return true;
    }
} 