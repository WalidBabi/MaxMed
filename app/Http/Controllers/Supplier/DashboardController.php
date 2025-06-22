<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the supplier dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Simple permission check for now - can be enhanced later
        // TODO: Add proper permission checking once middleware is working
        
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

        // Calculate overall performance metrics
        $performanceMetrics = [
            'total_categories' => $assignedCategories->count(),
            'avg_win_rate' => $assignedCategories->avg('quotation_win_rate') ?? 0,
            'avg_response_time' => $assignedCategories->avg('avg_response_time_hours') ?? 24,
            'avg_rating' => $assignedCategories->avg('avg_customer_rating') ?? 5.0,
            'total_quotations' => $assignedCategories->sum('total_quotations'),
            'won_quotations' => $assignedCategories->sum('won_quotations'),
        ];

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

        return view('supplier.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'recentProducts',
            'assignedCategories',
            'performanceMetrics',
            'orderStats',
            'recentOrders'
        ));
    }
} 