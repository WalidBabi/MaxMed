@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="relative bg-white py-16">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-indigo-50 opacity-70"></div>
        <svg class="absolute bottom-0 left-0 w-full text-white" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="currentColor" fill-opacity="1" d="M0,256L80,234.7C160,213,320,171,480,160C640,149,800,171,960,170.7C1120,171,1280,149,1360,138.7L1440,128L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center">
            <h1 class="text-4xl tracking-tight text-[#171e60] sm:text-5xl md:text-6xl font-bold">
                Our Trusted Partners
            </h1>
        </div>
    </div>
</div>

<!-- Suppliers Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Supplier 1 -->
            <div class="flex items-center justify-center p-8 bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300">
                <img src="{{ asset('images/partners/Ringbiologo.png') }}" alt="Partner 1" class="max-h-40 w-auto">
            </div>

            <!-- Supplier 2 -->
            <div class="flex items-center justify-center p-8 bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300">
                <img src="{{ asset('images/partners/Dlablogo.svg') }}" alt="HealthTech Solutions" class="max-h-40 w-auto">
            </div>

            <!-- Supplier 3 -->
            <div class="flex items-center justify-center p-8 bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300">
                <img src="{{ asset('images/partners/Biobaselogo.png') }}" alt="Global Medical Supply" class="max-h-40 w-auto">
            </div>
        </div>
    </div>
</div>
@endsection

