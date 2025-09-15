<?php

namespace App\Http\Middleware;

use App\Services\AccessControlService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if (!auth()->check()) {
            Log::warning('AdminMiddleware::handle() - User not authenticated', [
                'url' => $request->url(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            // Don't store API endpoints or notification checks as intended URL
            if (!$this->shouldStoreIntendedUrl($request)) {
                return redirect()->route('login');
            }
            
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Use the new AccessControlService for consistent permission checking
        $hasAdminAccess = AccessControlService::canAccessAdmin($user);

        // Log the access attempt
        AccessControlService::logAccessAttempt(
            $user, 
            'admin.dashboard.access', 
            $hasAdminAccess, 
            'AdminMiddleware'
        );

        if (!$hasAdminAccess) {
            Log::warning('AdminMiddleware::handle() - Access denied - User lacks admin permissions', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role?->name,
                'role_info' => AccessControlService::getUserRoleInfo($user),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            abort(403, 'Unauthorized access to admin area.');
        }

        // Check if user is trying to access dashboard without permission and redirect them
        if ($request->routeIs('admin.dashboard') && !$user->hasPermission('dashboard.view')) {
            if ($user->hasPermission('purchase_orders.view')) {
                return redirect(route('admin.purchase-orders.index'));
            } elseif ($user->hasPermission('quotations.view')) {
                return redirect(route('admin.quotes.index'));
            } elseif ($user->hasPermission('orders.view')) {
                return redirect(route('admin.orders.index'));
            } elseif ($user->hasPermission('products.view')) {
                return redirect(route('admin.products.index'));
            } elseif ($user->hasPermission('crm.leads.view')) {
                return redirect(route('crm.dashboard'));
            } elseif ($user->hasPermission('suppliers.view')) {
                return redirect(route('admin.supplier-profiles.index'));
            } elseif ($user->hasPermission('news.manage')) {
                return redirect(route('admin.news.index'));
            } elseif ($user->hasPermission('feedback.view')) {
                return redirect(route('admin.feedback.index'));
            } elseif ($user->hasPermission('analytics.view')) {
                return redirect(route('admin.analytics.dashboard'));
            } elseif ($user->hasPermission('users.view')) {
                return redirect(route('admin.users.index'));
            } elseif ($user->hasPermission('roles.view')) {
                return redirect(route('admin.roles.index'));
            }
        }

        return $next($request);
    }
    
    /**
     * Determine if we should store the current URL as intended URL
     */
    private function shouldStoreIntendedUrl(Request $request): bool
    {
        $url = $request->url();
        $path = $request->path();
        
        // Don't store these URLs as intended URLs
        $excludePatterns = [
            '/notifications/check-new',
            '/notifications/count',
            '/notifications/stream',
            '/api/',
            '.json',
            '.xml',
            '.txt'
        ];
        
        foreach ($excludePatterns as $pattern) {
            if (str_contains($url, $pattern) || str_contains($path, $pattern)) {
                return false;
            }
        }
        
        // Don't store AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }
        
        return true;
    }
} 