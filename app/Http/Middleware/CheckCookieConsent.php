<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCookieConsent
{
    public function handle(Request $request, Closure $next)
    {
        // Skip if user has already given consent
        if ($request->hasCookie('cookie_consent')) {
            return $next($request);
        }

        // Skip for API requests and AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return $next($request);
        }

        $response = $next($request);
        
        // Only set the cookie if it's a normal response and not a redirect
        if (method_exists($response, 'withCookie') && $response->getStatusCode() === 200) {
            $response->withCookie(cookie()->forever('cookie_consent', 'denied'));
        }
        
        return $response;
    }
}
