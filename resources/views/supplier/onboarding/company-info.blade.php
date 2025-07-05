@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-12">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                <span class="ml-2 font-medium text-green-500">Company Info</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold">2</div>
                <span class="ml-2 text-gray-600">Documents</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold">3</div>
                <span class="ml-2 text-gray-600">Categories</span>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-5">Company Information</h2>
            
            <form action="{{ route('supplier.onboarding.company') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Company Name -->
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="company_name" id="company_name" 
                               value="{{ old('company_name', $supplierInfo->company_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand Name -->
                    <div class="sm:col-span-2">
                        <label for="brand_name" class="block text-sm font-medium text-gray-700">Brand Name</label>
                        <input type="text" name="brand_name" id="brand_name"
                               value="{{ old('brand_name', $supplierInfo->brand ? $supplierInfo->brand->name : '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="text-sm text-gray-500 mt-1">This will be shown as your product brand. Leave blank to use your company name.</p>
                        @error('brand_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand Logo -->
                    <div class="sm:col-span-2">
                        <label for="brand_logo" class="block text-sm font-medium text-gray-700">Brand Logo</label>
                        <div class="flex items-center space-x-4">
                            @if($supplierInfo->brand && $supplierInfo->brand->logo_url)
                                <img src="{{ asset($supplierInfo->brand->logo_url) }}" alt="Brand Logo" class="h-16 w-16 object-contain bg-white border rounded">
                            @endif
                            <input type="file" name="brand_logo" id="brand_logo" accept="image/*" class="block">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">JPG, PNG, GIF, or WEBP. Max size 2MB.</p>
                        @error('brand_logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Registration Numbers -->
                    <div>
                        <label for="business_registration_number" class="block text-sm font-medium text-gray-700">Business Registration Number</label>
                        <input type="text" name="business_registration_number" id="business_registration_number"
                               value="{{ old('business_registration_number', $supplierInfo->business_registration_number ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('business_registration_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_registration_number" class="block text-sm font-medium text-gray-700">Tax Registration Number (Optional)</label>
                        <input type="text" name="tax_registration_number" id="tax_registration_number"
                               value="{{ old('tax_registration_number', $supplierInfo->tax_registration_number ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('tax_registration_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="trade_license_number" class="block text-sm font-medium text-gray-700">Trade License Number</label>
                        <input type="text" name="trade_license_number" id="trade_license_number"
                               value="{{ old('trade_license_number', $supplierInfo->trade_license_number ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('trade_license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Address -->
                    <div class="sm:col-span-2">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">Business Address</label>
                        <textarea name="business_address" id="business_address" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  required>{{ old('business_address', $supplierInfo->business_address ?? '') }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city', $supplierInfo->city ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State/Province -->
                    <div>
                        <label for="state_province" class="block text-sm font-medium text-gray-700">State/Province</label>
                        <input type="text" name="state_province" id="state_province"
                               value="{{ old('state_province', $supplierInfo->state_province ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('state_province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code"
                               value="{{ old('postal_code', $supplierInfo->postal_code ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="country" id="country"
                               value="{{ old('country', $supplierInfo->country ?? 'UAE') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone_primary" class="block text-sm font-medium text-gray-700">Primary Phone</label>
                        <input type="text" name="phone_primary" id="phone_primary"
                               value="{{ old('phone_primary', $supplierInfo->phone_primary ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('phone_primary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="text" name="website" id="website"
                               value="{{ old('website', $supplierInfo->website ?? '') }}"
                               placeholder="e.g., www.yourcompany.com or yourcompany.com"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Years in Business -->
                    <div>
                        <label for="years_in_business" class="block text-sm font-medium text-gray-700">Years in Business</label>
                        <input type="number" name="years_in_business" id="years_in_business"
                               value="{{ old('years_in_business', $supplierInfo->years_in_business ?? '') }}"
                               min="0" step="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('years_in_business')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Description -->
                    <div class="sm:col-span-2">
                        <label for="company_description" class="block text-sm font-medium text-gray-700">Company Description</label>
                        <textarea name="company_description" id="company_description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  required>{{ old('company_description', $supplierInfo->company_description ?? '') }}</textarea>
                        @error('company_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Primary Contact Information</h3>
                    </div>

                    <!-- Primary Contact Name -->
                    <div>
                        <label for="primary_contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                        <input type="text" name="primary_contact_name" id="primary_contact_name"
                               value="{{ old('primary_contact_name', $supplierInfo->primary_contact_name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Contact Position -->
                    <div>
                        <label for="primary_contact_position" class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="primary_contact_position" id="primary_contact_position"
                               value="{{ old('primary_contact_position', $supplierInfo->primary_contact_position ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Contact Email -->
                    <div>
                        <label for="primary_contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="primary_contact_email" id="primary_contact_email"
                               value="{{ old('primary_contact_email', $supplierInfo->primary_contact_email ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primary Contact Phone -->
                    <div>
                        <label for="primary_contact_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="primary_contact_phone" id="primary_contact_phone"
                               value="{{ old('primary_contact_phone', $supplierInfo->primary_contact_phone ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required>
                        @error('primary_contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Continue to Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 