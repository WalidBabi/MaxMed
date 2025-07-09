<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = $this->getDashboardStats();
            
            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'environment' => app()->environment()
            ]);
            
            // Return a simplified dashboard without stats if there's an error
            $stats = [
                'quotation_stats' => [
                    'total_quotations' => 0,
                    'pending_quotations' => 0,
                    'quotations_this_week' => 0,
                    'approved_quotations' => 0
                ],
                'order_stats' => [
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'completed_orders' => 0
                ],
                'customer_stats' => [
                    'total_customers' => 0,
                    'new_customers_this_month' => 0
                ],
                'revenue_stats' => [
                    'total_revenue' => 0,
                    'revenue_this_month' => 0
                ]
            ];
            
            return view('admin.dashboard', compact('stats'));
        }
    }
    
    private function getDashboardStats()
    {
        $stats = [
            'quotation_stats' => [
                'total_quotations' => 0,
                'pending_quotations' => 0,
                'quotations_this_week' => 0,
                'approved_quotations' => 0
            ],
            'order_stats' => [
                'total_orders' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0
            ],
            'customer_stats' => [
                'total_customers' => 0,
                'new_customers_this_month' => 0
            ],
            'revenue_stats' => [
                'total_revenue' => 0,
                'revenue_this_month' => 0
            ]
        ];
        
        try {
            // Get quotation stats
            $stats['quotation_stats']['total_quotations'] = DB::table('quotations')->count();
            $stats['quotation_stats']['pending_quotations'] = DB::table('quotations')->where('status', 'pending')->count();
            $stats['quotation_stats']['quotations_this_week'] = DB::table('quotations')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();
            $stats['quotation_stats']['approved_quotations'] = DB::table('quotations')->where('status', 'approved')->count();
            
            // Get order stats
            $stats['order_stats']['total_orders'] = DB::table('orders')->count();
            $stats['order_stats']['pending_orders'] = DB::table('orders')->where('status', 'pending')->count();
            $stats['order_stats']['completed_orders'] = DB::table('orders')->where('status', 'completed')->count();
            
            // Get customer stats
            $stats['customer_stats']['total_customers'] = DB::table('customers')->count();
            $stats['customer_stats']['new_customers_this_month'] = DB::table('customers')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            
            // Get revenue stats
            $stats['revenue_stats']['total_revenue'] = DB::table('invoices')->where('status', 'paid')->sum('total_amount');
            $stats['revenue_stats']['revenue_this_month'] = DB::table('invoices')
                ->where('status', 'paid')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_amount');
                
        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage(),
                'environment' => app()->environment()
            ]);
        }
        
        return $stats;
    }
} 