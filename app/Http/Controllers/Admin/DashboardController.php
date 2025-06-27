<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\News;
use App\Models\SupplierQuotation;
use App\Models\QuotationRequest;
use App\Models\SupplierInquiry;
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
            'quotation_stats' => $this->getQuotationStats(),
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

    private function getQuotationStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_quotations' => SupplierQuotation::count(),
            'pending_quotations' => SupplierQuotation::where('status', 'submitted')->count(),
            'approved_quotations' => SupplierQuotation::where('status', 'accepted')->count(),
            'rejected_quotations' => SupplierQuotation::where('status', 'rejected')->count(),
            'quotations_today' => SupplierQuotation::whereDate('created_at', $today)->count(),
            'quotations_this_week' => SupplierQuotation::where('created_at', '>=', $thisWeek)->count(),
            'quotations_this_month' => SupplierQuotation::where('created_at', '>=', $thisMonth)->count(),
            'total_inquiries' => QuotationRequest::count() + SupplierInquiry::count(),
            'pending_inquiries' => QuotationRequest::where('status', 'pending')->count() + 
                                 SupplierInquiry::where('status', 'pending')->count(),
            'recent_quotations' => SupplierQuotation::with(['supplier', 'product'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
} 