<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsExportService
{
    /**
     * Export analytics data to Excel format
     */
    public function exportToExcel($filters)
    {
        // This would integrate with Laravel Excel package
        // For now, return a placeholder response
        
        $data = $this->prepareExportData($filters);
        
        return [
            'success' => true,
            'message' => 'Excel export functionality will be implemented with Laravel Excel package',
            'data' => $data
        ];
    }

    /**
     * Export analytics data to PDF format
     */
    public function exportToPDF($filters)
    {
        // This would integrate with a PDF generation package like DomPDF or TCPDF
        // For now, return a placeholder response
        
        $data = $this->prepareExportData($filters);
        
        return [
            'success' => true,
            'message' => 'PDF export functionality will be implemented with DomPDF package',
            'data' => $data
        ];
    }

    /**
     * Export analytics data to CSV format
     */
    public function exportToCSV($filters)
    {
        $data = $this->prepareExportData($filters);
        
        $csvData = $this->convertToCSV($data);
        
        return [
            'success' => true,
            'message' => 'CSV export completed',
            'data' => $csvData
        ];
    }

    /**
     * Prepare data for export
     */
    private function prepareExportData($filters)
    {
        $dateRange = $filters['date_range'] ?? $this->getDefaultDateRange();
        
        return [
            'summary' => $this->getSummaryData($dateRange),
            'revenue_trends' => $this->getRevenueTrendsData($dateRange),
            'top_customers' => $this->getTopCustomersData($dateRange),
            'top_products' => $this->getTopProductsData($dateRange),
            'cash_flow' => $this->getCashFlowData($dateRange),
            'export_date' => now()->format('Y-m-d H:i:s'),
            'date_range' => [
                'start' => $dateRange['start']->format('Y-m-d'),
                'end' => $dateRange['end']->format('Y-m-d')
            ]
        ];
    }

    /**
     * Get summary data for export
     */
    private function getSummaryData($dateRange)
    {
        $totalRevenue = DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $totalCashReceived = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.payment_date', [$dateRange['start'], $dateRange['end']])
            ->sum('payments.amount');

        $totalLeads = DB::table('crm_leads')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $totalDeals = DB::table('crm_deals')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_cash_received' => $totalCashReceived,
            'total_leads' => $totalLeads,
            'total_deals' => $totalDeals,
            'conversion_rate' => $totalLeads > 0 ? round(($totalDeals / $totalLeads) * 100, 2) : 0
        ];
    }

    /**
     * Get revenue trends data for export
     */
    private function getRevenueTrendsData($dateRange)
    {
        return DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                'currency',
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month', 'currency')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get top customers data for export
     */
    private function getTopCustomersData($dateRange)
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
            ->limit(50)
            ->get();
    }

    /**
     * Get top products data for export
     */
    private function getTopProductsData($dateRange)
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
                DB::raw('SUM(invoice_items.amount) as total_revenue'),
                DB::raw('AVG(invoice_items.unit_price) as avg_price')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(50)
            ->get();
    }

    /**
     * Get cash flow data for export
     */
    private function getCashFlowData($dateRange)
    {
        $invoiced = DB::table('invoices')
            ->where('type', 'final')
            ->whereBetween('invoice_date', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $cashReceived = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.payment_date', [$dateRange['start'], $dateRange['end']])
            ->sum('payments.amount');

        return [
            'total_invoiced' => $invoiced,
            'total_cash_received' => $cashReceived,
            'outstanding_amount' => $invoiced - $cashReceived
        ];
    }

    /**
     * Convert data to CSV format
     */
    private function convertToCSV($data)
    {
        $csv = "MaxMed Sales Analytics Export\n";
        $csv .= "Export Date: " . $data['export_date'] . "\n";
        $csv .= "Date Range: " . $data['date_range']['start'] . " to " . $data['date_range']['end'] . "\n\n";

        // Summary section
        $csv .= "SUMMARY METRICS\n";
        $csv .= "Total Revenue," . $data['summary']['total_revenue'] . "\n";
        $csv .= "Total Cash Received," . $data['summary']['total_cash_received'] . "\n";
        $csv .= "Total Leads," . $data['summary']['total_leads'] . "\n";
        $csv .= "Total Deals," . $data['summary']['total_deals'] . "\n";
        $csv .= "Conversion Rate," . $data['summary']['conversion_rate'] . "%\n\n";

        // Revenue trends section
        $csv .= "REVENUE TRENDS\n";
        $csv .= "Month,Currency,Total,Count\n";
        foreach ($data['revenue_trends'] as $trend) {
            $csv .= $trend->month . "," . $trend->currency . "," . $trend->total . "," . $trend->count . "\n";
        }
        $csv .= "\n";

        // Top customers section
        $csv .= "TOP CUSTOMERS\n";
        $csv .= "Customer Name,Currency,Total Spent,Order Count,Avg Order Value\n";
        foreach ($data['top_customers'] as $customer) {
            $csv .= $customer->customer_name . "," . $customer->currency . "," . $customer->total_spent . "," . $customer->order_count . "," . $customer->avg_order_value . "\n";
        }
        $csv .= "\n";

        // Top products section
        $csv .= "TOP PRODUCTS\n";
        $csv .= "Product Name,SKU,Total Sold,Total Revenue,Avg Price\n";
        foreach ($data['top_products'] as $product) {
            $csv .= $product->product_name . "," . $product->sku . "," . $product->total_sold . "," . $product->total_revenue . "," . $product->avg_price . "\n";
        }

        return $csv;
    }

    /**
     * Get default date range
     */
    private function getDefaultDateRange()
    {
        return [
            'start' => Carbon::now()->subMonths(12)->startOfDay(),
            'end' => Carbon::now()->endOfDay()
        ];
    }
}
