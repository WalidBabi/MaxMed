@extends('supplier.layouts.app')

@section('title', 'Submit Quotation - ' . $inquiry->reference_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <p class="text-gray-600 mt-2">Reference #{{ $inquiry->reference_number }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Product Information</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Quotation Details</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('supplier.inquiries.quotation.store', $inquiry->id) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Unit Price -->
                            <div>
                                <label for="unit_price" class="block text-sm font-medium text-gray-700">Unit Price</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <div class="relative flex items-stretch flex-grow focus-within:z-10">
                                        <input type="number" step="0.01" name="unit_price" id="unit_price" 
                                               class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                               placeholder="0.00" required>
                                    </div>
                                    <select name="currency" class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="AED">AED</option>
                                        <option value="AED">CNY</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Shipping Cost -->
                            <div>
                                <label for="shipping_cost" class="block text-sm font-medium text-gray-700">Shipping Cost (Optional)</label>
                                <div class="mt-1">
                                    <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" 
                                           class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           placeholder="0.00">
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

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('supplier.inquiries.show', $inquiry->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
            <!-- Inquiry Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Inquiry Details</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
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
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 