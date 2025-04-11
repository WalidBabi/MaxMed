<div class="flex flex-col w-64 h-full overflow-hidden text-gray-100 bg-gradient-to-b from-[#171e60] to-[#0c1244] rounded-lg shadow-xl">
   
    
    <!-- Navigation -->
    <div class="flex-grow overflow-y-auto">
        <div class="px-4 py-2">
            <h2 class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-3 mt-4">Browse Categories</h2>
            
            <a class="flex items-center h-10 px-3 mb-3 rounded-lg hover:bg-[#2a3387] transition-colors" 
               href="{{ route('categories.index') }}">
                <span class="text-sm font-medium">All Categories</span>
            </a>
            
            <div class="space-y-1">
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
                <div class="category-item mb-1" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('categories.show', $category) }}"
                        class="flex items-center h-10 px-3 rounded-lg transition-colors {{ request('category') === $category->name ? 'bg-[#0a5694] text-white' : 'hover:bg-[#2a3387] group' }}"
                        @click.prevent="window.location.href = $el.href">
                        <span class="text-sm font-medium">{{ $category->name }}</span>
                        @if($category->subcategories->isNotEmpty())
                        <span class="ml-auto transform transition-transform duration-200" :class="{ 'rotate-180': open }">▼</span>
                        @endif
                    </a>
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
                        <div class="subcategory-item" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                            <a href="{{ route('categories.subcategory.show', [$category, $subcategory]) }}"
                                class="flex items-center h-8 px-3 rounded-lg transition-colors {{ request('subcategory') === $subcategory->name ? 'bg-[#0a5694] text-white' : 'text-gray-300 hover:bg-[#2a3387] hover:text-white group' }}"
                                @click.prevent="window.location.href = $el.href">
                                <span class="text-xs font-medium">{{ $subcategory->name }}</span>
                                @if($subcategory->subcategories->isNotEmpty())
                                <span class="ml-auto transform transition-transform duration-200 text-xs" :class="{ 'rotate-180': subOpen }">▼</span>
                                @endif
                            </a>
                            
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
                                    @click.prevent="window.location.href = $el.href">
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
        </div>
    </div>
    
   
</div> 