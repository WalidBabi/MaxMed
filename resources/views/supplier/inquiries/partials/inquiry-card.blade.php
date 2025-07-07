@php
$currentStatus = $inquiry instanceof \App\Models\QuotationRequest 
    ? ($inquiry->supplier_response === 'not_available' 
        ? 'not_interested' 
        : ($inquiry->supplier_response === 'available' 
            ? 'quoted' 
            : 'pending'))
    : ($inquiry->supplierResponses->where('user_id', auth()->id())->first()->status ?? 'pending');
@endphp
<!-- Inquiry Card -->
<div class="bg-white rounded-lg shadow-sm border border-{{ $color }}-200 hover:border-{{ $color }}-300 transition-colors duration-200 inquiry-card group"
     draggable="true"
     ondragstart="handleDragStart(event)"
     ondragend="handleDragEnd(event)"
     ondragover="handleDragOver(event)"
     ondrop="handleDrop(event, '{{ $currentStatus }}')"
     data-inquiry-id="{{ $inquiry->id }}"
     data-current-status="{{ $currentStatus }}">
    <div class="p-4 relative">
        <!-- Drag Handle Indicator -->
        <div class="absolute -left-0.5 top-1/2 -translate-y-1/2 w-1 h-12 bg-{{ $color }}-200 group-hover:bg-{{ $color }}-300 rounded-r transition-colors duration-200"></div>
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-mono text-gray-500">{{ $inquiry->reference_number }}</span>
            <span class="text-xs text-gray-500">{{ $inquiry->created_at->format('M d, Y') }}</span>
        </div>

        <!-- Product Info -->
        <div class="mb-3">
            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">
                @if($inquiry->product && $inquiry->product->name)
                    {{ $inquiry->product->name }}
                @elseif($inquiry->product_name)
                    {{ $inquiry->product_name }}
                @elseif($inquiry->requirements)
                    {{ Str::limit($inquiry->requirements, 50) }}
                @elseif($inquiry->product_description)
                    {{ Str::limit($inquiry->product_description, 50) }}
                @else
                    Product Inquiry
                @endif
            </h4>
            @if($inquiry->product_description && $inquiry->product_description !== $inquiry->product_name)
                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $inquiry->product_description }}</p>
            @endif
        </div>

        <!-- Quantity -->
        @if($inquiry->quantity && $inquiry->quantity > 0)
            <div class="flex items-center text-sm text-gray-600 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span>{{ number_format($inquiry->quantity) }} units</span>
            </div>
        @elseif($inquiry->attachments && is_array($inquiry->attachments) && count($inquiry->attachments) > 0)
            <div class="flex items-center text-sm text-red-600 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>PDF Document</span>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
            @if(in_array($color, ['yellow', 'blue', 'indigo']))
                <!-- Submit Quotation Button -->
                                        <a href="{{ route('supplier.inquiries.quotation', $inquiry->id) }}" 
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Submit Quotation
                </a>
                <!-- Not Available Button -->
                <button onclick="markNotAvailable('{{ $inquiry->id }}')" 
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Not Available
                </button>
            @endif

            @if(in_array($color, ['purple', 'emerald']))
                <!-- View Quotation Button -->
                <a href="{{ route('supplier.inquiries.show', $inquiry->id) }}" 
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Quotation
                </a>
            @endif
        </div>

        <div class="flex items-start space-x-3">
            <!-- Removed avatar/circle area -->
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $inquiry->customer_name }}</p>
                <p class="text-sm text-gray-500 truncate">{{ $inquiry->email }}</p>
                @if(!empty($inquiry->product_description))
                    <p class="text-xs text-gray-700 mt-1">{{ Str::limit($inquiry->product_description, 80) }}</p>
                @elseif(!empty($inquiry->notes))
                    <p class="text-xs text-gray-700 mt-1">{{ Str::limit($inquiry->notes, 80) }}</p>
                @elseif(!empty($inquiry->attachments) && is_array($inquiry->attachments) && count($inquiry->attachments) > 0)
                    <div class="flex items-center mt-1">
                        <svg class="w-4 h-4 text-red-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V6m0 0l-7 7m7-7l7 7" />
                        </svg>
                        <a href="{{ asset('storage/' . $inquiry->attachments[0]) }}" target="_blank" class="text-xs text-red-700 underline">View PDF</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 