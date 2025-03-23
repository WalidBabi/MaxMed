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
            <h1 class="text-4xl font-extrabold tracking-tight text-[#171e60] sm:text-5xl md:text-6xl">
                Our Trusted Partners
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-[#0a5694]">
                We collaborate with leading healthcare organizations to provide innovative medical solutions worldwide.
            </p>
        </div>
    </div>
</div>

<!-- Partners Categories -->
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center space-x-2 md:space-x-4 overflow-x-auto py-4">
            <button class="px-4 py-2 bg-[#171e60] text-white rounded-full font-medium text-sm">All Partners</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Equipment Suppliers</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Technology Providers</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Research Collaborators</button>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full font-medium text-sm">Logistics Partners</button>
        </div>
    </div>
</div>

<!-- Featured Partners -->
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold text-[#0a5694] uppercase tracking-wide">Collaborations</h2>
            <p class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">Featured Partners</p>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Our premium partnerships driving innovation in healthcare
            </p>
        </div>

        <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Partner 1 -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl transition-all duration-200 hover:shadow-xl transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-center h-48 bg-gray-50 rounded-lg mb-6">
                        <img src="{{ asset('images/partners/partner1.png') }}" alt="Medical Innovations Inc." class="max-h-36 p-4">
                    </div>
                    <div class="flex items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Medical Innovations Inc.</h3>
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Featured</span>
                    </div>
                    <p class="text-gray-600">Leading provider of innovative medical equipment and solutions for healthcare facilities worldwide.</p>
                    <div class="mt-6 flex justify-between items-center">
                        <a href="#" class="text-[#0a5694] hover:text-[#171e60] font-medium flex items-center">
                            Learn more 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <div class="flex space-x-2">
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Equipment</span>
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Global</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner 2 -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl transition-all duration-200 hover:shadow-xl transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-center h-48 bg-gray-50 rounded-lg mb-6">
                        <img src="{{ asset('images/partners/partner2.png') }}" alt="HealthTech Solutions" class="max-h-36 p-4">
                    </div>
                    <div class="flex items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">HealthTech Solutions</h3>
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Featured</span>
                    </div>
                    <p class="text-gray-600">Specializing in advanced diagnostic equipment and healthcare technology solutions.</p>
                    <div class="mt-6 flex justify-between items-center">
                        <a href="#" class="text-[#0a5694] hover:text-[#171e60] font-medium flex items-center">
                            Learn more 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <div class="flex space-x-2">
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Technology</span>
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Diagnostics</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner 3 -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl transition-all duration-200 hover:shadow-xl transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-center h-48 bg-gray-50 rounded-lg mb-6">
                        <img src="{{ asset('images/partners/partner3.png') }}" alt="Global Medical Supply" class="max-h-36 p-4">
                    </div>
                    <div class="flex items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Global Medical Supply</h3>
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Featured</span>
                    </div>
                    <p class="text-gray-600">Trusted supplier of medical equipment and consumables with global distribution network.</p>
                    <div class="mt-6 flex justify-between items-center">
                        <a href="#" class="text-[#0a5694] hover:text-[#171e60] font-medium flex items-center">
                            Learn more 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <div class="flex space-x-2">
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Supply</span>
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Distribution</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Partners -->
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-base font-semibold text-[#0a5694] uppercase tracking-wide">Network</h2>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">Our Partner Ecosystem</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Partner 4 -->
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-200 hover:shadow-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-32 bg-gray-50 rounded-lg mb-4">
                        <img src="{{ asset('images/partners/partner4.png') }}" alt="MedResearch Labs" class="max-h-24 p-4">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">MedResearch Labs</h3>
                    <p class="mt-2 text-sm text-gray-600">Pioneering research and development in medical technology and healthcare solutions.</p>
                    <div class="mt-4">
                        <a href="#" class="text-[#0a5694] hover:text-[#171e60] text-sm font-medium">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 5 -->
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-200 hover:shadow-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-32 bg-gray-50 rounded-lg mb-4">
                        <img src="{{ asset('images/partners/partner5.png') }}" alt="Healthcare Logistics" class="max-h-24 p-4">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Healthcare Logistics</h3>
                    <p class="mt-2 text-sm text-gray-600">Specialized in medical supply chain management and healthcare logistics solutions.</p>
                    <div class="mt-4">
                        <a href="#" class="text-[#0a5694] hover:text-[#171e60] text-sm font-medium">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 6 -->
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100 transition-all duration-200 hover:shadow-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-32 bg-gray-50 rounded-lg mb-4">
                        <img src="{{ asset('images/partners/partner6.png') }}" alt="MedTech Innovations" class="max-h-24 p-4">
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">MedTech Innovations</h3>
                    <p class="mt-2 text-sm text-gray-600">Developing cutting-edge medical devices and technology for modern healthcare needs.</p>
                    <div class="mt-4">
                        <a href="#" class="text-[#0a5694] hover:text-[#171e60] text-sm font-medium">Learn more &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <button class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-medium">
                View All Partners
            </button>
        </div>
    </div>
</div>

<!-- Partnership Benefits -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-[#0a5694] font-semibold tracking-wide uppercase">Benefits</h2>
            <p class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">Why Partner With Us</p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                Discover the advantages of becoming a partner in our growing healthcare ecosystem.
            </p>
        </div>

        <div class="mt-12">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Benefit 1 -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-[#171e60] rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-medium text-gray-900">Expanded Market Reach</h3>
                    </div>
                    <p class="text-gray-600">Access new markets and customer segments through our established global network.</p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-[#171e60] rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-medium text-gray-900">Collaborative Innovation</h3>
                    </div>
                    <p class="text-gray-600">Join forces with industry leaders to develop breakthrough healthcare solutions.</p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-[#171e60] rounded-md p-3">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-medium text-gray-900">Brand Association</h3>
                    </div>
                    <p class="text-gray-600">Align with our trusted healthcare brand to enhance your market credibility and reputation.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Become a Partner -->
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-[#171e60] to-[#0a5694] rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-12 sm:px-12 lg:flex lg:items-center lg:py-16">
                <div class="lg:w-0 lg:flex-1">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white">
                        Ready to become a MaxMed partner?
                    </h2>
                    <p class="mt-4 max-w-3xl text-lg text-blue-100">
                        We're always looking for innovative companies to collaborate with in providing exceptional healthcare solutions. Join our ecosystem of healthcare pioneers.
                    </p>
                    
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-[#171e60] bg-white hover:bg-gray-50 shadow-md">
                            Contact Us
                        </a>
                        <a href="#" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-blue-800 hover:bg-opacity-30">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="mt-8 lg:mt-0 lg:ml-8">
                    <div class="bg-white p-5 rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Partner Application Process</h3>
                        <ul class="space-y-3">
                            <li class="flex">
                                <svg class="h-5 w-5 text-[#0a5694] mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-gray-700">Submit an inquiry</span>
                            </li>
                            <li class="flex">
                                <svg class="h-5 w-5 text-[#0a5694] mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-gray-700">Initial consultation</span>
                            </li>
                            <li class="flex">
                                <svg class="h-5 w-5 text-[#0a5694] mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-gray-700">Partnership agreement</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

