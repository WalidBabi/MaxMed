<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\QuotationRequest;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    /**
     * Display the supplier dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Show 'Profile Under Review' only if there are pending categories
        $isPendingApproval = $user->supplierCategories()->where('status', 'pending_approval')->exists();
        
        // Get supplier's products with related data
        $products = Product::with(['category', 'brand', 'inventory', 'images'])
            ->where('supplier_id', $user->id)
            ->get();

        // Calculate product statistics
        $totalProducts = $products->count();
        $activeProducts = $products->where('price', '>', 0)->count();

        // Recent products (last 5)
        $recentProducts = Product::with(['category', 'inventory'])
            ->where('supplier_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get supplier's assigned categories with performance data
        $assignedCategories = $user->activeSupplierCategories()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        // Order statistics (without customer/pricing info)
        $orderStats = [
            'pending' => \App\Models\Order::whereHas('delivery', function($q) { 
                $q->where('status', 'pending'); 
            })->count(),
            'processing' => \App\Models\Order::whereHas('delivery', function($q) { 
                $q->where('status', 'processing'); 
            })->count(),
            'in_transit' => \App\Models\Order::whereHas('delivery', function($q) { 
                $q->where('status', 'in_transit'); 
            })->count(),
            'delivered_today' => \App\Models\Order::whereHas('delivery', function($q) { 
                $q->where('status', 'delivered')
                  ->whereDate('delivered_at', today()); 
            })->count(),
        ];

        // Recent orders requiring attention (pending/processing)
        $recentOrders = \App\Models\Order::with(['delivery', 'items.product'])
            ->whereHas('delivery', function($q) {
                $q->whereIn('status', ['pending', 'processing']);
            })
            ->latest()
            ->take(5)
            ->get();

        // Badge counts for navigation
        $pendingInquiriesCount = QuotationRequest::where('supplier_id', $user->id)
            ->where('status', 'forwarded')
            ->where('supplier_response', 'pending')
            ->count();

        $activeOrdersCount = \App\Models\Order::whereHas('delivery', function($q) {
            $q->whereIn('status', ['pending', 'processing', 'in_transit']);
        })->count();

        // Share badge counts with all supplier views
        View::share([
            'pendingInquiriesCount' => $pendingInquiriesCount,
            'activeOrdersCount' => $activeOrdersCount
        ]);

        return view('supplier.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'recentProducts',
            'assignedCategories',
            'orderStats',
            'recentOrders',
            'pendingInquiriesCount',
            'activeOrdersCount',
            'isPendingApproval'
        ));
    }
} 