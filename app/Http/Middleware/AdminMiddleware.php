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
                return false;
            }
        }
        
        // Don't store AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }
        
        return true;
    }
} 