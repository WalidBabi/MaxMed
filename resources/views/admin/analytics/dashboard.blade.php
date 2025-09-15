@extends('admin.layouts.app')

@section('title', 'Enterprise Sales Analytics Dashboard - MaxMed')

@section('content')
@can('dashboard.analytics')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Enterprise Sales Analytics Dashboard</h1>
                <p class="text-gray-600 mt-2">Comprehensive sales insights with advanced analytics and real-time data</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ now()->format('l, F j, Y') }}
                </span>
                <button id="refresh-data" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-2xl shadow-lg ring-1 ring-gray-200/50 p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">🔍 Advanced Analytics Filters</h3>
                <p class="text-sm text-gray-600">Customize your analytics view with powerful filtering options</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6">
                <!-- Time Period -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Time Period
                        </span>
                    </label>
                    <select id="period-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <!-- Currency -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Currency
                        </span>
                    </label>
                    <select id="currency-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">All Currencies</option>
                        <option value="AED">AED</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Date Range
                        </span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" id="start-date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="date" id="end-date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Export Options -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                        </span>
                    </label>
                    <div class="flex space-x-2">
                        <button id="export-pdf" class="flex-1 inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                            PDF
                        </button>
                        <button id="export-excel" class="flex-1 inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                            Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 shadow-sm ring-1 ring-blue-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-revenue">Loading...</p>
                        <p class="text-xs text-blue-500" id="revenue-period">This month</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Cash Flow -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 shadow-sm ring-1 ring-green-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Cash Received</p>
                        <p class="text-2xl font-bold text-gray-900" id="cash-received">Loading...</p>
                        <p class="text-xs text-green-500" id="cash-period">This month</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Leads -->
            <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 shadow-sm ring-1 ring-purple-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Total Leads</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-leads">Loading...</p>
                        <p class="text-xs text-purple-500" id="leads-period">This month</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-purple-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl p-6 shadow-sm ring-1 ring-orange-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-600">Conversion Rate</p>
                        <p class="text-2xl font-bold text-gray-900" id="conversion-rate">Loading...</p>
                        <p class="text-xs text-orange-500" id="conversion-period">Lead to Deal</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-orange-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Revenue Trends Chart -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Trends</h3>
                <p class="text-sm text-gray-600">Revenue performance over time</p>
            </div>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Cash Flow Analysis</h3>
                <p class="text-sm text-gray-600">Cash received vs invoiced amounts</p>
            </div>
            <div class="h-80">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Analytics Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Sales Pipeline -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Sales Pipeline</h3>
                <p class="text-sm text-gray-600">Lead and deal distribution</p>
            </div>
            <div class="h-64">
                <canvas id="pipelineChart"></canvas>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Top Customers</h3>
                <p class="text-sm text-gray-600">Highest revenue customers</p>
            </div>
            <div class="h-64">
                <canvas id="topCustomersChart"></canvas>
            </div>
        </div>

        <!-- Product Performance -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Product Performance</h3>
                <p class="text-sm text-gray-600">Top performing products</p>
            </div>
            <div class="h-64">
                <canvas id="productPerformanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Outstanding Receivables -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Outstanding Receivables</h3>
                <p class="text-sm text-gray-600">Pending and partial payments</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="receivables-table" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Sales Activities</h3>
                <p class="text-sm text-gray-600">Latest transactions and activities</p>
            </div>
            <div class="space-y-4" id="recent-activities">
                <!-- Data will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="text-gray-600">Loading analytics data...</span>
    </div>
</div>

@else
<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">Access Denied</h3>
    <p class="mt-1 text-sm text-gray-500">You don't have permission to view analytics.</p>
</div>
@endcan

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let charts = {};
    let currentFilters = {
        period: 'monthly',
        currency: 'all',
        start_date: null,
        end_date: null
    };

    // Initialize date inputs
    initializeDateInputs();
    
    // Load initial data
    loadAnalyticsData();

    // Event listeners
    document.getElementById('period-filter').addEventListener('change', function() {
        currentFilters.period = this.value;
        loadAnalyticsData();
    });

    document.getElementById('currency-filter').addEventListener('change', function() {
        currentFilters.currency = this.value;
        loadAnalyticsData();
    });

    document.getElementById('start-date').addEventListener('change', function() {
        currentFilters.start_date = this.value;
        loadAnalyticsData();
    });

    document.getElementById('end-date').addEventListener('change', function() {
        currentFilters.end_date = this.value;
        loadAnalyticsData();
    });

    document.getElementById('refresh-data').addEventListener('click', function() {
        loadAnalyticsData();
    });

    // Export functionality
    document.getElementById('export-pdf').addEventListener('click', function() {
        exportData('pdf');
    });

    document.getElementById('export-excel').addEventListener('click', function() {
        exportData('excel');
    });

    function initializeDateInputs() {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setMonth(endDate.getMonth() - 12);
        
        document.getElementById('start-date').value = startDate.toISOString().split('T')[0];
        document.getElementById('end-date').value = endDate.toISOString().split('T')[0];
        
        currentFilters.start_date = startDate.toISOString().split('T')[0];
        currentFilters.end_date = endDate.toISOString().split('T')[0];
    }

    function loadAnalyticsData() {
        showLoading();
        
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] !== null && currentFilters[key] !== '') {
                params.append(key, currentFilters[key]);
            }
        });

        fetch(`{{ route('admin.analytics.data') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboard(data.data);
                } else {
                    console.error('Error loading analytics data:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading analytics data:', error);
            })
            .finally(() => {
                hideLoading();
            });
    }

    function updateDashboard(data) {
        // Update summary metrics
        updateSummaryMetrics(data.summary);
        
        // Update charts
        updateRevenueChart(data.revenue);
        updateCashFlowChart(data.cash_flow);
        updatePipelineChart(data.sales_performance);
        updateTopCustomersChart(data.customers);
        updateProductPerformanceChart(data.products);
        
        // Update tables
        updateReceivablesTable(data.cash_flow.outstanding_receivables);
        updateRecentActivities(data);
    }

    function updateSummaryMetrics(summary) {
        document.getElementById('total-revenue').textContent = formatCurrency(summary.total_revenue.combined);
        document.getElementById('cash-received').textContent = formatCurrency(summary.total_cash_received);
        document.getElementById('total-leads').textContent = summary.total_leads.toLocaleString();
        document.getElementById('conversion-rate').textContent = summary.conversion_rate + '%';
    }

    function updateRevenueChart(revenueData) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        if (charts.revenue) {
            charts.revenue.destroy();
        }
        
        charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.trends.labels,
                datasets: [
                    {
                        label: 'AED Revenue',
                        data: revenueData.trends.aed,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'USD Revenue',
                        data: revenueData.trends.usd,
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }

    function updateCashFlowChart(cashFlowData) {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        
        if (charts.cashFlow) {
            charts.cashFlow.destroy();
        }
        
        charts.cashFlow = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Invoiced', 'Cash Received'],
                datasets: [{
                    label: 'Amount',
                    data: [
                        cashFlowData.total_invoiced,
                        cashFlowData.total_cash_received
                    ],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(34, 197, 94)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }

    function updatePipelineChart(performanceData) {
        const ctx = document.getElementById('pipelineChart').getContext('2d');
        
        if (charts.pipeline) {
            charts.pipeline.destroy();
        }
        
        const leadsData = performanceData.pipeline.leads_by_status;
        const labels = leadsData.map(item => item.status);
        const data = leadsData.map(item => item.count);
        
        charts.pipeline = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function updateTopCustomersChart(customerData) {
        const ctx = document.getElementById('topCustomersChart').getContext('2d');
        
        if (charts.topCustomers) {
            charts.topCustomers.destroy();
        }
        
        const topCustomers = customerData.top_customers.slice(0, 5);
        const labels = topCustomers.map(customer => customer.customer_name);
        const data = topCustomers.map(customer => customer.total_spent);
        
        charts.topCustomers = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }

    function updateProductPerformanceChart(productData) {
        const ctx = document.getElementById('productPerformanceChart').getContext('2d');
        
        if (charts.productPerformance) {
            charts.productPerformance.destroy();
        }
        
        const topProducts = productData.performance.slice(0, 5);
        const labels = topProducts.map(product => product.product_name);
        const data = topProducts.map(product => product.total_revenue);
        
        charts.productPerformance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function updateReceivablesTable(receivables) {
        const tbody = document.getElementById('receivables-table');
        tbody.innerHTML = '';
        
        receivables.slice(0, 10).forEach(receivable => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${receivable.customer_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatCurrency(receivable.outstanding_amount)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(receivable.payment_status)}">
                        ${receivable.payment_status}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function updateRecentActivities(data) {
        const container = document.getElementById('recent-activities');
        container.innerHTML = '<p class="text-sm text-gray-500">Recent activities will be displayed here</p>';
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-AE', {
            style: 'currency',
            currency: 'AED',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    function getStatusColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'partial': 'bg-blue-100 text-blue-800',
            'paid': 'bg-green-100 text-green-800',
            'overdue': 'bg-red-100 text-red-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }

    function showLoading() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loading-overlay').style.display = 'none';
    }

    function exportData(format) {
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] !== null && currentFilters[key] !== '') {
                params.append(key, currentFilters[key]);
            }
        });
        params.append('format', format);
        
        window.open(`{{ route('admin.analytics.export') }}?${params.toString()}`, '_blank');
    }
});
</script>
@endpush
