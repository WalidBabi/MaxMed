<nav x-data="{ open: false }" x-cloak class="bg-white border-b border-gray-100 dark:border-gray-700 shadow-sm mt-0">
    <style>
        [x-cloak] { display: none !important; }
        
        /* Ensure the navigation bar is only shown when Alpine.js is fully initialized */
        nav.initialized {
            display: block !important;
            margin-top: 0 !important;
        }
        
        /* Remove all top margins/padding that might cause spacing */
        nav {
            margin-top: 0 !important; 
            padding-top: 0 !important;
            top: 0 !important;
        }
        
        /* Add a placeholder during load to prevent layout shift */
        body::before {
            display: none; /* Disable the placeholder */
        }
    </style>
    <!-- Single Bar with Logo, Links, Search and Navigation -->
    <div class="bg-white py-2" x-init="$el.closest('nav').classList.add('initialized')">
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
                        <div class="relative w-full">
                            <input type="text" name="query" id="desktop-search-input" placeholder="Search product names or codes"
                                class="w-full py-2 pl-4 bg-gray-100 border-none rounded-l-full focus:outline-none text-sm"
                                value="{{ request('query') }}" autocomplete="off">
                            <div id="desktop-search-suggestions" class="absolute z-50 w-full bg-white mt-1 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <button type="submit" aria-label="Search products" class="bg-gradient-to-r from-[#171e60] to-[#0a5694] text-white p-2.5 rounded-lg hover:from-[#0a5694] hover:to-[#171e60] transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#171e60] focus:ring-opacity-50 shadow-md">
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
                        <a href="{{ route('partners.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">Partners</a>

                        <!-- Products Dropdown -->
                        <div class="relative inline-block" 
                            x-data="{ 
                                open: false, 
                                activeSubmenu: null, 
                                activeSubSubmenu: null
                            }" 
                            @click.away="open = false; activeSubmenu = null; activeSubSubmenu = null"
                            x-init="$nextTick(() => { 
                                // Initialize dropdown safely after Alpine is fully loaded
                                // This helps prevent flickering on page loads/navigation
                                setTimeout(() => { 
                                    // Delay initialization to ensure smooth operation
                                }, 50);
                            })">
                            <button @click="open = !open" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">
                                <span class="whitespace-nowrap">Products</span>
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="fixed left-0 right-0 mt-2 bg-white shadow-lg py-2 z-50">
                                <div class="container mx-auto px-4">
                                    <div class="flex">
                                        <!-- First Level Menu -->
                                        <div class="w-64 border-r border-gray-100">
                                            @foreach($navCategories->sortBy(function($category) {
                                                // Define the preferred order
                                                $order = [
                                                    'Molecular & Clinical Diagnostics' => 1,
                                                    'Life Science & Research' => 2,
                                                    'Lab Equipment' => 3,
                                                    'Medical Consumables' => 4,
                                                    'Lab Consumables' => 5
                                                ];
                                                return $order[$category->name] ?? 999; // Categories not in list will appear last
                                            }) as $category)
                                                <div class="relative" x-data="{ id: {{ $category->id }} }">
                                                    <button @click="if (activeSubmenu !== id) { activeSubmenu = id; activeSubSubmenu = null; } else { activeSubmenu = null; }" 
                                                        class="w-full text-left px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 flex justify-between items-center">
                                                        {{ $category->name }}
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100">
                                                View All Products
                                            </a>
                                        </div>

                                        <!-- Second Level Menu -->
                                        <div x-show="activeSubmenu !== null" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0"
                                            x-transition:enter-end="transform opacity-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100"
                                            x-transition:leave-end="transform opacity-0"
                                            class="w-64 border-r border-gray-100">
                                            @foreach($navCategories as $category)
                                                <div x-show="activeSubmenu === {{ $category->id }}">
                                                    <a href="{{ route('categories.show', $category) }}" class="block px-4 py-2 text-sm font-medium text-gray-900 border-b border-gray-100">
                                                        All {{ $category->name }}
                                                    </a>
                                                    @foreach($category->subcategories as $subcategory)
                                                        @if($subcategory->subcategories->isNotEmpty())
                                                            <div class="relative" x-data="{ subId: {{ $subcategory->id }} }">
                                                                <button @click="if (activeSubSubmenu !== subId) { activeSubSubmenu = subId; } else { activeSubSubmenu = null; }"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex justify-between items-center">
                                                                    {{ $subcategory->name }}
                                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <a href="{{ route('categories.subcategory.show', [$category, $subcategory]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                {{ $subcategory->name }}
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Third Level Menu -->
                                        <div x-show="activeSubSubmenu !== null"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0"
                                            x-transition:enter-end="transform opacity-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100"
                                            x-transition:leave-end="transform opacity-0"
                                            class="w-64">
                                            @foreach($navCategories as $category)
                                                @foreach($category->subcategories as $subcategory)
                                                    @if($subcategory->subcategories->isNotEmpty())
                                                        <div x-show="activeSubSubmenu === {{ $subcategory->id }}">
                                                            <a href="{{ route('categories.subcategory.show', [$category, $subcategory]) }}" class="block px-4 py-2 text-sm font-medium text-gray-900 border-b border-gray-100">
                                                                All {{ $subcategory->name }}
                                                            </a>
                                                            @foreach($subcategory->subcategories as $subsubcategory)
                                                                <a href="{{ route('categories.subsubcategory.show', [$category, $subcategory, $subsubcategory]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    {{ $subsubcategory->name }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Replace with simple clickable link -->
                        <a href="{{ route('industry.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full whitespace-nowrap">Industries & Solutions</a>
                        
                        <!-- <a href="{{ route('partners.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">Partners</a> -->
                        <a href="{{ route('news.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">News</a>
                        <a href="{{ route('contact') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-normal h-full">Contact</a>
                        @auth
                        <div class="relative inline-block" x-data="{ open: false }" x-cloak @click.away="open = false">
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 flex items-center font-normal">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                @elseif(Auth::user()->hasPermission('supplier.products.view'))
                                <a href="{{ route('supplier.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier Dashboard</a>
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
                        <input type="text" name="query" id="mobile-search-input" placeholder="Search product names, codes or CAS number"
                            class="w-full py-2 pl-4 pr-12 bg-gray-200 border-none rounded-full focus:outline-none text-sm"
                            value="{{ request('query') }}" autocomplete="off">
                        <div id="mobile-search-suggestions" class="absolute z-50 w-full bg-white mt-1 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        <button type="submit" aria-label="Search products" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-[#0064a8] text-white p-2 rounded-full hover:bg-[#0052a3] focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <a href="{{ route('welcome') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Home</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">About Us</a>
                <a href="/partners" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Partners</a>

                <!-- Mobile Products Dropdown -->
                <div x-data="{ open: false }" x-cloak class="relative">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                        <span class="whitespace-nowrap">Products</span>
                        <svg :class="{'rotate-180': open}" class="ml-2 h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0"
                        x-transition:enter-end="transform opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="transform opacity-100"
                        x-transition:leave-end="transform opacity-0"
                        class="pl-4 space-y-1 mt-1">
                        @foreach($navCategories->sortBy(function($category) {
                            // Define the preferred order
                            $order = [
                                'Molecular & Clinical Diagnostics' => 1,
                                'Life Science & Research' => 2,
                                'Lab Equipment' => 3,
                                'Medical Consumables' => 4,
                                'Lab Consumables' => 5
                            ];
                            return $order[$category->name] ?? 999; // Categories not in list will appear last
                        }) as $category)
                            @if($category->subcategories->isNotEmpty())
                                <div x-data="{ subOpen: false }" x-cloak class="relative">
                                    <div class="flex items-start">
                                        <a href="{{ route('categories.show', $category) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 flex-grow">
                                            {{ $category->name }}
                                        </a>
                                        <button @click.prevent="subOpen = !subOpen" class="px-2 py-2 text-gray-500 hover:text-[#00a9e0] focus:outline-none">
                                            <svg :class="{'rotate-90': subOpen}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="subOpen" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="transform opacity-0"
                                        x-transition:enter-end="transform opacity-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="transform opacity-100"
                                        x-transition:leave-end="transform opacity-0"
                                        class="pl-4 space-y-1 mt-1">
                                        @foreach($category->subcategories as $subcategory)
                                            <a href="{{ route('categories.subcategory.show', [$category, $subcategory]) }}" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                                {{ $subcategory->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('categories.show', $category) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    {{ $category->name }}
                                </a>
                            @endif
                        @endforeach
                        <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-gray-50">
                            View All Products
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Who We Serve Dropdown -->
                <div x-data="{ open: false }" x-cloak class="relative">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                        <span class="whitespace-nowrap">Industries & Solutions</span>
                        <svg :class="{'rotate-180': open}" class="ml-2 h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0"
                        x-transition:enter-end="transform opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="transform opacity-100"
                        x-transition:leave-end="transform opacity-0"
                        class="pl-4 space-y-1 mt-1">
                        <!-- Healthcare & Medical Facilities -->
                        <div x-data="{ subOpen: false }" x-cloak class="relative">
                            <div class="flex items-start">
                                <a href="{{ route('industry.index') }}#healthcare" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 flex-grow">
                                    Medical & Healthcare
                                </a>
                                <button @click.prevent="subOpen = !subOpen" class="px-2 py-2 text-gray-500 hover:text-[#00a9e0] focus:outline-none">
                                    <svg :class="{'rotate-90': subOpen}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="subOpen" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0"
                                x-transition:enter-end="transform opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100"
                                x-transition:leave-end="transform opacity-0"
                                class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('industry.index') }}#clinics" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Clinics & Medical Centers
                                </a>
                                <a href="{{ route('industry.index') }}#hospitals" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Hospitals
                                </a>
                                <a href="{{ route('industry.index') }}#veterinary" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Veterinary Clinics
                                </a>
                                <a href="{{ route('industry.index') }}#medical-labs" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Medical Laboratories
                                </a>
                            </div>
                        </div>
                        
                        <!-- Scientific & Research Institutions -->
                        <div x-data="{ subOpen: false }" x-cloak class="relative">
                            <div class="flex items-start">
                                <a href="{{ route('industry.index') }}#research" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 flex-grow">
                                    Research & Development
                                </a>
                                <button @click.prevent="subOpen = !subOpen" class="px-2 py-2 text-gray-500 hover:text-[#00a9e0] focus:outline-none">
                                    <svg :class="{'rotate-90': subOpen}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="subOpen" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0"
                                x-transition:enter-end="transform opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100"
                                x-transition:leave-end="transform opacity-0"
                                class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('industry.index') }}#research-labs" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Research Laboratories
                                </a>
                                <a href="{{ route('industry.index') }}#academia" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Universities & Academia
                                </a>
                                <a href="{{ route('industry.index') }}#biotech" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Biotech & Pharmaceutical Industries
                                </a>
                                <a href="{{ route('industry.index') }}#forensic" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Forensic Laboratories
                                </a>
                            </div>
                        </div>
                        
                        <!-- Government & Regulatory Bodies -->
                        <div x-data="{ subOpen: false }" x-cloak class="relative">
                            <div class="flex items-start">
                                <a href="{{ route('industry.index') }}#government" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 flex-grow">
                                    Public Sector & Regulation
                                </a>
                                <button @click.prevent="subOpen = !subOpen" class="px-2 py-2 text-gray-500 hover:text-[#00a9e0] focus:outline-none">
                                    <svg :class="{'rotate-90': subOpen}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="subOpen" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0"
                                x-transition:enter-end="transform opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100"
                                x-transition:leave-end="transform opacity-0"
                                class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('industry.index') }}#public-health" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Public Health Institutions
                                </a>
                                <a href="{{ route('industry.index') }}#military" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Military & Defense Research Centers
                                </a>
                                <a href="{{ route('industry.index') }}#regulatory" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Health Ministries & Regulatory Agencies
                                </a>
                            </div>
                        </div>
                        
                        <!-- Specialized Testing & Diagnostics -->
                        <div x-data="{ subOpen: false }" x-cloak class="relative">
                            <div class="flex items-start">
                                <a href="{{ route('industry.index') }}#testing" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 flex-grow">
                                    Testing & Analysis
                                </a>
                                <button @click.prevent="subOpen = !subOpen" class="px-2 py-2 text-gray-500 hover:text-[#00a9e0] focus:outline-none">
                                    <svg :class="{'rotate-90': subOpen}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="subOpen" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0"
                                x-transition:enter-end="transform opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100"
                                x-transition:leave-end="transform opacity-0"
                                class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('industry.index') }}#environment" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Environment Laboratories
                                </a>
                                <a href="{{ route('industry.index') }}#food" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Food Laboratories
                                </a>
                                <a href="{{ route('industry.index') }}#material" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Material Testing Laboratories
                                </a>
                                <a href="{{ route('industry.index') }}#cosmetic" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Cosmetic & Dermatology Labs
                                </a>
                            </div>
                        </div>
                        
                        <!-- Emerging & AI-driven Healthcare -->
                        <div x-data="{ subOpen: false }" x-cloak class="relative">
                            <div class="flex items-start">
                                <a href="{{ route('industry.index') }}#technology" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 flex-grow">
                                    Emerging & AI-driven Healthcare
                                </a>
                                <button @click.prevent="subOpen = !subOpen" class="px-2 py-2 text-gray-500 hover:text-[#00a9e0] focus:outline-none">
                                    <svg :class="{'rotate-90': subOpen}" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="subOpen" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0"
                                x-transition:enter-end="transform opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100"
                                x-transition:leave-end="transform opacity-0"
                                class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('industry.index') }}#telemedicine" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    Telemedicine & Remote Diagnostics
                                </a>
                                <a href="{{ route('industry.index') }}#ai-medical" class="block px-3 py-2 rounded-md text-xs font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">
                                    AI-powered Medical Technology Firms
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Replace with simple clickable link -->
                <!-- <a href="{{ route('industry.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Industries & Solutions</a> -->

                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Contact Us</a>
                <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">News</a>

                @auth
                @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Admin Dashboard</a>
                @elseif(Auth::user()->hasPermission('supplier.products.view'))
                <a href="{{ route('supplier.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Supplier Dashboard</a>
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

{{-- Add autocomplete functionality script before the closing </nav> tag --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Page transition to prevent navbar flashing during navigation
        const links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript"]):not([href^="mailto"])');
        
        // Apply to internal links that navigate to other pages
        links.forEach(link => {
            if (link.hostname === window.location.hostname) {
                link.addEventListener('click', function(e) {
                    // Skip if user is pressing modifier keys
                    if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                    
                    // Don't apply to download links or links with data-no-transition
                    if (link.hasAttribute('download') || link.hasAttribute('data-no-transition')) return;
                    
                    const href = link.getAttribute('href');
                    if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                    
                    // Store current scroll position in session storage
                    sessionStorage.setItem('scrollPos', window.scrollY);
                    
                    // Don't hide the navbar during the transition to prevent flashing
                    document.querySelector('nav').style.opacity = '1';
                    document.querySelector('nav').style.transition = 'none';
                });
            }
        });
        
        // Search autocomplete functionality
        const setupSearchAutocomplete = (inputId, suggestionsId) => {
            const searchInput = document.getElementById(inputId);
            const suggestionsContainer = document.getElementById(suggestionsId);
            
            if (!searchInput || !suggestionsContainer) return;
            
            let debounceTimer;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    suggestionsContainer.classList.add('hidden');
                    return;
                }
                
                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('search.suggestions') }}?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            suggestionsContainer.innerHTML = '';
                            
                            if (data.completions && data.completions.length > 0) {
                                data.completions.forEach(suggestion => {
                                    const suggestionElement = document.createElement('div');
                                    suggestionElement.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                                    suggestionElement.textContent = suggestion;
                                    
                                    suggestionElement.addEventListener('click', () => {
                                        searchInput.value = suggestion;
                                        suggestionsContainer.classList.add('hidden');
                                        // Submit the parent form
                                        searchInput.closest('form').submit();
                                    });
                                    
                                    suggestionsContainer.appendChild(suggestionElement);
                                });
                                
                                suggestionsContainer.classList.remove('hidden');
                            } else {
                                suggestionsContainer.classList.add('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching search suggestions:', error);
                            suggestionsContainer.classList.add('hidden');
                        });
                }, 300);
            });
            
            // Close suggestions when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
                    suggestionsContainer.classList.add('hidden');
                }
            });
            
            // Navigate through suggestions with keyboard
            searchInput.addEventListener('keydown', function(e) {
                if (suggestionsContainer.classList.contains('hidden')) return;
                
                const suggestions = suggestionsContainer.querySelectorAll('div');
                if (suggestions.length === 0) return;
                
                let activeIndex = Array.from(suggestions).findIndex(el => el.classList.contains('bg-gray-200'));
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (activeIndex === -1 || activeIndex === suggestions.length - 1) {
                        activeIndex = 0;
                    } else {
                        activeIndex++;
                    }
                    updateActiveSuggestion(suggestions, activeIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (activeIndex === -1 || activeIndex === 0) {
                        activeIndex = suggestions.length - 1;
                    } else {
                        activeIndex--;
                    }
                    updateActiveSuggestion(suggestions, activeIndex);
                } else if (e.key === 'Enter' && activeIndex !== -1) {
                    e.preventDefault();
                    searchInput.value = suggestions[activeIndex].textContent;
                    suggestionsContainer.classList.add('hidden');
                    searchInput.closest('form').submit();
                } else if (e.key === 'Escape') {
                    suggestionsContainer.classList.add('hidden');
                }
            });
            
            function updateActiveSuggestion(suggestions, activeIndex) {
                suggestions.forEach(s => s.classList.remove('bg-gray-200'));
                suggestions[activeIndex].classList.add('bg-gray-200');
                suggestions[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        };
        
        // Initialize autocomplete for both desktop and mobile search inputs
        setupSearchAutocomplete('desktop-search-input', 'desktop-search-suggestions');
        setupSearchAutocomplete('mobile-search-input', 'mobile-search-suggestions');
    });
</script>