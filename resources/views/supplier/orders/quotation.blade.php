@extends('supplier.layouts.app')

@section('title', 'Submit Quotation - Order ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('supplier.orders.show', $order) }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Submit Quotation</h1>
                </div>
                <p class="text-gray-600 mt-2">Order #{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Items to Quote ({{ $order->items->count() }})
                    </h3>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($item->product && $item->product->primaryImage)
                                                <img class="h-10 w-10 rounded-lg object-cover mr-4" src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                                </div>
                                                @if($item->product && $item->product->description)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($item->product->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->quantity }} units
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($item->variation)
                                            {{ $item->variation }}
                                        @else
                                            <span class="text-gray-400">Standard</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('supplier.orders.submit-quotation', $order) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Total Amount -->
                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <div class="relative flex-grow">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm" id="currency-symbol">{{ old('currency', 'AED') }}</span>
                                        </div>
                                        <input type="number" 
                                               name="total_amount" 
                                               id="total_amount" 
                                               step="0.01" 
                                               min="0" 
                                               required
                                               value="{{ old('total_amount') }}"
                                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-l-md"
                                               placeholder="0.00">
                                    </div>
                                    <select name="currency" 
                                            id="currency" 
                                            class="focus:ring-indigo-500 focus:border-indigo-500 -ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none"
                                            onchange="document.getElementById('currency-symbol').textContent = this.value">
                                                                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED</option>
                                <option value="CNY" {{ old('currency') == 'CNY' ? 'selected' : '' }}>CNY</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                                        <option value="QAR" {{ old('currency') == 'QAR' ? 'selected' : '' }}>QAR</option>
                                        <option value="KWD" {{ old('currency') == 'KWD' ? 'selected' : '' }}>KWD</option>
                                        <option value="BHD" {{ old('currency') == 'BHD' ? 'selected' : '' }}>BHD</option>
                                        <option value="OMR" {{ old('currency') == 'OMR' ? 'selected' : '' }}>OMR</option>
                                    </select>
                                </div>
                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Shipping Cost -->
                            <div>
                                <label for="shipping_cost" class="block text-sm font-medium text-gray-700">Shipping Cost (Optional)</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <div class="relative flex-grow">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm" id="shipping-currency-symbol">{{ old('currency', 'AED') }}</span>
                                        </div>
                                        <input type="number" 
                                               name="shipping_cost" 
                                               id="shipping_cost" 
                                               step="0.01" 
                                               min="0" 
                                               value="{{ old('shipping_cost') }}"
                                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-l-md"
                                               placeholder="0.00">
                                    </div>
                                    <div class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50">
                                        <span id="shipping-currency-display">{{ old('currency', 'AED') }}</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Enter shipping cost if applicable. This will be added to the total amount.</p>
                                @error('shipping_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes & Terms</label>
                                <div class="mt-1">
                                    <textarea id="notes" name="notes" rows="4"
                                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Add any additional notes, terms, or conditions for your quotation..."></textarea>
                                </div>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
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

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Important Notes
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Before submitting your quotation:</p>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li>Review all items and quantities carefully</li>
                                    <li>Include all costs (shipping, taxes, etc.) in total amount</li>
                                    <li>Specify any terms or conditions in the notes</li>
                                    <li>Double-check your pricing</li>
                                    <li>Once submitted, quotations cannot be modified</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency');
    const shippingCurrencySymbol = document.getElementById('shipping-currency-symbol');
    const shippingCurrencyDisplay = document.getElementById('shipping-currency-display');
    
    function updateShippingCurrency() {
        const selectedCurrency = currencySelect.value;
        shippingCurrencySymbol.textContent = selectedCurrency;
        shippingCurrencyDisplay.textContent = selectedCurrency;
    }
    
    // Update on page load
    updateShippingCurrency();
    
    // Update when currency changes
    currencySelect.addEventListener('change', updateShippingCurrency);
});
</script>
@endsection 