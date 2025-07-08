@extends('admin.layouts.app')

@section('title', 'Sales Target Details')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sales Target Details</h1>
                <p class="text-gray-600 mt-2">View all information about this sales target</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.sales-targets.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Targets
                </a>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $salesTarget->name }}</h3>
                <div class="text-sm text-gray-500">{{ $salesTarget->description }}</div>
            </div>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ \App\Models\SalesTarget::TARGET_TYPES[$salesTarget->target_type] ?? ucfirst($salesTarget->target_type) }}
                </span>
            </div>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Period</div>
                    <div class="text-sm text-gray-900 font-medium">
                        {{ \App\Models\SalesTarget::PERIOD_TYPES[$salesTarget->period_type] ?? ucfirst($salesTarget->period_type) }}<br>
                        <span class="text-xs text-gray-500">{{ $salesTarget->start_date->format('M d, Y') }} - {{ $salesTarget->end_date->format('M d, Y') }}</span>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Assigned To</div>
                    <div class="text-sm text-gray-900 font-medium">
                        {{ $salesTarget->assignedTo ? $salesTarget->assignedTo->name : 'Unassigned' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Target Amount</div>
                    <div class="text-lg font-bold text-gray-900">AED {{ number_format($salesTarget->target_amount, 2) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Achieved Amount</div>
                    <div class="text-lg font-bold text-gray-900">AED {{ number_format($salesTarget->achieved_amount, 2) }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-gray-500 mb-1">Progress</div>
                    <div class="flex items-center space-x-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $salesTarget->progress_percentage) }}%"></div>
                        </div>
                        <div class="text-sm font-medium text-gray-900">{{ $salesTarget->progress_percentage }}%</div>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Status</div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $salesTarget->progress_badge_class }}">
                        @switch($salesTarget->progress_status)
                            @case('completed')
                                Completed
                                @break
                            @case('on_track')
                                On Track
                                @break
                            @case('moderate')
                                Moderate
                                @break
                            @case('at_risk')
                                At Risk
                                @break
                            @case('overdue')
                                Overdue
                                @break
                            @default
                                Unknown
                        @endswitch
                    </span>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Created By</div>
                    <div class="text-sm text-gray-900 font-medium">
                        {{ $salesTarget->creator ? $salesTarget->creator->name : 'Unknown' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection 