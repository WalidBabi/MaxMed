<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            \Log::warning('AdminMiddleware: User not authenticated, redirecting to login');
            
            // Don't store API endpoints or notification checks as intended URL
            if (!$this->shouldStoreIntendedUrl($request)) {
                return redirect()->route('login');
            }
            
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {
            \Log::warning('AdminMiddleware: User is not admin', ['user_id' => auth()->id()]);
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