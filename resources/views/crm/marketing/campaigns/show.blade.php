@extends('layouts.crm')

@section('title', 'Campaign Details')

@section('content')
    @if(isset($error))
        <div class="mb-8">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error Loading Campaign Data</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $error }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8" data-campaign-id="{{ $campaign->id }}">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('crm.marketing.campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mb-4">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H6.5a.75.75 0 010-1.5h4.44L7.72 3.53a.75.75 0 011.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Campaigns
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $campaign->name }}</h1>
                @if($campaign->description)
                    <p class="text-gray-600 mt-2">{{ $campaign->description }}</p>
                @endif
            </div>
            <div>
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
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Performance Overview -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Performance Overview</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 px-4 py-6 ring-1 ring-blue-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-blue-700" data-stat="total-recipients">{{ number_format($campaign->total_recipients) }}</p>
                                <p class="text-sm font-medium text-blue-600 mt-1">Recipients</p>
                            </div>
                        </div>
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-green-50 to-green-100 px-4 py-6 ring-1 ring-green-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-green-700" data-stat="sent-count">{{ number_format($campaign->sent_count) }}</p>
                                <p class="text-sm font-medium text-green-600 mt-1">Sent</p>
                            </div>
                        </div>
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 px-4 py-6 ring-1 ring-emerald-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-emerald-700" data-stat="delivered-count">{{ number_format($campaign->delivered_count) }}</p>
                                <p class="text-sm font-medium text-emerald-600 mt-1">Delivered</p>
                            </div>
                        </div>
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100 px-4 py-6 ring-1 ring-yellow-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-yellow-700" data-stat="delivery-rate">{{ $campaign->delivery_rate }}%</p>
                                <p class="text-sm font-medium text-yellow-600 mt-1">Delivery Rate</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6 sm:grid-cols-4 mt-6">
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 px-4 py-6 ring-1 ring-indigo-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-indigo-700" data-stat="opened-count">{{ number_format($campaign->opened_count) }}</p>
                                <p class="text-sm font-medium text-indigo-600 mt-1">Opens (<span data-stat="open-rate">{{ $campaign->open_rate }}</span>)</p>
                            </div>
                        </div>
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 px-4 py-6 ring-1 ring-purple-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-purple-700" data-stat="clicked-count">{{ number_format($campaign->clicked_count) }}</p>
                                <p class="text-sm font-medium text-purple-600 mt-1">Clicks (<span data-stat="click-rate">{{ $campaign->click_rate }}</span>)</p>
                            </div>
                        </div>
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-red-50 to-red-100 px-4 py-6 ring-1 ring-red-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-red-700" data-stat="bounced-count">{{ number_format($campaign->bounced_count) }}</p>
                                <p class="text-sm font-medium text-red-600 mt-1">Bounced (<span data-stat="bounce-rate">{{ $campaign->bounce_rate }}</span>)</p>
                            </div>
                        </div>
                        <div class="card-hover overflow-hidden rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 px-4 py-6 ring-1 ring-gray-200/50">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-700" data-stat="unsubscribed-count">{{ number_format($campaign->unsubscribed_count) }}</p>
                                <p class="text-sm font-medium text-gray-600 mt-1">Unsubscribed (<span data-stat="unsubscribe-rate">{{ $campaign->unsubscribe_rate }}</span>)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- A/B Testing Results -->
            @if($campaign->isAbTest())
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">🧪 A/B Testing Results</h3>
                        @if($campaign->hasWinner())
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                Winner Selected
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                Test Running
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Variant A -->
                        <div class="relative {{ $campaign->ab_test_winner === 'variant_a' ? 'ring-2 ring-green-500' : '' }} rounded-lg border border-gray-200 p-4">
                            @if($campaign->ab_test_winner === 'variant_a')
                                <div class="absolute -top-3 left-4 inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                    🏆 Winner
                                </div>
                            @endif
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Variant A</h4>
                                <p class="text-sm text-gray-600 mb-4">"{{ $campaign->subject }}"</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-600">{{ $campaign->getVariantOpenRate('variant_a') }}%</p>
                                        <p class="text-xs text-gray-500">Open Rate</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-purple-600">{{ $campaign->getVariantClickRate('variant_a') }}%</p>
                                        <p class="text-xs text-gray-500">Click Rate</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    {{ $campaign->ab_test_split_percentage }}% of traffic
                                </div>
                            </div>
                        </div>

                        <!-- Variant B -->
                        <div class="relative {{ $campaign->ab_test_winner === 'variant_b' ? 'ring-2 ring-green-500' : '' }} rounded-lg border border-gray-200 p-4">
                            @if($campaign->ab_test_winner === 'variant_b')
                                <div class="absolute -top-3 left-4 inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                    🏆 Winner
                                </div>
                            @endif
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Variant B</h4>
                                <p class="text-sm text-gray-600 mb-4">"{{ $campaign->subject_variant_b }}"</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-600">{{ $campaign->getVariantOpenRate('variant_b') }}%</p>
                                        <p class="text-xs text-gray-500">Open Rate</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-purple-600">{{ $campaign->getVariantClickRate('variant_b') }}%</p>
                                        <p class="text-xs text-gray-500">Click Rate</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    {{ 100 - $campaign->ab_test_split_percentage }}% of traffic
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$campaign->hasWinner() && $campaign->isSent())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Select Winner</h4>
                                    <p class="text-sm text-gray-500">Choose the winning variant to conclude the test</p>
                                </div>
                                <div class="flex space-x-3">
                                    <form action="{{ route('crm.marketing.campaigns.select-winner', $campaign) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="winner" value="variant_a">
                                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                            Select A
                                        </button>
                                    </form>
                                    <form action="{{ route('crm.marketing.campaigns.select-winner', $campaign) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="winner" value="variant_b">
                                        <button type="submit" class="inline-flex items-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500">
                                            Select B
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Campaign Details -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Campaign Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Subject:</span>
                                <span class="text-sm text-gray-900">{{ $campaign->subject }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Type:</span>
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ ucfirst(str_replace('_', ' ', $campaign->type)) }}
                                </span>
                            </div>
                            @if($campaign->emailTemplate)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Template:</span>
                                <span class="text-sm text-gray-900">{{ $campaign->emailTemplate->name }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Creator:</span>
                                <span class="text-sm text-gray-900">{{ $campaign->creator ? $campaign->creator->name : 'Unknown' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Created:</span>
                                <span class="text-sm text-gray-900">{{ formatDubaiDate($campaign->created_at, 'M d, Y H:i') }}</span>
                            </div>
                            @if($campaign->scheduled_at)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Scheduled:</span>
                                <span class="text-sm text-gray-900">{{ formatDubaiDate($campaign->scheduled_at, 'M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($campaign->sent_at)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium text-gray-600">Sent:</span>
                                <span class="text-sm text-gray-900">{{ formatDubaiDate($campaign->sent_at, 'M d, Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            @if($recentLogs->count() > 0)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($recentLogs as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @if($log->status == 'sent')
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                                                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.896 28.896 0 003.105 2.289z" />
                                                    </svg>
                                                </span>
                                            @elseif($log->status == 'delivered')
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500">
                                                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @elseif($log->status == 'bounced')
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                                                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @elseif($log->status == 'failed')
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                                                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-500">
                                                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $log->contact->first_name }} {{ $log->contact->last_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $log->email }} - Email {{ $log->status }}</p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                <time datetime="{{ $log->created_at }}">{{ formatDubaiDateForHumans($log->created_at) }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Campaign Actions -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <a href="{{ route('crm.marketing.campaigns.preview', $campaign) }}" target="_blank" class="w-full inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        Preview Campaign
                    </a>
                    
                    @if($campaign->isDraft() || $campaign->isScheduled())
                        <form action="{{ route('crm.marketing.campaigns.send', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to send this campaign now? This action cannot be undone.')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 mb-3">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.896 28.896 0 003.105 2.289z" />
                                </svg>
                                Send Campaign Now
                            </button>
                        </form>
                    @endif

                    @if($campaign->isDraft())
                        <a href="{{ route('crm.marketing.campaigns.edit', $campaign) }}" class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                            </svg>
                            Edit Campaign
                        </a>
                    @endif
                    
                    <form action="{{ route('crm.marketing.campaigns.duplicate', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1z" />
                                <path d="M4.5 6A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h8a1.5 1.5 0 001.5-1.5v-5.879a1.5 1.5 0 00-.44-1.06L10.44 6.44A1.5 1.5 0 009.378 6H4.5z" />
                            </svg>
                            Duplicate Campaign
                        </button>
                    </form>
                </div>
            </div>

            <!-- Campaign Statistics -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistics</h3>
                    <div class="flex items-center space-x-2 mt-2">
                        <button data-trigger-stats-update class="inline-flex items-center text-xs text-blue-600 hover:text-blue-500">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                        <span class="text-xs text-gray-500" data-last-updated>Auto-updating every 30s</span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Total Recipients</span>
                        <span class="text-sm font-bold text-gray-900" data-sidebar-stat="total-recipients">{{ number_format($campaign->total_recipients) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Successfully Sent</span>
                        <span class="text-sm font-bold text-green-600" data-sidebar-stat="sent-count">{{ number_format($campaign->sent_count) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Delivery Rate</span>
                        <span class="text-sm font-bold text-blue-600" data-sidebar-stat="delivery-rate">{{ $campaign->delivery_rate }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Open Rate</span>
                        <span class="text-sm font-bold text-purple-600" data-sidebar-stat="open-rate">{{ $campaign->open_rate }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Click Through Rate</span>
                        <span class="text-sm font-bold text-indigo-600" data-sidebar-stat="click-rate">{{ $campaign->click_rate }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Bounce Rate</span>
                        <span class="text-sm font-bold text-red-600" data-sidebar-stat="bounce-rate">{{ $campaign->bounce_rate }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Unsubscribe Rate</span>
                        <span class="text-sm font-bold text-gray-600" data-sidebar-stat="unsubscribe-rate">{{ $campaign->unsubscribe_rate }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/campaign-stats-updater.js') }}"></script>
@endpush 