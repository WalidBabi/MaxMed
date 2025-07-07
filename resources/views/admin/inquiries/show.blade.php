@extends('admin.layouts.app')

@section('title', 'Inquiry Details - ' . $inquiry->reference_number)

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Inquiries
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $inquiry->reference_number }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Inquiry Details</h1>
                <p class="text-gray-600 mt-2">{{ $inquiry->reference_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($inquiry->status === 'pending')
                    <form action="{{ route('admin.inquiries.broadcast', $inquiry) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.935-2.186 2.25 2.25 0 00-3.935 2.186z" />
                            </svg>
                            Broadcast to Suppliers
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Inquiries
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Inquiry Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Inquiry Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $inquiry->reference_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'broadcast' => 'bg-indigo-100 text-indigo-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'quoted' => 'bg-green-100 text-green-800',
                                        'converted' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $color = $statusColors[$inquiry->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $color }}">
                                    {{ ucfirst($inquiry->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Products</dt>
                            <dd class="mt-1">
                                @if($inquiry->items && $inquiry->items->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($inquiry->items as $item)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        @if($item->product_id)
                                                            <div class="font-medium text-gray-900">{{ $item->product->name ?? 'Product Not Found' }}</div>
                                                            @if($item->product && $item->product->sku)
                                                                <div class="text-gray-500 text-xs">SKU: {{ $item->product->sku }}</div>
                                                            @endif
                                                        @else
                                                            <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                            @if($item->product_category)
                                                                <div class="text-gray-500 text-xs">Category: {{ $item->product_category }}</div>
                                                            @endif
                                                            @if($item->product_brand)
                                                                <div class="text-gray-500 text-xs">Brand: {{ $item->product_brand }}</div>
                                                            @endif
                                                        @endif
                                                        
                                                        @if($item->product_description)
                                                            <div class="text-gray-600 text-sm mt-1">{{ $item->product_description }}</div>
                                                        @endif
                                                        
                                                        @if($item->specifications)
                                                            <div class="text-gray-500 text-xs mt-1">
                                                                <span class="font-medium">Selected Specifications:</span>
                                                                @php
                                                                    $specs = json_decode($item->specifications, true);
                                                                    if (is_array($specs)) {
                                                                        echo implode(', ', $specs);
                                                                    } else {
                                                                        echo $item->specifications;
                                                                    }
                                                                @endphp
                                                            </div>
                                                        @endif
                                                        
                                                        @if($item->size)
                                                            <div class="text-gray-500 text-xs">
                                                                <span class="font-medium">Size:</span> {{ $item->size }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if($item->product_specifications)
                                                            <div class="text-gray-500 text-xs mt-1">Product Specifications: {{ $item->product_specifications }}</div>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($item->quantity)
                                                        <div class="ml-4 text-right">
                                                            <div class="text-sm font-medium text-gray-900">Qty: {{ number_format($item->quantity, 2) }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if($item->requirements)
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        <div class="text-xs font-medium text-gray-500">Requirements:</div>
                                                        <div class="text-sm text-gray-600">{{ $item->requirements }}</div>
                                                    </div>
                                                @endif
                                                
                                                @if($item->notes)
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        <div class="text-xs font-medium text-gray-500">Notes:</div>
                                                        <div class="text-sm text-gray-600">{{ $item->notes }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Fallback for legacy inquiries -->
                                    @if($inquiry->product_id)
                                        <div class="font-medium">{{ $inquiry->product->name ?? 'Product Not Found' }}</div>
                                        @if($inquiry->product && $inquiry->product->sku)
                                            <div class="text-gray-500 text-xs">SKU: {{ $inquiry->product->sku }}</div>
                                        @endif
                                    @else
                                        <div class="font-medium">{{ $inquiry->product_name }}</div>
                                        @if($inquiry->product_category)
                                            <div class="text-gray-500 text-xs">Category: {{ $inquiry->product_category }}</div>
                                        @endif
                                    @endif
                                    
                                    @if($inquiry->quantity)
                                        <div class="mt-2">
                                            <span class="text-sm font-medium text-gray-500">Quantity:</span>
                                            <span class="text-sm text-gray-900 font-semibold">{{ number_format($inquiry->quantity) }}</span>
                                        </div>
                                    @endif
                                @endif
                            </dd>
                        </div>
                    </div>

                    @if($inquiry->requirements)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Requirements</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->requirements }}</dd>
                        </div>
                    @endif

                    @if($inquiry->notes)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->notes }}</dd>
                        </div>
                    @endif

                    @if($inquiry->internal_notes)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Internal Notes</dt>
                            <dd class="mt-1 text-sm text-amber-600 font-medium">{{ $inquiry->internal_notes }}</dd>
                        </div>
                    @endif

                    @if($inquiry->customer_reference)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Customer Reference</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->customer_reference }}</dd>
                        </div>
                    @endif

                    @if($inquiry->attachments && count($inquiry->attachments) > 0)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Attachments</dt>
                            <dd class="mt-1">
                                <div class="space-y-2">
                                    @foreach($inquiry->attachments as $attachment)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ $attachment['original_name'] }}</p>
                                                    <p class="text-sm text-gray-500">{{ round($attachment['size'] / 1024 / 1024, 2) }} MB</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" 
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                                    View
                                                </a>
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" download="{{ $attachment['original_name'] }}"
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-gray-100 hover:bg-gray-200">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDubaiDate($inquiry->created_at, 'M j, Y g:i A') }}</dd>
                        </div>
                        @if($inquiry->broadcast_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Broadcast At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDubaiDate($inquiry->broadcast_at, 'M j, Y g:i A') }}</dd>
                            </div>
                        @endif
                    </div>

                    @if($inquiry->expires_at)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Expires</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDubaiDate($inquiry->expires_at, 'M j, Y g:i A') }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Supplier Responses and Quotations -->
            @if($inquiry->supplierResponses->count() > 0)
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            Supplier Responses ({{ $inquiry->supplierResponses->where('status', 'quoted')->count() }} Quotations)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation #</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inquiry->supplierResponses as $response)
                                    @php
                                        $quotation = $inquiry->quotations->where('supplier_id', $response->supplier->id)->first();
                                        $responseColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'viewed' => 'bg-blue-100 text-blue-800',
                                            'quoted' => 'bg-indigo-100 text-indigo-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'not_available' => 'bg-red-100 text-red-800'
                                        ];
                                        $color = $responseColors[$response->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $response->supplier->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $response->supplier->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $color }}">
                                                {{ ucfirst(str_replace('_', ' ', $response->status)) }}
                                            </span>
                                            @if($response->viewed_at)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Viewed {{ $response->viewed_at->diffForHumans() }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($quotation)
                                                <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $quotation->quotation_number }}</code>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($quotation)
                                                @if($quotation->items && $quotation->items->count() > 0)
                                                    <div class="text-sm text-gray-600">{{ $quotation->items->count() }} items</div>
                                                    <div class="text-xs text-gray-500">
                                                        @foreach($quotation->items->take(2) as $item)
                                                            {{ $item->product_name ?? $item->product->name ?? 'Product' }}: {{ $item->currency }} {{ number_format($item->unit_price, 2) }}<br>
                                                        @endforeach
                                                        @if($quotation->items->count() > 2)
                                                            <span class="text-gray-400">+{{ $quotation->items->count() - 2 }} more</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-sm font-medium text-gray-900">{{ $quotation->currency }} {{ number_format($quotation->unit_price ?? 0, 2) }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($quotation)
                                                @if($quotation->items && $quotation->items->count() > 0)
                                                    @php
                                                        $totalAmount = $quotation->items->sum(function($item) {
                                                            return ($item->unit_price ?? 0) * ($item->quantity ?? 1);
                                                        });
                                                        $totalShipping = $quotation->items->sum('shipping_cost');
                                                    @endphp
                                                    <div class="text-sm font-medium text-gray-900">{{ $quotation->items->first()->currency ?? 'AED' }} {{ number_format($totalAmount, 2) }}</div>
                                                    @if($totalShipping > 0)
                                                        <div class="text-sm text-gray-500">+ {{ $quotation->items->first()->currency ?? 'AED' }} {{ number_format($totalShipping, 2) }} shipping</div>
                                                    @endif
                                                @else
                                                    <div class="text-sm font-medium text-gray-900">{{ $quotation->currency }} {{ number_format($quotation->total_amount ?? 0, 2) }}</div>
                                                    @if($quotation->shipping_cost)
                                                        <div class="text-sm text-gray-500">+ {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }} shipping</div>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($quotation && $quotation->notes)
                                                <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $quotation->notes }}">
                                                    {{ $quotation->notes }}
                                                </div>
                                            @elseif($response->status === 'not_available' && $response->notes)
                                                <div class="text-sm text-red-600 max-w-xs truncate" title="{{ $response->notes }}">
                                                    <strong>Reason:</strong> {{ $response->notes }}
                                                </div>
                                            @elseif($response->status === 'not_available')
                                                <div class="text-sm text-red-600 max-w-xs">
                                                    <strong>Not Available</strong>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center space-x-3">
                                                @if($quotation)
                                                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        View Details
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
            @endif

            <!-- Not Available Responses -->
            @php
                $notAvailableResponses = $inquiry->supplierResponses->where('status', 'not_available');
            @endphp
            @if($notAvailableResponses->count() > 0)
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Not Available Responses ({{ $notAvailableResponses->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($notAvailableResponses as $response)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-red-800">{{ $response->supplier->name }}</h4>
                                                    <p class="text-sm text-red-600">{{ $response->supplier->email }}</p>
                                                </div>
                                            </div>
                                            @if($response->notes)
                                                <div class="mt-3">
                                                    <p class="text-sm text-red-700">
                                                        <strong>Reason:</strong> {{ $response->notes }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div class="mt-2 text-xs text-red-500">
                                                Responded {{ $response->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-800">
                                                Not Available
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Supplier Targeting -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-orange-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        Supplier Targeting
                    </h3>
                </div>
                <div class="p-6">
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Suppliers with relevant categories</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>This inquiry is broadcast to all suppliers with relevant product categories.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unlisted Product Details -->
            @if(!$inquiry->product_id)
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            Unlisted Product Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($inquiry->product_description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->product_description }}</dd>
                            </div>
                        @endif

                        @if($inquiry->product_brand)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->product_brand }}</dd>
                            </div>
                        @endif

                        @if($inquiry->product_specifications)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Specifications</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->product_specifications }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                        </svg>
                        Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($inquiry->status === 'pending')
                        <form action="{{ route('admin.inquiries.status.update', $inquiry) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="broadcast">
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.935-2.186 2.25 2.25 0 00-3.935 2.186z" />
                                </svg>
                                Broadcast to Suppliers
                            </button>
                        </form>
                    @endif

                    @if(in_array($inquiry->status, ['pending', 'broadcast', 'in_progress']))
                        <form action="{{ route('admin.inquiries.status.update', $inquiry) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this inquiry?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel Inquiry
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Purchase Orders Created from This Inquiry -->
            @php
                $purchaseOrders = \App\Models\PurchaseOrder::where('quotation_request_id', $inquiry->id)->get();
            @endphp
            @if($purchaseOrders->count() > 0)
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Purchase Orders Created ({{ $purchaseOrders->count() }})
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($purchaseOrders as $po)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $po->po_number }}</div>
                                            <div class="text-sm text-gray-500">{{ formatDubaiDate($po->po_date, 'M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $po->supplier_name }}</div>
                                            @if($po->supplier_email)
                                                <div class="text-sm text-gray-500">{{ $po->supplier_email }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $po->currency }} {{ $po->formatted_total }}</div>
                                            @if($po->paid_amount > 0)
                                                <div class="text-sm text-gray-500">Paid: {{ $po->currency }} {{ $po->formatted_paid_amount }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $po->status_badge_class }}">
                                                {{ \App\Models\PurchaseOrder::$statuses[$po->status] ?? ucfirst($po->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDubaiDate($po->created_at, 'M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.purchase-orders.show', $po) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                View PO
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 