<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @param  string|null  $guard
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission, ?string $guard = null): Response
    {
        $authGuard = Auth::guard($guard);

        if (!$authGuard->check()) {
            Log::warning('PermissionMiddleware: User not authenticated', [
                'permission' => $permission,
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return redirect()->route('login');
        }

        $user = $authGuard->user();

        if (!$user->hasPermission($permission)) {
            Log::warning('PermissionMiddleware: Access denied - Missing permission', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'required_permission' => $permission,
                'user_role' => $user->role?->name,
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => "You don't have permission to perform this action.",
                    'required_permission' => $permission
                ], 403);
            }

            abort(403, "Access denied. Required permission: {$permission}");
        }

        return $next($request);
    }
}
