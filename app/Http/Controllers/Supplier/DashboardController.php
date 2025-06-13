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

        // Calculate statistics
        $totalProducts = $products->count();
        $activeProducts = $products->where('price', '>', 0)->count();

        // Recent products (last 5)
        $recentProducts = Product::with(['category', 'inventory'])
            ->where('supplier_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('supplier.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'recentProducts'
        ));
    }
} 