@php
    $breadcrumbs = $breadcrumbs ?? [];
    $showHome = $showHome ?? true;
    
    // Auto-generate breadcrumbs if not provided
    if (empty($breadcrumbs)) {
        $breadcrumbs = [];
        
        if ($showHome) {
            $breadcrumbs[] = [
                'name' => 'Home',
                'url' => route('welcome')
            ];
        }
        
        $route = request()->route();
        $routeName = $route ? $route->getName() : '';
        
        // Auto-generate based on current route
        switch ($routeName) {
            case 'products.index':
                $breadcrumbs[] = [
                    'name' => 'Products',
                    'url' => route('products.index')
                ];
                break;
                
            case 'product.show':
                $product = $route->parameter('product');
                $breadcrumbs[] = [
                    'name' => 'Products',
                    'url' => route('products.index')
                ];
                if ($product) {
                    $breadcrumbs[] = [
                        'name' => $product->name,
                        'url' => route('product.show', $product)
                    ];
                }
                break;
                
            case 'news.index':
                $breadcrumbs[] = [
                    'name' => 'News',
                    'url' => route('news.index')
                ];
                break;
                
            case 'news.show':
                $news = $route->parameter('news');
                $breadcrumbs[] = [
                    'name' => 'News',
                    'url' => route('news.index')
                ];
                if ($news) {
                    $breadcrumbs[] = [
                        'name' => Str::limit($news->title, 50),
                        'url' => route('news.show', $news)
                    ];
                }
                break;
                
            case 'contact':
                $breadcrumbs[] = [
                    'name' => 'Contact',
                    'url' => route('contact')
                ];
                break;
                
            case 'about':
                $breadcrumbs[] = [
                    'name' => 'About',
                    'url' => route('about')
                ];
                break;
                
            case 'partners.index':
                $breadcrumbs[] = [
                    'name' => 'Partners',
                    'url' => route('partners.index')
                ];
                break;
                
            case 'industry.index':
                $breadcrumbs[] = [
                    'name' => 'Industries',
                    'url' => route('industry.index')
                ];
                break;
        }
        
        // Handle category breadcrumbs
        if (str_starts_with($routeName, 'categories.')) {
            $breadcrumbs[] = [
                'name' => 'Products',
                'url' => route('products.index')
            ];
            
            $category = $route->parameter('category');
            if ($category) {
                $breadcrumbs[] = [
                    'name' => $category->name,
                    'url' => route('categories.show', $category)
                ];
                
                $subcategory = $route->parameter('subcategory');
                if ($subcategory) {
                    $breadcrumbs[] = [
                        'name' => $subcategory->name,
                        'url' => route('categories.subcategory', [$category, $subcategory])
                    ];
                    
                    $subsubcategory = $route->parameter('subsubcategory');
                    if ($subsubcategory) {
                        $breadcrumbs[] = [
                            'name' => $subsubcategory->name,
                            'url' => route('categories.subsubcategory', [$category, $subcategory, $subsubcategory])
                        ];
                    }
                }
            }
        }
    }
@endphp

@if(count($breadcrumbs) > 1)
<nav aria-label="Breadcrumb" class="flex" id="breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 list-none p-0">
        @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="inline-flex items-center">
                @if(!$loop->first)
                    <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                @endif
                
                @if($loop->last)
                    <span class="ms-1 text-sm font-medium text-gray-700 md:ms-2" aria-current="page">
                        {{ $breadcrumb['name'] }}
                    </span>
                @else
                    <a href="{{ $breadcrumb['url'] }}" 
                       class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200"
                       @if($loop->first) 
                           aria-label="Navigate to homepage"
                       @else
                           aria-label="Navigate to {{ $breadcrumb['name'] }}"
                       @endif>
                        @if($loop->first)
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                        @endif
                        {{ $breadcrumb['name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

{{-- Pass breadcrumbs data to schema markup --}}
@push('meta')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($breadcrumbs as $index => $breadcrumb)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "name": "{{ $breadcrumb['name'] }}",
            "item": "{{ $breadcrumb['url'] }}"
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endpush
@endif 