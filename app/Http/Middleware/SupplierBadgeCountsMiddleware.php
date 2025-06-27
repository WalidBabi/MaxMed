<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\QuotationRequest;
use App\Models\PurchaseOrder;

class SupplierBadgeCountsMiddleware
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
        // Only run for authenticated users in supplier routes
        if (auth()->check() && $request->route()->getPrefix() === 'supplier') {
            $supplierId = auth()->id();
            
            // Calculate badge counts
            $pendingInquiriesCount = QuotationRequest::where('supplier_id', $supplierId)
                ->where('status', 'forwarded')
                ->where('supplier_response', 'pending')
                ->count();

            $activeOrdersCount = PurchaseOrder::where('supplier_id', $supplierId)
                ->whereIn('status', ['sent_to_supplier', 'acknowledged', 'in_production'])
                ->count();

            // Share with all views
            View::share([
                'pendingInquiriesCount' => $pendingInquiriesCount,
                'activeOrdersCount' => $activeOrdersCount
            ]);
        }

        return $next($request);
    }
} 