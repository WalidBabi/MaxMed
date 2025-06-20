@extends('admin.layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Purchase Order</h1>
                <p class="text-gray-600 mt-2">Create a new purchase order to send to suppliers</p>
            </div>
            <div>
                <a href="{{ route('admin.purchase-orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Purchase Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" class="space-y-6 p-6">
            @csrf
            
            <!-- Order Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">Customer Order *</label>
                    <select id="order_id" name="order_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select a customer order</option>
                        @foreach($availableOrders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                {{ $order->order_number }} - {{ $order->getCustomerName() }} ({{ $order->currency }} {{ number_format($order->total_amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="po_date" class="block text-sm font-medium text-gray-700 mb-2">PO Date *</label>
                    <input type="date" id="po_date" name="po_date" value="{{ old('po_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('po_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Supplier Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-2">Supplier Name *</label>
                        <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name', 'MaxMed Supplier') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('supplier_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="supplier_email" class="block text-sm font-medium text-gray-700 mb-2">Supplier Email</label>
                        <input type="email" id="supplier_email" name="supplier_email" value="{{ old('supplier_email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('supplier_email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="supplier_phone" class="block text-sm font-medium text-gray-700 mb-2">Supplier Phone</label>
                        <input type="text" id="supplier_phone" name="supplier_phone" value="{{ old('supplier_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('supplier_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_date_requested" class="block text-sm font-medium text-gray-700 mb-2">Requested Delivery Date</label>
                        <input type="date" id="delivery_date_requested" name="delivery_date_requested" value="{{ old('delivery_date_requested') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('delivery_date_requested')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="supplier_address" class="block text-sm font-medium text-gray-700 mb-2">Supplier Address</label>
                    <textarea id="supplier_address" name="supplier_address" rows="3"
                              placeholder="Enter supplier address"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('supplier_address') }}</textarea>
                    @error('supplier_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- PO Details -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Order Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency *</label>
                        <select id="currency" name="currency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                        </select>
                        @error('currency')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sub_total" class="block text-sm font-medium text-gray-700 mb-2">Subtotal *</label>
                        <input type="number" id="sub_total" name="sub_total" value="{{ old('sub_total') }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('sub_total')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-2">Tax Amount</label>
                        <input type="number" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('tax_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shipping_cost" class="block text-sm font-medium text-gray-700 mb-2">Shipping Cost</label>
                        <input type="number" id="shipping_cost" name="shipping_cost" value="{{ old('shipping_cost', 0) }}" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('shipping_cost')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount *</label>
                        <input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('total_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Describe the items to be purchased"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                        <textarea id="terms_conditions" name="terms_conditions" rows="3"
                                  placeholder="Payment terms, delivery conditions, etc."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('terms_conditions') }}</textarea>
                        @error('terms_conditions')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  placeholder="Internal notes (not visible to supplier)"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.purchase-orders.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="draft"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="create"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Purchase Order
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-calculate total amount
document.addEventListener('DOMContentLoaded', function() {
    const subTotal = document.getElementById('sub_total');
    const taxAmount = document.getElementById('tax_amount');
    const shippingCost = document.getElementById('shipping_cost');
    const totalAmount = document.getElementById('total_amount');

    function calculateTotal() {
        const sub = parseFloat(subTotal.value) || 0;
        const tax = parseFloat(taxAmount.value) || 0;
        const shipping = parseFloat(shippingCost.value) || 0;
        
        totalAmount.value = (sub + tax + shipping).toFixed(2);
    }

    subTotal.addEventListener('input', calculateTotal);
    taxAmount.addEventListener('input', calculateTotal);
    shippingCost.addEventListener('input', calculateTotal);
});
</script>
@endsection 