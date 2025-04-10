<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:border-gray-700 shadow-sm">
    <!-- Single Bar with Logo, Links, Search and Navigation -->
    <div class="bg-white py-2">
        <div class="container mx-auto px-4 sm:px-6 lg:px-12">
            <!-- Top section with Logo and Links -->
            <div class="flex justify-between items-center">
                <!-- Logo - Now positioned between top and main navigation -->
                <div class="flex justify-center py-1 flex-shrink-0">
                    <a href="{{ route('welcome') }}" class="transition hover:opacity-90 flex-shrink-0">
                        <img src="{{ asset('Images/logo.png') }}" alt="MaxMed Logo" class="block h-[60px] w-[236px] mr-5 min-h-[60px] min-w-[236px] flex-shrink-0 !important">
                    </a>
                </div>
                
                <!-- Search Bar - Now in the middle -->
                <div class="hidden md:block mx-4 w-1/3">
                    <form action="{{ route('search') }}" method="GET" class="flex items-center mb-0">
                        <input type="text" name="query" placeholder="Search product names or codes"
                            class="w-full py-2 pl-4 bg-gray-100 border-none rounded-l-full focus:outline-none text-sm"
                            value="{{ request('query') }}">
                        <button type="submit" aria-label="Search products" class=" bg-gradient-to-r from-[#171e60] to-[#0a5694] text-white p-2.5 rounded-lg hover:from-[#0a5694] hover:to-[#171e60] transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#171e60] focus:ring-opacity-50 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
               
                    <div class="flex items-center space-x-6 text-sm">
                

                        <a href="{{ route('welcome') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">Home</a>
                        <a href="{{ route('about') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">About</a>
                        <a href="{{ route('contact') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">Contact</a>
                        <a href="{{ route('products.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">Products</a>
                        <a href="{{ route('partners.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">partners</a>
                        <a href="{{ route('news.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">News</a>

                       
                        @auth
                        <div class="relative inline-block" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 flex items-center font-normal">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                @endif
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('register') }}" class="flex items-center text-[#0064a8] hover:text-[#0052a3] font-normal h-full">Register</a>
                        <a href="{{ route('login') }}" class="flex items-center text-[#0064a8] hover:text-[#0052a3] font-normal h-full">Login</a>
                        @endauth

                        <!-- Main Navigation Categories -->


                        <!-- Cart Icon -->
                        <a href="{{ route('cart.view') }}" class="flex items-center text-gray-600 hover:text-gray-900" aria-label="Shopping cart">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @php
                                $cartQuantity = 0;
                                if (session('cart')) {
                                foreach (session('cart') as $item) {
                                $cartQuantity += intval($item['quantity']);
                                }
                                }
                                @endphp
                                @if($cartQuantity > 0)
                                <span class="absolute -top-2 -right-2 bg-[#00a9e0] text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $cartQuantity }}
                                </span>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center justify-between px-4 py-2 border-t border-gray-100">
            <a href="{{ route('cart.view') }}" class="flex items-center text-gray-600" aria-label="Shopping cart">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @if($cartQuantity > 0)
                <span class="ml-1 bg-[#00a9e0] text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $cartQuantity }}
                </span>
                @endif
                <span class="sr-only">View shopping cart</span>
            </a>
            <button @click="open = !open" class="text-gray-500 hover:text-gray-600 focus:outline-none" aria-label="Toggle navigation menu">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-100">
                <!-- Mobile Search - Updated to match SLS style -->
                <div class="px-4 py-2">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <input type="text" name="query" placeholder="Search product names, codes or CAS number"
                            class="w-full py-2 pl-4 pr-12 bg-gray-200 border-none rounded-full focus:outline-none text-sm"
                            value="{{ request('query') }}">
                        <button type="submit" aria-label="Search products" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#0064a8] text-white p-2 rounded-full hover:bg-[#0052a3] focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <a href="{{ route('welcome') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Home</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">About Us</a>
                <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Products</a>
                <a href="{{ route('partners.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Partners</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Contact Us</a>
                <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">News</a>

                @auth
                @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Admin Dashboard</a>
                @endif
                <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">My Orders</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Log Out</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#0064a8] hover:bg-gray-50">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#0064a8] hover:bg-gray-50">Register</a>
                @endauth
            </div>
        </div>
</nav>

<!-- Cart Notification Popup -->
<div id="cartNotification" class="hidden fixed top-20 right-4 bg-white rounded-lg shadow-lg w-64 z-50 border-l-4 border-green-500 p-3 text-xs">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="cartNotificationMessage" class="text-gray-700">Product added to cart!</span>
    </div>
</div>

<script>
    // Function to show cart notification (defined globally)
    function showCartNotification(message) {
        const notification = document.getElementById('cartNotification');
        if (!notification) return; // Guard clause
        const messageElement = document.getElementById('cartNotificationMessage');
        if (!messageElement) return; // Guard clause

        // Set message
        messageElement.textContent = message;

        // Show notification
        notification.classList.remove('hidden');
        // Removed animation classes for simplicity and potential conflicts

        // Hide after 3 seconds
        setTimeout(function() {
            notification.classList.add('hidden');
        }, 3000);
    }
</script>

{{-- Conditionally add script to call the notification function on DOMContentLoaded --}}
@if(session('success') && str_contains(session('success'), 'cart'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showCartNotification("{{ addslashes(session('success')) }}");
    });
</script>
@endif