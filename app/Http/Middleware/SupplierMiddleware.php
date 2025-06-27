<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Response as ResponseFacade;

class SupplierMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();
        
        // Allow admin access
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Check if user is a supplier
        if (!$user->isSupplier()) {
            return ResponseFacade::make('Access denied. This area is for suppliers only.', 403);
        }

        return $next($request);
    }
} 