<!-- Street Address -->
<div class="mb-4">
    <label for="{{ $prefix }}street" class="block text-sm font-medium leading-6 text-gray-900">Street Address</label>
    <div class="mt-2">
        <input type="text" name="{{ $prefix }}street" id="{{ $prefix }}street" 
               value="{{ old($prefix.'street', isset($customer) ? $customer->{$prefix.'street'} : '') }}"
               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Enter street address">
    </div>
</div>

<!-- City -->
<div class="mb-4">
    <label for="{{ $prefix }}city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
    <div class="mt-2">
        <input type="text" name="{{ $prefix }}city" id="{{ $prefix }}city" 
               value="{{ old($prefix.'city', isset($customer) ? $customer->{$prefix.'city'} : '') }}"
               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Enter city">
    </div>
</div>

<!-- State -->
<div class="mb-4">
    <label for="{{ $prefix }}state" class="block text-sm font-medium leading-6 text-gray-900">State/Province</label>
    <div class="mt-2">
        <input type="text" name="{{ $prefix }}state" id="{{ $prefix }}state" 
               value="{{ old($prefix.'state', isset($customer) ? $customer->{$prefix.'state'} : '') }}"
               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Enter state/province">
    </div>
</div>

<!-- ZIP Code -->
<div class="mb-4">
    <label for="{{ $prefix }}zip" class="block text-sm font-medium leading-6 text-gray-900">ZIP/Postal Code</label>
    <div class="mt-2">
        <input type="text" name="{{ $prefix }}zip" id="{{ $prefix }}zip" 
               value="{{ old($prefix.'zip', isset($customer) ? $customer->{$prefix.'zip'} : '') }}"
               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Enter ZIP/postal code">
    </div>
</div>

<!-- Country -->
<div class="mb-4">
    <label for="{{ $prefix }}country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
    <div class="mt-2">
        <input type="text" name="{{ $prefix }}country" id="{{ $prefix }}country" 
               value="{{ old($prefix.'country', isset($customer) ? $customer->{$prefix.'country'} : '') }}"
               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Enter country">
    </div>
</div> 