<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only add headers for successful HTML responses
        if ($response->getStatusCode() === 200 && 
            $request->accepts('text/html') && 
            !$request->ajax() && 
            !$request->wantsJson()) {
            
            // Add cache control headers for better crawling
            $response->headers->set('Cache-Control', 'public, max-age=3600');
            
            // Add ETag for better caching
            if (!$response->headers->has('ETag')) {
                $response->headers->set('ETag', '"' . md5($response->getContent()) . '"');
            }
            
            // Add Last-Modified header
            if (!$response->headers->has('Last-Modified')) {
                $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            }
            
            // Add proper content type
            if (!$response->headers->has('Content-Type')) {
                $response->headers->set('Content-Type', 'text/html; charset=utf-8');
            }
            
            // Add security headers that don't interfere with SEO
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            
            // Add canonical domain hint
            if (strpos($request->getHost(), 'www.') === 0) {
                $canonicalUrl = 'https://maxmedme.com' . $request->getRequestUri();
                $response->headers->set('Link', '<' . $canonicalUrl . '>; rel="canonical"');
            }
        }
        
        return $response;
    }
}
