@props(['paginator', 'showMeta' => true])

@if($paginator->hasPages())
    {{-- Add pagination meta tags to head --}}
    @if($showMeta)
        @push('head')
            @if(!$paginator->onFirstPage())
                <link rel="prev" href="{{ $paginator->previousPageUrl() }}">
            @endif
            @if($paginator->hasMorePages())
                <link rel="next" href="{{ $paginator->nextPageUrl() }}">
            @endif
            
            {{-- Add canonical URL for current page --}}
            <link rel="canonical" href="{{ $paginator->url($paginator->currentPage()) }}">
            
            {{-- Add View-All link if available --}}
            @if($paginator->total() <= 100)
                <link rel="alternate" href="{{ $paginator->url(1) }}?show=all" hreflang="x-default" title="View All">
            @endif
        @endpush
    @endif

    <nav class="seo-pagination-nav" 
         role="navigation" 
         aria-label="Pagination Navigation"
         itemscope 
         itemtype="https://schema.org/SiteNavigationElement">
        
        {{-- Results summary for better UX --}}
        <div class="pagination-summary mb-4 text-center text-gray-600">
            <p>
                Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> 
                of <strong>{{ $paginator->total() }}</strong> results
                @if($paginator->hasPages())
                    (Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }})
                @endif
            </p>
        </div>

        <ul class="pagination pagination-lg justify-content-center" 
            itemscope 
            itemtype="https://schema.org/SiteNavigationElement">
            
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-label="Previous page">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        <span class="sr-only">Previous</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" 
                       href="{{ $paginator->previousPageUrl() }}" 
                       rel="prev"
                       aria-label="Go to previous page ({{ $paginator->currentPage() - 1 }})"
                       itemprop="url">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @endif

            {{-- First Page Link --}}
            @if($paginator->currentPage() > 3)
                <li class="page-item">
                    <a class="page-link" 
                       href="{{ $paginator->url(1) }}"
                       aria-label="Go to first page">1</a>
                </li>
                @if($paginator->currentPage() > 4)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Pagination Elements --}}
            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link" 
                              aria-label="Current page ({{ $page }})"
                              itemprop="url">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" 
                           href="{{ $url }}"
                           aria-label="Go to page {{ $page }}"
                           itemprop="url">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Last Page Link --}}
            @if($paginator->currentPage() < $paginator->lastPage() - 2)
                @if($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" 
                       href="{{ $paginator->url($paginator->lastPage()) }}"
                       aria-label="Go to last page ({{ $paginator->lastPage() }})">{{ $paginator->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" 
                       href="{{ $paginator->nextPageUrl() }}" 
                       rel="next"
                       aria-label="Go to next page ({{ $paginator->currentPage() + 1 }})"
                       itemprop="url">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-label="Next page">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </span>
                </li>
            @endif
        </ul>

        {{-- JSON-LD for Pagination --}}
        @if($paginator->hasPages())
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "CollectionPage",
                "name": "{{ $paginator->hasPages() ? 'Page ' . $paginator->currentPage() . ' of ' . $paginator->lastPage() : 'Results' }}",
                "url": "{{ $paginator->url($paginator->currentPage()) }}",
                "isPartOf": {
                    "@type": "WebSite",
                    "name": "MaxMed UAE",
                    "url": "{{ url('/') }}"
                },
                "mainEntity": {
                    "@type": "ItemList",
                    "numberOfItems": {{ $paginator->total() }},
                    "itemListOrder": "Ascending"
                }
                @if(!$paginator->onFirstPage())
                ,"previousPage": "{{ $paginator->previousPageUrl() }}"
                @endif
                @if($paginator->hasMorePages())
                ,"nextPage": "{{ $paginator->nextPageUrl() }}"
                @endif
            }
            </script>
        @endif
    </nav>

    {{-- Performance hint for pagination --}}
    @if($paginator->hasMorePages())
        <link rel="prefetch" href="{{ $paginator->nextPageUrl() }}">
    @endif
@endif

@push('styles')
<style>
    .seo-pagination-nav .pagination {
        margin-bottom: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .seo-pagination-nav .page-link {
        border: none;
        color: #6c757d;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        background-color: #fff;
    }
    
    .seo-pagination-nav .page-link:hover {
        background-color: #f8f9fa;
        color: #171e60;
        transform: translateY(-1px);
    }
    
    .seo-pagination-nav .page-item.active .page-link {
        background-color: #171e60;
        border-color: #171e60;
        color: white;
        font-weight: 600;
    }
    
    .seo-pagination-nav .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .pagination-summary {
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 576px) {
        .seo-pagination-nav .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .pagination-summary {
            font-size: 0.8rem;
        }
    }
</style>
@endpush 