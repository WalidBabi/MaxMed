<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Allow search engines to crawl authenticated pages
        $userAgent = $request->header('User-Agent');
        if ($this->isSearchEngine($userAgent)) {
            return $next($request);
        }
        
        try {
            $result = parent::handle($request, $next, ...$guards);
            return $result;
        } catch (\Exception $e) {
            Log::error('Authenticate::handle() - Authentication failed', [
                'url' => $request->url(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'timestamp' => now()->toISOString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
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