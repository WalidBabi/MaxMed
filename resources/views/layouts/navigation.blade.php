<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:border-gray-700 shadow-lg sticky top-0 z-50">
    <!-- Top Bar with Contact Info - Redesigned -->
    <div class="bg-gradient-to-r from-[#0a3369] to-[#0a5694] text-white py-2.5 px-4 text-sm hidden sm:block">
        <div class="container mx-auto flex flex-wrap justify-between items-center">
            <div class="flex items-center space-x-6">
                <a href="mailto:sales@maxmedme.com" class="flex items-center hover:text-blue-200 transition duration-300 group">
                    <div class="bg-white/10 rounded-full p-1.5 mr-2 group-hover:bg-white/20 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    sales@maxmedme.com
                </a>
                <a href="tel:+97155460250" class="flex items-center hover:text-blue-200 transition duration-300 group">
                    <div class="bg-white/10 rounded-full p-1.5 mr-2 group-hover:bg-white/20 transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    +971 55 460 2500
                </a>
            </div>
            <div class="flex space-x-4">
                <a href="https://www.linkedin.com/company/maxmed-me/about/?viewAsMember=true" class="transition duration-300 bg-white/10 hover:bg-white/20 rounded-full p-1.5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                </a>
                <a href="#" class="transition duration-300 bg-white/10 hover:bg-white/20 rounded-full p-1.5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                    </svg>
                </a>
                <a href="#" class="transition duration-300 bg-white/10 hover:bg-white/20 rounded-full p-1.5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation - Improved -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <style>
            /* Cart notification animations */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-10px); }
            }
            
            .animate__animated { animation-duration: 0.5s; }
            .animate__fadeIn { animation-name: fadeIn; }
            .animate__fadeOut { animation-name: fadeOut; }
            
            #cartNotification {
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            
            /* Navigation styling - Updated */
            .nav-item {
                position: relative;
                margin: 0 0.75rem;
            }
            
            .nav-link {
                color: #4B5563;
                font-weight: 500;
                padding: 1.75rem 0.75rem;
                transition: all 0.3s ease;
                position: relative;
                letter-spacing: 0.01em;
            }
            
            .nav-link:hover {
                color: #0a5694;
            }
            
            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 3px;
                background: linear-gradient(to right, #0a5694, #171e60);
                transition: width 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
                border-top-left-radius: 3px;
                border-top-right-radius: 3px;
            }
            
            .nav-link:hover::after {
                width: 100%;
            }
            
            .nav-link.active {
                color: #0a5694;
                font-weight: 600;
            }
            
            .nav-link.active::after {
                width: 100%;
                height: 4px;
            }
            
            /* Cart Icon Styling - Enhanced */
            .cart-icon-container {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                background-color: rgba(10, 86, 148, 0.1);
                transition: all 0.3s ease;
            }
            
            .cart-icon-container:hover {
                transform: translateY(-3px);
                background-color: rgba(10, 86, 148, 0.2);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
            
            .cart-icon-container svg {
                color: #0a5694;
            }
            
            /* User menu styling - Improved */
            .user-menu-button {
                background-color: rgba(10, 86, 148, 0.1);
                border: 1px solid rgba(10, 86, 148, 0.2) !important;
                transition: all 0.3s ease;
                color: #4B5563 !important;
                padding: 0.5rem 1rem !important;
                border-radius: 0.5rem !important;
            }
            
            .user-menu-button:hover {
                background-color: rgba(10, 86, 148, 0.2);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }
            
            /* Styled action buttons */
            .action-button {
                background: linear-gradient(to right, #0a5694, #171e60);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .action-button:hover {
                background: linear-gradient(to right, #0c62a6, #1d2575);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(10, 86, 148, 0.3);
            }
            
            .action-button::after {
                content: '';
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: -100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: all 0.6s ease;
            }
            
            .action-button:hover::after {
                left: 100%;
            }
            
            /* Search styling - Enhanced */
            .search-container {
                position: relative;
                max-width: 280px;
            }
            
            .search-input {
                width: 100%;
                padding: 0.625rem 2.5rem 0.625rem 1.25rem;
                border: 1px solid #E5E7EB;
                border-radius: 9999px;
                font-size: 0.875rem;
                transition: all 0.3s ease;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            
            .search-input:focus {
                outline: none;
                border-color: #0a5694;
                box-shadow: 0 0 0 3px rgba(10, 86, 148, 0.1);
            }
            
            .search-button {
                position: absolute;
                right: 0.5rem;
                top: 50%;
                transform: translateY(-50%);
                background: linear-gradient(to right, #0a5694, #171e60);
                color: white;
                width: 1.75rem;
                height: 1.75rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            
            .search-button:hover {
                transform: translateY(-50%) scale(1.05);
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            
            /* Mobile menu - Enhanced */
            .mobile-menu-container {
                background-color: white;
                border-top: 1px solid #E5E7EB;
            }
            
            .mobile-nav-link {
                display: block;
                padding: 0.875rem 1.25rem;
                font-size: 1.1rem;
                transition: all 0.3s ease;
            }
            
            .mobile-nav-link:hover {
                background-color: rgba(10, 86, 148, 0.05);
                color: #0a5694;
                border-left-color: #0a5694;
            }
            
            .mobile-nav-link.active {
                background-color: rgba(10, 86, 148, 0.1);
                border-left: 4px solid #0a5694;
            }
            
            .mobile-menu-container {
                max-height: 80vh;
                overflow-y: auto;
            }
        </style>
        <div class="flex justify-between h-22">
            <div class="flex items-center">
                <!-- Logo - Enhanced spacing -->
                <div class="shrink-0 flex items-center py-4">
                    <a href="{{ route('welcome') }}" class="transition hover:opacity-90">
                        <img src="{{ asset('Images/logo.png') }}" alt="MaxMed Logo" class="block h-[46px] w-auto">
                    </a>
                </div>

                <!-- Navigation Links - Improved spacing -->
                <div class="hidden sm:flex sm:space-x-6 sm:ml-16">
                    <div class="nav-item">
                        <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')"
                            class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                            {{ __('Home') }}
                        </x-nav-link>
                    </div>
                    <div class="nav-item">
                        <x-nav-link :href="route('about')" :active="request()->routeIs('about')"
                            class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                            {{ __('About Us') }}
                        </x-nav-link>
                    </div>
                    <div class="nav-item group relative">
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products')"
                            class="nav-link {{ request()->routeIs('products') ? 'active' : '' }} flex items-center">
                            {{ __('Products') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </x-nav-link>
                        <div class="absolute left-0 mt-0 w-48 bg-white shadow-lg rounded-b-lg py-2 z-50 hidden group-hover:block transition-all duration-300 ease-in-out">
                            @foreach(\App\Models\Category::whereNull('parent_id')->take(8)->get() as $category)
                                @php
                                    $hasChildren = \App\Models\Category::where('parent_id', $category->id)->exists();
                                @endphp
                                <div class="relative group/subcategory">
                                    <a href="{{ route('categories.show', $category) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#0a5694] flex justify-between items-center">
                                        {{ $category->name }}
                                        @if($hasChildren)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                    </a>
                                    @if($hasChildren)
                                        <div class="absolute left-full top-0 w-48 bg-white shadow-lg rounded-lg py-2 z-50 hidden group-hover/subcategory:block">
                                            @foreach(\App\Models\Category::where('parent_id', $category->id)->get() as $subcategory)
                                                <a href="{{ route('categories.show', $subcategory) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#0a5694]">
                                                    {{ $subcategory->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            <div class="border-t border-gray-100 mt-2 pt-2">
                                <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm text-[#0a5694] font-medium hover:bg-gray-100">
                                    View All Categories →
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item">
                        <x-nav-link :href="route('partners.index')" :active="request()->routeIs('partners')"
                            class="nav-link {{ request()->routeIs('partners') ? 'active' : '' }}">
                            {{ __('Partners') }}
                        </x-nav-link>
                    </div>
                    <div class="nav-item">
                        <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')"
                            class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                            {{ __('Contact Us') }}
                        </x-nav-link>
                    </div>
                    <div class="nav-item">
                        <x-nav-link :href="route('news.index')" :active="request()->routeIs('news')"
                            class="nav-link {{ request()->routeIs('news') ? 'active' : '' }}">
                            {{ __('News') }}
                        </x-nav-link>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-8">
                <!-- Search Bar (Desktop) - Refined -->
                <div class="hidden sm:block search-container">
                    <form action="{{ route('search') }}" method="GET">
                        <input type="text" name="query" placeholder="Search products..." 
                               class="search-input"
                               value="{{ request('query') }}">
                        <button type="submit" class="search-button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Cart Icon - Enhanced animation -->
                <a href="{{ route('cart.view') }}" class="transition-colors duration-300">
                    <div class="cart-icon-container relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                            {{ $cartQuantity }}
                        </span>
                        @endif
                        
                        <!-- Cart Notification Popup -->
                        <div id="cartNotification" class="hidden absolute right-0 top-12 bg-white rounded-lg shadow-lg w-64 z-50 border-l-4 border-green-500 p-3 text-sm">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span id="cartNotificationMessage" class="text-gray-700">Product added to cart!</span>
                            </div>
                        </div>
                    </div>
                </a>

                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="user-menu-button inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-transparent focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            @auth
                            @if(Auth::user()->is_admin)
                            <x-dropdown-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                                class="hover:text-[#0a5694]">
                                {{ __('Admin Dashboard') }}
                            </x-dropdown-link>
                            @endif
                            @endauth

                            <x-dropdown-link :href="route('orders.index')" :active="request()->routeIs('orders.index')"
                                class="hover:text-[#0a5694]">
                                {{ __('My Orders') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div class="hidden sm:flex space-x-3">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-[#0a5694] px-4 py-2.5 rounded-lg transition-colors duration-300 whitespace-nowrap border border-gray-200 hover:border-[#0a5694] bg-white hover:bg-gray-50 shadow-sm hover:shadow">Sign In</a>
                    <a href="{{ route('register') }}"
                        class="action-button text-white px-4 py-2.5 rounded-lg whitespace-nowrap shadow-sm hover:shadow">
                        Sign Up
                    </a>
                </div>
                @endauth

                <!-- Hamburger - Refined touch target -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2.5 rounded-md text-gray-500 hover:text-[#0a5694] hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu - Slightly redesigned -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden mobile-menu-container">
        <div class="pt-3 pb-4 space-y-1.5">
            <!-- Responsive Search Bar -->
            <div class="px-4 py-2">
                <form action="{{ route('search') }}" method="GET" class="flex">
                    <input type="text" name="query" placeholder="Search products..."
                        class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-[#0a5694] focus:ring focus:ring-[#0a5694] focus:ring-opacity-50 text-sm"
                        value="{{ request('query') }}">
                    <button type="submit" class="action-button text-white px-3 py-2 rounded-r-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <x-responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')" 
                                   class="mobile-nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')"
                                   class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                {{ __('About Us') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products')"
                                   class="mobile-nav-link {{ request()->routeIs('products') ? 'active' : '' }}">
                {{ __('Products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')"
                                   class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                {{ __('Contact Us') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('partners.index')" :active="request()->routeIs('partners')"
                                   class="mobile-nav-link {{ request()->routeIs('partners') ? 'active' : '' }}">
                {{ __('Partners') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('news.index')" :active="request()->routeIs('news')"
                                   class="mobile-nav-link {{ request()->routeIs('news') ? 'active' : '' }}">
                {{ __('News') }}
            </x-responsive-nav-link>
            <!-- Responsive Cart Link -->
            <x-responsive-nav-link :href="route('cart.view')"
                                   class="mobile-nav-link">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#0a5694]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Cart') }}
                    @if($cartQuantity > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $cartQuantity }}
                    </span>
                    @endif
                </div>
            </x-responsive-nav-link>
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    
                    @if(Auth::user()->is_admin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" 
                                          class="mobile-nav-link">
                        {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>
                    @endif
                    
                    <x-responsive-nav-link :href="route('orders.index')"
                                          class="mobile-nav-link">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                            this.closest('form').submit();" 
                                          class="mobile-nav-link">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('login')" 
                                      class="mobile-nav-link">
                    {{ __('Sign In') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')"
                                      class="mobile-nav-link">
                    {{ __('Sign Up') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for success message in session
        @if(session('success') && str_contains(session('success'), 'cart'))
            showCartNotification("{{ session('success') }}");
        @endif
        
        // Function to show cart notification
        function showCartNotification(message) {
            const notification = document.getElementById('cartNotification');
            const messageElement = document.getElementById('cartNotificationMessage');
            
            // Set message
            messageElement.textContent = message;
            
            // Show notification
            notification.classList.remove('hidden');
            notification.classList.add('animate__animated', 'animate__fadeIn');
            
            // Hide after 3 seconds
            setTimeout(function() {
                notification.classList.add('animate__fadeOut');
                setTimeout(function() {
                    notification.classList.add('hidden');
                    notification.classList.remove('animate__animated', 'animate__fadeIn', 'animate__fadeOut');
                }, 500);
            }, 3000);
        }
    });
</script>