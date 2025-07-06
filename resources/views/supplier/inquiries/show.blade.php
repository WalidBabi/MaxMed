@extends('supplier.layouts.app')

@section('title', 'Inquiry Details - ' . $inquiry->reference_number)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('supplier.inquiries.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Inquiry Details</h1>
                </div>
                <p class="text-gray-600 mt-2">Reference #{{ $inquiry->reference_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @php
                    $showResponseActions = false;
                    $isQuoted = false;
                    
                    // Handle both QuotationRequest (legacy) and SupplierInquiry (new) models
                    if ($inquiry instanceof \App\Models\QuotationRequest) {
                        // Legacy QuotationRequest model
                        $responseStatus = $inquiry->supplier_response ?? 'pending';
                        $isQuoted = $responseStatus === 'available';
                        $showResponseActions = in_array($responseStatus, ['pending']) && !$isQuoted;
                    } else {
                        // New SupplierInquiry model
                        $response = $inquiry->supplierResponses->where('user_id', auth()->id())->first();
                        
                        if ($response) {
                            $showResponseActions = in_array($response->status, ['pending', 'viewed']) && $response->status !== 'quoted';
                            $isQuoted = $response->status === 'quoted';
                            $responseStatus = $response->status;
                        } else {
                            $showResponseActions = false;
                            $isQuoted = false;
                            $responseStatus = 'pending';
                        }
                    }

                    $statusClass = match($responseStatus) {
                        'pending' => 'bg-orange-100 text-orange-800',
                        'viewed' => 'bg-blue-100 text-blue-800',
                        'quoted' => 'bg-green-100 text-green-800',
                        'accepted' => 'bg-indigo-100 text-indigo-800',
                        'not_available', 'not_interested' => 'bg-red-100 text-red-800',
                        'available' => 'bg-green-100 text-green-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                    
                    $statusText = match($responseStatus) {
                        'pending' => 'Awaiting Response',
                        'viewed' => 'Viewed',
                        'quoted' => 'Quotation Submitted',
                        'accepted' => 'Accepted',
                        'not_available', 'not_interested' => 'Not Available',
                        'available' => 'Quotation Submitted',
                        default => ucfirst($inquiry->status)
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                    {{ $statusText }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Debug Info -->
            @if(auth()->user()->isAdmin())
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <p>Debug Info:</p>
                    <ul class="list-disc list-inside">
                        <li>Model Type: {{ get_class($inquiry) }}</li>
                        <li>supplier_response: {{ $inquiry->supplier_response ?? 'N/A' }}</li>
                        <li>status: {{ $inquiry->status }}</li>
                        @if($inquiry instanceof \App\Models\SupplierInquiry)
                            <li>supplier_response_status: {{ $inquiry->supplierResponses->where('user_id', auth()->id())->first()->status ?? 'N/A' }}</li>
                        @endif
                    </ul>
                </div>
            @endif
            
            <!-- Response Actions -->
            @if($isQuoted)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-blue-800">Quotation Submitted</h3>
                            <p class="text-blue-700 mt-1">You have already submitted a quotation for this inquiry. The status cannot be changed unless an admin accepts or rejects your quotation.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($showResponseActions)
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-orange-800">Response Required</h3>
                            <p class="text-orange-700 mt-1">Please review this inquiry and provide your response.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('supplier.inquiries.quotation', $inquiry) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Submit Quotation
                        </a>
                        <button type="button" 
                                onclick="markNotAvailable('{{ $inquiry->id }}')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Mark Not Available
                        </button>
                    </div>
                </div>
            @endif

            <!-- My Quotation -->
            @php
                $myQuotation = null;
                if ($inquiry instanceof \App\Models\QuotationRequest) {
                    // For legacy QuotationRequest, get quotation directly
                    $myQuotation = $inquiry->supplierQuotations->where('supplier_id', auth()->id())->first();
                } else {
                    // For new SupplierInquiry, get quotation through response
                    if ($response && $response->quotation) {
                        $myQuotation = $response->quotation;
                    }
                }
            @endphp
            
            @if($myQuotation)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Your Quotation
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $myQuotation->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($myQuotation->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $myQuotation->currency }} {{ number_format($myQuotation->unit_price, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subtotal ({{ number_format($inquiry->quantity) }} units)</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $myQuotation->currency }} {{ number_format($myQuotation->unit_price * $inquiry->quantity, 2) }}
                                </dd>
                            </div>
                        </div>

                        @if($myQuotation->shipping_cost)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-500">Shipping Cost</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $myQuotation->currency }} {{ number_format($myQuotation->shipping_cost, 2) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center mt-3">
                                    <dt class="text-base font-medium text-gray-900">Total Amount (Including Shipping)</dt>
                                    <dd class="text-base font-bold text-gray-900">
                                        {{ $myQuotation->currency }} {{ number_format(($myQuotation->unit_price * $inquiry->quantity) + $myQuotation->shipping_cost, 2) }}
                                    </dd>
                                </div>
                            </div>
                        @endif

                        @if($myQuotation->size)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-gray-500">Product Condition/Size</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $myQuotation->size }}</dd>
                            </div>
                        @endif

                        @if($myQuotation->notes)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-gray-500">Notes & Terms</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-lg p-3">
                                    {{ $myQuotation->notes }}
                                </dd>
                            </div>
                        @endif

                        @if($myQuotation->status === 'approved')
                            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-green-800">Quotation Approved</h4>
                                        <p class="mt-1 text-sm text-green-700">
                                            Your quotation has been approved. A purchase order will be sent to you. Please proceed with order processing.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($myQuotation->status === 'accepted')
                            <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-emerald-800">Quotation Accepted - Proceed with Order</h4>
                                        <p class="mt-1 text-sm text-emerald-700">
                                            Your quotation has been accepted by the admin. A purchase order will be sent to you shortly. Please proceed with order processing.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Product Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start space-x-6">
                        @if($inquiry->product_id && $inquiry->product && $inquiry->product->primaryImage)
                            <img class="h-20 w-20 rounded-lg object-cover" src="{{ asset('storage/' . $inquiry->product->primaryImage->image_path) }}" alt="{{ $inquiry->product->name }}">
                        @else
                            <div class="h-20 w-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h4 class="text-xl font-medium text-gray-900">
                                @if($inquiry->product_id && $inquiry->product)
                                    {{ $inquiry->product->name }}
                                @else
                                    {{ $inquiry->product_name }}
                                @endif
                            </h4>
                            
                            @if($inquiry->product_description)
                                <p class="text-gray-600 mt-2">{{ $inquiry->product_description }}</p>
                            @endif
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quantity Requested</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($inquiry->quantity) }} units</dd>
                                </div>
                                
                                @if($inquiry->product_category)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($inquiry->product_id && $inquiry->product && $inquiry->product->category)
                                                {{ $inquiry->product->category->name }}
                                            @elseif($inquiry->product_category_id)
                                                {{ \App\Models\Category::find($inquiry->product_category_id)?->name ?? $inquiry->product_category }}
                                            @else
                                                {{ $inquiry->product_category }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                                
                                @if($inquiry->product_brand)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->product_brand }}</dd>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Requirements -->
                    @if($inquiry->requirements)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h5 class="text-sm font-medium text-gray-900">Requirements</h5>
                            <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-700">{{ $inquiry->requirements }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Product Specifications -->
                    @if($inquiry->product_specifications)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h5 class="text-sm font-medium text-gray-900">Product Specifications</h5>
                            <div class="mt-2 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                @if(is_array($inquiry->product_specifications))
                                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                        @foreach($inquiry->product_specifications as $spec)
                                            <li>{{ $spec }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-700">{{ $inquiry->product_specifications }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Additional Notes -->
                    @if($inquiry->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h5 class="text-sm font-medium text-gray-900">Additional Notes</h5>
                            <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-700">{{ $inquiry->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Attachments Section --}}
            @if(!empty($inquiry->attachments) && is_array($inquiry->attachments) && count($inquiry->attachments) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-8">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Attachments
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        @foreach($inquiry->attachments as $attachment)
                            <div class="bg-gray-50 rounded-xl shadow p-6 flex flex-col items-center">
                                <div class="w-full flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                    <div>
                                        <div class="font-medium text-gray-800 text-lg">{{ $attachment['original_name'] ?? 'Attachment' }}</div>
                                        <div class="text-xs text-gray-500 mb-2">{{ $attachment['mime_type'] ?? '' }} | {{ isset($attachment['size']) ? number_format($attachment['size']/1024, 2) . ' KB' : '' }}</div>
                                    </div>
                                    <div class="flex space-x-2 mt-2 md:mt-0">
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                            View
                                        </a>
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" download="{{ $attachment['original_name'] ?? '' }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-gray-100 hover:bg-gray-200">
                                            Download
                                        </a>
                                    </div>
                                </div>
                                @if(isset($attachment['mime_type']) && Str::startsWith($attachment['mime_type'], 'application/pdf'))
                                    <div class="w-full flex justify-center">
                                        <iframe src="{{ asset('storage/' . $attachment['path']) }}" width="100%" height="700px" style="border:1px solid #e5e7eb; border-radius: 0.75rem; background: white;"></iframe>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Inquiry Status
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Current Status -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-700">Current Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        
                        <!-- Workflow Steps -->
                        <div class="space-y-3">
                            <!-- Step 1: Inquiry Received -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Inquiry Received</p>
                                    <p class="text-xs text-gray-500">{{ $inquiry->created_at->format('M d, g:i A') }}</p>
                                </div>
                            </div>

                            <!-- Step 2: Response -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($responseStatus === 'pending')
                                        <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @elseif($responseStatus === 'quoted' || $responseStatus === 'available')
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @elseif($responseStatus === 'not_available' || $responseStatus === 'not_interested')
                                        <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $statusText }}</p>
                                    @if($inquiry->supplier_responded_at)
                                        <p class="text-xs text-gray-500">{{ $inquiry->supplier_responded_at->format('M d, g:i A') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3: Order Creation (if quotation approved) -->
                            @if($inquiry->supplier_response === 'available')
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($inquiry->status === 'converted')
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            @if($inquiry->status === 'converted')
                                                Order Created
                                            @else
                                                Awaiting Approval
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if($inquiry->status === 'converted')
                                                Ready for processing
                                            @else
                                                Under admin review
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Inquiry Details -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $inquiry->reference_number }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->created_at->format('M d, Y g:i A') }}</dd>
                        </div>

                        @if($inquiry->expires_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Response Deadline</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->expires_at->format('M d, Y g:i A') }}</dd>
                                @if($inquiry->expires_at->isPast())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                        Expired
                                    </span>
                                @elseif($inquiry->expires_at->diffInDays() <= 1)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                        Due Soon
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if($inquiry->customer_reference)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Customer Reference</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->customer_reference }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Not Available Modal -->
<div id="notAvailableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Mark as Not Available</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">
                        Please provide a reason why this product is not available.
                    </p>
                </div>
                <form id="notAvailableForm" class="mt-4" action="{{ route('supplier.inquiries.not-available', $inquiry) }}" method="POST">
                    @csrf
                    <div class="text-left">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <textarea id="reason" name="reason" rows="3" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                  placeholder="e.g., Out of stock, Discontinued, Not in our catalog..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeNotAvailableModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Mark Not Available
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function markNotAvailable(inquiryId) {
    const modal = document.getElementById('notAvailableModal');
    modal.classList.remove('hidden');
}

function closeNotAvailableModal() {
    const modal = document.getElementById('notAvailableModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('notAvailableModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeNotAvailableModal();
    }
});
</script>
@endsection 