@extends('admin.layouts.app')

@section('title', 'Sales Targets')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sales Target Management</h1>
                <p class="text-gray-600 mt-2">Manage and track your sales goals and targets</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.sales-targets.analytics') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Analytics
                </a>
                <a href="{{ route('admin.sales-targets.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Target
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Targets</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $targets->total() }}</p>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $targets->where('progress_status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">On Track</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $targets->where('progress_status', 'on_track')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">At Risk</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $targets->whereIn('progress_status', ['at_risk', 'overdue'])->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.sales-targets.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Status</option>
                        @foreach(\App\Models\SalesTarget::STATUS_OPTIONS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="target_type" class="block text-sm font-medium text-gray-700 mb-1">Target Type</label>
                    <select name="target_type" id="target_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Types</option>
                        @foreach(\App\Models\SalesTarget::TARGET_TYPES as $value => $label)
                            <option value="{{ $value }}" {{ request('target_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="period_type" class="block text-sm font-medium text-gray-700 mb-1">Period Type</label>
                    <select name="period_type" id="period_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Periods</option>
                        @foreach(\App\Models\SalesTarget::PERIOD_TYPES as $value => $label)
                            <option value="{{ $value }}" {{ request('period_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.sales-targets.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Targets Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Sales Targets</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage and track all sales targets ({{ $targets->total() }})</p>
                </div>
                <form method="POST" action="{{ route('admin.sales-targets.update-all-achievements') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Achievements
                    </button>
                </form>
            </div>
        </div>

        @if($targets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($targets as $target)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $target->name }}</div>
                                        @if($target->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($target->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ \App\Models\SalesTarget::TARGET_TYPES[$target->target_type] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ \App\Models\SalesTarget::PERIOD_TYPES[$target->period_type] }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $target->start_date->format('M d, Y') }} - {{ $target->end_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1 mr-3">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($target->achieved_amount) }} / {{ number_format($target->target_amount) }}
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $target->progress_percentage) }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $target->progress_percentage }}%</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $target->progress_badge_class }}">
                                        @switch($target->progress_status)
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $target->assignedTo ? $target->assignedTo->name : 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.sales-targets.show', $target) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('admin.sales-targets.edit', $target) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.sales-targets.destroy', $target) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this target?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $targets->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No sales targets</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new sales target.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.sales-targets.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Target
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</div>
@endsection 