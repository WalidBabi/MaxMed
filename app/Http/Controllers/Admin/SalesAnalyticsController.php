<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\Order;
use App\Services\AnalyticsExportService;

class SalesAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard.analytics');
    }

    /**
     * Main analytics dashboard
     */
    public function index()
    {
        try {
            // Get comprehensive analytics data
            $analyticsData = $this->getComprehensiveAnalytics();
            $filterOptions = $this->getFilterOptions();
            
            return view('admin.analytics.dashboard', compact('analyticsData', 'filterOptions'));
        } catch (\Exception $e) {
            Log::error('Analytics dashboard error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return view('admin.analytics.dashboard', [
                'analyticsData' => $this->getEmptyAnalyticsData(),
                'filterOptions' => $this->getEmptyFilterOptions()
            ]);
        }
    }

    /**
     * API endpoint for filtered analytics data
     */
    public function getAnalyticsData(Request $request)
    {
        try {
            $filters = $this->parseFilters($request);
            $analyticsData = $this->getFilteredAnalyticsData($filters);
            
            return response()->json([
                'success' => true,
                'data' => $analyticsData
            ]);
        } catch (\Exception $e) {
            Log::error('Analytics data API error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching analytics data',
                'data' => $this->getEmptyAnalyticsData()
            ], 500);
        }
    }

    /**
     * Revenue analytics endpoint
     */
    public function getRevenueAnalytics(Request $request)
    {
        try {
            $filters = $this->parseFilters($request);
            $revenueData = $this->getRevenueData($filters);
            
            return response()->json([
                'success' => true,
                'data' => $revenueData
            ]);
        } catch (\Exception $e) {
            Log::error('Revenue analytics error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching revenue data'], 500);
        }
    }

    /**
     * Cash flow analytics endpoint
     */
    public function getCashFlowAnalytics(Request $request)
    {
        try {
            $filters = $this->parseFilters($request);
            $cashFlowData = $this->getCashFlowData($filters);
            
            return response()->json([
                'success' => true,
                'data' => $cashFlowData
            ]);
        } catch (\Exception $e) {
            Log::error('Cash flow analytics error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching cash flow data'], 500);
        }
    }

    /**
     * Sales performance analytics endpoint
     */
    public function getSalesPerformanceAnalytics(Request $request)
    {
        try {
            $filters = $this->parseFilters($request);
            $performanceData = $this->getSalesPerformanceData($filters);
            
            return response()->json([
                'success' => true,
                'data' => $performanceData
            ]);
        } catch (\Exception $e) {
            Log::error('Sales performance analytics error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching sales performance data'], 500);
        }
    }

    /**
     * Customer analytics endpoint
     */
    public function getCustomerAnalytics(Request $request)
    {
        try {
            $filters = $this->parseFilters($request);
            $customerData = $this->getCustomerData($filters);
            
            return response()->json([
                'success' => true,
                'data' => $customerData
            ]);
        } catch (\Exception $e) {
            Log::error('Customer analytics error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching customer data'], 500);
        }
    }

    /**
     * Product analytics endpoint
     */
    public function getProductAnalytics(Request $request)
    {
        try {
            $filters = $this->parseFilters($request);
            $productData = $this->getProductData($filters);
            
            return response()->json([
                'success' => true,
                'data' => $productData
            ]);
        } catch (\Exception $e) {
            Log::error('Product analytics error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error fetching product data'], 500);
        }
    }

    /**
     * Export analytics data
     */
    public function exportAnalytics(Request $request)
    {
        try {
            $format = $request->get('format', 'excel'); // excel, pdf, csv
            $filters = $this->parseFilters($request);
            
            $exportService = new AnalyticsExportService();
            
            switch ($format) {
                case 'pdf':
                    $result = $exportService->exportToPDF($filters);
                    break;
                case 'csv':
                    $result = $exportService->exportToCSV($filters);
                    break;
                case 'excel':
                default:
                    $result = $exportService->exportToExcel($filters);
                    break;
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Export analytics error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error exporting data'], 500);
        }
    }

    /**
     * Get comprehensive analytics data
     */
    private function getComprehensiveAnalytics()
    {
        $dateRange = $this->getDefaultDateRange();
        
        return [
            'revenue' => $this->getRevenueData(['date_range' => $dateRange]),
            'cash_flow' => $this->getCashFlowData(['date_range' => $dateRange]),
            'sales_performance' => $this->getSalesPerformanceData(['date_range' => $dateRange]),
            'customers' => $this->getCustomerData(['date_range' => $dateRange]),
            'products' => $this->getProductData(['date_range' => $dateRange]),
            'summary' => $this->getSummaryMetrics($dateRange)
        ];
    }

    /**
     * Get revenue analytics data
     */
    private function getRevenueData($filters)
    {
        $dateRange = $filters['date_range'] ?? $this->getDefaultDateRange();
        $period = $filters['period'] ?? 'monthly';
        
        // Revenue trends
        $revenueTrends = $this->getRevenueTrends($dateRange, $period);
        
        // Revenue by category
        $revenueByCategory = $this->getRevenueByCategory($dateRange);
        
        // Revenue by customer
        $revenueByCustomer = $this->getRevenueByCustomer($dateRange);
        
        // Revenue by sales rep
        $revenueBySalesRep = $this->getRevenueBySalesRep($dateRange);
        
        // Revenue growth rate
        $growthRate = $this->calculateGrowthRate($revenueTrends);
        
        return [
            'trends' => $revenueTrends,
            'by_category' => $revenueByCategory,
            'by_customer' => $revenueByCustomer,
            'by_sales_rep' => $revenueBySalesRep,
            'growth_rate' => $growthRate,
            'total_revenue' => $this->getTotalRevenue($dateRange),
            'currency_breakdown' => $this->getCurrencyBreakdown($dateRange)
        ];
    }

    /**
     * Get cash flow analytics data
     */
    private function getCashFlowData($filters)
    {
        $dateRange = $filters['date_range'] ?? $this->getDefaultDateRange();
        
        // Cash received vs invoiced
        $cashVsInvoiced = $this->getCashVsInvoiced($dateRange);
        
        // Payment status analysis
        $paymentStatus = $this->getPaymentStatusAnalysis($dateRange);
        
        // Outstanding receivables
        $outstandingReceivables = $this->getOutstandingReceivables();
        
        // Payment method analysis
        $paymentMethods = $this->getPaymentMethodAnalysis($dateRange);
        
        // Cash flow forecasting
        $forecast = $this->getCashFlowForecast($dateRange);
        
        return [
            'cash_vs_invoiced' => $cashVsInvoiced,
            'payment_status' => $paymentStatus,
            'outstanding_receivables' => $outstandingReceivables,
            'payment_methods' => $paymentMethods,
            'forecast' => $forecast,
            'total_cash_received' => $this->getTotalCashReceived($dateRange),
            'total_invoiced' => $this->getTotalInvoiced($dateRange)
        ];
    }

    /**
     * Get sales performance analytics data
     */
    private function getSalesPerformanceData($filters)
    {
        $dateRange = $filters['date_range'] ?? $this->getDefaultDateRange();
        
        // Sales pipeline analysis
        $pipeline = $this->getSalesPipeline();
        
        // Lead conversion rates
        $conversionRates = $this->getConversionRates($dateRange);
        
        // Deal win/loss analysis
        $winLossAnalysis = $this->getWinLossAnalysis($dateRange);
        
        // Sales cycle analysis
        $salesCycle = $this->getSalesCycleAnalysis($dateRange);
        
        // Top performing products
        $topProducts = $this->getTopPerformingProducts($dateRange);
        
        return [
            'pipeline' => $pipeline,
            'conversion_rates' => $conversionRates,
            'win_loss_analysis' => $winLossAnalysis,
            'sales_cycle' => $salesCycle,
            'top_products' => $topProducts,
            'total_leads' => $this->getTotalLeads($dateRange),
            'total_deals' => $this->getTotalDeals($dateRange)
        ];
    }

    /**
     * Get customer analytics data
     */
    private function getCustomerData($filters)
    {
        $dateRange = $filters['date_range'] ?? $this->getDefaultDateRange();
        
        // Customer acquisition trends
        $acquisitionTrends = $this->getCustomerAcquisitionTrends($dateRange);
        
        // Customer lifetime value
        $customerLTV = $this->getCustomerLifetimeValue();
        
        // Customer segmentation
        $segmentation = $this->getCustomerSegmentation();
        
        // Top customers
        $topCustomers = $this->getTopCustomers($dateRange);
        
        return [
            'acquisition_trends' => $acquisitionTrends,
            'lifetime_value' => $customerLTV,
            'segmentation' => $segmentation,
            'top_customers' => $topCustomers,
            'total_customers' => $this->getTotalCustomers(),
            'new_customers' => $this->getNewCustomers($dateRange)
        ];
    }

    /**
     * Get product analytics data
     */
    private function getProductData($filters)
    {
        $dateRange = $filters['date_range'] ?? $this->getDefaultDateRange();
        
        // Product performance
        $productPerformance = $this->getProductPerformance($dateRange);
        
        // Category performance
        $categoryPerformance = $this->getCategoryPerformance($dateRange);
        
        // Inventory analysis
        $inventoryAnalysis = $this->getInventoryAnalysis();
        
        // Product trends
        $productTrends = $this->getProductTrends($dateRange);
        
        return [
            'performance' => $productPerformance,
            'category_performance' => $categoryPerformance,
            'inventory_analysis' => $inventoryAnalysis,
            'trends' => $productTrends,
            'total_products' => $this->getTotalProducts(),
            'active_products' => $this->getActiveProducts()
        ];
    }

    /**
     * Get revenue trends data
     */
    private function getRevenueTrends($dateRange, $period = 'monthly')
    {
        $groupByFormat = $this->getGroupByFormat($period);
        
        $revenueData = DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') as period"),
                'currency',
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period', 'currency')
            ->orderBy('period')
            ->get();
        
        $trends = [];
        $labels = [];
        $aedData = [];
        $usdData = [];
        $combinedData = [];
        
        // Generate all periods in range
        $periods = $this->generatePeriods($dateRange, $period);
        
        foreach ($periods as $periodLabel) {
            $labels[] = $periodLabel;
            $aedData[] = 0;
            $usdData[] = 0;
            $combinedData[] = 0;
        }
        
        // Fill in actual data
        foreach ($revenueData as $data) {
            // Convert query period (2025-08) to display format (Aug 2025)
            $displayPeriod = Carbon::createFromFormat('Y-m', $data->period)->format('M Y');
            $index = array_search($displayPeriod, $labels);
            if ($index !== false) {
                if (strtoupper($data->currency) === 'AED') {
                    $aedData[$index] = (float) $data->total;
                } else {
                    $usdData[$index] = (float) $data->total;
                }
                $combinedData[$index] = $aedData[$index] + ($usdData[$index] * 3.67);
            }
        }
        
        return [
            'labels' => $labels,
            'aed' => $aedData,
            'usd' => $usdData,
            'combined' => $combinedData,
            'total_aed' => array_sum($aedData),
            'total_usd' => array_sum($usdData),
            'total_combined' => array_sum($combinedData)
        ];
    }

    /**
     * Get revenue by category
     */
    private function getRevenueByCategory($dateRange)
    {
        return DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('invoices.type', 'final')
            ->whereBetween('invoices.invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'categories.name as category_name',
                'invoices.currency',
                DB::raw('SUM(invoice_items.line_total) as total'),
                DB::raw('COUNT(DISTINCT invoices.id) as invoice_count')
            )
            ->groupBy('categories.name', 'invoices.currency')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Get revenue by customer
     */
    private function getRevenueByCustomer($dateRange)
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'customer_name',
                'currency',
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->groupBy('customer_name', 'currency')
            ->orderByDesc('total')
            ->limit(20)
            ->get();
    }

    /**
     * Get revenue by sales rep
     */
    private function getRevenueBySalesRep($dateRange)
    {
        return DB::table('invoices')
            ->join('users', 'invoices.created_by', '=', 'users.id')
            ->where('invoices.type', 'final')
            ->whereBetween('invoices.invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'users.name as sales_rep',
                'invoices.currency',
                DB::raw('SUM(invoices.total_amount) as total'),
                DB::raw('COUNT(invoices.id) as invoice_count')
            )
            ->groupBy('users.name', 'invoices.currency')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Get cash vs invoiced data
     */
    private function getCashVsInvoiced($dateRange)
    {
        $invoiced = DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'currency',
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('currency')
            ->get();
        
        $cashReceived = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.payment_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'invoices.currency',
                DB::raw('SUM(payments.amount) as total')
            )
            ->groupBy('invoices.currency')
            ->get();
        
        return [
            'invoiced' => $invoiced,
            'cash_received' => $cashReceived
        ];
    }

    /**
     * Get payment status analysis
     */
    private function getPaymentStatusAnalysis($dateRange)
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'payment_status',
                'currency',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_status', 'currency')
            ->get();
    }

    /**
     * Get outstanding receivables
     */
    private function getOutstandingReceivables()
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->select(
                'customer_name',
                'invoice_number',
                'invoice_date',
                'due_date',
                'total_amount',
                'paid_amount',
                'currency',
                'payment_status',
                DB::raw('(total_amount - COALESCE(paid_amount, 0)) as outstanding_amount')
            )
            ->orderByDesc('outstanding_amount')
            ->get();
    }

    /**
     * Get payment method analysis
     */
    private function getPaymentMethodAnalysis($dateRange)
    {
        return DB::table('payments')
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Get sales pipeline data
     */
    private function getSalesPipeline()
    {
        // Get leads by status
        $leadsByStatus = DB::table('crm_leads')
            ->select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(estimated_value) as total_value')
            )
            ->groupBy('status')
            ->get();
        
        // Get deals by stage
        $dealsByStage = DB::table('crm_deals')
            ->select(
                'stage',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(deal_value) as total_value'),
                DB::raw('AVG(probability) as avg_probability')
            )
            ->groupBy('stage')
            ->get();
        
        return [
            'leads_by_status' => $leadsByStatus,
            'deals_by_stage' => $dealsByStage
        ];
    }

    /**
     * Get conversion rates
     */
    private function getConversionRates($dateRange)
    {
        $totalLeads = DB::table('crm_leads')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
        
        $qualifiedLeads = DB::table('crm_leads')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', ['qualified', 'proposal', 'won', 'order_confirmed'])
            ->count();
        
        $wonLeads = DB::table('crm_leads')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', ['won', 'order_confirmed'])
            ->count();
        
        return [
            'lead_to_qualified' => $totalLeads > 0 ? round(($qualifiedLeads / $totalLeads) * 100, 2) : 0,
            'qualified_to_won' => $qualifiedLeads > 0 ? round(($wonLeads / $qualifiedLeads) * 100, 2) : 0,
            'lead_to_won' => $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 2) : 0
        ];
    }

    /**
     * Get win/loss analysis
     */
    private function getWinLossAnalysis($dateRange)
    {
        return DB::table('crm_deals')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select(
                'stage',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(deal_value) as total_value')
            )
            ->whereIn('stage', ['closed_won', 'closed_lost'])
            ->groupBy('stage')
            ->get();
    }

    /**
     * Get sales cycle analysis
     */
    private function getSalesCycleAnalysis($dateRange)
    {
        return DB::table('crm_deals')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('actual_close_date')
            ->select(
                DB::raw('AVG(DATEDIFF(actual_close_date, created_at)) as avg_days'),
                DB::raw('MIN(DATEDIFF(actual_close_date, created_at)) as min_days'),
                DB::raw('MAX(DATEDIFF(actual_close_date, created_at)) as max_days')
            )
            ->first();
    }

    /**
     * Get top performing products
     */
    private function getTopPerformingProducts($dateRange)
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.type', 'final')
            ->whereBetween('invoices.invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT invoices.id) as invoice_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(20)
            ->get();
    }

    /**
     * Get customer acquisition trends
     */
    private function getCustomerAcquisitionTrends($dateRange)
    {
        return DB::table('customers')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as new_customers')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get customer lifetime value
     */
    private function getCustomerLifetimeValue()
    {
        return DB::table('invoices')
            ->join('customers', 'invoices.customer_name', '=', 'customers.name')
            ->where('invoices.type', 'final')
            ->select(
                'customers.name',
                'customers.email',
                DB::raw('COUNT(invoices.id) as total_orders'),
                DB::raw('SUM(invoices.total_amount) as total_spent'),
                DB::raw('AVG(invoices.total_amount) as avg_order_value'),
                DB::raw('MIN(invoices.invoice_date) as first_order'),
                DB::raw('MAX(invoices.invoice_date) as last_order')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();
    }

    /**
     * Get customer segmentation
     */
    private function getCustomerSegmentation()
    {
        $segments = DB::table('invoices')
            ->join('customers', 'invoices.customer_name', '=', 'customers.name')
            ->where('invoices.type', 'final')
            ->select(
                'customers.id',
                'customers.name',
                DB::raw('SUM(invoices.total_amount) as total_spent'),
                DB::raw('COUNT(invoices.id) as order_count')
            )
            ->groupBy('customers.id', 'customers.name')
            ->get();
        
        $segmentation = [
            'high_value' => 0,
            'medium_value' => 0,
            'low_value' => 0
        ];
        
        foreach ($segments as $segment) {
            if ($segment->total_spent >= 10000) {
                $segmentation['high_value']++;
            } elseif ($segment->total_spent >= 1000) {
                $segmentation['medium_value']++;
            } else {
                $segmentation['low_value']++;
            }
        }
        
        return $segmentation;
    }

    /**
     * Get top customers
     */
    private function getTopCustomers($dateRange)
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'customer_name',
                'currency',
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->groupBy('customer_name', 'currency')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();
    }

    /**
     * Get product performance
     */
    private function getProductPerformance($dateRange)
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.type', 'final')
            ->whereBetween('invoices.invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(invoice_items.quantity) as total_sold'),
                DB::raw('SUM(invoice_items.line_total) as total_revenue'),
                DB::raw('AVG(invoice_items.unit_price) as avg_price')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(50)
            ->get();
    }

    /**
     * Get category performance
     */
    private function getCategoryPerformance($dateRange)
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('invoices.type', 'final')
            ->whereBetween('invoices.invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(invoice_items.quantity) as total_sold'),
                DB::raw('SUM(invoice_items.line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT products.id) as product_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Get inventory analysis
     */
    private function getInventoryAnalysis()
    {
        return DB::table('products')
            ->leftJoin('inventories', 'products.id', '=', 'inventories.product_id')
            ->select(
                'products.name',
                'products.sku',
                DB::raw('COALESCE(inventories.quantity, 0) as current_stock'),
                'products.price_aed',
                DB::raw('COALESCE(inventories.quantity, 0) * products.price_aed as stock_value')
            )
            ->orderByDesc('stock_value')
            ->limit(50)
            ->get();
    }

    /**
     * Get product trends
     */
    private function getProductTrends($dateRange)
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.type', 'final')
            ->whereBetween('invoices.invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m") as month'),
                'products.name as product_name',
                DB::raw('SUM(invoice_items.quantity) as quantity_sold'),
                DB::raw('SUM(invoice_items.line_total) as revenue')
            )
            ->groupBy('month', 'products.id', 'products.name')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get summary metrics
     */
    private function getSummaryMetrics($dateRange)
    {
        $totalRevenue = $this->getTotalRevenue($dateRange);
        $totalCashReceived = $this->getTotalCashReceived($dateRange);
        $totalLeads = $this->getTotalLeads($dateRange);
        $totalDeals = $this->getTotalDeals($dateRange);
        $totalCustomers = $this->getTotalCustomers();
        $newCustomers = $this->getNewCustomers($dateRange);
        
        return [
            'total_revenue' => $totalRevenue,
            'total_cash_received' => $totalCashReceived,
            'total_leads' => $totalLeads,
            'total_deals' => $totalDeals,
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'conversion_rate' => $totalLeads > 0 ? round(($totalDeals / $totalLeads) * 100, 2) : 0,
            'avg_deal_size' => $totalDeals > 0 ? round($totalRevenue['combined'] / $totalDeals, 2) : 0
        ];
    }

    /**
     * Helper methods for getting totals
     */
    private function getTotalRevenue($dateRange)
    {
        $revenue = DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'currency',
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('currency')
            ->get();
        
        $aed = $revenue->where('currency', 'AED')->sum('total');
        $usd = $revenue->where('currency', 'USD')->sum('total');
        
        return [
            'aed' => $aed,
            'usd' => $usd,
            'combined' => $aed + ($usd * 3.67)
        ];
    }

    private function getTotalCashReceived($dateRange)
    {
        return DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.payment_date', [$dateRange['start'], $dateRange['end']])
            ->sum('payments.amount');
    }

    private function getTotalInvoiced($dateRange)
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');
    }

    private function getTotalLeads($dateRange)
    {
        return DB::table('crm_leads')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
    }

    private function getTotalDeals($dateRange)
    {
        return DB::table('crm_deals')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
    }

    private function getTotalCustomers()
    {
        return DB::table('customers')->count();
    }

    private function getNewCustomers($dateRange)
    {
        return DB::table('customers')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
    }

    private function getTotalProducts()
    {
        return DB::table('products')->count();
    }

    private function getActiveProducts()
    {
        return DB::table('products')->where('is_active', true)->count();
    }

    private function getCurrencyBreakdown($dateRange)
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                'currency',
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('currency')
            ->get();
    }

    /**
     * Parse filters from request
     */
    private function parseFilters(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $period = $request->get('period', 'monthly');
        
        if (!$startDate || !$endDate) {
            $dateRange = $this->getDefaultDateRange($period);
        } else {
            $dateRange = [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay()
            ];
        }
        
        return [
            'date_range' => $dateRange,
            'period' => $period,
            'currency' => $request->get('currency', 'all'),
            'customer_id' => $request->get('customer_id', 'all'),
            'category_id' => $request->get('category_id', 'all'),
            'sales_rep_id' => $request->get('sales_rep_id', 'all')
        ];
    }

    /**
     * Get filtered analytics data
     */
    private function getFilteredAnalyticsData($filters)
    {
        return [
            'revenue' => $this->getRevenueData($filters),
            'cash_flow' => $this->getCashFlowData($filters),
            'sales_performance' => $this->getSalesPerformanceData($filters),
            'customers' => $this->getCustomerData($filters),
            'products' => $this->getProductData($filters),
            'summary' => $this->getSummaryMetrics($filters['date_range'])
        ];
    }

    /**
     * Get filter options
     */
    private function getFilterOptions()
    {
        return [
            'currencies' => ['AED', 'USD'],
            'customers' => DB::table('customers')->select('id', 'name')->orderBy('name')->get(),
            'categories' => DB::table('categories')->select('id', 'name')->orderBy('name')->get(),
            'sales_reps' => DB::table('users')->whereIn('role_id', [1, 2])->select('id', 'name')->orderBy('name')->get(),
            'periods' => ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']
        ];
    }

    /**
     * Get default date range
     */
    private function getDefaultDateRange($period = 'monthly')
    {
        $endDate = Carbon::now()->endOfDay();
        
        switch ($period) {
            case 'daily':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                break;
            case 'weekly':
                $startDate = Carbon::now()->subWeeks(12)->startOfDay();
                break;
            case 'quarterly':
                $startDate = Carbon::now()->subQuarters(4)->startOfDay();
                break;
            case 'yearly':
                $startDate = Carbon::now()->subYears(3)->startOfDay();
                break;
            default: // monthly
                $startDate = Carbon::now()->subMonths(12)->startOfDay();
                break;
        }
        
        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }

    /**
     * Get group by format for SQL
     */
    private function getGroupByFormat($period)
    {
        return match($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'quarterly' => '%Y-%q',
            'yearly' => '%Y',
            default => '%Y-%m' // monthly
        };
    }

    /**
     * Generate periods for chart labels
     */
    private function generatePeriods($dateRange, $period)
    {
        $periods = [];
        $current = $dateRange['start']->copy();
        
        while ($current->lte($dateRange['end'])) {
            switch ($period) {
                case 'daily':
                    $periods[] = $current->format('M d');
                    $current->addDay();
                    break;
                case 'weekly':
                    $periods[] = 'Week ' . $current->format('W, Y');
                    $current->addWeek();
                    break;
                case 'quarterly':
                    $periods[] = 'Q' . $current->quarter . ' ' . $current->year;
                    $current->addQuarter();
                    break;
                case 'yearly':
                    $periods[] = $current->year;
                    $current->addYear();
                    break;
                default: // monthly
                    $periods[] = $current->format('M Y');
                    $current->addMonth();
                    break;
            }
        }
        
        return $periods;
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate($revenueTrends)
    {
        $data = $revenueTrends['combined'];
        if (count($data) < 2) return 0;
        
        $current = end($data);
        $previous = $data[count($data) - 2];
        
        if ($previous == 0) return 0;
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get cash flow forecast
     */
    private function getCashFlowForecast($dateRange)
    {
        // Simple forecast based on historical data
        $historicalData = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.payment_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        if ($historicalData->count() < 2) {
            return [];
        }
        
        // Calculate average monthly growth
        $totals = $historicalData->pluck('total')->toArray();
        $growthRates = [];
        
        for ($i = 1; $i < count($totals); $i++) {
            if ($totals[$i-1] > 0) {
                $growthRates[] = (($totals[$i] - $totals[$i-1]) / $totals[$i-1]) * 100;
            }
        }
        
        $avgGrowthRate = count($growthRates) > 0 ? array_sum($growthRates) / count($growthRates) : 0;
        $lastMonthTotal = end($totals);
        
        // Generate forecast for next 3 months
        $forecast = [];
        $currentMonth = Carbon::now()->startOfMonth();
        
        for ($i = 1; $i <= 3; $i++) {
            $forecastMonth = $currentMonth->copy()->addMonths($i);
            $forecastAmount = $lastMonthTotal * pow(1 + ($avgGrowthRate / 100), $i);
            
            $forecast[] = [
                'month' => $forecastMonth->format('M Y'),
                'amount' => round($forecastAmount, 2)
            ];
        }
        
        return $forecast;
    }

    /**
     * Get empty analytics data for error handling
     */
    private function getEmptyAnalyticsData()
    {
        return [
            'revenue' => [
                'trends' => ['labels' => [], 'aed' => [], 'usd' => [], 'combined' => []],
                'by_category' => [],
                'by_customer' => [],
                'by_sales_rep' => [],
                'growth_rate' => 0,
                'total_revenue' => ['aed' => 0, 'usd' => 0, 'combined' => 0],
                'currency_breakdown' => []
            ],
            'cash_flow' => [
                'cash_vs_invoiced' => ['invoiced' => [], 'cash_received' => []],
                'payment_status' => [],
                'outstanding_receivables' => [],
                'payment_methods' => [],
                'forecast' => [],
                'total_cash_received' => 0,
                'total_invoiced' => 0
            ],
            'sales_performance' => [
                'pipeline' => ['leads_by_status' => [], 'deals_by_stage' => []],
                'conversion_rates' => ['lead_to_qualified' => 0, 'qualified_to_won' => 0, 'lead_to_won' => 0],
                'win_loss_analysis' => [],
                'sales_cycle' => null,
                'top_products' => [],
                'total_leads' => 0,
                'total_deals' => 0
            ],
            'customers' => [
                'acquisition_trends' => [],
                'lifetime_value' => [],
                'segmentation' => ['high_value' => 0, 'medium_value' => 0, 'low_value' => 0],
                'top_customers' => [],
                'total_customers' => 0,
                'new_customers' => 0
            ],
            'products' => [
                'performance' => [],
                'category_performance' => [],
                'inventory_analysis' => [],
                'trends' => [],
                'total_products' => 0,
                'active_products' => 0
            ],
            'summary' => [
                'total_revenue' => ['aed' => 0, 'usd' => 0, 'combined' => 0],
                'total_cash_received' => 0,
                'total_leads' => 0,
                'total_deals' => 0,
                'total_customers' => 0,
                'new_customers' => 0,
                'conversion_rate' => 0,
                'avg_deal_size' => 0
            ]
        ];
    }

    /**
     * Get empty filter options for error handling
     */
    private function getEmptyFilterOptions()
    {
        return [
            'currencies' => ['AED', 'USD'],
            'customers' => [],
            'categories' => [],
            'sales_reps' => [],
            'periods' => ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']
        ];
    }
}