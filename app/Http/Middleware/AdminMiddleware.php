<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow search engines to crawl without authentication
        $userAgent = $request->header('User-Agent');
        if ($this->isSearchEngine($userAgent)) {
            return $next($request);
        }
        
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }
        
        return redirect()->route('welcome')->with('error', 'You do not have permission to access this area.');
    }
    
    /**
     * Check if the user agent is a search engine bot
     * 
     * @param string|null $userAgent
     * @return bool
     */
    private function isSearchEngine(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }
        
        $bots = [
            'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider',
            'YandexBot', 'Sogou', 'facebot', 'ia_archiver', 'AhrefsBot'
        ];
        
        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }
        
        return false;
    }
} 