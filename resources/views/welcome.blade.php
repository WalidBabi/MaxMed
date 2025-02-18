@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section h-[300px] flex items-center justify-center text-white">
        <div class="text-center">
            <h2 class="text-4xl md:text-6xl font-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">Laboratory Solutions</h2>
            <p class="text-xl mb-8" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">Providing cutting-edge medical Laboratory equipment for modern laboratories</p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-semibold">
                Explore Products
            </a>
        </div>
    </div>

        <!-- Featured Products -->
        <div class="max-w-7xl mx-auto px-4 py-16">
        <h3 class="text-3xl font-bold text-center mb-12">Featured Equipment</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Product Card 1 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                   <img src="{{ asset('images/Glass.jpg') }}"
                     alt="Microscope" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2">Laboratory Glass</h4>
                    <p class="text-gray-600">Premium quality laboratory glassware for precise measurements and experiments.</p>
                    <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Learn More →</a>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ asset('images/centrifuges.jpg') }}"
                     alt="Centrifuge" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2">Centrifuges</h4>
                    <p class="text-gray-600">Advanced centrifuge equipment for laboratory separation and analysis.</p>
                    <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Learn More →</a>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                  <img src="{{ asset('images/microscopes.jpg') }}"
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
                        <div class="w-1/2 bg-blue-600 p-12 flex items-center hover:scale-105 transform transition duration-500">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Innovation First</h4>
                                <p class="text-xl">Leading the industry with cutting-edge medical laboratory solutions that define the future of diagnostics.</p>
                            </div>
                        </div>
                        <div class="w-1/2">
                             <img src="{{ asset('images/Innovation.jpg') }}"
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
                            <img src="{{ asset('images/bacteria.jpg') }}" 
                                 alt="Quality" 
                                 class="w-full h-full object-cover hover:scale-105 transform transition duration-500">
                        </div>
                        <div class="w-1/2 bg-green-600 p-12 flex items-center hover:scale-105 transform transition duration-500">
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
                        <div class="w-1/2 bg-purple-600 p-12 flex items-center hover:scale-105 transform transition duration-500">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Expert Support</h4>
                                <p class="text-xl">Our team of specialists provides comprehensive training and ongoing technical support.</p>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <img src="{{ asset('images/Expert.jpg') }}"
                                 alt="Support" 
                                 class="w-full h-full object-cover hover:scale-105 transform transition duration-500">
                        </div>
                    </div>
                </div>

                <!-- Navigation dots -->
                <div class="absolute bottom-5 left-0 right-0 flex justify-center space-x-2 z-10">
                    <button @click="activeSlide = 0" 
                            class="h-3 transition-all duration-300"
                            :class="activeSlide === 0 ? 'w-8 bg-blue-600' : 'w-3 bg-gray-400 hover:bg-blue-400'">
                        <span class="sr-only">Slide 1</span>
                    </button>
                    <button @click="activeSlide = 1" 
                            class="h-3 transition-all duration-300"
                            :class="activeSlide === 1 ? 'w-8 bg-blue-600' : 'w-3 bg-gray-400 hover:bg-blue-400'">
                        <span class="sr-only">Slide 2</span>
                    </button>
                    <button @click="activeSlide = 2" 
                            class="h-3 transition-all duration-300"
                            :class="activeSlide === 2 ? 'w-8 bg-blue-600' : 'w-3 bg-gray-400 hover:bg-blue-400'">
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


    
   @include('layouts.footer')
@endsection

