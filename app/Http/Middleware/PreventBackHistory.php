<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only apply aggressive no-cache headers in production
        if (app()->environment('production')) {
            return $response->withHeaders([
                'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Sun, 02 Jan 1990 00:00:00 GMT'
            ]);
        }
        
        // In development, use minimal cache control to prevent double-click issues
        return $response->withHeaders([
            'Cache-Control' => 'no-cache, must-revalidate'
        ]);
    }
} 