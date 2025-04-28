@extends('layouts.app')

@section('content')
<div class="bg-gray-100">
    <div class="container mx-auto px-4 py-12 sm:px-6 lg:px-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl mb-6 tracking-tight">
                <span class="text-[#171e60]">Industries & Solutions</span>
            </h1>
            <p class="max-w-3xl mx-auto text-xl text-gray-600 leading-relaxed">
                MaxMed provides specialized equipment and tailored solutions for a wide range of industries.
            </p>
        </div>

        <!-- Industry Cards - Simplified Design -->
        <div class="mb-5 mt-5">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Clinics & Medical Centers</h3>
                    <p class="text-gray-600">Specialized equipment for diagnostic and treatment facilities.</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Hospitals</h3>
                    <p class="text-gray-600">Advanced solutions for hospital departments and specialized care.</p>
                </div>
                
                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Veterinary Clinics</h3>
                    <p class="text-gray-600">Equipment designed for animal healthcare facilities.</p>
                </div>
                
                <!-- Card 4 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Medical Laboratories</h3>
                    <p class="text-gray-600">Precision instruments for medical testing and analysis.</p>
                </div>
                
                <!-- Card 5 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Research Laboratories</h3>
                    <p class="text-gray-600">Advanced equipment for scientific research facilities.</p>
                </div>
                
                <!-- Card 6 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Universities & Academia</h3>
                    <p class="text-gray-600">Specialized tools for educational research programs.</p>
                </div>
                
                <!-- Card 7 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Biotech & Pharmaceutical</h3>
                    <p class="text-gray-600">Equipment for developing new medical treatments.</p>
                </div>
                
                <!-- Card 8 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Forensic Laboratories</h3>
                    <p class="text-gray-600">Precision equipment for forensic analysis and investigation.</p>
                </div>
                
                <!-- Card 9 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Environment Laboratories</h3>
                    <p class="text-gray-600">Equipment for environmental monitoring and testing.</p>
                </div>
                
                <!-- Card 10 -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0a5694] mb-2">Food Laboratories</h3>
                    <p class="text-gray-600">Specialized equipment for food safety and quality testing.</p>
                </div>
                
              
            </div>
        </div>

        <!-- Contact Section -->
        <div class="bg-[#0a5694] text-white rounded-lg shadow-xl overflow-hidden">
            <div class="p-8">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl font-bold mb-6">Looking for Industry-Specific Solutions?</h2>
                    <p class="text-xl text-blue-100 mb-10">
                        Our specialists can help you find the perfect equipment tailored to your unique industry requirements.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4 mt-4">
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-2 py-4 bg-white text-[#0a5694] font-semibold rounded-lg hover:bg-blue-50 transition-colors shadow-lg">
                            Contact Our Specialists
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 