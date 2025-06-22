@extends('layouts.crm')

@section('title', 'Contact Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Contact Analytics</h1>
            <p class="mt-2 text-sm text-gray-600">
                Insights into your contact base and engagement patterns
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

    <!-- Contact Statistics Overview -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5 mb-8">
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900">{{ number_format($contactStats['total_contacts']) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Total Contacts</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600">{{ number_format($contactStats['active_contacts']) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Active</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600">{{ number_format($contactStats['new_contacts']) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">New ({{ $dateRange }}d)</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-orange-600">{{ number_format($contactStats['unsubscribed_contacts']) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Unsubscribed</p>
            </div>
        </div>
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="text-center">
                <p class="text-3xl font-bold text-red-600">{{ number_format($contactStats['bounced_contacts']) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">Bounced</p>
            </div>
        </div>
    </div>

    <!-- Charts and Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Contact Sources -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Contact Sources</h3>
                <p class="text-sm text-gray-600 mt-1">Where your contacts are coming from</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($contactSources as $source)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($source->source ?: 'Unknown') }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ number_format($source->count) }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($source->count / $contactStats['total_contacts']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        <p>No source data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Industry Breakdown -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Industry Breakdown</h3>
                <p class="text-sm text-gray-600 mt-1">Top industries in your contact base</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($industryBreakdown as $industry)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $industry->industry }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ number_format($industry->count) }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($industry->count / $contactStats['total_contacts']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-8">
                        <p>No industry data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Growth Chart -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Contact Growth Over Time</h3>
            <p class="text-sm text-gray-600 mt-1">New contacts added in the last {{ $dateRange }} days</p>
        </div>
        <div class="p-6">
            @if($contactGrowth->count() > 0)
            <div class="grid grid-cols-1 gap-4">
                @foreach($contactGrowth as $growth)
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($growth->date)->format('M j, Y') }}</div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">{{ number_format($growth->count) }} contacts</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($growth->count / $contactGrowth->max('count')) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                <p class="mt-2">No new contacts in the selected period</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Most Engaged Contacts -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Most Engaged Contacts</h3>
            <p class="text-sm text-gray-600 mt-1">Contacts with highest email engagement</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaigns</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Opens</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Clicks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engagement Score</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topContacts as $contact)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('crm.marketing.contacts.show', $contact->id) }}" class="hover:text-indigo-600">
                                            {{ $contact->first_name }} {{ $contact->last_name }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $contact->company ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($contact->total_campaigns) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($contact->total_opens) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($contact->total_clicks) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $score = ($contact->total_opens * 2) + ($contact->total_clicks * 5);
                                $scoreColor = $score > 50 ? 'green' : ($score > 20 ? 'yellow' : 'gray');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $scoreColor }}-100 text-{{ $scoreColor }}-800">
                                {{ $score }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No engagement data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 