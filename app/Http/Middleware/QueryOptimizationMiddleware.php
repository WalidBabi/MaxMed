<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryOptimizationMiddleware
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
        // Enable query logging in development
        if (app()->environment('local')) {
            DB::enableQueryLog();
        }

        $response = $next($request);

        // Log slow queries in development
        if (app()->environment('local')) {
            $queries = DB::getQueryLog();
            $slowQueries = array_filter($queries, function($query) {
                return $query['time'] > 100; // Queries taking more than 100ms
            });

            if (!empty($slowQueries)) {
                Log::warning('Slow queries detected:', $slowQueries);
            }
        }

        return $response;
    }
}
