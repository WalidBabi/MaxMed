@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 text-red-500">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                500 - Internal Server Error
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                We're experiencing technical difficulties. Our team has been notified and is working to resolve the issue.
            </p>
        </div>

        <div class="mt-8 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">For Administrators:</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Check the following log files for detailed error information:
                </p>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• storage/logs/laravel.log</li>
                    <li>• storage/logs/critical-errors.log</li>
                    <li>• storage/logs/production-debug.log</li>
                </ul>
            </div>

            <div class="flex space-x-4">
                <a href="{{ route('welcome') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-center transition duration-150 ease-in-out">
                    Go Home
                </a>
                <a href="{{ route('login') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md text-center transition duration-150 ease-in-out">
                    Try Login Again
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 