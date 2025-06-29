@extends('layouts.guest')

@section('content')
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Errors:</strong>
            <ul class="mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Register as Supplier</h2>
        <p class="text-gray-600 mb-4">Are you a customer? <a href="{{ route('register') }}" class="text-[#0064a8] hover:text-[#0052a3] font-semibold">Register as Customer instead</a></p>
    </div>

    <form method="POST" action="{{ route('supplier.register.store') }}" id="supplierRegisterForm">
        @csrf
        @if($invitation)
            <input type="hidden" name="token" value="{{ $invitation->token }}">
        @endif

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Contact Person Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            @if($invitation)
                <x-text-input id="email" 
                    class="block mt-1 w-full bg-gray-100 text-gray-600 cursor-not-allowed" 
                    type="email" 
                    name="email" 
                    :value="old('email', $invitation->email)" 
                    required 
                    autocomplete="username" 
                    readonly />
            @else
                <x-text-input id="email" 
                    class="block mt-1 w-full" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username" />
            @endif
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Company Name -->
        <div class="mt-4">
            <x-input-label for="company_name" :value="__('Company Name')" />
            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Company Address -->
        <div class="mt-4">
            <x-input-label for="business_address" :value="__('Company Address')" />
            <x-text-input id="business_address" class="block mt-1 w-full" type="text" name="business_address" :value="old('business_address')" required />
            <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone_primary" :value="__('Phone Number')" />
            <x-text-input id="phone_primary" class="block mt-1 w-full" type="tel" name="phone_primary" :value="old('phone_primary')" required />
            <x-input-error :messages="$errors->get('phone_primary')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- reCAPTCHA v2 - Only show in production --}}
        @if(app()->environment('production'))
        <div class="mt-4">
           <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
           <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
        </div>
        @else
        {{-- Hidden field for development environment --}}
        <input type="hidden" name="g-recaptcha-response" value="dev-bypass">
        @endif

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4" id="submitBtn">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.getElementById('supplierRegisterForm').addEventListener('submit', function(e) {
            console.log('Form submitted');
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Registering...';
        });
    </script>
    @endpush
@endsection

{{-- Add reCAPTCHA script only in production --}}
@if(app()->environment('production'))
@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
@endif 