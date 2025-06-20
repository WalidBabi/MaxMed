@extends('layouts.crm')

@section('title', 'Email Campaigns')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Campaigns</h1>
                <p class="text-gray-600 mt-2">Create, manage, and track your email marketing campaigns</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.campaigns.export', request()->query()) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                    </svg>
                    Export Data
                </a>
                <a href="{{ route('crm.marketing.campaigns.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create Campaign
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Search & Filter Campaigns</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('crm.marketing.campaigns.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               placeholder="Campaign name or subject..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="one_time" {{ request('type') == 'one_time' ? 'selected' : '' }}>One Time</option>
                            <option value="recurring" {{ request('type') == 'recurring' ? 'selected' : '' }}>Recurring</option>
                            <option value="drip" {{ request('type') == 'drip' ? 'selected' : '' }}>Drip</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('crm.marketing.campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Campaigns</h3>
        </div>
        <div class="p-6">
            @if($campaigns->count() > 0)
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                            Campaign Name
                                            @if(request('sort') == 'name')
                                                <span class="ml-2 flex-none rounded text-gray-400">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        @if(request('direction') == 'asc')
                                                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                                        @else
                                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04L9.25 14.388V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                                        @endif
                                                    </svg>
                                                </span>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Subject</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Recipients</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Open Rate</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Click Rate</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                            Created
                                            @if(request('sort') == 'created_at')
                                                <span class="ml-2 flex-none rounded text-gray-400">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        @if(request('direction') == 'asc')
                                                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                                        @else
                                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04L9.25 14.388V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                                        @endif
                                                    </svg>
                                                </span>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($campaigns as $campaign)
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-0">
                                            <div>
                                                <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                    {{ $campaign->name }}
                                                </a>
                                                @if($campaign->description)
                                                    <p class="text-gray-500 mt-1">{{ Str::limit($campaign->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            {{ Str::limit($campaign->subject, 40) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                {{ ucfirst(str_replace('_', ' ', $campaign->type)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            @if($campaign->status == 'draft')
                                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Draft</span>
                                            @elseif($campaign->status == 'scheduled')
                                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Scheduled</span>
                                            @elseif($campaign->status == 'sending')
                                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Sending</span>
                                            @elseif($campaign->status == 'sent')
                                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Sent</span>
                                            @elseif($campaign->status == 'paused')
                                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Paused</span>
                                            @elseif($campaign->status == 'cancelled')
                                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            <div>
                                                {{ number_format($campaign->total_recipients) }}
                                                @if($campaign->sent_count > 0)
                                                    <p class="text-gray-500">{{ number_format($campaign->sent_count) }} sent</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            @if($campaign->delivered_count > 0)
                                                <div>
                                                    {{ $campaign->open_rate }}%
                                                    <p class="text-gray-500">{{ number_format($campaign->opened_count) }} opens</p>
                                                </div>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            @if($campaign->delivered_count > 0)
                                                <div>
                                                    {{ $campaign->click_rate }}%
                                                    <p class="text-gray-500">{{ number_format($campaign->clicked_count) }} clicks</p>
                                                </div>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            <div>
                                                {{ formatDubaiDate($campaign->created_at, 'M d, Y') }}
                                                <p class="text-gray-500">{{ $campaign->creator?->name ?? 'Unknown' }}</p>
                                            </div>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="inline-flex items-center rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('crm.marketing.campaigns.preview', $campaign) }}" target="_blank" class="inline-flex items-center rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                @if($campaign->isDraft())
                                                    <a href="{{ route('crm.marketing.campaigns.edit', $campaign) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($campaigns->hasPages())
                    <div class="mt-6">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No campaigns found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first email campaign.</p>
                    <div class="mt-6">
                        <a href="{{ route('crm.marketing.campaigns.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Create Campaign
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('select[name="status"], select[name="type"]').change(function() {
        $('#filterForm').submit();
    });
});
</script>
@endpush 