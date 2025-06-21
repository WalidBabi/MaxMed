@extends('supplier.layouts.app')

@section('title', 'My Product Categories')

@section('content')
<div class="p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Product Categories</h1>
                <p class="text-gray-600 mt-2">Categories you're authorized to supply products for</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ $activeCategories->count() }} Active Categories
                </span>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Performance Overview
                </h3>
                <p class="text-gray-600 mt-1">Your overall performance metrics across all categories</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Overall Performance -->
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">Overall Performance</p>
                                <p class="text-xl font-bold text-blue-900">{{ number_format($performanceData['overall_score'], 1) }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Quotations -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-600">Total Quotations</p>
                                <p class="text-xl font-bold text-green-900">{{ $performanceData['total_quotations'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Win Rate -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-amber-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-amber-600">Win Rate</p>
                                <p class="text-xl font-bold text-amber-900">
                                    {{ $performanceData['total_quotations'] > 0 ? number_format(($performanceData['won_quotations'] / $performanceData['total_quotations']) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Avg Response Time -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-purple-600">Avg Response Time</p>
                                <p class="text-xl font-bold text-purple-900">
                                    {{ $performanceData['avg_response_time'] ? number_format($performanceData['avg_response_time'], 1) . 'h' : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    @if($activeCategories->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Your Assigned Categories
                </h3>
                <p class="text-gray-600 mt-1">Detailed performance for each category</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($activeCategories as $assignment)
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                            <!-- Category Header -->
                            <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-semibold text-gray-900">{{ $assignment->category->name }}</h4>
                                    @php
                                        $score = $assignment->performance_score;
                                        $scoreClasses = $score >= 80 ? 'bg-green-100 text-green-800' : ($score >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $scoreClasses }}">
                                        {{ number_format($assignment->performance_score, 1) }}%
                                    </span>
                                </div>
                            </div>

                            <div class="p-5">
                                <!-- Performance Metrics -->
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-indigo-600">{{ $assignment->total_quotations }}</div>
                                        <div class="text-xs text-gray-500">Total</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-green-600">{{ $assignment->won_quotations }}</div>
                                        <div class="text-xs text-gray-500">Won</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-amber-600">{{ number_format($assignment->quotation_win_rate, 1) }}%</div>
                                        <div class="text-xs text-gray-500">Win Rate</div>
                                    </div>
                                </div>

                                <!-- Category Details -->
                                <div class="space-y-2 mb-4">
                                    @if($assignment->minimum_order_value)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Min Order:</span>
                                            <span class="font-medium text-gray-900">AED {{ number_format($assignment->minimum_order_value) }}</span>
                                        </div>
                                    @endif
                                    @if($assignment->lead_time_days)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Lead Time:</span>
                                            <span class="font-medium text-gray-900">{{ $assignment->lead_time_days }} days</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Response Time:</span>
                                        <span class="font-medium text-gray-900">{{ number_format($assignment->avg_response_time_hours, 1) }}h</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Customer Rating:</span>
                                        <span class="font-medium text-gray-900">{{ number_format($assignment->avg_customer_rating, 1) }}/5</span>
                                    </div>
                                </div>

                                <!-- Recent Activity -->
                                @if($assignment->last_quotation_at)
                                    <div class="text-center mb-4">
                                        <p class="text-xs text-gray-500">
                                            Last quotation: {{ $assignment->last_quotation_at->diffForHumans() }}
                                        </p>
                                    </div>
                                @else
                                    <div class="text-center mb-4">
                                        <p class="text-xs text-gray-500">No quotations yet</p>
                                    </div>
                                @endif

                                <!-- Notes -->
                                @if($assignment->notes)
                                    <div class="pt-3 border-t border-gray-100">
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Notes:</span> {{ $assignment->notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Performance Indicator -->
                            <div class="px-5 pb-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $progressClasses = $score >= 80 ? 'bg-green-500' : ($score >= 60 ? 'bg-amber-500' : 'bg-red-500');
                                        $textClasses = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-amber-600' : 'text-red-600');
                                    @endphp
                                    <div class="{{ $progressClasses }} h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $score }}%"></div>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="text-xs {{ $textClasses }} font-medium">
                                        @if($score >= 80)
                                            Excellent Performance
                                        @elseif($score >= 60)
                                            Good Performance
                                        @else
                                            Needs Improvement
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- No Categories Assigned -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No categories assigned</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Contact MaxMed admin to get assigned to product categories.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection 