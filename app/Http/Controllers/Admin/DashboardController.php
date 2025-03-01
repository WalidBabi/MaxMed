<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'total_news' => News::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'low_stock_products' => Product::whereHas('inventory', function($query) {
                $query->where('quantity', '<', 10);
            })->with('category')->take(5)->get(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'popular_products' => $this->getPopularProducts(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    private function getMonthlyRevenue()
    {
        return Order::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('sum(total_amount) as revenue'),
                DB::raw("DATE_FORMAT(created_at, '%M %Y') as month")
            )
            ->groupBy('month')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    private function getPopularProducts()
    {
        return Product::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();
    }
} 