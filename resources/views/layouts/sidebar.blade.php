<div class="flex flex-col items-center w-64 h-full overflow-hidden text-white bg-[#171e60] rounded">
    <a class="flex items-center w-full px-3 mt-3" href="{{ route('categories.index') }}">
        <span class="ml-2 text-sm font-bold">Categories</span>
    </a>
    <div class="w-full px-2">
        <div class="flex flex-col items-center w-full mt-3 border-t border-[#0a5694]">
            <a class="flex items-center w-full h-12 px-3 mt-2 rounded hover:bg-[#0a5694] hover:text-white" href="{{ route('categories.index') }}">
                <span class="ml-2 text-sm font-medium">All Categories</span>
            </a>
            @foreach(\App\Models\Category::whereNull('parent_id')->with('subcategories')->get() as $category)
            <div class="category-item" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="{{ route('categories.show', $category) }}"
                    class="flex items-center w-full h-12 px-3 mt-2 rounded hover:bg-[#0a5694] hover:text-white {{ request('category') === $category->name ? 'bg-[#0a5694] text-white' : '' }}"
                    @click.prevent="window.location.href = $el.href">
                    <span class="ml-2 text-sm font-medium">{{ $category->name }}</span>
                    @if($category->subcategories->isNotEmpty())
                    <span class="ml-auto" :class="{ 'rotate-180': open }">▼</span>
                    @endif
                </a>
                @if($category->subcategories->isNotEmpty())
                <div class="flex flex-col items-center w-full mt-2 border-t border-[#0a5694]" x-show="open">
                    @foreach($category->subcategories as $subcategory)
                    <a href="{{ route('categories.show', $subcategory) }}"
                        class="flex items-center w-full h-12 px-3 mt-2 rounded hover:bg-[#0a5694] hover:text-white {{ request('category') === $subcategory->name ? 'bg-[#0a5694] text-white' : '' }}"
                        @click.prevent="window.location.href = $el.href">
                        <span class="ml-2 text-sm font-medium">{{ $subcategory->name }}</span>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div> 