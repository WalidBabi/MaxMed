@extends('admin.layouts.app')

@section('title', 'Create Delivery')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Delivery</h1>
                <p class="text-gray-600 mt-2">Create a new delivery for an order</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.deliveries.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Deliveries
                </a>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <form action="{{ route('admin.deliveries.store') }}" method="POST" class="max-w-4xl mx-auto">
        @csrf
        
        <div class="space-y-8">
            <!-- Order & Status Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Order & Status Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Order Selection -->
                        <div>
                            <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">Order <span class="text-red-500">*</span></label>
                            <select id="order_id" name="order_id" required {{ $selectedOrder ? 'readonly' : '' }}
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('order_id') ring-red-500 focus:ring-red-500 @enderror">
                                <option value="">Select an order</option>
                                @foreach($orders as $id => $label)
                                    <option value="{{ $id }}" {{ (old('order_id', $selectedOrder?->id) == $id) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if($selectedOrder)
                                <p class="mt-1 text-sm text-gray-500">Pre-selected from invoice</p>
                            @endif
                            @error('order_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('status') ring-red-500 focus:ring-red-500 @enderror">
                                @foreach(\App\Models\Delivery::$statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', 'pending') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Shipping Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Carrier -->
                        <div>
                            <label for="carrier" class="block text-sm font-medium text-gray-700 mb-2">Carrier <span class="text-red-500">*</span></label>
                            <input type="text" id="carrier" name="carrier" value="{{ old('carrier') }}" required
                                   placeholder="Enter carrier name"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('carrier') ring-red-500 focus:ring-red-500 @enderror">
                            @error('carrier')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tracking Number -->
                        <div>
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                            <input type="text" id="tracking_number" name="tracking_number" value="{{ old('tracking_number') }}"
                                   placeholder="Enter tracking number"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('tracking_number') ring-red-500 focus:ring-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Leave blank to generate automatically</p>
                            @error('tracking_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shipping Cost -->
                        <div>
                            <label for="shipping_cost" class="block text-sm font-medium text-gray-700 mb-2">Shipping Cost <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" min="0" id="shipping_cost" name="shipping_cost" 
                                       value="{{ old('shipping_cost', '0.00') }}" required
                                       class="block w-full pl-7 pr-3 py-1.5 border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 rounded-md @error('shipping_cost') ring-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('shipping_cost')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Weight -->
                        <div>
                            <label for="total_weight" class="block text-sm font-medium text-gray-700 mb-2">Total Weight (kg)</label>
                            <input type="number" step="0.01" min="0" id="total_weight" name="total_weight" 
                                   value="{{ old('total_weight') }}"
                                   placeholder="Enter weight in kg"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('total_weight') ring-red-500 focus:ring-red-500 @enderror">
                            @error('total_weight')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Notes
                    </h3>
                </div>
                <div class="p-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes</label>
                        <textarea id="notes" name="notes" rows="4"
                                  placeholder="Enter any additional notes about this delivery..."
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('notes') ring-red-500 focus:ring-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6">
                <a href="{{ route('admin.deliveries.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">Cancel</a>
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                    </svg>
                    Create Delivery
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Auto-select order if passed in URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');
        if (orderId) {
            document.getElementById('order_id').value = orderId;
        }
    });
</script>
@endpush
