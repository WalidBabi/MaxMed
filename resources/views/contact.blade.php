@extends('layouts.app')

@section('content')

    <!-- Hero Section -->
    <div class="bg-blue-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Us</h1>
            <p class="text-xl">Get in touch with our team for any inquiries or support</p>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6">Send us a Message</h2>
                <form x-data="{ submitted: false }" @submit.prevent="submitted = true">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Name</label>
                            <input type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Subject</label>
                            <select class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select a subject</option>
                                <option value="sales">Sales Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="service">Service Request</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Message</label>
                            <textarea rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition duration-300">
                            Send Message
                        </button>
                    </div>

                    <!-- Success Message -->
                    <div x-show="submitted" 
                         x-transition
                         class="mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        Thank you for your message! We'll get back to you soon.
                    </div>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Office Location -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Office Location
                    </h3>
                    <p class="text-gray-600">
                        MaxMed Medical Equipment Trading LLC<br>
                        Dubai Science Park<br>
                        Dubai, United Arab Emirates
                    </p>
                </div>

                <!-- Contact Details -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Contact Details
                    </h3>
                    <div class="space-y-3">
                        <p class="text-gray-600">Phone: +971 52 634 4688</p>
                        <p class="text-gray-600">Email: sales@maxmedme.com</p>
                        <p class="text-gray-600">Working Hours: Sun-Thu 9:00 AM - 6:00 PM</p>
                    </div>
                </div>

                <!-- Map -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-4">Location Map</h3>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe 
                            class="w-full h-[300px] rounded-lg"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3613.7277317517393!2d55.2177!3d25.1277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f6b45ef30e0cd%3A0x6f7b3f70f41c787c!2sDubai%20Science%20Park!5e0!3m2!1sen!2sae!4v1234567890"
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection