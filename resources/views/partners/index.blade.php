@extends('layouts.app')

@section('content')
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Our Partners
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                We collaborate with leading healthcare organizations to provide the best medical solutions.
            </p>
        </div>

        <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Partner 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-40 bg-gray-100 rounded-md mb-4">
                        <img src="{{ asset('images/partners/partner1.png') }}" alt="Partner 1" class="max-h-32 p-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Medical Innovations Inc.</h3>
                    <p class="mt-2 text-gray-600">Leading provider of innovative medical equipment and solutions for healthcare facilities worldwide.</p>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 2 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-40 bg-gray-100 rounded-md mb-4">
                        <img src="{{ asset('images/partners/partner2.png') }}" alt="Partner 2" class="max-h-32 p-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">HealthTech Solutions</h3>
                    <p class="mt-2 text-gray-600">Specializing in advanced diagnostic equipment and healthcare technology solutions.</p>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 3 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-40 bg-gray-100 rounded-md mb-4">
                        <img src="{{ asset('images/partners/partner3.png') }}" alt="Partner 3" class="max-h-32 p-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Global Medical Supply</h3>
                    <p class="mt-2 text-gray-600">Trusted supplier of medical equipment and consumables with global distribution network.</p>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 4 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-40 bg-gray-100 rounded-md mb-4">
                        <img src="{{ asset('images/partners/partner4.png') }}" alt="Partner 4" class="max-h-32 p-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">MedResearch Labs</h3>
                    <p class="mt-2 text-gray-600">Pioneering research and development in medical technology and healthcare solutions.</p>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 5 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-40 bg-gray-100 rounded-md mb-4">
                        <img src="{{ asset('images/partners/partner5.png') }}" alt="Partner 5" class="max-h-32 p-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Healthcare Logistics</h3>
                    <p class="mt-2 text-gray-600">Specialized in medical supply chain management and healthcare logistics solutions.</p>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">Learn more &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Partner 6 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-center h-40 bg-gray-100 rounded-md mb-4">
                        <img src="{{ asset('images/partners/partner6.png') }}" alt="Partner 6" class="max-h-32 p-4">
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">MedTech Innovations</h3>
                    <p class="mt-2 text-gray-600">Developing cutting-edge medical devices and technology for modern healthcare needs.</p>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">Learn more &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-16 text-center">
            <h3 class="text-2xl font-bold text-gray-900">Become a Partner</h3>
            <p class="mt-4 text-lg text-gray-500 max-w-3xl mx-auto">
                Interested in partnering with MaxMed? We're always looking for innovative companies to collaborate with in providing exceptional healthcare solutions.
            </p>
            <div class="mt-6">
                <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-[#171e60] hover:bg-[#0a5694]">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

