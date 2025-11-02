<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecurringExpense;
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
            $expenseKpis = $this->getBusinessExpenseKpis();
            
            return view('admin.dashboard', compact('salesData', 'expenseKpis'));
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
            $expenseKpis = $this->getBusinessExpenseKpis();
            
            return view('admin.dashboard', compact('salesData', 'expenseKpis'));
        }
    }

    /**
     * API endpoint for filtered sales data
     */
    public function getSalesData(Request $request)
    {
        try {
            $period = $request->get('period', 'monthly'); // daily, monthly, quarterly
            $selectedDate = $request->get('selected_date');
            $selectedMonth = $request->get('selected_month');
            $selectedYear = $request->get('selected_year');
            $selectedQuarter = $request->get('selected_quarter');
            
            $salesData = $this->getFilteredSalesData($period, $selectedDate, $selectedMonth, $selectedYear, $selectedQuarter);
            
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
            
            // Get ONLY final invoice data (actual sales) - no quotes, no proforma
            $invoiceData = $this->getInvoiceSalesData($startDate, $endDate);
            
            // Process data for each month using ONLY final invoices
            for ($i = 0; $i < $monthsDiff; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $monthKey = $month->format('Y-m');
                
                $aedAmount = 0;
                $usdAmount = 0;
                
                // Add ONLY final invoice amounts (actual sales)
                if (isset($invoiceData[$monthKey])) {
                    $aedAmount = $invoiceData[$monthKey]['aed'] ?? 0;
                    $usdAmount = $invoiceData[$monthKey]['usd'] ?? 0;
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
            
            // Calculate Revenue and Cash Flow metrics
            $revenueMetrics = $this->getRevenueMetrics();
            $cashFlowMetrics = $this->getCashFlowMetrics();
            
            return [
                'labels' => $labels,
                'aed_data' => $aedData,
                'usd_data' => $usdData,
                'proforma_data' => [], // No proforma data - using final invoices only
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
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('GROUP_CONCAT(CONCAT(invoice_number, " (", customer_name, " - ", total_amount, " ", currency, ")") SEPARATOR "; ") as invoice_details')
                )
                ->groupBy('month', 'currency')
                ->get();
            
            foreach ($invoices as $invoice) {
                if (!isset($data[$invoice->month])) {
                    $data[$invoice->month] = ['aed' => 0, 'usd' => 0, 'invoice_details' => ['aed' => [], 'usd' => []]];
                }
                
                if (strtoupper($invoice->currency) === 'AED') {
                    $data[$invoice->month]['aed'] += $invoice->total;
                    $data[$invoice->month]['invoice_details']['aed'][] = $invoice->invoice_details;
                } else {
                    $data[$invoice->month]['usd'] += $invoice->total;
                    $data[$invoice->month]['invoice_details']['usd'][] = $invoice->invoice_details;
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
     * Get filtered sales data based on parameters
     */
    private function getFilteredSalesData($period, $selectedDate = null, $selectedMonth = null, $selectedYear = null, $selectedQuarter = null)
    {
        try {
            // Calculate date range based on period and selected values
            [$startDate, $endDate] = $this->calculateDateRange($period, $selectedDate, $selectedMonth, $selectedYear, $selectedQuarter);

            // Get sales data for the period
            $salesData = $this->getSalesDataForPeriod($period, $startDate, $endDate);
            
            // Generate labels only for periods that have data
            $labels = $this->generateLabelsFromData($salesData, $period, $startDate, $endDate);
            
            // Format data for Chart.js
            $datasets = $this->formatDataForChart($salesData, $period, $labels);
            
            return [
                'labels' => $labels,
                'datasets' => $datasets,
                'summary' => $this->calculateSummary($salesData)
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
     * Calculate date range based on period and selected values
     */
    private function calculateDateRange($period, $selectedDate, $selectedMonth, $selectedYear, $selectedQuarter)
    {
        switch ($period) {
            case 'daily':
                if ($selectedDate) {
                    $date = Carbon::parse($selectedDate);
                    // For daily view, show the selected day plus surrounding days (e.g., ±15 days)
                    $startDate = $date->copy()->subDays(15)->startOfDay();
                    $endDate = $date->copy()->addDays(15)->endOfDay();
                } else {
                    // Default: last 30 days
                    $endDate = Carbon::now()->endOfDay();
                    $startDate = Carbon::now()->subDays(30)->startOfDay();
                }
                break;
                
            case 'monthly':
                if ($selectedMonth) {
                    $date = Carbon::parse($selectedMonth . '-01');
                    // For monthly view, show the selected month plus surrounding months (e.g., ±6 months)
                    $startDate = $date->copy()->subMonths(6)->startOfMonth();
                    $endDate = $date->copy()->addMonths(6)->endOfMonth();
                } else {
                    // Default: last 12 months
                    $endDate = Carbon::now();
                    $startDate = Carbon::now()->subMonths(12);
                }
                break;
                
            case 'quarterly':
                if ($selectedYear && $selectedQuarter) {
                    $year = (int)$selectedYear;
                    $quarter = (int)$selectedQuarter;
                    
                    // Calculate start and end of the selected quarter only
                    $quarterStartMonth = ($quarter - 1) * 3 + 1;
                    $startDate = Carbon::create($year, $quarterStartMonth, 1)->startOfMonth();
                    $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                } else {
                    // Default: use a wider range to capture all data
                    $endDate = Carbon::now()->addYear();
                    $startDate = Carbon::now()->subYears(2);
                }
                break;
                
            default:
                $endDate = Carbon::now();
                $startDate = Carbon::now()->subMonths(12);
                break;
        }
        
        return [$startDate, $endDate];
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
     * Get sales data for specific period
     */
    private function getSalesDataForPeriod($period, $startDate, $endDate)
    {
        $data = [];
        
        // Build base query for invoices
        $invoiceQuery = DB::table('invoices')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('type', 'final'); // Only final invoices for sales data
        
        // For quarterly period with a specific single quarter (3 months or less), group by month
        $actualGroupingPeriod = $period;
        if ($period === 'quarterly' && $startDate->diffInMonths($endDate) <= 3) {
            $actualGroupingPeriod = 'monthly';
        }
        
        // Group by period
        $groupByFormat = $this->getGroupByFormat($actualGroupingPeriod);
        
        $invoices = $invoiceQuery
            ->select(
                DB::raw("DATE_FORMAT(invoice_date, '{$groupByFormat}') as period"),
                'currency',
                DB::raw('SUM(total_amount) as total'),
                DB::raw('GROUP_CONCAT(CONCAT(invoice_number, " (", customer_name, " - ", total_amount, " ", currency, ")") SEPARATOR "; ") as invoice_details')
            )
            ->groupBy('period', 'currency')
            ->get();
        
        // Process invoice data
        foreach ($invoices as $invoice) {
            $periodKey = $invoice->period;
            
            // For quarterly grouping with multiple quarters, convert month to quarter
            if ($period === 'quarterly' && $startDate->diffInMonths($endDate) > 3) {
                $periodKey = $this->convertMonthToQuarter($invoice->period);
            }
            
            if (!isset($data[$periodKey])) {
                $data[$periodKey] = ['AED' => 0, 'USD' => 0, 'invoice_details' => ['AED' => [], 'USD' => []]];
            }
            $data[$periodKey][$invoice->currency] += $invoice->total;
            $data[$periodKey]['invoice_details'][$invoice->currency][] = $invoice->invoice_details;
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
    private function formatDataForChart($salesData, $period = null, $labels = [])
    {
        $datasets = [];
        
        // Extract data arrays for each currency
        $aedData = [];
        $usdData = [];
        $combinedData = [];
        
        // Create a mapping of labels to data
        $labelToDataMap = [];
        
        // For quarterly data, ensure we have all quarters represented
        if ($period === 'quarterly') {
            // Don't override the sales data with a template - use the actual data
            // The labels will be generated based on the actual data range
        }
        
        // Sort data by period key to ensure proper order
        ksort($salesData);
        
        // Create mapping from period keys to labels
        foreach ($labels as $index => $label) {
            $periodKey = $this->convertLabelToPeriodKey($label, $period);
            $labelToDataMap[$label] = $salesData[$periodKey] ?? ['AED' => 0, 'USD' => 0];
        }
        
        // Generate data arrays in the same order as labels
        $aedInvoiceDetails = [];
        $usdInvoiceDetails = [];
        $combinedInvoiceDetails = [];
        
        foreach ($labels as $label) {
            $periodData = $labelToDataMap[$label];
            $aedAmount = $periodData['AED'] ?? 0;
            $usdAmount = $periodData['USD'] ?? 0;
            $combinedAmount = $aedAmount + ($usdAmount * 3.67); // Convert USD to AED
            
            $aedData[] = round($aedAmount, 2);
            $usdData[] = round($usdAmount, 2);
            $combinedData[] = round($combinedAmount, 2);
            
            // Collect invoice details for tooltips
            $aedInvoiceDetails[] = $periodData['invoice_details']['AED'] ?? [];
            $usdInvoiceDetails[] = $periodData['invoice_details']['USD'] ?? [];
            $combinedInvoiceDetails[] = array_merge(
                $periodData['invoice_details']['AED'] ?? [],
                $periodData['invoice_details']['USD'] ?? []
            );
        }
        
        // Always show all datasets
        $datasets[] = [
            'label' => 'AED Sales',
            'data' => $aedData,
            'invoiceDetails' => $aedInvoiceDetails,
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
        
        $datasets[] = [
            'label' => 'USD Sales',
            'data' => $usdData,
            'invoiceDetails' => $usdInvoiceDetails,
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
        
        $datasets[] = [
            'label' => 'Combined Sales (AED)',
            'data' => $combinedData,
            'invoiceDetails' => $combinedInvoiceDetails,
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
        
        return $datasets;
    }

    /**
     * Generate labels from actual sales data
     */
    private function generateLabelsFromData($salesData, $period, $startDate = null, $endDate = null)
    {
        $labels = [];
        
        // If we have specific date range, generate labels based on that range
        if ($startDate && $endDate) {
            $current = $startDate->copy();
            
            switch ($period) {
                case 'daily':
                    while ($current->lte($endDate)) {
                        $labels[] = $current->format('M j');
                        $current->addDay();
                    }
                    break;
                    
                case 'quarterly':
                    // For quarterly view, if we have a specific range (single quarter), 
                    // show monthly breakdown within that quarter
                    if ($startDate->diffInMonths($endDate) <= 3) {
                        while ($current->lte($endDate)) {
                            $labels[] = $current->format('M Y');
                            $current->addMonth();
                        }
                    } else {
                        // Multiple quarters
                        while ($current->lte($endDate)) {
                            $labels[] = 'Q' . $current->quarter . ' ' . $current->year;
                            $current->addQuarter();
                        }
                    }
                    break;
                    
                default: // monthly
                    while ($current->lte($endDate)) {
                        $labels[] = $current->format('M Y');
                        $current->addMonth();
                    }
                    break;
            }
            
            return $labels;
        }
        
        // Fallback to original logic for backward compatibility
        // For quarterly, ensure we have all quarters represented based on data
        if ($period === 'quarterly') {
            $currentDate = Carbon::now();
            $quarters = [];
            
            // Generate quarters based on actual data range
            if (!empty($salesData)) {
                // Find the earliest and latest quarters from the data
                $earliestQuarter = null;
                $latestQuarter = null;
                
                foreach ($salesData as $periodKey => $periodData) {
                    if (strpos($periodKey, '-Q') !== false) {
                        if ($earliestQuarter === null || $periodKey < $earliestQuarter) {
                            $earliestQuarter = $periodKey;
                        }
                        if ($latestQuarter === null || $periodKey > $latestQuarter) {
                            $latestQuarter = $periodKey;
                        }
                    }
                }
                
                if ($earliestQuarter && $latestQuarter) {
                    // Parse quarter strings manually
                    $startParts = explode('-Q', $earliestQuarter);
                    $endParts = explode('-Q', $latestQuarter);
                    
                    $startYear = (int)$startParts[0];
                    $startQuarterNum = (int)$startParts[1];
                    $endYear = (int)$endParts[0];
                    $endQuarterNum = (int)$endParts[1];
                    
                    $currentYear = $startYear;
                    $currentQuarter = $startQuarterNum;
                    
                    while ($currentYear < $endYear || ($currentYear == $endYear && $currentQuarter <= $endQuarterNum)) {
                        $quarterKey = $currentYear . '-Q' . $currentQuarter;
                        $quarters[$quarterKey] = 'Q' . $currentQuarter . ' ' . $currentYear;
                        
                        $currentQuarter++;
                        if ($currentQuarter > 4) {
                            $currentQuarter = 1;
                            $currentYear++;
                        }
                    }
                }
            }
            
            // If no quarters found, generate last 4 quarters
            if (empty($quarters)) {
                for ($i = 3; $i >= 0; $i--) {
                    $quarterDate = $currentDate->copy()->subQuarters($i);
                    $year = $quarterDate->year;
                    $quarter = $quarterDate->quarter;
                    $quarterKey = $year . '-Q' . $quarter;
                    $quarters[$quarterKey] = 'Q' . $quarter . ' ' . $year;
                }
            }
            
            // Sort quarters chronologically (Q1, Q2, Q3, Q4)
            ksort($quarters);
            
            return array_values($quarters);
        }
        
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
                // periodKey format: "2023-Q1" (converted from quarter)
                if (strpos($periodKey, '-Q') !== false) {
                    // Format: "2023-Q1"
                    $parts = explode('-Q', $periodKey);
                    return 'Q' . $parts[1] . ' ' . $parts[0];
                } else {
                    // Fallback for old format: "2023-01"
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $periodKey);
                    $quarter = ceil($date->month / 3);
                    return 'Q' . $quarter . ' ' . $date->year;
                }
                
            default: // monthly
                // periodKey format: "2023-01"
                $date = \Carbon\Carbon::createFromFormat('Y-m', $periodKey);
                return $date->format('M Y');
        }
    }

    /**
     * Convert chart label back to period key format
     */
    private function convertLabelToPeriodKey($label, $period)
    {
        // Handle different label formats
        if (strpos($label, 'Q') === 0) {
            // Quarterly format: "Q1 2023" -> "2023-Q1"
            preg_match('/Q(\d) (\d{4})/', $label, $matches);
            if (count($matches) === 3) {
                $quarter = (int)$matches[1];
                $year = $matches[2];
                return $year . '-Q' . $quarter;
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
    private function calculateSummary($salesData)
    {
        $total = 0;
        $count = 0;
        
        foreach ($salesData as $periodData) {
            // Always calculate combined total (AED + USD converted to AED)
            $total += $periodData['AED'] + ($periodData['USD'] * 3.67);
            $count++;
        }
        
        return [
            'total' => round($total, 2),
            'count' => $count,
            'average' => $count > 0 ? round($total / $count, 2) : 0
        ];
    }

    /**
     * Get the start date for charts (always January 1st of current year)
     */
    private function getEarliestTransactionDate()
    {
        try {
            // Always start from January 1st of the current year for complete yearly view
            return Carbon::now()->startOfYear();
            
        } catch (\Exception $e) {
            Log::error('Error getting earliest transaction date', ['error' => $e->getMessage()]);
            // Fallback to start of current year
            return Carbon::now()->startOfYear();
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

    /**
     * Get Business Expenses KPIs (This Month and Next Month totals)
     */
    private function getBusinessExpenseKpis()
    {
        try {
            $now = Carbon::now();
            $expenses = RecurringExpense::where('status', RecurringExpense::STATUS_ACTIVE)->get();
            $thisMonth = 0.0;
            $nextMonth = 0.0;
            
            foreach ($expenses as $exp) {
                // Check this month
                if ($exp->isActiveInMonth((int) $now->format('n'))) {
                    // Apply frequency filter for this month
                    $includeThisMonth = $this->shouldIncludeExpenseThisMonth($exp, $now);
                    if ($includeThisMonth) {
                        $thisMonth += (float) $exp->unit_amount * (int) $exp->quantity;
                    }
                }
                
                // Check next month
                $nextMonthDate = $now->copy()->addMonth();
                if ($exp->isActiveInMonth((int) $nextMonthDate->format('n'))) {
                    // Apply frequency filter for next month
                    $includeNextMonth = $this->shouldIncludeExpenseThisMonth($exp, $nextMonthDate);
                    if ($includeNextMonth) {
                        $nextMonth += (float) $exp->unit_amount * (int) $exp->quantity;
                    }
                }
            }
            
            return [
                'this_month_total' => $thisMonth,
                'next_month_total' => $nextMonth,
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating business expense KPIs', ['error' => $e->getMessage()]);
            return [
                'this_month_total' => 0,
                'next_month_total' => 0,
            ];
        }
    }

    /**
     * Check if an expense should be included for a given month based on frequency
     */
    private function shouldIncludeExpenseThisMonth($expense, $monthDate)
    {
        switch ($expense->frequency) {
            case RecurringExpense::FREQUENCY_MONTHLY:
            case RecurringExpense::FREQUENCY_WEEKLY:
                return true; // Always include monthly/weekly expenses
                
            case RecurringExpense::FREQUENCY_QUARTERLY:
                if (!$expense->start_date) {
                    return false;
                }
                $startMonth = Carbon::parse($expense->start_date)->month;
                // Check if the month is in the same position within the quarter cycle
                // (e.g., if start is Jan, include Jan, Apr, Jul, Oct)
                return (($monthDate->month - $startMonth) % 3 === 0);
                
            case RecurringExpense::FREQUENCY_YEARLY:
                if (!$expense->start_date) {
                    return false;
                }
                $startMonth = Carbon::parse($expense->start_date)->month;
                return (int) $monthDate->format('n') === $startMonth;
                
            default:
                return true;
        }
    }

} 