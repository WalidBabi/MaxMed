@extends('layouts.app')

@section('title', 'About MaxMed UAE - Leading Laboratory Equipment Supplier in Dubai')

@section('content')
    <!-- Hero Section -->
    <div class="about-hero h-[400px] flex items-center justify-center text-white" style="background-image: url('{{ asset('Images/maxmed building.png') }}'); background-size: cover; background-position: center;">
        <div class="text-center">
            <h2 class="text-4xl md:text-6xl font-bold mb-4">About MaxMed</h2>
            <p class="text-xl">Leading the Future of Medical Laboratory Equipment</p>
        </div>
    </div>

    <!-- Our Story Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-3xl font-bold mb-6">Our Story</h3>
                <p class="text-gray-600 mb-4">
                    Founded in 2010, MaxMed has been at the forefront of medical laboratory innovation in the UAE.
                    What started as a small distribution company has grown into one of the region's leading providers
                    of cutting-edge laboratory equipment.
                </p>
                <p class="text-gray-600">
                    Our commitment to quality, innovation, and customer service has helped us build lasting
                    relationships with healthcare facilities across the Middle East.
                </p>
            </div>
            <div class="rounded-lg overflow-hidden shadow-lg">
                <img src="https://images.unsplash.com/photo-1581093458791-9f3c3900df4b?ixlib=rb-1.2.1"
                    alt="Laboratory Equipment"
                    class="w-full h-[400px] object-cover">
            </div>
        </div>
    </div>

    <!-- Values Section -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12">Our Core Values</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="text-[#171e60] mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-4">Innovation</h4>
                    <p class="text-gray-600">Continuously pushing boundaries to provide the latest technological advancements in laboratory equipment.</p>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="text-[#171e60] mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-4">Quality</h4>
                    <p class="text-gray-600">Ensuring the highest standards in every piece of equipment we provide to our clients.</p>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="text-[#171e60] mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-4">Customer Support</h4>
                    <p class="text-gray-600">Dedicated to providing exceptional service and support to our clients at every step.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <h3 class="text-3xl font-bold text-center mb-12">Our Leadership Team</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="mb-4">
                    <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-1.2.1"
                        alt="CEO"
                        class="w-48 h-48 rounded-full mx-auto object-cover">
                </div>
                <h4 class="text-xl font-bold text-[#171e60]">Dr. Ahmed Hassan</h4>
                <p class="text-gray-600">Chief Executive Officer</p>
            </div>
            <div class="text-center">
                <div class="mb-4">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1"
                        alt="COO"
                        class="w-48 h-48 rounded-full mx-auto object-cover">
                </div>
                <h4 class="text-xl font-bold">Sarah Thompson</h4>
                <p class="text-gray-600">Chief Operations Officer</p>
            </div>
            <div class="text-center">
                <div class="mb-4">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1"
                        alt="CTO"
                        class="w-48 h-48 rounded-full mx-auto object-cover">
                </div>
                <h4 class="text-xl font-bold">Dr. Michael Chen</h4>
                <p class="text-gray-600">Chief Technical Officer</p>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection