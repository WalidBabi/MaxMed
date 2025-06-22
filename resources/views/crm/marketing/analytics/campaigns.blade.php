@extends('layouts.crm')

@section('title', 'Campaign Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Campaign Analytics</h1>
            <p class="mt-2 text-sm text-gray-600">
                Detailed performance insights for your email campaigns
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('crm.marketing.analytics.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Back to Overview
            </a>
        </div>
    </div>

    <!-- Campaign Performance Summary -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics->total_campaigns ?? 0) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Total Campaigns</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600">{{ number_format($metrics->total_recipients ?? 0) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Total Recipients</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600">{{ round($metrics->avg_delivery_rate ?? 0, 1) }}%</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Avg Delivery Rate</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-purple-600">{{ round($metrics->avg_open_rate ?? 0, 1) }}%</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Avg Open Rate</p>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Metrics -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Performance Breakdown</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics->total_sent ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Emails Sent</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($metrics->total_delivered ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Delivered</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($metrics->total_opened ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Opened</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($metrics->total_clicked ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Clicked</div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 mt-6 pt-6 border-t border-gray-200">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ number_format($metrics->total_bounced ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Bounced</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($metrics->total_unsubscribed ?? 0) }}</div>
                    <div class="text-sm text-gray-600">Unsubscribed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ round($metrics->avg_click_rate ?? 0, 1) }}%</div>
                    <div class="text-sm text-gray-600">Avg Click Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign List -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Campaign Performance Details</h3>
                <div class="text-sm text-gray-600">
                    Showing campaigns from last {{ $dateRange }} days
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Click Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($campaigns as $campaign)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="hover:text-indigo-600">
                                            {{ $campaign->name }}
                                        </a>
                                    </div>
                                    @if($campaign->emailTemplate)
                                    <div class="text-sm text-gray-500">{{ $campaign->emailTemplate->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($campaign->status === 'sent')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Sent
                                </span>
                            @elseif($campaign->status === 'sending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Sending
                                </span>
                            @elseif($campaign->status === 'scheduled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Scheduled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($campaign->total_recipients) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $campaign->delivery_rate }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $campaign->open_rate }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $campaign->click_rate }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $campaign->sent_at ? formatDubaiDateForHumans($campaign->sent_at) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No campaigns found for the selected time period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($campaigns->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $campaigns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 