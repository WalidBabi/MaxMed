<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanonicalDomainMiddleware
{
    /**
     * Handle an incoming request and enforce consistent domain usage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Define your preferred domain (with or without www)
        $preferredDomain = 'maxmedme.com'; // without www
        
        $host = $request->getHost();
        
        // If using www but preferred is non-www (or vice versa), redirect
        if ($host !== $preferredDomain) {
            // Only redirect if it's a www vs non-www issue
            if ($host === 'www.' . $preferredDomain || 'www.' . $host === $preferredDomain) {
                $scheme = $request->getScheme();
                $uri = $request->getRequestUri();
                
                // Use 301 permanent redirect for canonical domain
                return redirect()->to($scheme . '://' . $preferredDomain . $uri, 301);
            }
        }
        
        return $next($request);
    }
} 