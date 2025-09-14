<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard.view')->only(['index']);
        $this->middleware('permission:dashboard.analytics')->only(['analytics']);
        $this->middleware('permission:dashboard.admin')->only(['admin']);
    }

    public function index()
    {
        try {
            $salesData = $this->getSalesChartData();
            
            // Get filter options for the dashboard
            $filterOptions = $this->getFilterOptions();
            
            return view('admin.dashboard', compact('salesData', 'filterOptions'));
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'environment' => app()->environment()
            ]);
            
            // Return empty sales data if there's an error
            $salesData = [
                'labels' => [],
                'aed_data' => [],
                'usd_data' => [],
                'proforma_data' => [],
                'combined_data' => [],
                'total_aed' => 0,
                'total_usd' => 0,
                'total_combined' => 0,
                'peak_months' => [],
                'zero_months' => []
            ];
            
            $filterOptions = [
                'currencies' => ['AED', 'USD'],
                'customers' => [],
                'categories' => []
            ];
            
            return view('admin.dashboard', compact('salesData', 'filterOptions'));
        }
    }

    /**
     * API endpoint for filtered sales data
     */
    public function getSalesData(Request $request)
    {
        try {
            $period = $request->get('period', 'monthly'); // daily, monthly, quarterly
            $currency = $request->get('currency', 'all'); // all, AED, USD
            $customerId = $request->get('customer_id', 'all');
            $categoryId = $request->get('category_id', 'all');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $salesData = $this->getFilteredSalesData($period, $currency, $customerId, $categoryId, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $salesData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Sales data API error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sales data',
                'data' => [
                    'labels' => [],
                    'datasets' => []
                ]
            ], 500);
        }
    }
    
    private function getSalesChartData()
    {
        try {
            // Get the date range from the first transaction to now
            $startDate = $this->getEarliestTransactionDate();
            $endDate = Carbon::now()->endOfMonth();
            
            // Initialize arrays for chart data
            $labels = [];
            $aedData = [];
            $usdData = [];
            $combinedData = [];
            $peakMonths = [];
            $zeroMonths = [];
            
            // Calculate the number of months between start and end date
            $monthsDiff = $startDate->diffInMonths($endDate) + 1;
            
            // Generate month labels from first transaction to now
            for ($i = 0; $i < $monthsDiff; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $labels[] = $month->format('M Y');
            }
            
            // Get final invoice data (actual sales)
            $invoiceData = $this->getInvoiceSalesData($startDate, $endDate);
            
            // Get converted quotes data (quotes that became invoices)
            $quoteData = $this->getConvertedQuotesData($startDate, $endDate);
            
            // Get proforma-only data (quotes converted to proforma but not final)
            $proformaData = $this->getProformaOnlyData($startDate, $endDate);
            
            // Combine and process data for each month
            for ($i = 0; $i < $monthsDiff; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $monthKey = $month->format('Y-m');
                
                $aedAmount = 0;
                $usdAmount = 0;
                
                // Add final invoice amounts (actual sales)
                if (isset($invoiceData[$monthKey])) {
                    $aedAmount += $invoiceData[$monthKey]['aed'] ?? 0;
                    $usdAmount += $invoiceData[$monthKey]['usd'] ?? 0;
                }
                
                // Add converted quote amounts (quotes that became invoices)
                if (isset($quoteData[$monthKey])) {
                    $aedAmount += $quoteData[$monthKey]['aed'] ?? 0;
                    $usdAmount += $quoteData[$monthKey]['usd'] ?? 0;
                }
                
                $aedData[] = round($aedAmount, 2);
                $usdData[] = round($usdAmount, 2);
                
                // Calculate combined total (AED + USD converted to AED at 3.67 rate)
                $combinedAmount = $aedAmount + ($usdAmount * 3.67);
                $combinedData[] = round($combinedAmount, 2);
                
                // Track zero sales months (only count months with no actual sales)
                if ($aedAmount == 0 && $usdAmount == 0) {
                    $zeroMonths[] = $month->format('M Y');
                }
            }
            
            // Find peak months
            $maxAed = max($aedData);
            $maxUsd = max($usdData);
            $maxCombined = max($combinedData);
            
            for ($i = 0; $i < $monthsDiff; $i++) {
                if ($aedData[$i] == $maxAed && $maxAed > 0) {
                    $peakMonths[] = $labels[$i] . ' (AED: ' . number_format($maxAed, 2) . ')';
                }
                if ($usdData[$i] == $maxUsd && $maxUsd > 0) {
                    $peakMonths[] = $labels[$i] . ' (USD: ' . number_format($maxUsd, 2) . ')';
                }
                if ($combinedData[$i] == $maxCombined && $maxCombined > 0) {
                    $peakMonths[] = $labels[$i] . ' (Combined: ' . number_format($maxCombined, 2) . ' AED)';
                }
            }
            
            // Remove duplicates from peak months
            $peakMonths = array_unique($peakMonths);
            
            // Process proforma-only data for the chart
            $proformaAedData = [];
            $proformaUsdData = [];
            
            for ($i = 0; $i < $monthsDiff; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $monthKey = $month->format('Y-m');
                
                $proformaAed = 0;
                $proformaUsd = 0;
                
                if (isset($proformaData[$monthKey])) {
                    $proformaAed = $proformaData[$monthKey]['aed'] ?? 0;
                    $proformaUsd = $proformaData[$monthKey]['usd'] ?? 0;
                }
                
                $proformaAedData[] = round($proformaAed, 2);
                $proformaUsdData[] = round($proformaUsd, 2);
            }
            
            // Calculate Revenue and Cash Flow metrics
            $revenueMetrics = $this->getRevenueMetrics();
            $cashFlowMetrics = $this->getCashFlowMetrics();
            
            return [
                'labels' => $labels,
                'aed_data' => $aedData,
                'usd_data' => $usdData,
                'proforma_data' => $proformaAedData, // Proforma-only data for chart
                'combined_data' => $combinedData,
                'total_aed' => array_sum($aedData),
                'total_usd' => array_sum($usdData),
                'total_combined' => array_sum($combinedData),
                'peak_months' => array_values($peakMonths),
                'zero_months' => $zeroMonths,
                'revenue' => $revenueMetrics,
                'cash_flow' => $cashFlowMetrics
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting sales chart data', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'labels' => [],
                'aed_data' => [],
                'usd_data' => [],
                'proforma_data' => [],
                'combined_data' => [],
                'total_aed' => 0,
                'total_usd' => 0,
                'total_combined' => 0,
                'peak_months' => [],
                'zero_months' => []
            ];
        }
    }
    
    private function getInvoiceSalesData($startDate, $endDate)
    {
        try {
            $data = [];
            
            // Check if invoices table exists
            DB::select("SELECT 1 FROM invoices LIMIT 1");
            
            // Only get final invoices for sales data, not proforma
            $invoices = DB::table('invoices')
                ->where('type', 'final') // Only final invoices count as actual sales
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                    'currency',
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('month', 'currency')
                ->get();
            
            foreach ($invoices as $invoice) {
                if (!isset($data[$invoice->month])) {
                    $data[$invoice->month] = ['aed' => 0, 'usd' => 0];
                }
                
                if (strtoupper($invoice->currency) === 'AED') {
                    $data[$invoice->month]['aed'] += $invoice->total;
                } else {
                    $data[$invoice->month]['usd'] += $invoice->total;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::info('Invoices table not available or error occurred', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    private function getConvertedQuotesData($startDate, $endDate)
    {
        try {
            $data = [];
            
            // Check if quotes table exists
            DB::select("SELECT 1 FROM quotes LIMIT 1");
            
            // Get quotes that have been converted to invoices (status = 'invoiced')
            $quotes = DB::table('quotes')
                ->where('status', 'invoiced')
                ->whereBetween('quote_date', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE_FORMAT(quote_date, "%Y-%m") as month'),
                    'currency',
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('month', 'currency')
                ->get();
            
            foreach ($quotes as $quote) {
                if (!isset($data[$quote->month])) {
                    $data[$quote->month] = ['aed' => 0, 'usd' => 0];
                }
                
                if (strtoupper($quote->currency) === 'AED') {
                    $data[$quote->month]['aed'] += $quote->total;
                } else {
                    $data[$quote->month]['usd'] += $quote->total;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::info('Quotes table not available or error occurred', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    private function getProformaOnlyData($startDate, $endDate)
    {
        try {
            $data = [];
            
            // Check if invoices table exists
            DB::select("SELECT 1 FROM invoices LIMIT 1");
            
            // Get proforma invoices that don't have corresponding final invoices
            $proformaInvoices = DB::table('invoices')
                ->where('type', 'proforma')
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('invoices as final_invoices')
                        ->whereColumn('final_invoices.parent_invoice_id', 'invoices.id')
                        ->where('final_invoices.type', 'final');
                })
                ->select(
                    DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                    'currency',
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('month', 'currency')
                ->get();
            
            foreach ($proformaInvoices as $invoice) {
                if (!isset($data[$invoice->month])) {
                    $data[$invoice->month] = ['aed' => 0, 'usd' => 0];
                }
                
                if (strtoupper($invoice->currency) === 'AED') {
                    $data[$invoice->month]['aed'] += $invoice->total;
                } else {
                    $data[$invoice->month]['usd'] += $invoice->total;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::info('Error getting proforma-only data', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get filter options for the dashboard
     */
    private function getFilterOptions()
    {
        try {
            $currencies = ['AED', 'USD'];
            
            // Get customers with sales
            $customers = DB::table('customers')
                ->join('invoices', 'customers.name', '=', 'invoices.customer_name')
                ->select('customers.id', 'customers.name')
                ->distinct()
                ->orderBy('customers.name')
                ->get();
            
            // Get categories with products that have sales
            $categories = DB::table('categories')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('invoice_items', 'products.id', '=', 'invoice_items.product_id')
                ->select('categories.id', 'categories.name')
                ->distinct()
                ->orderBy('categories.name')
                ->get();
            
            return [
                'currencies' => $currencies,
                'customers' => $customers,
                'categories' => $categories
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting filter options', ['error' => $e->getMessage()]);
            return [
                'currencies' => ['AED', 'USD'],
                'customers' => [],
                'categories' => []
            ];
        }
    }

    /**
     * Get filtered sales data based on parameters
     */
    private function getFilteredSalesData($period, $currency, $customerId, $categoryId, $startDate, $endDate)
    {
        try {
            // Set default date range if not provided
            if (!$startDate || !$endDate) {
                $endDate = Carbon::now();
                switch ($period) {
                    case 'daily':
                        $startDate = Carbon::now()->subDays(30);
                        break;
                    case 'quarterly':
                        $startDate = Carbon::now()->subQuarters(4);
                        break;
                    default: // monthly
                        $startDate = Carbon::now()->subMonths(12);
                        break;
                }
            } else {
                $startDate = Carbon::parse($startDate);
                $endDate = Carbon::parse($endDate);
            }

            // Get sales data first
            $salesData = $this->getSalesDataForPeriod($period, $startDate, $endDate, $currency, $customerId, $categoryId);
            
            // Generate labels only for periods that have data
            $labels = $this->generateLabelsFromData($salesData, $period);
            
            // Format data for Chart.js
            $datasets = $this->formatDataForChart($salesData, $currency);
            
            return [
                'labels' => $labels,
                'datasets' => $datasets,
                'summary' => $this->calculateSummary($salesData, $currency)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting filtered sales data', ['error' => $e->getMessage()]);
            return [
                'labels' => [],
                'datasets' => [],
                'summary' => ['total' => 0, 'count' => 0]
            ];
        }
    }

    /**
     * Generate labels based on period
     */
    private function generateLabels($period, $startDate, $endDate)
    {
        $labels = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            switch ($period) {
                case 'daily':
                    $labels[] = $current->format('M j');
                    $current->addDay();
                    break;
                case 'quarterly':
                    $labels[] = 'Q' . $current->quarter . ' ' . $current->year;
                    $current->addQuarter();
                    break;
                default: // monthly
                    $labels[] = $current->format('M Y');
                    $current->addMonth();
                    break;
            }
        }
        
        return $labels;
    }

    /**
     * Get sales data for specific period with filters
     */
    private function getSalesDataForPeriod($period, $startDate, $endDate, $currency, $customerId, $categoryId)
    {
        $data = [];
        
        // Build base query for invoices
        $invoiceQuery = DB::table('invoices')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('type', 'final'); // Only final invoices for sales data
        
        // Apply currency filter
        if ($currency !== 'all') {
            $invoiceQuery->where('currency', $currency);
        }
        
        // Apply customer filter
        if ($customerId !== 'all') {
            $customer = DB::table('customers')->where('id', $customerId)->first();
            if ($customer) {
                $invoiceQuery->where('customer_name', $customer->name);
            }
        }
        
        // Apply category filter through invoice items
        if ($categoryId !== 'all') {
            $invoiceQuery->whereExists(function ($query) use ($categoryId) {
                $query->select(DB::raw(1))
                    ->from('invoice_items')
                    ->join('products', 'invoice_items.product_id', '=', 'products.id')
                    ->whereColumn('invoice_items.invoice_id', 'invoices.id')
                    ->where('products.category_id', $categoryId);
            });
        }
        
        // Group by period
        $groupByFormat = $this->getGroupByFormat($period);
        
        $invoices = $invoiceQuery
            ->select(
                DB::raw("DATE_FORMAT(invoice_date, '{$groupByFormat}') as period"),
                'currency',
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('period', 'currency')
            ->get();
        
        // Process invoice data
        foreach ($invoices as $invoice) {
            $periodKey = $invoice->period;
            
            // For quarterly grouping, convert month to quarter
            if ($period === 'quarterly') {
                $periodKey = $this->convertMonthToQuarter($invoice->period);
            }
            
            if (!isset($data[$periodKey])) {
                $data[$periodKey] = ['AED' => 0, 'USD' => 0];
            }
            $data[$periodKey][$invoice->currency] += $invoice->total;
        }
        
        return $data;
    }

    /**
     * Get MySQL date format for grouping
     */
    private function getGroupByFormat($period)
    {
        switch ($period) {
            case 'daily':
                return '%Y-%m-%d';
            case 'quarterly':
                return '%Y-%m'; // Use monthly for now, we'll group by quarters in PHP
            default: // monthly
                return '%Y-%m';
        }
    }

    /**
     * Format data for Chart.js
     */
    private function formatDataForChart($salesData, $currency)
    {
        $datasets = [];
        
        // Extract data arrays for each currency
        $aedData = [];
        $usdData = [];
        $combinedData = [];
        
        // Sort data by period key to ensure proper order
        ksort($salesData);
        
        foreach ($salesData as $periodData) {
            $aedAmount = $periodData['AED'] ?? 0;
            $usdAmount = $periodData['USD'] ?? 0;
            $combinedAmount = $aedAmount + ($usdAmount * 3.67); // Convert USD to AED
            
            $aedData[] = round($aedAmount, 2);
            $usdData[] = round($usdAmount, 2);
            $combinedData[] = round($combinedAmount, 2);
        }
        
        if ($currency === 'all' || $currency === 'AED') {
            $datasets[] = [
                'label' => 'AED Sales',
                'data' => $aedData,
                'borderColor' => 'rgb(34, 197, 94)',
                'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                'borderWidth' => 3,
                'fill' => false,
                'tension' => 0.4,
                'pointBackgroundColor' => 'rgb(34, 197, 94)',
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 2,
                'pointRadius' => 6,
                'pointHoverRadius' => 8
            ];
        }
        
        if ($currency === 'all' || $currency === 'USD') {
            $datasets[] = [
                'label' => 'USD Sales',
                'data' => $usdData,
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'borderWidth' => 3,
                'fill' => false,
                'tension' => 0.4,
                'pointBackgroundColor' => 'rgb(59, 130, 246)',
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 2,
                'pointRadius' => 6,
                'pointHoverRadius' => 8
            ];
        }
        
        if ($currency === 'all') {
            $datasets[] = [
                'label' => 'Combined Sales (AED)',
                'data' => $combinedData,
                'borderColor' => 'rgb(147, 51, 234)',
                'backgroundColor' => 'rgba(147, 51, 234, 0.1)',
                'borderWidth' => 4,
                'fill' => false,
                'tension' => 0.4,
                'pointBackgroundColor' => 'rgb(147, 51, 234)',
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 3,
                'pointRadius' => 7,
                'pointHoverRadius' => 9,
                'borderDash' => [5, 5]
            ];
        }
        
        return $datasets;
    }

    /**
     * Generate labels from actual sales data
     */
    private function generateLabelsFromData($salesData, $period)
    {
        $labels = [];
        
        // Sort data by period key to ensure proper order
        ksort($salesData);
        
        foreach ($salesData as $periodKey => $periodData) {
            $labels[] = $this->formatPeriodKeyAsLabel($periodKey, $period);
        }
        
        return $labels;
    }

    /**
     * Format period key as display label
     */
    private function formatPeriodKeyAsLabel($periodKey, $period)
    {
        switch ($period) {
            case 'daily':
                // periodKey format: "2023-01-15"
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $periodKey);
                return $date->format('M j');
                
            case 'quarterly':
                // periodKey format: "2023-01" (converted from quarter)
                $date = \Carbon\Carbon::createFromFormat('Y-m', $periodKey);
                $quarter = ceil($date->month / 3);
                return 'Q' . $quarter . ' ' . $date->year;
                
            default: // monthly
                // periodKey format: "2023-01"
                $date = \Carbon\Carbon::createFromFormat('Y-m', $periodKey);
                return $date->format('M Y');
        }
    }

    /**
     * Convert chart label back to period key format
     */
    private function convertLabelToPeriodKey($label)
    {
        // Handle different label formats
        if (strpos($label, 'Q') === 0) {
            // Quarterly format: "Q1 2023" -> "2023-01"
            preg_match('/Q(\d) (\d{4})/', $label, $matches);
            if (count($matches) === 3) {
                $quarter = (int)$matches[1];
                $year = $matches[2];
                $month = ($quarter - 1) * 3 + 1; // Q1=1, Q2=4, Q3=7, Q4=10
                return sprintf('%04d-%02d', $year, $month);
            }
        } elseif (preg_match('/^[A-Za-z]{3} \d{4}$/', $label)) {
            // Monthly format: "Jan 2023" -> "2023-01"
            $date = \Carbon\Carbon::createFromFormat('M Y', $label);
            return $date->format('Y-m');
        } elseif (preg_match('/^[A-Za-z]{3} \d{1,2}$/', $label)) {
            // Daily format: "Jan 15" -> current year
            $currentYear = date('Y');
            $date = \Carbon\Carbon::createFromFormat('M j', $label)->year($currentYear);
            return $date->format('Y-m-d');
        }
        
        // Fallback: try to parse as-is
        return $label;
    }

    /**
     * Convert month (YYYY-MM) to quarter (YYYY-QX)
     */
    private function convertMonthToQuarter($month)
    {
        $year = substr($month, 0, 4);
        $monthNum = (int)substr($month, 5, 2);
        $quarter = ceil($monthNum / 3);
        return $year . '-Q' . $quarter;
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($salesData, $currency)
    {
        $total = 0;
        $count = 0;
        
        foreach ($salesData as $periodData) {
            if ($currency === 'all') {
                $total += $periodData['AED'] + ($periodData['USD'] * 3.67); // Convert USD to AED
            } else {
                $total += $periodData[$currency] ?? 0;
            }
            $count++;
        }
        
        return [
            'total' => round($total, 2),
            'count' => $count,
            'average' => $count > 0 ? round($total / $count, 2) : 0
        ];
    }

    /**
     * Get the earliest transaction date from invoices and quotes
     */
    private function getEarliestTransactionDate()
    {
        try {
            $earliestInvoice = null;
            $earliestQuote = null;
            
            // Get earliest invoice date
            try {
                $earliestInvoice = DB::table('invoices')->min('invoice_date');
            } catch (\Exception $e) {
                // Table might not exist
            }
            
            // Get earliest quote date
            try {
                $earliestQuote = DB::table('quotes')->min('quote_date');
            } catch (\Exception $e) {
                // Table might not exist
            }
            
            // Find the earliest date between invoices and quotes
            $dates = array_filter([$earliestInvoice, $earliestQuote]);
            
            if (empty($dates)) {
                // If no transactions found, default to 12 months ago
                return Carbon::now()->subMonths(11)->startOfMonth();
            }
            
            $earliestDate = min($dates);
            return Carbon::parse($earliestDate)->startOfMonth();
            
        } catch (\Exception $e) {
            Log::error('Error getting earliest transaction date', ['error' => $e->getMessage()]);
            // Fallback to 12 months ago
            return Carbon::now()->subMonths(11)->startOfMonth();
        }
    }
    
    /**
     * Get Revenue metrics (all sent invoices regardless of payment status)
     */
    private function getRevenueMetrics()
    {
        try {
            // Revenue = All sent final invoices (regardless of payment status)
            $revenueQuery = DB::table('invoices')
                ->where('type', 'final')
                ->where('status', 'sent');
            
            $aedRevenue = (clone $revenueQuery)->where('currency', 'AED')->sum('total_amount');
            $usdRevenue = (clone $revenueQuery)->where('currency', 'USD')->sum('total_amount');
            
            // Convert USD to AED for combined total
            $usdToAedRate = 3.67;
            $combinedRevenue = $aedRevenue + ($usdRevenue * $usdToAedRate);
            
            return [
                'aed' => $aedRevenue,
                'usd' => $usdRevenue,
                'combined' => $combinedRevenue
            ];
            
        } catch (\Exception $e) {
            Log::error('Error calculating revenue metrics', ['error' => $e->getMessage()]);
            return ['aed' => 0, 'usd' => 0, 'combined' => 0];
        }
    }
    
    /**
     * Get Cash Flow metrics (only paid invoices)
     */
    private function getCashFlowMetrics()
    {
        try {
            // Cash Flow = Only paid final invoices
            $cashFlowQuery = DB::table('invoices')
                ->where('type', 'final')
                ->where('status', 'sent')
                ->where('payment_status', 'paid');
            
            $aedCashFlow = (clone $cashFlowQuery)->where('currency', 'AED')->sum('total_amount');
            $usdCashFlow = (clone $cashFlowQuery)->where('currency', 'USD')->sum('total_amount');
            
            // Convert USD to AED for combined total
            $usdToAedRate = 3.67;
            $combinedCashFlow = $aedCashFlow + ($usdCashFlow * $usdToAedRate);
            
            return [
                'aed' => $aedCashFlow,
                'usd' => $usdCashFlow,
                'combined' => $combinedCashFlow
            ];
            
        } catch (\Exception $e) {
            Log::error('Error calculating cash flow metrics', ['error' => $e->getMessage()]);
            return ['aed' => 0, 'usd' => 0, 'combined' => 0];
        }
    }
} 