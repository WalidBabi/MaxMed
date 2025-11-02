@extends('admin.layouts.app')

@section('title', 'Sales Dashboard - MaxMed Admin')

@section('content')
@can('dashboard.view')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sales Analytics Dashboard</h1>
                <p class="text-gray-600 mt-2">Comprehensive sales trends and analytics with advanced filtering</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Revenue and Cash Flow Section -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Combined Revenue Card -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-green-50 to-emerald-100 p-4 shadow-sm ring-1 ring-green-200/50 transition-all duration-300 hover:shadow-md hover:ring-green-300/50">
                <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-green-200/30"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-500 shadow-sm">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Revenue</p>
                            <p class="text-xs text-green-500">All sent invoices</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-600">AED currency</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($salesData['revenue']['aed'], 0) }} AED</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-600">USD currency</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($salesData['revenue']['usd'], 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-green-200">
                            <span class="text-xs font-bold text-green-700">Combined Total</span>
                            <span class="text-lg font-bold text-green-700">{{ number_format($salesData['revenue']['combined'], 2) }} AED</span>
                        </div>
                    </div>
                    <p class="text-xs text-green-600 mt-2">Total revenue</p>
                </div>
            </div>

            <!-- Combined Cash Flow Card -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-teal-50 to-cyan-100 p-4 shadow-sm ring-1 ring-teal-200/50 transition-all duration-300 hover:shadow-md hover:ring-teal-300/50">
                <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-teal-200/30"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-500 shadow-sm">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-teal-600 uppercase tracking-wide">Cash Flow</p>
                            <p class="text-xs text-teal-500">Paid invoices only</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-600">AED currency</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($salesData['cash_flow']['aed'], 0) }} AED</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-600">USD currency</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($salesData['cash_flow']['usd'], 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-teal-200">
                            <span class="text-xs font-bold text-teal-700">Combined Total</span>
                            <span class="text-lg font-bold text-teal-700">{{ number_format($salesData['cash_flow']['combined'], 2) }} AED</span>
                        </div>
                    </div>
                    <p class="text-xs text-teal-600 mt-2">Actual received</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Expenses Section (Superadmin only) -->
    @php
        $isSuperAdmin = Auth::user() && (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('super-administrator'));
    @endphp
    @if($isSuperAdmin && isset($expenseKpis))
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Business Expenses</h2>
            <a href="{{ route('admin.business-expenses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                Manage Expenses →
            </a>
        </div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- This Month Card -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-orange-50 to-amber-100 p-4 shadow-sm ring-1 ring-orange-200/50 transition-all duration-300 hover:shadow-md hover:ring-orange-300/50">
                <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-orange-200/30"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 shadow-sm">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-orange-600 uppercase tracking-wide">This Month</p>
                            <p class="text-xs text-orange-500">Expected expenses</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($expenseKpis['this_month_total'], 2) }} AED</p>
                    </div>
                </div>
            </div>

            <!-- Next Month Card -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-red-50 to-rose-100 p-4 shadow-sm ring-1 ring-red-200/50 transition-all duration-300 hover:shadow-md hover:ring-red-300/50">
                <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-red-200/30"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 shadow-sm">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-red-600 uppercase tracking-wide">Next Month</p>
                            <p class="text-xs text-red-500">Expected expenses</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($expenseKpis['next_month_total'], 2) }} AED</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Metrics Cards -->
    <div class="mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <!-- Peak Sales Months -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-yellow-50 to-orange-100 p-4 shadow-sm ring-1 ring-yellow-200/50 transition-all duration-300 hover:shadow-md hover:ring-yellow-300/50">
                <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-yellow-200/30"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-500 shadow-sm">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-yellow-600 uppercase tracking-wide">Peak Months</p>
                            <p class="text-xs text-yellow-500">Highest sales periods</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="space-y-0">
                            <p class="text-2xl font-bold text-gray-900">{{ count($salesData['peak_months']) }}</p>
                            <p class="text-sm font-semibold text-gray-700">Peak sales months</p>
                        </div>
                        <p class="text-xs text-yellow-600 mt-2">Highest performance</p>
                    </div>
                </div>
            </div>

            <!-- CARD COMPLETELY REMOVED -->
            <div class="hidden">
                <div class="absolute -right-1 -top-1 h-12 w-12 rounded-full bg-purple-200/30 transition-all duration-300 group-hover:scale-110"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-purple-500 shadow-sm">
                            <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">REMOVED</p>
                            <p class="text-xs text-purple-500">Card Removed</p>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="space-y-0">
                            <p class="text-sm font-bold text-gray-900">{{ number_format($salesData['total_combined'], 2) }} AED</p>
                            <p class="text-xs font-semibold text-gray-700">All currencies combined</p>
                        </div>
                        <p class="text-xs text-purple-600 mt-0.5">Total sales volume</p>
                    </div>
                </div>
            </div>

            <!-- Zero Sales Months -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-red-50 to-pink-100 p-4 shadow-sm ring-1 ring-red-200/50 transition-all duration-300 hover:shadow-md hover:ring-red-300/50">
                <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-red-200/30"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 shadow-sm">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-600 uppercase tracking-wide">Zero Sales Months</p>
                            <p class="text-xs text-red-500">Months with no sales</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="space-y-0">
                            <p class="text-2xl font-bold text-gray-900">{{ count($salesData['zero_months']) }}</p>
                            <p class="text-sm font-semibold text-gray-700">Zero sales months</p>
                        </div>
                        <p class="text-xs text-red-600 mt-2">Requires attention</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters and Controls -->
    <div class="mb-8 filter-section">
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-2xl shadow-lg ring-1 ring-gray-200/50 p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">📊 Advanced Analytics Filters</h3>
                <p class="text-sm text-gray-600">Customize your sales analysis with powerful filtering options</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Time Period Toggle -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Time Period
                        </span>
                    </label>
                    <div class="flex rounded-xl bg-gray-100 p-1 shadow-inner">
                        <button type="button" class="period-toggle flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 ease-in-out" data-period="daily">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Daily
                            </span>
                        </button>
                        <button type="button" class="period-toggle flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 ease-in-out active" data-period="monthly">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Monthly
                            </span>
                        </button>
                        <button type="button" class="period-toggle flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 ease-in-out" data-period="quarterly">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Quarterly
                            </span>
                        </button>
                </div>

                    <!-- Date Selectors -->
                    <div class="mt-4">
                        <!-- Daily Date Selector -->
                        <div id="daily-selector" class="hidden">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">
                        <span class="flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                                    Select Day
                        </span>
                    </label>
                            <input type="date" id="daily-date" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-sm">
                </div>

                        <!-- Monthly Selector -->
                        <div id="monthly-selector" class="">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">
                        <span class="flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                                    Select Month
                        </span>
                    </label>
                            <input type="month" id="monthly-date" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 text-sm">
                </div>

                        <!-- Quarterly Selector -->
                        <div id="quarterly-selector" class="hidden">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">
                        <span class="flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                                    Select Quarter
                        </span>
                    </label>
                            <div class="grid grid-cols-2 gap-2">
                                <select id="quarterly-year" class="px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 text-sm">
                                    <!-- Years will be populated by JavaScript -->
                                </select>
                                <select id="quarterly-quarter" class="px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 text-sm">
                                    <option value="1">Q1 (Jan-Mar)</option>
                                    <option value="2">Q2 (Apr-Jun)</option>
                                    <option value="3">Q3 (Jul-Sep)</option>
                                    <option value="4">Q4 (Oct-Dec)</option>
                    </select>
                </div>
                        </div>
                    </div>
                </div>

                <!-- Export Buttons -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Options
                        </span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <button id="export-png" class="inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            PNG
                        </button>
                        <button id="export-pdf" class="inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-4 focus:ring-red-200 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Sales Trends Analysis</h3>
                    <p class="text-sm text-gray-600 mt-1">Interactive sales data from {{ $salesData['labels'][0] ?? 'N/A' }} to {{ end($salesData['labels']) ?? 'N/A' }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Total Sales:</span>
                        <span id="total-sales" class="ml-1 font-semibold text-gray-900">Loading...</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Periods:</span>
                        <span id="period-count" class="ml-1 font-semibold text-gray-900">-</span>
                    </div>
                </div>
            </div>

            <!-- Loading indicator -->
            <div id="chart-loading" class="flex items-center justify-center h-96">
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <span class="text-gray-600">Loading chart data...</span>
                </div>
            </div>

            <!-- Single Chart container -->
            <div id="chart-container" class="relative h-96" style="display: none;">
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Quarterly Charts container -->
            <div id="quarterly-charts-container" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3" id="quarter-1-title">Q1</h4>
                    <div class="relative h-64">
                        <canvas id="quarterChart1"></canvas>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3" id="quarter-2-title">Q2</h4>
                    <div class="relative h-64">
                        <canvas id="quarterChart2"></canvas>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3" id="quarter-3-title">Q3</h4>
                    <div class="relative h-64">
                        <canvas id="quarterChart3"></canvas>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3" id="quarter-4-title">Q4</h4>
                    <div class="relative h-64">
                        <canvas id="quarterChart4"></canvas>
                    </div>
                </div>
            </div>

            <!-- No data message -->
            <div id="no-data-message" class="flex items-center justify-center h-96" style="display: none;">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No sales data</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters to see sales data.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Sales Analysis -->
    @if(count($salesData['peak_months']) > 0)
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Peak Sales Analysis</h3>
                <p class="text-sm text-gray-600 mt-1">Months with highest sales performance</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($salesData['peak_months'] as $peakMonth)
                <div class="rounded-lg bg-yellow-50 px-4 py-3 border border-yellow-200">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                </svg>
                        <span class="text-sm font-medium text-yellow-800">{{ $peakMonth }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Zero Sales Alert -->
    @if(count($salesData['zero_months']) > 0)
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Zero Sales Alert</h3>
                <p class="text-sm text-gray-600 mt-1">Months with no sales activity requiring attention</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($salesData['zero_months'] as $zeroMonth)
                <div class="rounded-lg bg-red-50 px-4 py-3 border border-red-200">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                        <span class="text-sm font-medium text-red-800">{{ $zeroMonth }}</span>
                    </div>
            </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let salesChart = null;
    let quarterlyCharts = [];
    let currentFilters = {
        period: 'monthly',
        selectedDate: null,
        selectedMonth: null,
        selectedYear: null,
        selectedQuarter: null
    };
    
    // Initialize date inputs and load initial data
    initializeDateSelectors();
    loadChartData();

    // Event listeners for period toggles
    document.querySelectorAll('.period-toggle').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.period-toggle').forEach(btn => btn.classList.remove('active', 'bg-white', 'text-gray-900', 'shadow-sm'));
            this.classList.add('active', 'bg-white', 'text-gray-900', 'shadow-sm');
            currentFilters.period = this.dataset.period;
            
            // Show/hide appropriate date selectors
            showDateSelector(this.dataset.period);
            
            loadChartData();
        });
    });

    // Event listeners for date selectors
    document.getElementById('daily-date').addEventListener('change', function() {
        currentFilters.selectedDate = this.value;
        loadChartData();
    });

    document.getElementById('monthly-date').addEventListener('change', function() {
        currentFilters.selectedMonth = this.value;
        loadChartData();
    });

    document.getElementById('quarterly-year').addEventListener('change', function() {
        currentFilters.selectedYear = this.value;
        loadChartData();
    });

    document.getElementById('quarterly-quarter').addEventListener('change', function() {
        currentFilters.selectedQuarter = this.value;
        loadChartData();
    });

    // Export functionality
    document.getElementById('export-png').addEventListener('click', exportChartAsPNG);
    document.getElementById('export-pdf').addEventListener('click', exportChartAsPDF);

    function initializeDateSelectors() {
        const today = new Date();
        
        // Initialize daily date selector (default to today)
        document.getElementById('daily-date').value = today.toISOString().split('T')[0];
        currentFilters.selectedDate = today.toISOString().split('T')[0];
        
        // Initialize monthly date selector (default to current month)
        const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
        document.getElementById('monthly-date').value = currentMonth;
        currentFilters.selectedMonth = currentMonth;
        
        // Initialize quarterly selectors (default to current quarter)
        const currentYear = today.getFullYear();
        const currentQuarter = Math.ceil((today.getMonth() + 1) / 3);
        
        // Populate year dropdown (last 5 years to next 2 years)
        const yearSelect = document.getElementById('quarterly-year');
        for (let year = currentYear - 5; year <= currentYear + 2; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === currentYear) option.selected = true;
            yearSelect.appendChild(option);
        }
        
        document.getElementById('quarterly-quarter').value = currentQuarter;
        currentFilters.selectedYear = currentYear;
        currentFilters.selectedQuarter = currentQuarter;
        
        // Show the appropriate selector based on default period (monthly)
        showDateSelector('monthly');
    }

    function showDateSelector(period) {
        // Hide all selectors first
        document.getElementById('daily-selector').classList.add('hidden');
        document.getElementById('monthly-selector').classList.add('hidden');
        document.getElementById('quarterly-selector').classList.add('hidden');
        
        // Show the appropriate selector
        switch (period) {
            case 'daily':
                document.getElementById('daily-selector').classList.remove('hidden');
                break;
            case 'monthly':
                document.getElementById('monthly-selector').classList.remove('hidden');
                break;
            case 'quarterly':
                document.getElementById('quarterly-selector').classList.remove('hidden');
                break;
        }
    }

    function loadChartData() {
        showLoading();
        
        const params = new URLSearchParams();
        params.append('period', currentFilters.period);
        
        // Add specific date parameters based on period
        switch (currentFilters.period) {
            case 'daily':
                if (currentFilters.selectedDate) {
                    params.append('selected_date', currentFilters.selectedDate);
                }
                break;
            case 'monthly':
                if (currentFilters.selectedMonth) {
                    params.append('selected_month', currentFilters.selectedMonth);
                }
                break;
            case 'quarterly':
                if (currentFilters.selectedYear && currentFilters.selectedQuarter) {
                    params.append('selected_year', currentFilters.selectedYear);
                    params.append('selected_quarter', currentFilters.selectedQuarter);
                }
                break;
        }

        fetch(`{{ route('admin.dashboard.sales-data') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateChart(data.data);
                    updateSummary(data.data.summary);
                } else {
                    showNoData();
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                showNoData();
            });
    }

    function updateChart(data) {
        // Check if quarterly view should show separate charts
        // Only show multiple quarterly charts if no specific quarter is selected
        if (currentFilters.period === 'quarterly' && !currentFilters.selectedYear && !currentFilters.selectedQuarter) {
            updateQuarterlyCharts(data);
        } else {
            updateSingleChart(data);
        }
    }

    function updateSingleChart(data) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        if (salesChart) {
            salesChart.destroy();
        }

        // Process data for Chart.js
        const processedData = processChartData(data);
        
        if (processedData.datasets.length === 0) {
            showNoData();
            return;
        }

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: processedData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: getChartTitle(),
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#374151'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                const value = context.parsed.y;
                                const currency = context.dataset.label.includes('USD') ? 'USD' : 'AED';
                                const label = `${context.dataset.label}: ${currency} ${value.toLocaleString()}`;
                                
                                // Add invoice details if available
                                if (context.dataset.invoiceDetails && context.dataset.invoiceDetails[context.dataIndex]) {
                                    const invoiceDetails = context.dataset.invoiceDetails[context.dataIndex];
                                    if (invoiceDetails && invoiceDetails.length > 0) {
                                        const details = invoiceDetails.join('; ');
                                        return [label, `Invoices: ${details}`];
                                    }
                                }
                                
                                return label;
                            },
                            afterBody: function(context) {
                                // Show additional invoice information
                                const invoiceDetails = [];
                                context.forEach(function(item) {
                                    if (item.dataset.invoiceDetails && item.dataset.invoiceDetails[item.dataIndex]) {
                                        const details = item.dataset.invoiceDetails[item.dataIndex];
                                        if (details && details.length > 0) {
                                            invoiceDetails.push(...details);
                                        }
                                    }
                                });
                                
                                if (invoiceDetails.length > 0) {
                                    return ['', 'Invoice Details:', ...invoiceDetails];
                                }
                                
                                return [];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: getXAxisTitle(),
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#374151'
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Sales Amount',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#374151'
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#fff'
                    }
                }
            }
        });

        showChart();
    }

    function updateQuarterlyCharts(data) {
        // Destroy existing quarterly charts
        quarterlyCharts.forEach(chart => {
            if (chart) chart.destroy();
        });
        quarterlyCharts = [];

        // Show quarterly charts container
        showQuarterlyCharts();

        // Create individual charts for each quarter
        for (let i = 0; i < data.labels.length && i < 4; i++) {
            const quarterLabel = data.labels[i];
            const ctx = document.getElementById(`quarterChart${i + 1}`).getContext('2d');
            
            // Update quarter title
            document.getElementById(`quarter-${i + 1}-title`).textContent = quarterLabel;

            // Get data for this specific quarter
            const quarterData = data.datasets.map(dataset => ({
                ...dataset,
                data: [dataset.data[i] || 0] // Single data point for this quarter
            }));

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Sales'],
                    datasets: quarterData
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: quarterLabel,
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#374151'
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 10,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    const value = context.parsed.y;
                                    const currency = context.dataset.label.includes('USD') ? 'USD' : 'AED';
                                    const label = `${context.dataset.label}: ${currency} ${value.toLocaleString()}`;
                                    
                                    // Add invoice details if available
                                    if (context.dataset.invoiceDetails && context.dataset.invoiceDetails[context.dataIndex]) {
                                        const invoiceDetails = context.dataset.invoiceDetails[context.dataIndex];
                                        if (invoiceDetails && invoiceDetails.length > 0) {
                                            const details = invoiceDetails.join('; ');
                                            return [label, `Invoices: ${details}`];
                                        }
                                    }
                                    
                                    return label;
                                },
                                afterBody: function(context) {
                                    // Show additional invoice information
                                    const invoiceDetails = [];
                                    context.forEach(function(item) {
                                        if (item.dataset.invoiceDetails && item.dataset.invoiceDetails[item.dataIndex]) {
                                            const details = item.dataset.invoiceDetails[item.dataIndex];
                                            if (details && details.length > 0) {
                                                invoiceDetails.push(...details);
                                            }
                                        }
                                    });
                                    
                                    if (invoiceDetails.length > 0) {
                                        return ['', 'Invoice Details:', ...invoiceDetails];
                                    }
                                    
                                    return [];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Sales Amount',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            quarterlyCharts.push(chart);
        }
    }

    function processChartData(data) {
        const datasets = [];
        
        // This is a simplified version - in a real implementation, you'd process the actual data
        // For now, we'll use the original data structure
        if (data.datasets && data.datasets.length > 0) {
            return { datasets: data.datasets };
        }
        
        // Fallback to original data if new format is not available
        return {
            datasets: [
                {
                    label: 'AED Sales',
                    data: {!! json_encode($salesData['aed_data']) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'USD Sales',
                    data: {!! json_encode($salesData['usd_data']) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Combined Sales (AED)',
                    data: {!! json_encode($salesData['combined_data']) !!},
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    borderWidth: 4,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(147, 51, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 7,
                    pointHoverRadius: 9,
                    borderDash: [5, 5]
                }
            ]
        };
    }

    function updateSummary(summary) {
        document.getElementById('total-sales').textContent = `AED ${summary.total.toLocaleString()}`;
        document.getElementById('period-count').textContent = summary.count;
    }

    function getChartTitle() {
        const period = currentFilters.period.charAt(0).toUpperCase() + currentFilters.period.slice(1);
        
        // Add specific date info to title when available
        if (currentFilters.period === 'daily' && currentFilters.selectedDate) {
            const date = new Date(currentFilters.selectedDate);
            return `Daily Sales - ${date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
        } else if (currentFilters.period === 'monthly' && currentFilters.selectedMonth) {
            const date = new Date(currentFilters.selectedMonth + '-01');
            return `Monthly Sales - ${date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}`;
        } else if (currentFilters.period === 'quarterly' && currentFilters.selectedYear && currentFilters.selectedQuarter) {
            return `Q${currentFilters.selectedQuarter} ${currentFilters.selectedYear} Sales`;
        }
        
        return `${period} Sales Trends`;
    }

    function getXAxisTitle() {
        switch (currentFilters.period) {
            case 'daily': return 'Date';
            case 'quarterly': 
                // If specific quarter is selected, show months within that quarter
                if (currentFilters.selectedYear && currentFilters.selectedQuarter) {
                    return 'Month';
                }
                return 'Quarter';
            default: return 'Month';
        }
    }

    function showLoading() {
        document.getElementById('chart-loading').style.display = 'flex';
        document.getElementById('chart-container').style.display = 'none';
        document.getElementById('quarterly-charts-container').style.display = 'none';
        document.getElementById('no-data-message').style.display = 'none';
    }

    function showChart() {
        document.getElementById('chart-loading').style.display = 'none';
        document.getElementById('chart-container').style.display = 'block';
        document.getElementById('quarterly-charts-container').style.display = 'none';
        document.getElementById('no-data-message').style.display = 'none';
    }

    function showQuarterlyCharts() {
        document.getElementById('chart-loading').style.display = 'none';
        document.getElementById('chart-container').style.display = 'none';
        document.getElementById('quarterly-charts-container').style.display = 'grid';
        document.getElementById('no-data-message').style.display = 'none';
    }

    function showNoData() {
        document.getElementById('chart-loading').style.display = 'none';
        document.getElementById('chart-container').style.display = 'none';
        document.getElementById('quarterly-charts-container').style.display = 'none';
        document.getElementById('no-data-message').style.display = 'flex';
        document.getElementById('total-sales').textContent = 'AED 0';
        document.getElementById('period-count').textContent = '0';
    }

    function exportChartAsPNG() {
        if (!salesChart) return;
        
        const canvas = document.getElementById('salesChart');
        const link = document.createElement('a');
        link.download = `sales-chart-${currentFilters.period}-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }

    function exportChartAsPDF() {
        if (!salesChart) return;
        
        const canvas = document.getElementById('salesChart');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 280;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
        pdf.save(`sales-chart-${currentFilters.period}-${new Date().toISOString().split('T')[0]}.pdf`);
    }
});
</script>

<style>
/* Custom styles for better chart appearance */
#salesChart {
    max-height: 400px;
}

/* Enhanced Period toggle button styles */
.period-toggle {
    color: #6b7280;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.period-toggle:hover {
    color: #374151;
    transform: translateY(-1px);
}

.period-toggle.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transform: translateY(-2px);
}

.period-toggle.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.period-toggle.active:hover::before {
    left: 100%;
}

/* Enhanced form controls */
select, input[type="date"] {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

select:focus, input[type="date"]:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
}

/* Enhanced export buttons */
#export-png, #export-pdf {
    position: relative;
    overflow: hidden;
}

#export-png::before, #export-pdf::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

#export-png:hover::before, #export-pdf:hover::before {
    left: 100%;
}

/* Filter section animations */
.filter-section {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Gradient background animation */
.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradientShift 8s ease infinite;
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Enhanced responsive design for filters */
@media (max-width: 1024px) {
    .grid.grid-cols-1.lg\\:grid-cols-2.xl\\:grid-cols-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .grid.grid-cols-1.lg\\:grid-cols-2.xl\\:grid-cols-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .filter-section .p-8 {
        padding: 1.5rem;
    }
    
    .period-toggle {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    
    .grid.grid-cols-2.gap-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.75rem;
    }
}

@media (max-width: 640px) {
    .filter-section .p-8 {
        padding: 1rem;
    }
    
    .period-toggle span {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .period-toggle svg {
        margin-right: 0;
        margin-bottom: 0.25rem;
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .sm\\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1025px) {
    .lg\\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    
    .lg\\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
</style>
@endcan
@endsection