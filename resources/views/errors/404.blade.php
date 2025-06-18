@extends('layouts.app')

@section('title', 'Page Not Found - MaxMed UAE')
@section('meta_description', 'The page you are looking for could not be found. Browse our laboratory equipment and medical supplies or contact MaxMed UAE at +971 55 460 2500.')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Error Code -->
        <div>
            <h1 class="text-9xl font-bold text-[#171e60] opacity-80">404</h1>
            <h2 class="mt-4 text-3xl font-bold text-gray-900">Page Not Found</h2>
            <p class="mt-4 text-lg text-gray-600">
                The page you're looking for doesn't exist or has been moved.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="mt-8">
            <form method="GET" action="{{ route('products.index') }}" class="flex rounded-lg shadow-sm">
                <input type="text" 
                       name="search" 
                       placeholder="Search for laboratory equipment..."
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-[#171e60] focus:border-transparent"
                       value="{{ request('search') }}">
                <button type="submit" 
                        class="px-6 py-3 bg-[#171e60] text-white rounded-r-lg hover:bg-[#0a5694] transition-colors duration-200">
                    Search
                </button>
            </form>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Popular Pages</h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('welcome') }}" 
                   class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-[#171e60] hover:text-[#0a5694]">
                    🏠 Home
                </a>
                <a href="{{ route('products.index') }}" 
                   class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-[#171e60] hover:text-[#0a5694]">
                    🔬 Laboratory Equipment
                </a>
                <a href="{{ route('categories.index') }}" 
                   class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-[#171e60] hover:text-[#0a5694]">
                    📂 Categories
                </a>
                <a href="{{ route('contact') }}" 
                   class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-[#171e60] hover:text-[#0a5694]">
                    📞 Contact Us
                </a>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="mt-8 p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Need Help?</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p>📞 Call us: <a href="tel:+971554602500" class="text-[#171e60] hover:text-[#0a5694]">+971 55 460 2500</a></p>
                <p>📧 Email: <a href="mailto:sales@maxmedme.com" class="text-[#171e60] hover:text-[#0a5694]">sales@maxmedme.com</a></p>
                <p>🕒 Hours: Monday-Friday 9:00 AM - 6:00 PM</p>
            </div>
        </div>

        <!-- Go Back Button -->
        <div class="mt-8">
            <button onclick="history.back()" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#171e60] hover:bg-[#0a5694] transition-colors duration-200">
                ← Go Back
            </button>
        </div>
    </div>
</div>

<!-- JSON-LD Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Page Not Found",
    "description": "The requested page could not be found on MaxMed UAE website",
    "url": "{{ url()->current() }}",
    "mainEntity": {
        "@type": "Organization",
        "name": "MaxMed UAE",
        "url": "https://maxmedme.com",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+971-55-460-2500",
            "contactType": "Customer Service"
        }
    }
}
</script>
@endsection 