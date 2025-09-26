<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            Log::warning('RoleMiddleware: User not authenticated', [
                'required_roles' => $roles,
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->hasAnyRole($roles)) {
            Log::warning('RoleMiddleware: Access denied - Missing role', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role?->name,
                'user_roles' => (function () use ($user) {
                    try {
                        return Schema::hasTable('role_user') ? $user->roles()->pluck('name') : collect();
                    } catch (\Throwable $e) {
                        return collect();
                    }
                })(),
                'required_roles' => $roles,
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => "You don't have the required role to access this resource.",
                    'required_roles' => $roles,
                    'user_role' => $user->role?->name,
                    'user_roles' => (function () use ($user) {
                        try {
                            return Schema::hasTable('role_user') ? $user->roles()->pluck('name') : collect();
                        } catch (\Throwable $e) {
                            return collect();
                        }
                    })()
                ], 403);
            }

            $rolesList = implode(', ', $roles);
            abort(403, "Access denied. Required roles: {$rolesList}");
        }

        return $next($request);
    }
}
