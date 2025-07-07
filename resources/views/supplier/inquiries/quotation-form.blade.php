@extends('supplier.layouts.app')

@section('title', 'Submit Quotation - ' . $inquiry->reference_number)

@section('content')
<div class="max-w-7xl mx-auto">
    @php
        // Check if supplier has already submitted a quotation for this inquiry
        $existingQuotation = \App\Models\SupplierQuotation::where('supplier_inquiry_id', $inquiry->id)
            ->where('supplier_id', auth()->id())
            ->first();
    @endphp

    @if($existingQuotation)
        <!-- Quotation Already Submitted -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('supplier.inquiries.index') }}" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Quotation Already Submitted</h1>
                    </div>
                    <p class="text-gray-600 mt-2">Inquiry #{{ $inquiry->reference_number }}</p>
                </div>
            </div>
        </div>

        <!-- Already Submitted Message -->
        <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-green-600 to-emerald-700">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 text-green-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Quotation Submitted Successfully
                </h3>
            </div>
            <div class="p-8">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Quotation Already Submitted</h3>
                    <p class="text-gray-600 mb-6">
                        You have already submitted a quotation for this inquiry. 
                        @if($existingQuotation->status === 'accepted')
                            <span class="font-semibold text-green-600">Your quotation has been accepted!</span>
                        @elseif($existingQuotation->status === 'rejected')
                            <span class="font-semibold text-red-600">Your quotation was not accepted.</span>
                        @else
                            <span class="font-semibold text-blue-600">Your quotation is under review.</span>
                        @endif
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('supplier.inquiries.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Back to Inquiries
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
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
                        <h1 class="text-3xl font-bold text-gray-900">Submit Quotation</h1>
                    </div>
                    <p class="text-gray-600 mt-2">Inquiry #{{ $inquiry->reference_number }}</p>
                </div>
            </div>
        </div>

    <!-- Main Content Section -->
    <div class="mb-8">
        <!-- Main Content Area -->
        <div class="w-full">
            <!-- Quotation Form -->
            <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden">
                <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-700">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 text-blue-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Your Quotation
                        @if($existingQuotation)
                            <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white/20 text-white backdrop-blur-sm">
                                Update Existing
                            </span>
                        @endif
                    </h3>
                </div>
                <div class="p-8">
                    <form action="{{ route('supplier.inquiries.quotation.store', $inquiry) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-8">
                            @if($inquiry->items && $inquiry->items->count() > 0)
                                @foreach($inquiry->items as $item)
                                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100 shadow-sm">
                                        <h4 class="text-md font-semibold text-gray-800 mb-2">Product: {{ $item->product->name ?? $item->product_name ?? 'Unlisted Product' }}</h4>
                                        
                                        <input type="hidden" name="items[{{ $item->id }}][item_id]" value="{{ $item->id }}">
                                        
                                        <!-- Product Details with Image -->
                                        <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <div class="flex items-start space-x-4">
                                                <!-- Product Image -->
                                                @if($item->product_id && $item->product)
                                                    @if($item->product->primaryImage)
                                                        <img class="h-28 w-28 rounded-xl object-contain bg-white border-2 border-gray-100 shadow-sm" src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product->name }}">
                                                    @elseif($item->product->image_url)
                                                        <img class="h-28 w-28 rounded-xl object-contain bg-white border-2 border-gray-100 shadow-sm" src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                                                    @elseif($item->product->images && $item->product->images->count() > 0)
                                                        <img class="h-28 w-28 rounded-xl object-contain bg-white border-2 border-gray-100 shadow-sm" src="{{ $item->product->images->first()->image_url }}" alt="{{ $item->product->name }}">
                                                    @else
                                                        <div class="h-28 w-28 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                                            <span class="text-sm font-medium text-gray-600">{{ substr($item->product->name, 0, 2) }}</span>
                                                        </div>
                                                    @endif
                                                @elseif($item->product_name)
                                                    <div class="h-28 w-28 rounded-xl bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center shadow-sm">
                                                        <svg class="h-14 w-14 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="h-28 w-28 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                                        <svg class="h-14 w-14 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                
                                                <!-- Product Details -->
                                                <div class="flex-1">
                                            @if($item->product_id && $item->product)
                                                @if($item->product->sku)
                                                    <div class="text-sm text-gray-600 mb-1">
                                                        <span class="font-medium">SKU:</span> {{ $item->product->sku }}
                                                    </div>
                                                @endif
                                                @if($item->product->brand)
                                                    <div class="text-sm text-gray-600 mb-1">
                                                        <span class="font-medium">Brand:</span> {{ $item->product->brand->name }}
                                                    </div>
                                                @endif
                                                @if($item->product->category)
                                                    <div class="text-sm text-gray-600 mb-1">
                                                        <span class="font-medium">Category:</span> {{ $item->product->category->name }}
                                                    </div>
                                                @endif
                                            @else
                                                @if($item->product_brand)
                                                    <div class="text-sm text-gray-600 mb-1">
                                                        <span class="font-medium">Brand:</span> {{ $item->product_brand }}
                                                    </div>
                                                @endif
                                                @if($item->product_category)
                                                    <div class="text-sm text-gray-600 mb-1">
                                                        <span class="font-medium">Category:</span> {{ $item->product_category }}
                                                    </div>
                                                @endif
                                            @endif
                                            
                                            @if($item->specifications)
                                                <div class="text-sm text-gray-600 mb-1">
                                                    <span class="font-medium">Requested Specifications:</span>
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
                                                <div class="text-sm text-gray-600 mb-1">
                                                    <span class="font-medium">Requested Size:</span> {{ $item->size }}
                                                </div>
                                            @endif
                                            
                                            @if($item->quantity)
                                                <div class="text-sm text-gray-600 mb-1">
                                                    <span class="font-medium">Quantity:</span> {{ number_format($item->quantity, 2) }}
                                                </div>
                                            @endif
                                            
                                            @if($item->requirements)
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">Requirements:</span> {{ $item->requirements }}
                                                </div>
                                            @endif
                                                </div>
                                            </div>
                                        
                                        <!-- Check if this is a PDF-only inquiry with multiple products -->
                                        @php
                                            // Check if this is a PDF-only inquiry
                                            $hasAttachments = $inquiry->attachments && is_array($inquiry->attachments) && count($inquiry->attachments) > 0;
                                            
                                                                                    // Check if the inquiry has any product information
                                        $hasMainProductInfo = ($inquiry->product_id && $inquiry->product && $inquiry->product->name) || 
                                                              $inquiry->product_name || 
                                                              $inquiry->product_description;
                                            
                                            // Check if any items have product information
                                            $hasItemsProductInfo = false;
                                            if ($inquiry->items && $inquiry->items->count() > 0) {
                                                foreach ($inquiry->items as $inquiryItem) {
                                                    if (($inquiryItem->product_id && $inquiryItem->product && $inquiryItem->product->name) || 
                                                        $inquiryItem->product_name || 
                                                        $inquiryItem->product_description) {
                                                        $hasItemsProductInfo = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            // PDF-only inquiry: has attachments but no product information
                                            $isPdfOnly = $hasAttachments && !$hasMainProductInfo && !$hasItemsProductInfo;
                                            
                                                                                    // Debug information (remove in production)
                                        // if (app()->environment('local')) {
                                        //     echo "<!-- DEBUG: hasAttachments=$hasAttachments, hasMainProductInfo=$hasMainProductInfo, hasItemsProductInfo=$hasItemsProductInfo, isPdfOnly=$isPdfOnly -->";
                                        //     echo "<!-- DEBUG DETAILS: product_id=" . ($inquiry->product_id ?? 'null') . ", product_name='" . ($inquiry->product_name ?? 'null') . "', requirements='" . ($inquiry->requirements ?? 'null') . "', product_description='" . ($inquiry->product_description ?? 'null') . "', notes='" . ($inquiry->notes ?? 'null') . "' -->";
                                        // }
                                        @endphp
                                        
                                        @if($isPdfOnly)
                                            <!-- PDF-only inquiry - only show file upload -->
                                            <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-blue-800">PDF Document Inquiry</span>
                                                </div>
                                                <p class="text-sm text-blue-700 mt-1">Please upload your quotation documents for this product.</p>
                                            </div>
                                            
                                            <!-- Hidden required fields for form validation -->
                                            <input type="hidden" name="items[{{ $item->id }}][unit_price]" value="0">
                                            <input type="hidden" name="items[{{ $item->id }}][currency]" value="AED">
                                            <input type="hidden" name="items[{{ $item->id }}][shipping_cost]" value="0">
                                            <input type="hidden" name="items[{{ $item->id }}][size]" value="">
                                            <input type="hidden" name="items[{{ $item->id }}][notes]" value="">
                                        @else
                                            <!-- Regular inquiry - show all input fields -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Price</label>
                                                    <div class="mt-1 flex rounded-lg shadow-sm">
                                                        <div class="relative flex items-stretch flex-grow focus-within:z-10">
                                                            <input type="number" name="items[{{ $item->id }}][unit_price]" step="0.01" min="0" required class="block w-full rounded-none rounded-l-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0.00">
                                                        </div>
                                                        <select name="items[{{ $item->id }}][currency]" class="relative -ml-px inline-flex items-center rounded-r-lg border border-gray-300 bg-gray-50 px-4 py-2 pr-8 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                            <option value="AED" {{ $defaultCurrency === 'AED' ? 'selected' : '' }}>AED</option>
                                                            <option value="CNY" {{ $defaultCurrency === 'CNY' ? 'selected' : '' }}>CNY</option>
                                                            <option value="USD" {{ $defaultCurrency === 'USD' ? 'selected' : '' }}>USD</option>
                                                            <option value="EUR" {{ $defaultCurrency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Shipping Cost (Optional)</label>
                                                    <input type="number" name="items[{{ $item->id }}][shipping_cost]" step="0.01" min="0" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-lg shadow-sm" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Condition/Size (Optional)</label>
                                                    <input type="text" name="items[{{ $item->id }}][size]" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-lg" placeholder="e.g., 100ml, Large, etc.">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes & Terms</label>
                                                    <textarea name="items[{{ $item->id }}][notes]" rows="2" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-lg" placeholder="Add any additional notes, terms, or conditions..."></textarea>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- File upload section - always show -->
                                        <div class="mt-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Attachments (Optional, multiple)</label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                                <div class="space-y-1 text-center">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="attachments-{{ $item->id }}" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                            <span>Upload files</span>
                                                            <input id="attachments-{{ $item->id }}" name="items[{{ $item->id }}][attachments][]" type="file" class="sr-only" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" data-file-type="attachments-{{ $item->id }}">
                                                        </label>
                                                        <p class="pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PDF, JPG, PNG, DOC up to 20MB each</p>
                                                </div>
                                            </div>
                                            <div id="attachments-{{ $item->id }}-files" class="mt-3 space-y-2 border border-gray-200 p-2 min-h-[50px] bg-gray-50">
                                                <p class="text-xs text-gray-500">Quotation files will appear here when selected</p>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">You may upload quotation documents, product datasheets, certifications, or other relevant files.</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Single Product Quotation Fields -->
                                <div class="space-y-6">
                                    @php
                                        // Check if this is a PDF-only inquiry (single product)
                                        $hasAttachments = $inquiry->attachments && is_array($inquiry->attachments) && count($inquiry->attachments) > 0;
                                        $hasProductInfo = ($inquiry->product_id && $inquiry->product && $inquiry->product->name) || 
                                                         $inquiry->product_name || 
                                                         $inquiry->product_description;
                                        $isPdfOnlySingle = $hasAttachments && !$hasProductInfo;
                                        
                                        // Debug information (remove in production)
                                        if (app()->environment('local')) {
                                            echo "<!-- DEBUG SINGLE: hasAttachments=$hasAttachments, hasProductInfo=$hasProductInfo, isPdfOnlySingle=$isPdfOnlySingle -->";
                                            echo "<!-- DEBUG SINGLE DETAILS: product_id=" . ($inquiry->product_id ?? 'null') . ", product_name='" . ($inquiry->product_name ?? 'null') . "', requirements='" . ($inquiry->requirements ?? 'null') . "', product_description='" . ($inquiry->product_description ?? 'null') . "', notes='" . ($inquiry->notes ?? 'null') . "' -->";
                                        }
                                    @endphp
                                    
                                    <!-- Product Information -->
                                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                                        <h4 class="text-md font-semibold text-gray-800 mb-3">Product Information</h4>
                                        
                                        <div class="flex items-start space-x-4">
                                            <!-- Product Image -->
                                            @if($inquiry->product_id && $inquiry->product)
                                                @if($inquiry->product->primaryImage)
                                                    <img class="h-24 w-24 rounded-lg object-contain bg-white border border-gray-200" src="{{ $inquiry->product->primaryImage->image_url }}" alt="{{ $inquiry->product->name }}">
                                                @elseif($inquiry->product->image_url)
                                                    <img class="h-24 w-24 rounded-lg object-contain bg-white border border-gray-200" src="{{ $inquiry->product->image_url }}" alt="{{ $inquiry->product->name }}">
                                                @elseif($inquiry->product->images && $inquiry->product->images->count() > 0)
                                                    <img class="h-24 w-24 rounded-lg object-contain bg-white border border-gray-200" src="{{ $inquiry->product->images->first()->image_url }}" alt="{{ $inquiry->product->name }}">
                                                @else
                                                    <div class="h-24 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-600">{{ substr($inquiry->product->name, 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            @elseif($inquiry->product_name)
                                                <div class="h-24 w-24 rounded-lg bg-orange-100 flex items-center justify-center">
                                                    <svg class="h-12 w-12 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="h-24 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            <!-- Product Details -->
                                            <div class="flex-1">
                                        @if($inquiry->product_id && $inquiry->product)
                                            <div class="space-y-2">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700">Product:</span> {{ $inquiry->product->name }}
                                                </div>
                                                @if($inquiry->product->sku)
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-700">SKU:</span> {{ $inquiry->product->sku }}
                                                    </div>
                                                @endif
                                                @if($inquiry->product->brand)
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-700">Brand:</span> {{ $inquiry->product->brand->name }}
                                                    </div>
                                                @endif
                                                @if($inquiry->product->category)
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-700">Category:</span> {{ $inquiry->product->category->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($inquiry->product_name)
                                            <div class="space-y-2">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700">Product:</span> {{ $inquiry->product_name }}
                                                </div>
                                                @if($inquiry->product_category)
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-700">Category:</span> {{ $inquiry->product_category }}
                                                    </div>
                                                @endif
                                                @if($inquiry->product_brand)
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-700">Brand:</span> {{ $inquiry->product_brand }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($inquiry->product_description)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700">Description:</span> {{ $inquiry->product_description }}
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($inquiry->quantity)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700">Quantity:</span> {{ number_format($inquiry->quantity, 2) }}
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($inquiry->requirements)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-700">Requirements:</span> {{ $inquiry->requirements }}
                                                </div>
                                            </div>
                                        @endif
                                            </div>
                                        </div>
                                    
                                    @if($isPdfOnlySingle)
                                        <!-- PDF-only inquiry - pricing is optional -->
                                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-blue-800">PDF Document Inquiry</span>
                                            </div>
                                            <p class="text-sm text-blue-700 mt-1">Pricing information is optional for PDF-only inquiries. You may provide pricing if available or leave blank.</p>
                                        </div>
                                    @else
                                        <!-- Unit Price -->
                                        <div>
                                            <label for="unit_price" class="block text-sm font-medium text-gray-700">
                                                Unit Price
                                                *
                                            </label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <div class="relative flex items-stretch flex-grow focus-within:z-10">
                                                    <input type="number" step="0.01" name="unit_price" id="unit_price" 
                                                           class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                           placeholder="0.00" required min="0">
                                                </div>
                                                <select name="currency" class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 bg-gray-50 px-4 py-2 pr-8 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <option value="AED" {{ $defaultCurrency === 'AED' ? 'selected' : '' }}>AED</option>
                                                    <option value="CNY" {{ $defaultCurrency === 'CNY' ? 'selected' : '' }}>CNY</option>
                                                    <option value="USD" {{ $defaultCurrency === 'USD' ? 'selected' : '' }}>USD</option>
                                                    <option value="EUR" {{ $defaultCurrency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Shipping Cost -->
                                        <div>
                                            <label for="shipping_cost" class="block text-sm font-medium text-gray-700">Shipping Cost (Optional)</label>
                                            <div class="mt-1">
                                                <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" 
                                                       class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                       placeholder="0.00" min="0">
                                            </div>
                                        </div>
                                        <!-- Size/Variant -->
                                        <div>
                                            <label for="size" class="block text-sm font-medium text-gray-700">Size/Variant (Optional)</label>
                                            <div class="mt-1">
                                                <input type="text" name="size" id="size" 
                                                       class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                       placeholder="e.g., Large, 500ml, etc.">
                                            </div>
                                        </div>
                                        <!-- Notes -->
                                        <div>
                                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                            <div class="mt-1">
                                                <textarea name="notes" id="notes" rows="4" 
                                                          class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                          placeholder="Any additional information about your quotation..."></textarea>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- Attachments -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Attachments (Optional, multiple)</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>Upload files</span>
                                                        <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" data-file-type="attachments">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PDF, JPG, PNG, DOC up to 20MB each</p>
                                            </div>
                                        </div>
                                        <div id="attachments-files" class="mt-3 space-y-2 border border-gray-200 p-2 min-h-[50px] bg-gray-50">
                                            <p class="text-xs text-gray-500">Quotation files will appear here when selected</p>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">You may upload quotation documents, product datasheets, certifications, or other relevant files.</p>
                                    </div>
                                </div>
                            @endif
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('supplier.inquiries.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Submit Quotation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing file upload handlers');
    
    const selectedFiles = new Map();
    
    // File input elements
    const fileInputs = document.querySelectorAll('input[type="file"]');
    console.log('Found file inputs:', fileInputs.length);
    
    // Log all containers to verify they exist
    fileInputs.forEach((input, index) => {
        const containerId = `${input.dataset.fileType}-files`;
        const container = document.getElementById(containerId);
        console.log(`Container ${index}:`, containerId, container);
    });
    
    fileInputs.forEach((input, index) => {
        console.log(`Setting up input ${index}:`, input.id, input.dataset.fileType);
        
        // Test if the input exists and has the right attributes
        console.log('Input details:', {
            id: input.id,
            dataset: input.dataset,
            fileType: input.dataset.fileType,
            type: input.type,
            multiple: input.multiple
        });
        
        input.addEventListener('change', function(e) {
            console.log('=== FILE INPUT CHANGED ===');
            console.log('Input ID:', this.id);
            console.log('Files selected:', e.target.files.length);
            console.log('Files:', Array.from(e.target.files).map(f => f.name));
            
            const files = Array.from(e.target.files);
            const fileType = this.dataset.fileType;
            
            console.log('File type from dataset:', fileType);
            
            // Don't clear existing files - add new ones instead
            // clearFilesForType(fileType);
            
            if (files.length > 0) {
                files.forEach(file => {
                    const fileId = `${fileType}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                    selectedFiles.set(fileId, {
                        file: file,
                        type: fileType,
                        input: this
                    });
                    console.log('Added file:', file.name, 'with ID:', fileId);
                });
            }
            
            updateFileDisplay(fileType);
        });
        
        // Also add a click event to test if the input is clickable
        input.addEventListener('click', function(e) {
            console.log('File input clicked:', this.id);
        });
    });
    
    function clearFilesForType(fileType) {
        console.log('Clearing files for type:', fileType);
        // Remove all files of this type from the map
        for (let [fileId, fileData] of selectedFiles.entries()) {
            if (fileData.type === fileType) {
                selectedFiles.delete(fileId);
            }
        }
    }
    
    function updateFileDisplay(fileType) {
        console.log('Updating file display for type:', fileType);
        const containerId = `${fileType}-files`;
        const container = document.getElementById(containerId);
        
        console.log('Container found:', containerId, container);
        
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }
        
        container.innerHTML = '';
        
        // Get all files for this type
        const filesForType = [];
        selectedFiles.forEach((fileData, fileId) => {
            if (fileData.type === fileType) {
                filesForType.push({ fileId, fileData });
            }
        });
        
        console.log('Files for type', fileType, ':', filesForType.length);
        
        if (filesForType.length === 0) {
            container.innerHTML = '<p class="text-xs text-gray-500">Quotation files will appear here when selected</p>';
            return;
        }
        
        filesForType.forEach(({ fileId, fileData }) => {
            const file = fileData.file;
            const fileSize = formatFileSize(file.size);
            const isPdf = file.name.toLowerCase().endsWith('.pdf');
            
            console.log('Creating file element for:', file.name, 'isPdf:', isPdf);
            
            const fileElement = document.createElement('div');
            fileElement.className = 'flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg';
            
            fileElement.innerHTML = `
                <div class="flex items-center">
                    ${isPdf ? 
                        `<svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 2v6a1 1 0 001 1h6"></path>
                        </svg>` :
                        `<svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>`
                    }
                    <div>
                        <h4 class="font-medium text-blue-900 text-sm">${file.name}${isPdf ? ' <span class="text-red-600 text-xs">(PDF)</span>' : ''}</h4>
                        <p class="text-xs text-blue-700">${fileSize}</p>
                    </div>
                </div>
                <button type="button" onclick="removeFile('${fileId}')" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(fileElement);
            console.log('File element added to container');
        });
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Make removeFile function global
    window.removeFile = function(fileId) {
        console.log('Removing file:', fileId);
        const fileData = selectedFiles.get(fileId);
        if (fileData) {
            // Clear the file input
            fileData.input.value = '';
            selectedFiles.delete(fileId);
            updateFileDisplay(fileData.type);
        }
    };
    
    // Form submission handling
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        
        // Transfer files from selectedFiles map to actual file inputs
        selectedFiles.forEach((fileData, fileId) => {
            const input = fileData.input;
            const file = fileData.file;
            
            console.log('Transferring file to input:', file.name, 'to input:', input.name);
            
            // Create a new FileList-like object
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            
            console.log('File transferred. Input now has', input.files.length, 'files');
        });
        
        // Clear any existing files from the map to prevent memory leaks
        selectedFiles.clear();
    });
});

function updateCurrencySymbols() {
    const currency = document.getElementById('currency');
    if (currency) {
        const currencyValue = currency.value;
        const unitSymbol = document.getElementById('unit-currency-symbol');
        const totalSymbol = document.getElementById('total-currency-symbol');
        const shippingSymbol = document.getElementById('shipping-currency-symbol');
        const shippingDisplay = document.getElementById('shipping-currency-display');
        
        if (unitSymbol) unitSymbol.textContent = currencyValue;
        if (totalSymbol) totalSymbol.textContent = currencyValue;
        if (shippingSymbol) shippingSymbol.textContent = currencyValue;
        if (shippingDisplay) shippingDisplay.textContent = currencyValue;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCurrencySymbols();
});
</script>
    @endif
@endsection