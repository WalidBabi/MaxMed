<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckSupplierOnboarding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Skip for non-suppliers and admins
        if (!$user || !$user->isSupplier() || $user->isAdmin()) {
            return $next($request);
        }

        // Get current route name
        $routeName = $request->route()->getName();

        // Allow access to onboarding routes
        if (str_starts_with($routeName, 'supplier.onboarding.')) {
            return $next($request);
        }

        // Check if supplier has completed onboarding
        $supplierInfo = $user->supplierInformation;
        
        if (!$supplierInfo) {
            return Redirect::route('supplier.onboarding.company')
                ->with('warning', 'Please complete your company information to continue.');
        }

        // Check each onboarding step and redirect to the appropriate step
        if (!$supplierInfo->company_name || !$supplierInfo->business_registration_number) {
            return Redirect::route('supplier.onboarding.company')
                ->with('warning', 'Please complete your company information to continue.');
        }

        if (!$supplierInfo->documents || empty($supplierInfo->documents)) {
            return Redirect::route('supplier.onboarding.documents')
                ->with('warning', 'Please upload your company documents to continue.');
        }

        if (!$supplierInfo->onboarding_completed) {
            return Redirect::route('supplier.onboarding.categories')
                ->with('warning', 'Please select your product categories to complete the onboarding process.');
        }

        return $next($request);
    }
} 