@extends('layouts.crm')

@section('title', 'CRM Dashboard')

@section('content')
    {{-- Dashboard Header --}}
    <x-dashboard-header 
        title="Welcome to MaxMed CRM"
        subtitle="Your comprehensive customer relationship management dashboard"
        :showDate="false"
    >
        <x-slot name="actions">
            <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <x-icons name="arrow-trending-up" class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" />
                Export Data
            </button>
            <a href="{{ route('crm.leads.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <x-icons name="plus" class="-ml-0.5 mr-1.5 h-5 w-5" />
                Add New Lead
            </a>
        </x-slot>
    </x-dashboard-header>

    {{-- Key Performance Metrics --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <x-metric-card
            title="Total Leads"
            :value="number_format($totalLeads ?? 0)"
            :icon="'<x-icons name=\"users\" class=\"h-8 w-8\" />'"
            trend="up"
            trendValue="+12% from last month"
            color="blue"
        />

        <x-metric-card
            title="Active Leads"
            :value="number_format($activeLeads ?? 0)"
            :icon="'<x-icons name=\"check-circle\" class=\"h-8 w-8\" />'"
            :subtitle="(($activeLeads ?? 0) > 0 && ($totalLeads ?? 0) > 0 ? round(($activeLeads / $totalLeads) * 100, 1) : 0) . '% of total'"
            color="green"
        />

        <x-metric-card
            title="Pipeline Value"
            value="AED {{ number_format($totalDealValue ?? 0, 0) }}"
            :icon="'<x-icons name=\"currency-dollar\" class=\"h-8 w-8\" />'"
            :subtitle="($openDeals ?? 0) . ' open deals'"
            color="yellow"
        />

        <x-metric-card
            title="Won Deals"
            :value="number_format($wonDeals ?? 0)"
            :icon="'<x-icons name=\"trophy\" class=\"h-8 w-8\" />'"
            trend="up"
            :trendValue="'Success rate: ' . (($totalLeads ?? 0) > 0 ? round((($wonDeals ?? 0) / $totalLeads) * 100, 1) : 0) . '%'"
            color="red"
        />
    </div>

    {{-- Charts and Activity Section --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        {{-- Recent Activities --}}
        <x-activity-timeline
            :activities="$recentActivities ?? collect()"
            title="Recent Activities"
            :viewAllUrl="route('crm.leads.index')"
            emptyMessage="No activities yet"
            emptyDescription="Get started by creating your first lead activity."
            :limit="5"
        />

        {{-- Pipeline Performance --}}
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pipeline by Stage</h3>
            </div>
            <div class="p-6">
                @if(isset($pipelineByStage) && $pipelineByStage->count() > 0)
                    <div class="space-y-6">
                        @foreach($pipelineByStage as $stage)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $stage->stage) }}</p>
                                        <p class="text-xs text-gray-500">{{ $stage->count }} {{ $stage->count === 1 ? 'deal' : 'deals' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">AED {{ number_format($stage->total_value, 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-empty-state 
                        title="No deals in pipeline"
                        message="Start by converting some leads to deals."
                        :icon="'<svg class=\"h-12 w-12 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z\" />
                        </svg>'"
                    />
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Actions and Additional Info --}}
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Quick Actions --}}
        <x-quick-actions-card />

        {{-- Lead Sources --}}
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Lead Sources</h3>
            </div>
            <div class="p-6">
                @if(isset($leadSources) && $leadSources->count() > 0)
                    <div class="space-y-4">
                        @foreach($leadSources as $source)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $source->source }}</span>
                                <span class="text-sm text-gray-500">{{ $source->count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-empty-state 
                        title="No lead sources yet"
                        message="Lead source data will appear here once you start adding leads."
                        class="text-center py-4"
                    />
                @endif
            </div>
        </div>

        {{-- Performance Stats --}}
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Performance</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900">Conversion Rate</span>
                        <span class="text-sm text-green-600 font-semibold">
                            {{ ($totalLeads ?? 0) > 0 ? round((($wonDeals ?? 0) / $totalLeads) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900">Avg. Deal Size</span>
                        <span class="text-sm text-gray-900 font-semibold">
                            AED {{ ($wonDeals ?? 0) > 0 ? number_format(($totalDealValue ?? 0) / $wonDeals, 0) : 0 }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900">Active Pipeline</span>
                        <span class="text-sm text-blue-600 font-semibold">{{ $activeLeads ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 