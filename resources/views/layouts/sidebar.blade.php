<div class="sidebar" style="overflow: visible;">
    <div 
        class="flex flex-col h-auto min-h-full text-gray-100 bg-gradient-to-b from-[#171e60] to-[#0c1244] shadow-xl rounded-lg"
        style="width: 360px; padding-right: 10px;"
    >
        <!-- Navigation -->
        <div class="flex-grow overflow-y-auto pr-3">
            <div class="pl-4 py-2">
                <!-- Header -->
                <h2 class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-3 mt-4 flex items-center">
                    <span class="flex-grow">Browse Categories</span>
                </h2>
                
                <a class="flex items-center h-10 px-3 mb-3 rounded-lg hover:bg-[#2a3387] transition-colors" 
                   href="{{ route('categories.index') }}"
                   style="cursor: pointer !important;">
                    <span class="text-sm font-medium">All Categories</span>
                </a>
                
                <!-- Categories section -->
                <div class="space-y-1 pr-2">
                    @foreach(\App\Models\Category::whereNull('parent_id')->with(['subcategories.subcategories'])->get()->sortBy(function($category) {
                        // Define your preferred order here - same as welcome page
                        $order = [
                            'Molecular & Clinical Diagnostics' => 1,
                            'Lab Equipment' => 2, 
                            'Medical Consumables' => 3,
                            'Life Science & Research' => 4
                            // Add more as needed
                        ];
                        return $order[$category->name] ?? 999; // Categories not in list will appear last
                    }) as $category)
                    <div class="category-item mb-1" x-data="{ open: false }">
                        <div class="flex items-center h-10 px-3 rounded-lg transition-colors {{ request('category') === $category->name ? 'bg-[#0a5694] text-white' : 'hover:bg-[#2a3387] group' }}"
                             @click="open = !open"
                             style="cursor: pointer !important;">
                            <span class="flex-grow text-sm font-medium">
                                {{ $category->name }}
                            </span>
                            @if($category->subcategories->isNotEmpty())
                            <span class="ml-auto transform transition-transform duration-200" 
                                  :class="{ 'rotate-180': open }">▼</span>
                            @endif
                        </div>
                        @if($category->subcategories->isNotEmpty())
                        <div class="pl-4 ml-2 border-l border-[#2a3387] mt-1 space-y-1"
                             x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2">
                            @foreach($category->subcategories as $subcategory)
                            <div class="subcategory-item" x-data="{ subOpen: false }">
                                @if($subcategory->subcategories->isNotEmpty())
                                <div class="flex items-center h-8 px-3 rounded-lg transition-colors {{ request('subcategory') === $subcategory->name ? 'bg-[#0a5694] text-white' : 'text-gray-300 hover:bg-[#2a3387] hover:text-white group' }}"
                                     @click="subOpen = !subOpen"
                                     style="cursor: pointer !important;">
                                    <span class="flex-grow text-xs font-medium">
                                        {{ $subcategory->name }}
                                    </span>
                                    <span class="ml-auto transform transition-transform duration-200 text-xs" 
                                          :class="{ 'rotate-180': subOpen }">▼</span>
                                </div>
                                @else
                                <div class="flex items-center h-8 px-3 rounded-lg transition-colors {{ request('subcategory') === $subcategory->name ? 'bg-[#0a5694] text-white' : 'text-gray-300 hover:bg-[#2a3387] hover:text-white group' }}">
                                    <a href="{{ route('categories.subcategory.show', [$category, $subcategory]) }}" 
                                       class="flex-grow text-xs font-medium"
                                       style="cursor: pointer !important;">
                                        {{ $subcategory->name }}
                                    </a>
                                </div>
                                @endif
                                
                                @if($subcategory->subcategories->isNotEmpty())
                                <div class="pl-3 ml-2 border-l border-[#2a3387] mt-1 space-y-1"
                                    x-show="subOpen"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2">
                                    @foreach($subcategory->subcategories as $subsubcategory)
                                    <a href="{{ route('categories.subsubcategory.show', [$category, $subcategory, $subsubcategory]) }}"
                                       class="flex items-center h-7 px-3 rounded-lg transition-colors {{ request('subsubcategory') === $subsubcategory->name ? 'bg-[#0a5694] text-white' : 'text-gray-400 hover:bg-[#2a3387] hover:text-white group' }}"
                                       style="cursor: pointer !important;">
                                        <span class="text-xs font-medium">{{ $subsubcategory->name }}</span>
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <!-- Product Filters -->
                @if(request()->routeIs('categories.subcategory.show') || request()->routeIs('categories.subsubcategory.show') || request()->routeIs('product.show') || request()->is('products*') || (request()->routeIs('categories.show') && isset($products) && count($products) > 0))
                <div class="mt-8 filter-section" 
                    x-data="{ 
                        filtersOpen: true, 
                        showHint: false,
                        checkFirstVisit() {
                            if (!localStorage.getItem('filter_hint_seen')) {
                                this.showHint = true;
                                setTimeout(() => {
                                    this.showHint = false;
                                    localStorage.setItem('filter_hint_seen', 'true');
                                }, 5000);
                            }
                        }
                    }" 
                    x-init="checkFirstVisit()">
                    <h2 class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-3 relative">
                        <button class="flex items-center w-full filter-button" @click="filtersOpen = !filtersOpen">
                            <span>Filter Products</span>
                            @if(request()->hasAny(['search', 'in_stock', 'price_min', 'price_max', 'brand', 'application']))
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-[#0a5694] text-white rounded-full">
                                    {{ count(array_filter(request()->only(['search', 'in_stock', 'price_min', 'price_max', 'brand', 'application']))) }}
                                </span>
                            @endif
                            <svg :class="{'rotate-180': filtersOpen}" class="w-4 h-4 ml-2 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Filter hint tooltip -->
                        <div 
                            x-show="showHint" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform translate-y-2"
                            class="absolute -right-4 top-full mt-2 bg-[#0a5694] text-white text-xs py-2 px-3 rounded shadow-lg z-10 w-48"
                        >
                            <div class="absolute -top-2 right-6 w-4 h-4 bg-[#0a5694] transform rotate-45"></div>
                            <p>Filter products by price, brand & more to find exactly what you need!</p>
                        </div>
                    </h2>
                    
                    <div x-show="filtersOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-4">
                        
                        <form action="{{ request()->url() }}" method="GET" id="product-filter-form" class="space-y-4">
                            <!-- Search Filter -->
                            <div class="mb-3">
                                <label for="search" class="block text-xs font-medium text-gray-300 mb-1">Search</label>
                                <input type="text" class="w-full bg-white border-0 rounded-lg text-xs text-black placeholder-gray-500 p-2 focus:ring-[#0a5694] focus:ring-2" 
                                    id="search" name="search" placeholder="Product name or keywords" value="{{ request('search') }}">
                            </div>
                                
                            <!-- Available in stock -->
                            <div class="mb-3">
                                <label for="in_stock" class="block text-xs font-medium text-gray-300 mb-1">Availability</label>
                                <select class="w-full bg-white border-0 rounded-lg text-xs text-black p-2 focus:ring-[#0a5694] focus:ring-2" 
                                    id="in_stock" name="in_stock">
                                    <option value="">All Products</option>
                                    <option value="1" {{ request('in_stock') == '1' ? 'selected' : '' }}>In Stock</option>
                                    <option value="0" {{ request('in_stock') == '0' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                                
                            <!-- Price Range -->
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-300 mb-1">Price Range (AED)</label>
                                <div class="flex space-x-2">
                                    <input type="number" class="w-1/2 bg-white border-0 rounded-lg text-xs text-black p-2 focus:ring-[#0a5694] focus:ring-2" 
                                        id="price_min" name="price_min" min="0" step="0.01" placeholder="Min" value="{{ request('price_min') }}">
                                    <input type="number" class="w-1/2 bg-white border-0 rounded-lg text-xs text-black p-2 focus:ring-[#0a5694] focus:ring-2" 
                                        id="price_max" name="price_max" min="0" step="0.01" placeholder="Max" value="{{ request('price_max') }}">
                                </div>
                            </div>
                                
                            <!-- Sort By -->
                            <div class="mb-3">
                                <label for="sort" class="block text-xs font-medium text-gray-300 mb-1">Sort By</label>
                                <select class="w-full bg-white border-0 rounded-lg text-xs text-black p-2 focus:ring-[#0a5694] focus:ring-2" 
                                    id="sort" name="sort">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                </select>
                            </div>
                                
                            <!-- Brand -->
                            <div class="mb-3">
                                <label for="brand" class="block text-xs font-medium text-gray-300 mb-1">Brand/Manufacturer</label>
                                <select class="w-full bg-white border-0 rounded-lg text-xs text-black p-2 focus:ring-[#0a5694] focus:ring-2" 
                                    id="brand" name="brand">
                                    <option value="">All Brands</option>
                                    @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                                
                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2">
                                <button type="submit" class="w-full bg-[#0a5694] hover:bg-[#084980] text-white py-2 rounded-lg text-xs font-medium transition-colors">
                                    <i class="fas fa-search mr-1"></i> Apply Filters
                                </button>
                                <a href="{{ request()->url() }}" class="w-full bg-[#404e8d] hover:bg-[#323c6d] text-white py-2 px-4 rounded-lg text-xs font-medium transition-colors text-center">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter form localStorage functionality
        const filterForm = document.getElementById('product-filter-form');
        if (filterForm) {
            const filterInputs = filterForm.querySelectorAll('input, select');
            const storagePrefix = 'product_filter_';
            
            // Save filter values to localStorage
            filterInputs.forEach(input => {
                input.addEventListener('change', () => {
                    localStorage.setItem(`${storagePrefix}${input.name}`, input.value);
                });
            });
            
            // Function to restore saved filters
            const restoreFilters = () => {
                filterInputs.forEach(input => {
                    const savedValue = localStorage.getItem(`${storagePrefix}${input.name}`);
                    if (savedValue !== null && savedValue !== '') {
                        input.value = savedValue;
                    }
                });
            };
            
            // Reset button functionality
            const resetButton = filterForm.querySelector('a');
            resetButton.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Clear localStorage for filters
                filterInputs.forEach(input => {
                    localStorage.removeItem(`${storagePrefix}${input.name}`);
                });
                
                // Redirect to the current URL without query parameters
                window.location.href = window.location.pathname;
            });
            
            // Only restore filters if not already set in URL
            if (!window.location.search) {
                restoreFilters();
                
                // Check if there are any restored filters
                let hasFilters = false;
                filterInputs.forEach(input => {
                    if (input.value && input.name !== 'sort') {
                        hasFilters = true;
                    }
                });
                
                // Submit form if filters were restored
                if (hasFilters) {
                    filterForm.submit();
                }
            }
        }
    });
</script> 