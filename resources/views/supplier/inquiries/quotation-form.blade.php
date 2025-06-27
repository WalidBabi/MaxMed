@extends('supplier.layouts.app')

@section('title', 'Submit Quotation - ' . $inquiry->reference_number)

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
                    <h1 class="text-3xl font-bold text-gray-900">Submit Quotation</h1>
                </div>
                <p class="text-gray-600 mt-2">Inquiry #{{ $inquiry->reference_number }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Inquiry Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Product Request Details
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Product Information -->
                        <div class="flex items-start space-x-4">
                            @if($inquiry->product_id && $inquiry->product && $inquiry->product->primaryImage)
                                <img class="h-16 w-16 rounded-lg object-cover" src="{{ asset('storage/' . $inquiry->product->primaryImage->image_path) }}" alt="{{ $inquiry->product->name }}">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">
                                    @if($inquiry->product_id && $inquiry->product)
                                        {{ $inquiry->product->name }}
                                    @else
                                        {{ $inquiry->product_name }}
                                    @endif
                                </h4>
                                @if($inquiry->product_description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $inquiry->product_description }}</p>
                                @endif
                                
                                <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <span class="font-medium">Quantity:</span> {{ number_format($inquiry->quantity) }} units
                                    </div>
                                    @if($inquiry->product_category)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            <span class="font-medium">Category:</span> {{ $inquiry->product_category }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        @if($inquiry->requirements)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-blue-800 mb-2">Customer Requirements:</h5>
                                <p class="text-sm text-blue-700">{{ $inquiry->requirements }}</p>
                            </div>
                        @endif

                        <!-- Product Specifications -->
                        @if($inquiry->product_specifications)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-800 mb-2">Product Specifications:</h5>
                                <div class="text-sm text-gray-700">
                                    @if(is_array($inquiry->product_specifications))
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($inquiry->product_specifications as $spec)
                                                <li>{{ $spec }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>{{ $inquiry->product_specifications }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quotation Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Your Quotation
                        @if($existingQuotation)
                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Update Existing
                            </span>
                        @endif
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('supplier.inquiries.quotation.store', $inquiry) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Unit Price -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="unit_price" class="block text-sm font-medium text-gray-700">Unit Price</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <div class="relative flex-grow">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm" id="unit-currency-symbol">{{ old('currency', $existingQuotation->currency ?? 'AED') }}</span>
                                            </div>
                                            <input type="number" 
                                                   name="unit_price" 
                                                   id="unit_price" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required
                                                   value="{{ old('unit_price', $existingQuotation->unit_price ?? '') }}"
                                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-l-md"
                                                   placeholder="0.00"
                                                   onchange="calculateTotal()">
                                        </div>
                                        <select name="currency" 
                                                id="currency" 
                                                class="focus:ring-indigo-500 focus:border-indigo-500 -ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none"
                                                onchange="updateCurrencySymbols(); calculateTotal();">
                                            <option value="AED" {{ old('currency', $existingQuotation->currency ?? 'AED') == 'AED' ? 'selected' : '' }}>AED</option>
                                            <option value="USD" {{ old('currency', $existingQuotation->currency ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('currency', $existingQuotation->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ old('currency', $existingQuotation->currency ?? '') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                            <option value="SAR" {{ old('currency', $existingQuotation->currency ?? '') == 'SAR' ? 'selected' : '' }}>SAR</option>
                                            <option value="QAR" {{ old('currency', $existingQuotation->currency ?? '') == 'QAR' ? 'selected' : '' }}>QAR</option>
                                            <option value="KWD" {{ old('currency', $existingQuotation->currency ?? '') == 'KWD' ? 'selected' : '' }}>KWD</option>
                                            <option value="BHD" {{ old('currency', $existingQuotation->currency ?? '') == 'BHD' ? 'selected' : '' }}>BHD</option>
                                            <option value="OMR" {{ old('currency', $existingQuotation->currency ?? '') == 'OMR' ? 'selected' : '' }}>OMR</option>
                                        </select>
                                    </div>
                                    @error('unit_price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Total Amount (Auto-calculated) -->
                                <div>
                                    <label for="total_amount_display" class="block text-sm font-medium text-gray-700">Total Amount</label>
                                    <div class="mt-1">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm" id="total-currency-symbol">{{ old('currency', $existingQuotation->currency ?? 'AED') }}</span>
                                            </div>
                                            <input type="text" 
                                                   id="total_amount_display" 
                                                   readonly
                                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-3 sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                                   placeholder="0.00">
                                            <input type="hidden" name="total_amount" id="total_amount" value="{{ old('total_amount', $existingQuotation ? ($existingQuotation->unit_price * $inquiry->quantity) : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Cost -->
                            <div>
                                <label for="shipping_cost" class="block text-sm font-medium text-gray-700">Shipping Cost (Optional)</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <div class="relative flex-grow">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm" id="shipping-currency-symbol">{{ old('currency', $existingQuotation->currency ?? 'AED') }}</span>
                                        </div>
                                        <input type="number" 
                                               name="shipping_cost" 
                                               id="shipping_cost" 
                                               step="0.01" 
                                               min="0" 
                                               value="{{ old('shipping_cost', $existingQuotation->shipping_cost ?? '') }}"
                                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-l-md"
                                               placeholder="0.00"
                                               onchange="calculateTotal()">
                                    </div>
                                    <div class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50">
                                        <span id="shipping-currency-display">{{ old('currency', $existingQuotation->currency ?? 'AED') }}</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Enter shipping cost if applicable. This will be added to the total amount.</p>
                                @error('shipping_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product Condition/Size -->
                            <div>
                                <label for="size" class="block text-sm font-medium text-gray-700">Product Condition/Size (Optional)</label>
                                <div class="mt-1">
                                    <input type="text" 
                                           name="size" 
                                           id="size" 
                                           value="{{ old('size', $existingQuotation->size ?? '') }}"
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           placeholder="e.g., New, Refurbished, 100ml, Large, etc.">
                                </div>
                                @error('size')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes & Terms</label>
                                <div class="mt-1">
                                    <textarea id="notes" name="notes" rows="4"
                                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Add any additional notes, terms, or conditions for your quotation...">{{ old('notes', $existingQuotation->notes ?? '') }}</textarea>
                                </div>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
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
                                    {{ $existingQuotation ? 'Update Quotation' : 'Submit Quotation' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Inquiry Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
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

                    <!-- Quotation Guidelines -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Quotation Guidelines</h4>
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex items-start">
                                <svg class="w-3 h-3 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Provide competitive pricing
                            </div>
                            <div class="flex items-start">
                                <svg class="w-3 h-3 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Include delivery timeframe
                            </div>
                            <div class="flex items-start">
                                <svg class="w-3 h-3 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Specify product condition
                            </div>
                            <div class="flex items-start">
                                <svg class="w-3 h-3 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Add warranty information
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateCurrencySymbols() {
    const currency = document.getElementById('currency').value;
    document.getElementById('unit-currency-symbol').textContent = currency;
    document.getElementById('total-currency-symbol').textContent = currency;
    document.getElementById('shipping-currency-symbol').textContent = currency;
    document.getElementById('shipping-currency-display').textContent = currency;
}

function calculateTotal() {
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const quantity = {{ $inquiry->quantity }};
    
    const subtotal = unitPrice * quantity;
    const total = subtotal + shippingCost;
    
    document.getElementById('total_amount_display').value = total.toFixed(2);
    document.getElementById('total_amount').value = total.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCurrencySymbols();
    calculateTotal();
});
</script>
@endsection 