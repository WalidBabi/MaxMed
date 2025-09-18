<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventCrawlerIndexing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Add headers to prevent indexing of authentication endpoints
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        
        // If this is a crawler accessing a non-POST authentication endpoint, return a proper response
        if ($this->isCrawler($request) && !$request->isMethod('POST')) {
            return response()->json([
                'message' => 'This is an authentication endpoint that requires POST requests.',
                'status' => 'authentication_endpoint'
            ], 200)->withHeaders([
                'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }
        
        return $response;
    }
    
    /**
     * Check if the request is from a web crawler
     */
    private function isCrawler(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        $crawlers = [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'whatsapp',
            'crawler',
            'spider',
            'bot'
        ];
        
        foreach ($crawlers as $crawler) {
            if (strpos($userAgent, $crawler) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
