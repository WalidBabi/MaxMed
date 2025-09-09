@extends('admin.layouts.app')

@section('title', 'Sales Dashboard - MaxMed Admin')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sales Dashboard</h1>
                <p class="text-gray-600 mt-2">Monthly sales trends and analytics</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Sales Summary Cards -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total AED Sales -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total AED Sales</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['total_aed'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">Last 12 months</p>
                        </div>
                    </div>
            </div>

            <!-- Total USD Sales -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total USD Sales</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($salesData['total_usd'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Last 12 months</p>
                    </div>
                </div>
            </div>

            <!-- Peak Sales Months -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Peak Months</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($salesData['peak_months']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Highest sales periods</p>
                    </div>
                </div>
            </div>

            <!-- Total Combined Sales -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Combined Sales</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['total_combined'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">AED + USD converted</p>
                    </div>
                </div>
            </div>

            <!-- Zero Sales Months -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Zero Sales Months</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($salesData['zero_months']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Months with no sales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Monthly Sales Trends</h3>
                <p class="text-sm text-gray-600 mt-1">Sales data from invoices and converted quotes over the last 12 months. Combined line shows total sales in AED (USD converted at 3.67 rate)</p>
            </div>

            <div class="relative h-96">
                <canvas id="salesChart"></canvas>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($salesData['labels']),
            datasets: [
                {
                    label: 'AED Sales',
                    data: @json($salesData['aed_data']),
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
                    data: @json($salesData['usd_data']),
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
                    data: @json($salesData['combined_data']),
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
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Sales Trends (AED, USD & Combined)',
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
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label.includes('Combined')) {
                                return `${context.dataset.label}: AED ${context.parsed.y.toLocaleString()}`;
                            } else if (context.dataset.label.includes('AED')) {
                                return `${context.dataset.label}: AED ${context.parsed.y.toLocaleString()}`;
                            } else {
                                return `${context.dataset.label}: USD ${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Month',
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
});
</script>

<style>
/* Custom styles for better chart appearance */
#salesChart {
    max-height: 400px;
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
@endsection