<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Verify Your Email</h2>
        <p class="text-gray-600">We've sent a verification link to your email address</p>
    </div>

    <div class="mb-4 text-sm text-gray-600 text-center">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded-md p-3">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    @if (session('message'))
        <div class="mb-4 font-medium text-sm text-blue-600 bg-blue-50 border border-blue-200 rounded-md p-3">
            {{ session('message') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col space-y-4">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full">
            @csrf
            <x-primary-button class="w-full justify-center">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <x-secondary-button type="submit" class="w-full justify-center">
                {{ __('Log Out') }}
            </x-secondary-button>
        </form>
    </div>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Check your spam folder if you don't see the email in your inbox.
        </p>
    </div>
</x-guest-layout>
