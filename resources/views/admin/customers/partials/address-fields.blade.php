@php
    $oldValues = [];
    if (isset($customer)) {
        $oldValues = [
            'street' => old($prefix . 'street', $customer->{$prefix . 'street'}),
            'city' => old($prefix . 'city', $customer->{$prefix . 'city'}),
            'state' => old($prefix . 'state', $customer->{$prefix . 'state'}),
            'zip' => old($prefix . 'zip', $customer->{$prefix . 'zip'}),
            'country' => old($prefix . 'country', $customer->{$prefix . 'country'}),
        ];
    } else {
        $oldValues = [
            'street' => old($prefix . 'street'),
            'city' => old($prefix . 'city'),
            'state' => old($prefix . 'state'),
            'zip' => old($prefix . 'zip'),
            'country' => old($prefix . 'country'),
        ];
    }
    
    $label = ucfirst(str_replace('_', ' ', substr($prefix, 0, -1)));
@endphp

<div class="space-y-6">
    <!-- Street Address -->
    <div>
        <label for="{{ $prefix }}street" class="block text-sm font-medium leading-6 text-gray-900">Street Address</label>
        <div class="mt-2">
            <input type="text" id="{{ $prefix }}street" name="{{ $prefix }}street" 
                   value="{{ $oldValues['street'] }}"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error($prefix . 'street') ring-red-500 focus:ring-red-500 @enderror"
                   placeholder="Enter street address">
            @error($prefix . 'street')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- City, State, ZIP Row -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- City -->
        <div class="sm:col-span-2">
            <label for="{{ $prefix }}city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
            <div class="mt-2">
                <input type="text" id="{{ $prefix }}city" name="{{ $prefix }}city" 
                       value="{{ $oldValues['city'] }}"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error($prefix . 'city') ring-red-500 focus:ring-red-500 @enderror"
                       placeholder="Enter city">
                @error($prefix . 'city')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- ZIP/Postal Code -->
        <div>
            <label for="{{ $prefix }}zip" class="block text-sm font-medium leading-6 text-gray-900">ZIP/Postal Code</label>
            <div class="mt-2">
                <input type="text" id="{{ $prefix }}zip" name="{{ $prefix }}zip" 
                       value="{{ $oldValues['zip'] }}"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error($prefix . 'zip') ring-red-500 focus:ring-red-500 @enderror"
                       placeholder="Enter ZIP code">
                @error($prefix . 'zip')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- State and Country Row -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <!-- State/Province -->
        <div>
            <label for="{{ $prefix }}state" class="block text-sm font-medium leading-6 text-gray-900">State/Province</label>
            <div class="mt-2">
                <input type="text" id="{{ $prefix }}state" name="{{ $prefix }}state" 
                       value="{{ $oldValues['state'] }}"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error($prefix . 'state') ring-red-500 focus:ring-red-500 @enderror"
                       placeholder="Enter state or province">
                @error($prefix . 'state')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Country -->
        <div>
            <label for="{{ $prefix }}country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
            <div class="mt-2">
                <select id="{{ $prefix }}country" name="{{ $prefix }}country"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error($prefix . 'country') ring-red-500 focus:ring-red-500 @enderror">
                    <option value="">Select a country...</option>
                    @foreach([
                        'United Arab Emirates' => 'United Arab Emirates',
                        'Saudi Arabia' => 'Saudi Arabia',
                        'Qatar' => 'Qatar',
                        'Kuwait' => 'Kuwait',
                        'Oman' => 'Oman',
                        'Bahrain' => 'Bahrain',
                        'Other' => 'Other',
                    ] as $value => $label)
                        <option value="{{ $value }}" {{ $oldValues['country'] == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error($prefix . 'country')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
