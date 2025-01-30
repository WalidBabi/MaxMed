<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaxMed - Medical Laboratory Equipment</title>
    <!-- Add Tailwind CSS CDN for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Add custom styles -->
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('/Images/Header.jpg');
            background-size: cover;
            background-position: center;
        }

        /* Carousel fade animation */
        .carousel-item {
            transition: opacity 0.6s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <x-navigation />

    <!-- Hero Section -->
    <div class="hero-section h-[500px] flex items-center justify-center text-white mt-[70px]">
        <div class="text-center">
            <h2 class="text-4xl md:text-6xl font-bold mb-4">Laboratory Solutions</h2>
            <p class="text-xl mb-8">Providing cutting-edge medical Laboratory equipment for modern laboratories</p>
            <a href="{{ route('products') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-semibold">
                Explore Products
            </a>
        </div>
    </div>

    <!-- After Hero Section and before Featured Products -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4" x-data="{ activeSlide: 0 }">
            <h3 class="text-3xl font-bold text-center mb-12">Why Choose MaxMed?</h3>
            
            <!-- Carousel container -->
            <div class="relative h-[400px] overflow-hidden rounded-xl shadow-lg">
                <!-- Slide 1 -->
                <div class="carousel-item absolute inset-0 w-full h-full"
                     x-show="activeSlide === 0"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex h-full">
                        <div class="w-1/2 bg-blue-600 p-12 flex items-center">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Innovation First</h4>
                                <p class="text-xl">Leading the industry with cutting-edge medical laboratory solutions that define the future of diagnostics.</p>
                            </div>
                        </div>
                        <div class="w-1/2">
                             <img src="{{ asset('images/Innovation.jpg') }}"
                                 alt="Innovation" 
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item absolute inset-0 w-full h-full"
                     x-show="activeSlide === 1"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex h-full">
                        <div class="w-1/2">
                            <img src="https://images.unsplash.com/photo-1579154204601-01588f351e67?ixlib=rb-1.2.1" 
                                 alt="Quality" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="w-1/2 bg-green-600 p-12 flex items-center">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Quality Assured</h4>
                                <p class="text-xl">Every piece of equipment undergoes rigorous testing to ensure reliable performance and accurate results.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item absolute inset-0 w-full h-full"
                     x-show="activeSlide === 2"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex h-full">
                        <div class="w-1/2 bg-purple-600 p-12 flex items-center">
                            <div class="text-white">
                                <h4 class="text-3xl font-bold mb-4">Expert Support</h4>
                                <p class="text-xl">Our team of specialists provides comprehensive training and ongoing technical support.</p>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <img src="https://images.unsplash.com/photo-1576086213369-97a306d36557?ixlib=rb-1.2.1" 
                                 alt="Support" 
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <!-- Navigation buttons -->
                <div class="absolute bottom-5 left-0 right-0 flex justify-center space-x-2">
                    <button @click="activeSlide = 0" 
                            :class="{'bg-blue-600': activeSlide === 0, 'bg-gray-400': activeSlide !== 0}"
                            class="w-3 h-3 rounded-full transition-colors duration-300"></button>
                    <button @click="activeSlide = 1"
                            :class="{'bg-blue-600': activeSlide === 1, 'bg-gray-400': activeSlide !== 1}"
                            class="w-3 h-3 rounded-full transition-colors duration-300"></button>
                    <button @click="activeSlide = 2"
                            :class="{'bg-blue-600': activeSlide === 2, 'bg-gray-400': activeSlide !== 2}"
                            class="w-3 h-3 rounded-full transition-colors duration-300"></button>
                </div>

                <!-- Arrow buttons -->
                <button @click="activeSlide = activeSlide === 0 ? 2 : activeSlide - 1" 
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 rounded-full p-2 hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="activeSlide = activeSlide === 2 ? 0 : activeSlide + 1"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 rounded-full p-2 hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
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

    <!-- Newsletter Banner -->
    <div x-data="{ showBanner: false }" 
         x-init="setTimeout(() => { showBanner = true }, 3000)"
         x-show="showBanner"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 p-4 z-40">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0">
                <h4 class="text-xl font-semibold text-gray-800">Stay Updated with MaxMed</h4>
                <p class="text-gray-600">Get the latest news and updates about medical laboratory equipment</p>
            </div>
            <div class="flex space-x-2">
                <input type="email" 
                       placeholder="Enter your email" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Subscribe
                </button>
                <button @click="showBanner = false" 
                        class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Overview -->
                <div>
                    <img src="{{ asset('Images/Logofooter.jpg') }}" alt="MaxMed Logo" >
                    <p class="text-gray-400 mb-4 mt-2">Empowering medical laboratories with cutting-edge equipment since 2010.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Solutions -->
                <div>
                    <h5 class="text-xl font-bold mb-4">Solutions</h5>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Clinical Labs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Research Facilities</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Hospital Labs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Educational Institutes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Industrial Labs</a></li>
                    </ul>
                </div>

                <!-- Support & Resources -->
                <div>
                    <h5 class="text-xl font-bold mb-4">Support & Resources</h5>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Equipment Manuals</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Calibration Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Training Programs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Technical Support</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Knowledge Base</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Lab Safety Guidelines</a></li>
                    </ul>
                </div>

                <!-- Connect -->
                <div>
                    <h5 class="text-xl font-bold mb-4 flex items-center">
                        <span class="mr-2">Stay Connected</span>
                        <svg class="w-5 h-5 text-blue-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </h5>
                    <div class="mb-6 transform hover:scale-105 transition-transform duration-300">
                        <p class="text-gray-400 mb-4">Subscribe to our newsletter for industry insights and product updates.</p>
                        <form class="space-y-2">
                            <div class="relative">
                                <input type="email" placeholder="Enter your email" 
                                       class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-300">
                                <div class="absolute right-3 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transform hover:translate-y-[-2px] transition-all duration-300 hover:shadow-lg">
                                Subscribe Now
                            </button>
                        </form>
                    </div>
                    <div class="transform hover:scale-105 transition-transform duration-300 bg-gray-700 p-4 rounded-lg">
                        <h6 class="font-semibold mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            24/7 Support
                        </h6>
                        <p class="text-gray-400 hover:text-blue-400 transition-colors duration-300">UAE: +971 52 634 4688</p>
                        <p class="text-gray-400 hover:text-blue-400 transition-colors duration-300">Email: sales@maxmedme.com</p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 border-t border-gray-700 mt-8 pt-8">
                <div class="text-gray-400">
                    <h6 class="font-semibold mb-2">Certifications</h6>
                    <div class="flex space-x-4">
                        <img src="{{ asset('images/iso.png') }}" alt="ISO Certified" class="h-12">
                        <img src="{{ asset('images/ce.png') }}" alt="CE Marked" class="h-12">
                        <img src="{{ asset('images/fda.png') }}" alt="FDA Approved" class="h-12">
                    </div>
                </div>
                <div class="text-gray-400">
                    <h6 class="font-semibold mb-2">Emergency Service</h6>
                    <p>24/7 Technical Support Hotline</p>
                    <p class="text-blue-400">+971 52 634 4688</p>
                </div>
                <div class="text-gray-400">
                    <h6 class="font-semibold mb-2">Download Our App</h6>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:opacity-80">
                            <img src="{{ asset('images/app-store.png') }}" alt="App Store" class="h-8">
                        </a>
                        <a href="#" class="hover:opacity-80">
                            <img src="{{ asset('images/play-store.png') }}" alt="Play Store" class="h-8">
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center text-gray-400 text-sm">
                    <p>&copy; 2025 MaxMed. All rights reserved.</p>
                    <div class="flex space-x-4 mt-4 md:mt-0">
                        <a href="#" class="hover:text-white">Privacy Policy</a>
                        <a href="#" class="hover:text-white">Terms of Service</a>
                        <a href="#" class="hover:text-white">Cookie Policy</a>
                        <a href="#" class="hover:text-white">Quality Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>