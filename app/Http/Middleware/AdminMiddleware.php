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
        // Add logging to debug the issue
        \Log::info('AdminMiddleware: Checking authentication', [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'is_admin' => auth()->check() ? auth()->user()->isAdmin() : false,
            'route' => $request->route()->getName(),
            'method' => $request->method()
        ]);

        if (!auth()->check()) {
            \Log::warning('AdminMiddleware: User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {
            \Log::warning('AdminMiddleware: User is not admin', ['user_id' => auth()->id()]);
            abort(403, 'Unauthorized access to admin area.');
        }

        \Log::info('AdminMiddleware: Access granted');
        return $next($request);
    }
} 