@props(['breadcrumbs' => []])

@if(count($breadcrumbs) > 1)
<nav class="breadcrumb-nav bg-gray-50 py-3 mb-6" aria-label="Breadcrumb">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center space-x-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    @if($loop->last)
                        <span class="text-gray-500 font-medium" itemprop="name">{{ $breadcrumb['name'] }}</span>
                        <meta itemprop="position" content="{{ $index + 1 }}">
                    @else
                        <a href="{{ $breadcrumb['url'] }}" 
                           class="text-[#171e60] hover:text-[#0a5694] transition-colors duration-200 hover:underline"
                           itemprop="item">
                            <span itemprop="name">{{ $breadcrumb['name'] }}</span>
                        </a>
                        <meta itemprop="position" content="{{ $index + 1 }}">
                        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>

<!-- JSON-LD Structured Data for Breadcrumbs -->
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
@endif

@pushonce('styles')
<style>
.breadcrumb-nav {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.breadcrumb-nav ol {
    max-width: 100%;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.breadcrumb-nav ol::-webkit-scrollbar {
    display: none;
}

@media (max-width: 640px) {
    .breadcrumb-nav {
        padding: 0.5rem 0;
    }
    
    .breadcrumb-nav ol {
        padding: 0 1rem;
    }
    
    .breadcrumb-nav li {
        flex-shrink: 0;
    }
}
</style>
@endpushonce 