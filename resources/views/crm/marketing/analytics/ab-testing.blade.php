@extends('layouts.crm')

@section('title', 'A/B Testing Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">🧪 A/B Testing Analytics</h1>
            <p class="mt-2 text-sm text-gray-600">
                Analyze the performance of your A/B tests to optimize future campaigns
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('crm.marketing.analytics.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to Analytics
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 14.5M9.75 3.104L19.8 14.5m0 0l-5.25 3.104m0 0L9.75 21.896M19.8 14.5l-5.25 3.104m0 0L9.75 21.896m0 0L4.5 17.5m5.25 4.396L4.5 17.5m0 0L9.75 3.104" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total A/B Tests</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalTests ?? 0 }}</p>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-500">{{ $completedTests ?? 0 }} completed</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-600">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M9.497 14.25v-2.25m5.007 2.25v-2.25m0 0V9.375c0-.621-.504-1.125-1.125-1.125H9.497c-.621 0-1.125.504-1.125 1.125v2.25m0 0a4.5 4.5 0 009 0m-9 0a17.084 17.084 0 018.426 0" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Lift in Open Rate</p>
                    <p class="text-3xl font-bold text-gray-900">+{{ $avgOpenRateLift ?? 0 }}%</p>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-500">vs control variants</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-600">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M21.75 12H19.5m-.166 5.834l-1.591-1.591M12 19.5V21.75m-5.834-.166l1.591-1.591M2.25 12H4.5m.166-5.834l1.591 1.591" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Best Open Rate</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $bestOpenRate ?? 0 }}%</p>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-500">highest performing variant</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-600">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Best Click Rate</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $bestClickRate ?? 0 }}%</p>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-500">highest performing variant</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- A/B Test Results Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">A/B Test Results</h3>
            <p class="text-sm text-gray-600 mt-1">Detailed results for each A/B test campaign</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Winner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Rate Improvement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Click Rate Improvement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($abTestCampaigns ?? [] as $campaign)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                            <div class="text-sm text-gray-500">{{ $campaign->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                {{ ucfirst(str_replace('_', ' ', $campaign->ab_test_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($campaign->hasWinner())
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Complete
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                    Running
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($campaign->hasWinner())
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $campaign->ab_test_winner)) }}</span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($campaign->hasWinner())
                                @php
                                    $variantAOpenRate = $campaign->getVariantOpenRate('variant_a');
                                    $variantBOpenRate = $campaign->getVariantOpenRate('variant_b');
                                    $improvement = $variantBOpenRate - $variantAOpenRate;
                                @endphp
                                <div class="text-sm {{ $improvement > 0 ? 'text-green-600' : ($improvement < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $improvement > 0 ? '+' : '' }}{{ number_format($improvement, 1) }}%
                                </div>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($campaign->hasWinner())
                                @php
                                    $variantAClickRate = $campaign->getVariantClickRate('variant_a');
                                    $variantBClickRate = $campaign->getVariantClickRate('variant_b');
                                    $improvement = $variantBClickRate - $variantAClickRate;
                                @endphp
                                <div class="text-sm {{ $improvement > 0 ? 'text-green-600' : ($improvement < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $improvement > 0 ? '+' : '' }}{{ number_format($improvement, 1) }}%
                                </div>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="text-indigo-600 hover:text-indigo-900">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No A/B tests found. <a href="{{ route('crm.marketing.campaigns.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first A/B test campaign</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Key Insights -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">💡 Key Insights</h3>
            <p class="text-sm text-gray-600 mt-1">AI-powered recommendations based on your A/B test results</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @if(($totalTests ?? 0) > 0)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Testing Performance</h4>
                            <p class="text-sm text-gray-600">You've run {{ $totalTests }} A/B tests with an average open rate improvement of {{ $avgOpenRateLift ?? 0 }}%. Keep testing to optimize performance!</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 7.478a12.06 12.06 0 004.5 0m-7.5 0a12.06 12.06 0 014.5 0m-7.5 0a12.06 12.06 0 01-4.5 0m7.5 0V21.75m-4.5 0V21.75m0 0H7.5m0 0h3.75M9.75 21.75v-3.375a3.75 3.75 0 013.75-3.75V15a6 6 0 01-6 6v.75z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Best Practices</h4>
                            <p class="text-sm text-gray-600">Focus on testing one element at a time (subject lines, send times, content) for clearer results and actionable insights.</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="flex justify-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 14.5M9.75 3.104L19.8 14.5m0 0l-5.25 3.104m0 0L9.75 21.896M19.8 14.5l-5.25 3.104m0 0L9.75 21.896m0 0L4.5 17.5m5.25 4.396L4.5 17.5m0 0L9.75 3.104" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Ready to Start A/B Testing?</h3>
                        <p class="mt-2 text-sm text-gray-600">Create your first A/B test campaign to start optimizing your email performance</p>
                        <div class="mt-6">
                            <a href="{{ route('crm.marketing.campaigns.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Create A/B Test Campaign
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 