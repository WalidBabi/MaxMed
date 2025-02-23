@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div x-data="{ activeSlide: 0 }" 
         x-init="setInterval(() => { activeSlide = activeSlide === 2 ? 0 : activeSlide + 1 }, 5000)"
         class="relative h-[300px]">
        <!-- Background Images -->
        <div class="absolute inset-0 transition-opacity duration-500" 
             x-show="activeSlide === 0"
             style="background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)), url('/Images/banner.png'); background-size: cover; background-position: center;">
        </div>
        <div class="absolute inset-0 transition-opacity duration-500"
             x-show="activeSlide === 1" 
             style="background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)), url('/Images/banner2.jpeg'); background-size: cover; background-position: center;">
        </div>
        <div class="absolute inset-0 transition-opacity duration-500"
             x-show="activeSlide === 2"
             style="background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)), url('/Images/banner3.jpg'); background-size: cover; background-position: center;">
        </div>

        <!-- Content -->
        <div class="relative h-full flex items-center justify-center text-white">
            <div class="text-center">
                <h2 class="text-4xl md:text-6xl font-bold mb-4 text-white">Laboratory Solutions</h2>
                <p class="text-xl mb-8 text-white">Providing cutting-edge medical Laboratory equipment for modern laboratories</p>
                <a href="{{ route('products.index') }}" 
                    class="bg-[#171e60] hover:bg-[#0a5694] text-white px-8 py-3 rounded-full font-semibold transition-colors duration-300">
                    Explore Products
                </a>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button @click="activeSlide = activeSlide === 0 ? 2 : activeSlide - 1" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="activeSlide = activeSlide === 2 ? 0 : activeSlide + 1"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        <!-- Brand Border -->
        <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-b from-transparent to-white">
            <svg class="w-full h-full relative" preserveAspectRatio="none" viewBox="0 0 100 10" xmlns="http://www.w3.org/2000/svg">
                <path d="M 0 5 Q 25 0, 50 5 T 100 5" fill="none" stroke="url(#gradient)" stroke-width="2"/>
                <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color: #171e60"/>
                        <stop offset="50%" style="stop-color: #0a5694"/>
                        <stop offset="100%" style="stop-color: #171e60"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </div>

        <!-- Featured Products -->
        <div class="max-w-7xl mx-auto px-4 py-16">
        <h3 class="text-3xl font-bold text-center mb-12">Featured Equipment</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Product Card 1 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                   <img src="{{ asset('/Images/Glass.jpg') }}"
                     alt="Microscope" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2">Laboratory Glass</h4>
                    <p class="text-gray-600">Premium quality laboratory glassware for precise measurements and experiments.</p>
                    <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Learn More →</a>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ asset('/Images/centrifuges.jpg') }}"
                     alt="Centrifuge" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2">Centrifuges</h4>
                    <p class="text-gray-600">Advanced centrifuge equipment for laboratory separation and analysis.</p>
                    <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Learn More →</a>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                  <img src="{{ asset('/Images/microscopes.jpg') }}"
                     alt="Analyzer" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2">Microscopes</h4>
                    <p class="text-gray-600">High-quality microscopes for detailed specimen examination and research.</p>
                    <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Learn More →</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- After Hero Section and before Featured Products -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4" x-data="{ activeSlide: 0, autoplay: null }" x-init="autoplay = setInterval(() => { activeSlide = activeSlide === 2 ? 0 : activeSlide + 1 }, 5000)">
            <h3 class="text-3xl font-bold text-center mb-12">Why Choose MaxMed?</h3>
            
            <!-- Carousel container -->
            <div class="relative h-[400px] overflow-hidden rounded-xl shadow-lg">
                <!-- Slide 1 -->
                <div class="absolute inset-0 w-full h-full transition-opacity duration-500 ease-in-out"
                     x-show="activeSlide === 0"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex h-full">
                        <div class="w-1/2 bg-[#171e60] p-12 flex items-center hover:scale-105 transform transition duration-500">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Innovation First</h4>
                                <p class="text-xl">Leading the industry with cutting-edge medical laboratory solutions that define the future of diagnostics.</p>
                            </div>
                        </div>
                        <div class="w-1/2">
                             <img src="{{ asset('/Images/Innovation.jpg') }}"
                                 alt="Innovation" 
                                 class="w-full h-full object-cover hover:scale-105 transform transition duration-500">
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="absolute inset-0 w-full h-full transition-opacity duration-500 ease-in-out"
                     x-show="activeSlide === 1"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex h-full">
                        <div class="w-1/2">
                            <img src="{{ asset('/Images/bacteria.jpg') }}" 
                                 alt="Quality" 
                                 class="w-full h-full object-cover hover:scale-105 transform transition duration-500">
                        </div>
                        <div class="w-1/2 bg-[#0a5694] p-12 flex items-center hover:scale-105 transform transition duration-500">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Quality Assured</h4>
                                <p class="text-xl">Every piece of equipment undergoes rigorous testing to ensure reliable performance and accurate results.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="absolute inset-0 w-full h-full transition-opacity duration-500 ease-in-out"
                     x-show="activeSlide === 2"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex h-full">
                        <div class="w-1/2 bg-[#171e60] p-12 flex items-center hover:scale-105 transform transition duration-500">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Expert Support</h4>
                                <p class="text-xl">Our team of specialists provides comprehensive training and ongoing technical support.</p>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <img src="{{ asset('/Images/Expert.jpg') }}"
                                 alt="Support" 
                                 class="w-full h-full object-cover hover:scale-105 transform transition duration-500">
                        </div>
                    </div>
                </div>

                <!-- Navigation dots -->
                <div class="absolute bottom-5 left-0 right-0 flex justify-center space-x-2 z-10">
                    <button @click="activeSlide = 0" 
                            class="h-3 transition-all duration-300"
                            :class="activeSlide === 0 ? 'w-8 bg-[#171e60]' : 'w-3 bg-gray-400 hover:bg-[#171e60]'">
                        <span class="sr-only">Slide 1</span>
                    </button>
                    <button @click="activeSlide = 1" 
                            class="h-3 transition-all duration-300"
                            :class="activeSlide === 1 ? 'w-8 bg-[#171e60]' : 'w-3 bg-gray-400 hover:bg-[#171e60]'">
                        <span class="sr-only">Slide 2</span>
                    </button>
                    <button @click="activeSlide = 2" 
                            class="h-3 transition-all duration-300"
                            :class="activeSlide === 2 ? 'w-8 bg-[#171e60]' : 'w-3 bg-gray-400 hover:bg-[#171e60]'">
                        <span class="sr-only">Slide 3</span>
                    </button>
                </div>

                <!-- Arrow buttons -->
                <button @click="activeSlide = activeSlide === 0 ? 2 : activeSlide - 1" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-3 hover:bg-white hover:scale-110 transition duration-300 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="activeSlide = activeSlide === 2 ? 0 : activeSlide + 1"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-3 hover:bg-white hover:scale-110 transition duration-300 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Key Suppliers Section -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h3 class="text-3xl font-bold text-center mb-8">Key Suppliers</h3>
        <div x-data="{ activeSupplier: 0 }" 
             x-init="setInterval(() => { activeSupplier = activeSupplier === 1 ? 0 : activeSupplier + 1 }, 3000)"
             class="relative h-[100px] overflow-hidden">
            
            <!-- Supplier logos - Group 1 -->
            <div class="absolute inset-0 grid grid-cols-4 gap-8 transition-opacity duration-500"
                 x-show="activeSupplier === 0">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier1.png') }}" alt="Supplier 1" class="h-16 object-contain">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier2.png') }}" alt="Supplier 2" class="h-16 object-contain">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier3.png') }}" alt="Supplier 3" class="h-16 object-contain">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier4.png') }}" alt="Supplier 4" class="h-16 object-contain">
                </div>
            </div>

            <!-- Supplier logos - Group 2 -->
            <div class="absolute inset-0 grid grid-cols-4 gap-8 transition-opacity duration-500"
                 x-show="activeSupplier === 1">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier5.png') }}" alt="Supplier 5" class="h-16 object-contain">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier1.png') }}" alt="Supplier 1" class="h-16 object-contain">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier2.png') }}" alt="Supplier 2" class="h-16 object-contain">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('/Images/supplier3.png') }}" alt="Supplier 3" class="h-16 object-contain">
                </div>
            </div>

            <!-- Navigation dots -->
            <div class="absolute bottom-0 left-0 right-0 flex justify-center space-x-2">
                <button @click="activeSupplier = 0" 
                        class="h-2 transition-all duration-300"
                        :class="activeSupplier === 0 ? 'w-6 bg-[#171e60]' : 'w-2 bg-gray-300 hover:bg-[#171e60]'">
                </button>
                <button @click="activeSupplier = 1" 
                        class="h-2 transition-all duration-300"
                        :class="activeSupplier === 1 ? 'w-6 bg-[#171e60]' : 'w-2 bg-gray-300 hover:bg-[#171e60]'">
                </button>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection

