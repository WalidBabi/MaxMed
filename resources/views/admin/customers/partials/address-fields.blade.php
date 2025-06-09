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

<div class="mb-3">
    <label for="{{ $prefix }}street" class="form-label">Street Address</label>
    <input type="text" class="form-control @error($prefix . 'street') is-invalid @enderror" 
           id="{{ $prefix }}street" name="{{ $prefix }}street" 
           value="{{ $oldValues['street'] }}">
    @error($prefix . 'street')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="{{ $prefix }}city" class="form-label">City</label>
            <input type="text" class="form-control @error($prefix . 'city') is-invalid @enderror" 
                   id="{{ $prefix }}city" name="{{ $prefix }}city" 
                   value="{{ $oldValues['city'] }}">
            @error($prefix . 'city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="{{ $prefix }}state" class="form-label">State/Province</label>
            <input type="text" class="form-control @error($prefix . 'state') is-invalid @enderror" 
                   id="{{ $prefix }}state" name="{{ $prefix }}state" 
                   value="{{ $oldValues['state'] }}">
            @error($prefix . 'state')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="{{ $prefix }}zip" class="form-label">ZIP/Postal Code</label>
            <input type="text" class="form-control @error($prefix . 'zip') is-invalid @enderror" 
                   id="{{ $prefix }}zip" name="{{ $prefix }}zip" 
                   value="{{ $oldValues['zip'] }}">
            @error($prefix . 'zip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="{{ $prefix }}country" class="form-label">Country</label>
    <select class="form-select @error($prefix . 'country') is-invalid @enderror" 
            id="{{ $prefix }}country" name="{{ $prefix }}country">
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
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
