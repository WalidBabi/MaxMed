<!-- Google One Tap Sign In -->
@guest
    <div id="g_id_onload"
        data-client_id="{{ config('services.google.client_id') }}"
        data-callback="handleCredentialResponse"
        data-auto_prompt="false"
        data-context="signin"
        data-ux_mode="popup"
        data-itp_support="true">
    </div>
@endguest

<nav x-data="{ open: false }" x-cloak class="bg-white border-b border-gray-100 shadow-sm mt-0 sticky top-0 z-50" x-init="$nextTick(() => { $el.classList.add('initialized'); })">
    <style>
        [x-cloak] { display: none !important; }
        
        /* Ensure the navigation bar is only shown when Alpine.js is fully initialized */
        nav.initialized {
            display: block !important;
            margin-top: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Remove all top margins/padding that might cause spacing */
        nav {
            margin-top: 0 !important; 
            padding-top: 0 !important;
            top: 0 !important;
            transition: opacity 0.2s ease-in-out;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Show navigation once Alpine is ready */
        .alpine-ready nav {
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Add a placeholder during load to prevent layout shift */
        body::before {
            display: none; /* Disable the placeholder */
        }
        
        /* Search dropdown improvements */
        #desktop-search-suggestions,
        #mobile-search-suggestions {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            z-index: 9999;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.25rem;
            max-height: 300px;
            overflow-y: auto;
        }
        
        /* Ensure search input has proper focus states */
        #desktop-search-input:focus,
        #mobile-search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 86, 148, 0.1);
        }
        
        /* Improve search button styling */
        .search-button {
            transition: all 0.2s ease-in-out;
        }
        
        .search-button:hover {
            transform: scale(1.05);
        }
        
        /* Search suggestions styling */
        .search-suggestion-item {
            display: flex;
            align-items: center;
            padding: 1.1rem 1.25rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.15s ease-in-out;
            font-size: 1.15rem;
            line-height: 1.5rem;
            min-height: 64px;
        }
        
        .search-suggestion-item img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .search-suggestion-item span {
            font-size: 1.1rem;
            font-weight: 500;
            color: #1e293b;
            white-space: normal;
            word-break: break-word;
        }
        
        .search-suggestion-item:hover {
            background-color: #f9fafb;
        }
        
        .search-suggestion-item:last-child {
            border-bottom: none;
        }
        
        .search-suggestion-item.active {
            background-color: #eff6ff;
            color: #1e40af;
        }
        
        /* Ensure proper positioning for search container */
        .search-container {
            position: relative;
        }
        
        /* Improve scrollbar for suggestions */
        #desktop-search-suggestions::-webkit-scrollbar,
        #mobile-search-suggestions::-webkit-scrollbar {
            width: 6px;
        }
        
        #desktop-search-suggestions::-webkit-scrollbar-track,
        #mobile-search-suggestions::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        #desktop-search-suggestions::-webkit-scrollbar-thumb,
        #mobile-search-suggestions::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        #desktop-search-suggestions::-webkit-scrollbar-thumb:hover,
        #mobile-search-suggestions::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile-specific search improvements */
        @media (max-width: 768px) {
            #mobile-search-suggestions-top {
                position: fixed;
                top: auto;
                left: 1rem;
                right: 1rem;
                margin-top: 0.5rem;
                max-height: 200px;
                z-index: 10000;
            }
            
            .search-container {
                position: relative;
                z-index: 1000;
            }
            
            /* Mobile search bar styling */
            #mobile-search-input-top {
                font-size: 16px; /* Prevents zoom on iOS */
                min-height: 48px;
                padding: 12px 16px 12px 48px;
            }
            
            #mobile-search-input-top::placeholder {
                font-size: 16px;
                color: #6b7280;
            }
            
                    /* Mobile search button positioning */
        #mobile-search-input-top + .search-button {
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            padding: 0;
        }
        
        /* Enhanced mobile search container */
        .md\\:hidden.px-4.py-3.bg-white.border-b.border-gray-100 {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
        }
        
        /* Mobile search input focus state */
        #mobile-search-input-top:focus {
            background-color: white;
            box-shadow: 0 0 0 3px rgba(10, 86, 148, 0.1);
            border: 1px solid #0a5694;
        }
        
        /* Mobile search suggestions enhancement */
        #mobile-search-suggestions-top .search-suggestion-item {
            padding: 12px 16px;
            min-height: 56px;
            font-size: 14px;
        }
        
        #mobile-search-suggestions-top .search-suggestion-item img {
            width: 48px;
            height: 48px;
        }
        }

        /* Ensure search input placeholder is fully visible and uses ellipsis if too long */
        #desktop-search-input::placeholder,
        #mobile-search-input::placeholder {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.85rem;
            color: #64748b;
            opacity: 1;
            vertical-align: middle;
        }

        /* Make sure the input is wide enough for the placeholder */
        #desktop-search-input {
            min-width: 0;
            width: 100%;
            max-width: 100%;
        }
        /* Make the search input smaller and neater */
        #desktop-search-input, #mobile-search-input {
            height: 40px;
            font-size: 0.95rem;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            border-radius: 9999px 0 0 9999px;
        }
        .search-button {
            height: 40px;
            min-width: 40px;
            border-radius: 0 9999px 9999px 0;
            padding: 0 16px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes search-glow {
  0% { box-shadow: 0 0 0 0 rgba(10,86,148,0.35); border-radius: 9999px; }
  50% { box-shadow: 0 0 24px 12px rgba(10,86,148,0.45); border-radius: 9999px; }
  100% { box-shadow: 0 0 0 0 rgba(10,86,148,0.0); border-radius: 9999px; }
}
.search-bar-glow {
  animation: search-glow 4s ease-in-out infinite;
  border-radius: 9999px;
}
    </style>
    
    <!-- Initialize Alpine.js components without delays -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('navigation', () => ({
                open: false,
                activeSubmenu: null,
                activeSubSubmenu: null,
                
                init() {
                    // Mark navigation as initialized immediately
                    this.$el.closest('nav').classList.add('initialized');
                }
            }));
        });
    </script>
    
    <!-- Single Bar with Logo, Links, Search and Navigation -->
    <div class="bg-white py-2" x-init="$el.closest('nav').classList.add('initialized')">
        <div class="container mx-auto px-4 sm:px-6 lg:px-12">
            <!-- Top section with Logo and Links -->
            <div class="flex justify-between items-center">
                <!-- Logo - Reduced size to give more space to search -->
                <div class="flex justify-center py-1 flex-shrink-0">
                    <a href="{{ route('welcome') }}" class="transition hover:opacity-90 flex-shrink-0">
                        <img src="{{ asset('Images/logo.png') }}" alt="MaxMed Logo" class="block h-[50px] w-[200px] mr-4 min-h-[50px] min-w-[200px] flex-shrink-0 !important">
                    </a>
                </div>
                
                <!-- Search Bar - Increased width and improved positioning -->
                <div class="hidden md:block mx-4 flex-1 max-w-2xl">
                    <form action="{{ route('search') }}" method="GET" class="flex items-center mb-0">
                        <div class="search-container relative w-full search-bar-glow">
                            <input type="text" name="query" id="desktop-search-input" placeholder="Search products..."
                                class="w-full py-2 pl-4 pr-12 bg-gray-100 border-none rounded-l-full focus:outline-none text-sm focus:ring-2 focus:ring-[#0a5694] focus:ring-opacity-50"
                                value="{{ request('query') }}" autocomplete="off">
                            <div id="desktop-search-suggestions" class="absolute z-50 w-full bg-white rounded-lg shadow-lg hidden max-h-60 overflow-y-auto border border-gray-200 min-w-[300px]"></div>
                        </div>
                        <button type="submit" aria-label="Search products" class="search-button bg-gradient-to-r from-[#171e60] to-[#0a5694] text-white p-3 rounded-r-full hover:from-[#0a5694] hover:to-[#171e60] transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#171e60] focus:ring-opacity-50 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Navigation items - Reduced spacing to accommodate search -->
                <div class="hidden md:flex items-center space-x-4">
               
                    <div class="flex items-center space-x-4 text-sm">
                
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
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                <a href="{{ route('crm.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CRM Dashboard</a>
                                @endif
                                @if(Auth::user()->hasPermission('supplier.products.view'))
                                <a href="{{ route('supplier.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier Dashboard</a>
                                @endif
                                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 group">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 75.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                        My Orders
                                    </div>
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center text-xs">
                            <a href="{{ route('login') }}" class="flex items-center text-gray-600 hover:text-[#0a5694] font-normal transition-colors duration-200 px-1 py-0.5">Login</a>
                            <div class="w-px h-6 bg-gray-300 mx-2"></div>
                            <div class="flex flex-col space-y-0.5">
                                <a href="{{ route('register') }}" class="flex items-center text-gray-600 hover:text-[#0a5694] font-normal transition-colors duration-200 px-1 py-0.5">Register</a>
                                <a href="{{ route('supplier.register') }}" class="flex items-center text-gray-600 hover:text-[#0a5694] font-normal transition-colors duration-200 px-1 py-0.5">Register as Supplier</a>
                            </div>
                        </div>
                        @endauth

                        <!-- Main Navigation Categories -->


                        <!-- Cart Icon -->
                        <!-- REMOVED: <a href="{{ route('cart.view') }}" class="flex items-center text-gray-600 hover:text-gray-900" aria-label="Shopping cart"> ... </a> -->
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile Search Bar - Always Visible -->
        <div class="md:hidden px-4 py-3 bg-white border-b border-gray-100">
            <form action="{{ route('search') }}" method="GET" class="search-container relative">
                <input type="text" name="query" id="mobile-search-input-top" placeholder="Search products..."
                    class="w-full py-3 pl-4 pr-12 bg-gray-100 border-none rounded-full focus:outline-none text-base focus:ring-2 focus:ring-[#0a5694] focus:ring-opacity-50"
                    value="{{ request('query') }}" autocomplete="off">
                <div id="mobile-search-suggestions-top" class="absolute z-50 w-full bg-white rounded-lg shadow-lg hidden max-h-60 overflow-y-auto border border-gray-200"></div>
                <button type="submit" aria-label="Search products" class="search-button absolute right-1 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-[#171e60] to-[#0a5694] text-white p-2.5 rounded-full hover:from-[#0a5694] hover:to-[#171e60] focus:outline-none transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center justify-between px-4 py-2 border-t border-gray-100">
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
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Admin Dashboard</a>
                <a href="{{ route('crm.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">CRM Dashboard</a>
                @endif
                @if(Auth::user()->hasPermission('supplier.products.view'))
                <a href="{{ route('supplier.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Supplier Dashboard</a>
                @endif
                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50 group">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 75.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        My Orders
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#00a9e0] hover:bg-gray-50">Log Out</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#0064a8] hover:bg-gray-50">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#0064a8] hover:bg-gray-50">Register</a>
                <a href="{{ route('supplier.register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-[#0064a8] hover:bg-gray-50">Register as Supplier</a>
                
                <!-- Mobile Google One Tap Sign In -->
                <div class="px-3 py-4 border-t border-gray-100 mt-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                        <div class="text-center mb-3">
                            <h4 class="text-sm font-medium text-gray-800 mb-1">Quick Sign In</h4>
                            <p class="text-xs text-gray-600">Sign in with Google for a faster experience</p>
                        </div>
                        <div class="flex justify-center">
                            <div class="g_id_signin"
                                data-type="standard"
                                data-size="large"
                                data-theme="outline"
                                data-text="sign_in_with"
                                data-shape="rectangular"
                                data-logo_alignment="left"
                                data-width="280">
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-xs text-gray-500">By continuing, you agree to our <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline">Terms</a> and <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline">Privacy Policy</a></small>
                        </div>
                    </div>
                </div>
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

<!-- Orders Hint Tooltip -->
@auth
<div id="ordersHintTooltip" class="hidden fixed top-16 right-4 bg-blue-600 text-white rounded-lg shadow-xl w-64 p-3 text-xs" style="z-index: 9999;">
    <div class="relative">
        <!-- Arrow pointing to user dropdown -->
        <div class="absolute -top-2 right-6 w-3 h-3 bg-blue-600 transform rotate-45"></div>
        
        <!-- Close button -->
        <button id="closeOrdersHint" class="absolute top-1 right-1 text-blue-200 hover:text-white focus:outline-none">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Content -->
        <div class="pr-4" style="position: relative; z-index: 10000;">
            <p class="text-blue-100 leading-snug">
                💡 Click your name above to find <strong>"My Orders"</strong> and track your purchases!
            </p>
            <button id="gotItBtn" class="mt-2 bg-white text-blue-600 px-2 py-1 rounded text-xs font-medium hover:bg-blue-50 transition-colors">
                Got it!
            </button>
        </div>
    </div>
</div>
@endauth

<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
</style>

<script>
    // Function to show cart notification
    function showCartNotification(message) {
        const notification = document.getElementById('cartNotification');
        if (!notification) return; // Guard clause
        const messageElement = document.getElementById('cartNotificationMessage');
        if (!messageElement) return; // Guard clause

        // Set message
        messageElement.textContent = message;

        // Show notification
        notification.classList.remove('hidden');

        // Hide after 3 seconds
        setTimeout(function() {
            notification.classList.add('hidden');
        }, 3000);
    }

    // Function to show orders hint tooltip (one-time only)
    function showOrdersHint() {
        const tooltip = document.getElementById('ordersHintTooltip');
        if (!tooltip) return;

        // Check if hint has been shown before for this user
        const userId = '{{ Auth::id() }}';
        const hintShown = localStorage.getItem('ordersHintShown_' + userId);
        if (hintShown === 'true') return;

        // Show tooltip after a short delay
        setTimeout(() => {
            tooltip.classList.remove('hidden');
            tooltip.style.animation = 'slideInRight 0.3s ease-out';
        }, 2000);

        // Set up close functionality
        const closeBtn = document.getElementById('closeOrdersHint');
        const gotItBtn = document.getElementById('gotItBtn');

        function hideHint() {
            tooltip.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                tooltip.classList.add('hidden');
            }, 300);
            localStorage.setItem('ordersHintShown_' + userId, 'true');
        }

        if (closeBtn) closeBtn.addEventListener('click', hideHint);
        if (gotItBtn) gotItBtn.addEventListener('click', hideHint);
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
        // Setup search autocomplete functionality
        function setupSearchAutocomplete(inputId, suggestionsId) {
            const searchInput = document.getElementById(inputId);
            const suggestionsContainer = document.getElementById(suggestionsId);
            
            console.log('Setting up search autocomplete for:', inputId, suggestionsId);
            console.log('Search input found:', !!searchInput);
            console.log('Suggestions container found:', !!suggestionsContainer);
            
            if (!searchInput || !suggestionsContainer) {
                console.error('Search input or suggestions container not found');
                return;
            }

            // Helper function to clean suggestions
            function cleanSuggestion(suggestion) {
                // Remove brand names (format: "Product Name - Brand Name")
                return suggestion.replace(/\s*-\s*[^-]+$/, '').trim();
            }

            let debounceTimer;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                
                console.log('Search input changed:', query);
                
                if (query.length < 2) {
                    suggestionsContainer.classList.add('hidden');
                    return;
                }
                
                // Show loading state
                suggestionsContainer.innerHTML = '<div class="search-suggestion-item text-gray-500 italic">Searching...</div>';
                suggestionsContainer.classList.remove('hidden');
                
                debounceTimer = setTimeout(() => {
                    const url = `{{ route('search.suggestions') }}?query=${encodeURIComponent(query)}`;
                    console.log('Fetching suggestions from:', url);
                    
                    fetch(url, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Received suggestions:', data);
                            suggestionsContainer.innerHTML = '';
                            
                            if (data.completions && data.completions.length > 0) {
                                data.completions.forEach(suggestion => {
                                    const suggestionElement = document.createElement('div');
                                    suggestionElement.className = 'search-suggestion-item';
                                    
                                    // If it's a product suggestion with image, show image
                                    if (suggestion.type === 'product' && suggestion.image_url) {
                                        const img = document.createElement('img');
                                        img.src = suggestion.image_url;
                                        img.alt = suggestion.text;
                                        img.className = 'inline-block w-8 h-8 object-cover rounded mr-2 align-middle';
                                        suggestionElement.appendChild(img);
                                    }
                                    
                                    // Add the text
                                    const textSpan = document.createElement('span');
                                    textSpan.textContent = suggestion.text;
                                    suggestionElement.appendChild(textSpan);
                                    
                                    suggestionElement.addEventListener('click', () => {
                                        console.log('Suggestion clicked:', suggestion.text);
                                        if (suggestion.type === 'category' && suggestion.slug) {
                                            window.location.href = '/categories/' + suggestion.slug;
                                        } else {
                                            // Clean the suggestion before setting it as the search value
                                            const cleanedSuggestion = cleanSuggestion(suggestion.text);
                                            searchInput.value = cleanedSuggestion;
                                            suggestionsContainer.classList.add('hidden');
                                            // Submit the parent form
                                            searchInput.closest('form').submit();
                                        }
                                    });
                                    
                                    suggestionElement.addEventListener('mouseenter', () => {
                                        // Remove active class from all suggestions
                                        suggestionsContainer.querySelectorAll('.search-suggestion-item').forEach(item => {
                                            item.classList.remove('active');
                                        });
                                        // Add active class to current suggestion
                                        suggestionElement.classList.add('active');
                                    });
                                    
                                    suggestionsContainer.appendChild(suggestionElement);
                                });
                                
                                suggestionsContainer.classList.remove('hidden');
                            } else {
                                // Show "No suggestions found" message
                                const noResultsElement = document.createElement('div');
                                noResultsElement.className = 'search-suggestion-item text-gray-500 italic';
                                noResultsElement.textContent = 'No suggestions found';
                                suggestionsContainer.appendChild(noResultsElement);
                                suggestionsContainer.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching search suggestions:', error);
                            suggestionsContainer.innerHTML = '<div class="search-suggestion-item text-red-500 italic">Error loading suggestions</div>';
                            suggestionsContainer.classList.remove('hidden');
                            
                            // Hide error message after 3 seconds
                            setTimeout(() => {
                                if (suggestionsContainer.querySelector('.text-red-500')) {
                                    suggestionsContainer.classList.add('hidden');
                                }
                            }, 3000);
                        });
                }, 300);
            });
            
            // Show suggestions when input is focused (if there's a value)
            searchInput.addEventListener('focus', function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    // Trigger the input event to show suggestions
                    this.dispatchEvent(new Event('input'));
                }
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
                
                let activeIndex = Array.from(suggestions).findIndex(el => el.classList.contains('active'));
                
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
                    const selectedSuggestion = suggestions[activeIndex].textContent;
                    // Clean the suggestion before setting it as the search value
                    const cleanedSuggestion = cleanSuggestion(selectedSuggestion);
                    searchInput.value = cleanedSuggestion;
                    suggestionsContainer.classList.add('hidden');
                    searchInput.closest('form').submit();
                } else if (e.key === 'Escape') {
                    suggestionsContainer.classList.add('hidden');
                }
            });
            
            function updateActiveSuggestion(suggestions, activeIndex) {
                suggestions.forEach(s => s.classList.remove('active'));
                if (activeIndex >= 0 && activeIndex < suggestions.length) {
                    suggestions[activeIndex].classList.add('active');
                    suggestions[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            }
        }
        
        // Initialize autocomplete for desktop and top mobile search inputs
        setupSearchAutocomplete('desktop-search-input', 'desktop-search-suggestions');
        setupSearchAutocomplete('mobile-search-input-top', 'mobile-search-suggestions-top');
    });
</script>

{{-- Orders hint script for authenticated users --}}
@auth
@if(session('show_orders_hint'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showOrdersHint();
    });
</script>
@php
    // Clear the session flag after showing
    session()->forget('show_orders_hint');
@endphp
@endif
@endauth