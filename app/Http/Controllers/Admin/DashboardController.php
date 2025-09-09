<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
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
            // Get the last 12 months of data
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            
            // Initialize arrays for chart data
            $labels = [];
            $aedData = [];
            $usdData = [];
            $combinedData = [];
            $peakMonths = [];
            $zeroMonths = [];
            
            // Generate month labels
            for ($i = 0; $i < 12; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $labels[] = $month->format('M Y');
            }
            
            // Get invoice data
            $invoiceData = $this->getInvoiceSalesData($startDate, $endDate);
            
            // Get converted quotes data
            $quoteData = $this->getConvertedQuotesData($startDate, $endDate);
            
            // Combine and process data for each month
            for ($i = 0; $i < 12; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $monthKey = $month->format('Y-m');
                
                $aedAmount = 0;
                $usdAmount = 0;
                
                // Add invoice amounts
                if (isset($invoiceData[$monthKey])) {
                    $aedAmount += $invoiceData[$monthKey]['aed'] ?? 0;
                    $usdAmount += $invoiceData[$monthKey]['usd'] ?? 0;
                }
                
                // Add converted quote amounts
                if (isset($quoteData[$monthKey])) {
                    $aedAmount += $quoteData[$monthKey]['aed'] ?? 0;
                    $usdAmount += $quoteData[$monthKey]['usd'] ?? 0;
                }
                
                $aedData[] = round($aedAmount, 2);
                $usdData[] = round($usdAmount, 2);
                
                // Calculate combined total (AED + USD converted to AED at 3.67 rate)
                $combinedAmount = $aedAmount + ($usdAmount * 3.67);
                $combinedData[] = round($combinedAmount, 2);
                
                // Track zero sales months
                if ($aedAmount == 0 && $usdAmount == 0) {
                    $zeroMonths[] = $month->format('M Y');
                }
            }
            
            // Find peak months
            $maxAed = max($aedData);
            $maxUsd = max($usdData);
            $maxCombined = max($combinedData);
            
            for ($i = 0; $i < 12; $i++) {
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
            
            return [
                'labels' => $labels,
                'aed_data' => $aedData,
                'usd_data' => $usdData,
                'combined_data' => $combinedData,
                'total_aed' => array_sum($aedData),
                'total_usd' => array_sum($usdData),
                'total_combined' => array_sum($combinedData),
                'peak_months' => array_values($peakMonths),
                'zero_months' => $zeroMonths
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
            
            $invoices = DB::table('invoices')
                ->whereIn('status', ['confirmed', 'paid', 'completed'])
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
            
            $quotes = DB::table('quotes')
                ->where('status', 'converted')
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

            // Generate labels based on period
            $labels = $this->generateLabels($period, $startDate, $endDate);
            
            // Get sales data
            $salesData = $this->getSalesDataForPeriod($period, $startDate, $endDate, $currency, $customerId, $categoryId);
            
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
} 