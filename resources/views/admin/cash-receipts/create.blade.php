@extends('admin.layouts.app')

@section('title', 'Create Cash Receipt')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Cash Receipt</h1>
                <p class="text-gray-600 mt-2">Generate a new cash receipt for customer payment</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.cash-receipts.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.cash-receipts.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Customer Information Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Selection -->
                    <div class="md:col-span-2">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                        <select name="customer_id" id="customer_id" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_id') border-red-300 @enderror">
                            <option value="">Select a customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                        data-name="{{ $customer->name }}"
                                        data-email="{{ $customer->email }}"
                                        data-phone="{{ $customer->phone }}"
                                        data-address="{{ $customer->address }}, {{ $customer->city }}, {{ $customer->state }}, {{ $customer->country }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} - {{ $customer->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name" 
                               value="{{ old('customer_name') }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_name') border-red-300 @enderror">
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">Customer Email</label>
                        <input type="email" name="customer_email" id="customer_email" 
                               value="{{ old('customer_email') }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_email') border-red-300 @enderror">
                        @error('customer_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customer Phone -->
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">Customer Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" 
                               value="{{ old('customer_phone') }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_phone') border-red-300 @enderror">
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customer Address -->
                    <div>
                        <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">Customer Address</label>
                        <textarea name="customer_address" id="customer_address" rows="3" 
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('customer_address') border-red-300 @enderror">{{ old('customer_address') }}</textarea>
                        @error('customer_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Order Selection -->
                    <div class="md:col-span-2">
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">Related Order (Optional)</label>
                        <select name="order_id" id="order_id" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('order_id') border-red-300 @enderror">
                            <option value="">Select an order (optional)</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" 
                                        data-amount="{{ $order->total_amount }}"
                                        data-currency="{{ $order->currency ?? 'AED' }}"
                                        {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                    {{ $order->order_number }} - {{ $order->customer->name ?? 'N/A' }} - {{ number_format($order->total_amount, 2) }} {{ $order->currency ?? 'AED' }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Receipt Date -->
                    <div>
                        <label for="receipt_date" class="block text-sm font-medium text-gray-700 mb-2">Receipt Date</label>
                        <input type="date" name="receipt_date" id="receipt_date" 
                               value="{{ old('receipt_date', date('Y-m-d')) }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('receipt_date') border-red-300 @enderror">
                        @error('receipt_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" id="payment_method" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_method') border-red-300 @enderror">
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" 
                               value="{{ old('amount') }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('amount') border-red-300 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" id="currency" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('currency') border-red-300 @enderror">
                            <option value="AED" {{ old('currency', 'AED') == 'AED' ? 'selected' : '' }}>AED</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  placeholder="Payment description or notes..."
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number (Optional)</label>
                        <input type="text" name="reference_number" id="reference_number" 
                               value="{{ old('reference_number') }}" 
                               placeholder="Check number, transaction ID, etc."
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('reference_number') border-red-300 @enderror">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.cash-receipts.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Cash Receipt
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const orderSelect = document.getElementById('order_id');
    const customerNameInput = document.getElementById('customer_name');
    const customerEmailInput = document.getElementById('customer_email');
    const customerPhoneInput = document.getElementById('customer_phone');
    const customerAddressInput = document.getElementById('customer_address');
    const amountInput = document.getElementById('amount');
    const currencySelect = document.getElementById('currency');
    const form = document.querySelector('form');

    // Add form submission debugging
    form.addEventListener('submit', function(e) {
        console.log('Form submission started');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Log form data
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Basic validation
        const customerId = document.getElementById('customer_id').value;
        const amount = document.getElementById('amount').value;
        const receiptDate = document.getElementById('receipt_date').value;
        
        if (!customerId) {
            e.preventDefault();
            alert('Please select a customer');
            return false;
        }
        
        if (!amount || amount <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount');
            return false;
        }
        
        if (!receiptDate) {
            e.preventDefault();
            alert('Please select a receipt date');
            return false;
        }
        
        console.log('Form validation passed, submitting...');
    });

    // Auto-fill customer details when customer is selected
    customerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            customerNameInput.value = selectedOption.dataset.name || '';
            customerEmailInput.value = selectedOption.dataset.email || '';
            customerPhoneInput.value = selectedOption.dataset.phone || '';
            customerAddressInput.value = selectedOption.dataset.address || '';
        } else {
            customerNameInput.value = '';
            customerEmailInput.value = '';
            customerPhoneInput.value = '';
            customerAddressInput.value = '';
        }
    });

    // Auto-fill amount when order is selected
    orderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            amountInput.value = selectedOption.dataset.amount || '';
            currencySelect.value = selectedOption.dataset.currency || 'AED';
        }
    });
});
</script>
@endsection 