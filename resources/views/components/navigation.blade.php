<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
</div>

<nav class="fixed w-full top-0 bg-white shadow-lg py-2 z-50 transition-all duration-300" 
     x-data="{ atTop: true }"
     @scroll.window="atTop = (window.pageYOffset > 30 ? false : true)"
     :class="{ 'py-2': atTop, 'py-1 bg-white/95 backdrop-blur-sm': !atTop }">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <img src="{{ asset('Images/Logo.png') }}" alt="MaxMed Logo" 
                     class="transition-transform duration-300 hover:scale-105"
                     style="height: 250px;">
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-blue-600 transition-colors duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-blue-600 after:transition-all">Home</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 transition-colors duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-blue-600 after:transition-all">About Us</a>
                <a href="{{ route('products') }}" class="text-gray-700 hover:text-blue-600 transition-colors duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-blue-600 after:transition-all">Products</a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-600 transition-colors duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-blue-600 after:transition-all">Contact Us</a>
            </div>
        </div>
    </div>
</nav>