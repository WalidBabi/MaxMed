@extends('admin.layouts.app')

@section('title', 'Add New Customer')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Customer</h1>
                <p class="text-gray-600 mt-2">Create a new customer profile</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Customers
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Basic Information -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Basic Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full Name <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('name') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="Enter full name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('email') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="Enter email address">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Phone</label>
                        <div class="mt-2">
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('phone') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="Enter phone number">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Link to User Account -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium leading-6 text-gray-900">Link to User Account (Optional)</label>
                        <div class="mt-2">
                            <select name="user_id" id="user_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('user_id') ring-red-500 focus:ring-red-500 @enderror">
                                <option value="">Select a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m-3.75-16.5h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                    </svg>
                    Company Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900">Company Name</label>
                        <div class="mt-2">
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('company_name') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="Enter company name">
                            @error('company_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <label for="tax_id" class="block text-sm font-medium leading-6 text-gray-900">Tax ID</label>
                        <div class="mt-2">
                            <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('tax_id') ring-red-500 focus:ring-red-500 @enderror"
                                   placeholder="Enter tax ID">
                            @error('tax_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center justify-center">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="is_active" class="ml-3 text-sm font-medium leading-6 text-gray-900">Active Customer</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    Address Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Billing Address -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-900 mb-4">Billing Address</h4>
                        @include('admin.customers.partials.address-fields', ['prefix' => 'billing_'])
                    </div>

                    <!-- Shipping Address -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-900">Shipping Address</h4>
                            <div class="flex items-center">
                                <input id="same_as_billing" type="checkbox" onchange="copyBillingToShipping()"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="same_as_billing" class="ml-2 text-sm text-gray-600">Same as billing</label>
                            </div>
                        </div>
                        @include('admin.customers.partials.address-fields', ['prefix' => 'shipping_'])
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                    Notes
                </h3>
            </div>
            <div class="p-6">
                <div>
                    <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Customer Notes</label>
                    <div class="mt-2">
                        <textarea name="notes" id="notes" rows="4"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('notes') ring-red-500 focus:ring-red-500 @enderror"
                                  placeholder="Enter any additional notes about this customer...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6">
            <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">Cancel</a>
            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                </svg>
                Save Customer
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        function copyBillingToShipping() {
            const sameAsBilling = document.getElementById('same_as_billing').checked;
            if (sameAsBilling) {
                document.getElementById('shipping_street').value = document.getElementById('billing_street').value;
                document.getElementById('shipping_city').value = document.getElementById('billing_city').value;
                document.getElementById('shipping_state').value = document.getElementById('billing_state').value;
                document.getElementById('shipping_zip').value = document.getElementById('billing_zip').value;
                document.getElementById('shipping_country').value = document.getElementById('billing_country').value;
            }
        }
    </script>
    @endpush
@endsection
