@props(['breadcrumbs' => []])

@if(count($breadcrumbs) > 1)
<nav class="breadcrumb-nav bg-gray-50 py-3 mb-6" aria-label="Breadcrumb navigation" role="navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center space-x-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    @if($loop->last)
                        <span class="text-gray-700 font-semibold" 
                              itemprop="name"
                              aria-current="page">{{ $breadcrumb['name'] }}</span>
                        <meta itemprop="position" content="{{ $index + 1 }}">
                        <link itemprop="item" href="{{ $breadcrumb['url'] ?? url()->current() }}">
                    @else
                        <a href="{{ $breadcrumb['url'] }}" 
                           class="text-[#171e60] hover:text-[#0a5694] transition-colors duration-200 hover:underline focus:outline-none focus:ring-2 focus:ring-[#171e60] focus:ring-opacity-50 rounded"
                           itemprop="item"
                           title="Go to {{ $breadcrumb['name'] }}">
                            <span itemprop="name">{{ $breadcrumb['name'] }}</span>
                        </a>
                        <meta itemprop="position" content="{{ $index + 1 }}">
                        
                        <!-- Separator -->
                        <svg class="w-4 h-4 text-gray-400 mx-2 flex-shrink-0" 
                             fill="currentColor" 
                             viewBox="0 0 20 20" 
                             aria-hidden="true">
                            <path fill-rule="evenodd" 
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" 
                                  clip-rule="evenodd">
                            </path>
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>

<!-- JSON-LD Structured Data for Enhanced Search Results -->
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
            "item": "{{ $breadcrumb['url'] ?? url()->current() }}"
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>

@endif 