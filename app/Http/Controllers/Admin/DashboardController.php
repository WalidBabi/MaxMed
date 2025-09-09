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
            
            return view('admin.dashboard', compact('salesData'));
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
            
            return view('admin.dashboard', compact('salesData'));
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
} 