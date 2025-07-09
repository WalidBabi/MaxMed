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
        // Temporary fix: Return empty stats to prevent server errors
        // This will be reverted once the table issues are resolved
        return [
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
        
        // Original code commented out for now
        /*
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
            // Check if tables exist before querying them
            $tables = ['quotes', 'orders', 'customers', 'invoices'];
            $existingTables = [];
            
            foreach ($tables as $table) {
                try {
                    DB::select("SELECT 1 FROM {$table} LIMIT 1");
                    $existingTables[] = $table;
                } catch (\Exception $e) {
                    Log::info("Table {$table} does not exist, skipping stats");
                }
            }
            
            Log::info('Available tables for stats', ['tables' => $existingTables]);
            
            // Get quotation stats only if table exists
            if (in_array('quotes', $existingTables)) {
                try {
                    $stats['quotation_stats']['total_quotations'] = DB::table('quotes')->count();
                    $stats['quotation_stats']['pending_quotations'] = DB::table('quotes')->where('status', 'pending')->count();
                    $stats['quotation_stats']['quotations_this_week'] = DB::table('quotes')
                        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                        ->count();
                    $stats['quotation_stats']['approved_quotations'] = DB::table('quotes')->where('status', 'approved')->count();
                } catch (\Exception $e) {
                    Log::error('Error getting quotation stats', ['error' => $e->getMessage()]);
                }
            }
            
            // Get order stats only if table exists
            if (in_array('orders', $existingTables)) {
                try {
                    $stats['order_stats']['total_orders'] = DB::table('orders')->count();
                    $stats['order_stats']['pending_orders'] = DB::table('orders')->where('status', 'pending')->count();
                    $stats['order_stats']['completed_orders'] = DB::table('orders')->where('status', 'completed')->count();
                } catch (\Exception $e) {
                    Log::error('Error getting order stats', ['error' => $e->getMessage()]);
                }
            }
            
            // Get customer stats only if table exists
            if (in_array('customers', $existingTables)) {
                try {
                    $stats['customer_stats']['total_customers'] = DB::table('customers')->count();
                    $stats['customer_stats']['new_customers_this_month'] = DB::table('customers')
                        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                        ->count();
                } catch (\Exception $e) {
                    Log::error('Error getting customer stats', ['error' => $e->getMessage()]);
                }
            }
            
            // Get revenue stats only if table exists
            if (in_array('invoices', $existingTables)) {
                try {
                    $stats['revenue_stats']['total_revenue'] = DB::table('invoices')->where('status', 'paid')->sum('total_amount');
                    $stats['revenue_stats']['revenue_this_month'] = DB::table('invoices')
                        ->where('status', 'paid')
                        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                        ->sum('total_amount');
                } catch (\Exception $e) {
                    Log::error('Error getting revenue stats', ['error' => $e->getMessage()]);
                }
            }
                
        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage(),
                'environment' => app()->environment()
            ]);
        }
        
        return $stats;
        */
    }
} 