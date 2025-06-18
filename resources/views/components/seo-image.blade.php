@props([
    'src' => '',
    'alt' => '',
    'title' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'sizes' => null,
    'product' => null,
    'category' => null,
    'brand' => null,
    'priority' => false,
    'caption' => null,
    'schema' => true
])

@php
    // Generate SEO-optimized alt text if not provided
    $optimizedAlt = $alt;
    if (empty($optimizedAlt)) {
        if ($product) {
            $optimizedAlt = $product->name . ' - ' . ($product->category ? $product->category->name : 'Laboratory Equipment') . ' by MaxMed UAE';
        } elseif ($category) {
            $optimizedAlt = $category->name . ' Equipment - Laboratory Supplies by MaxMed UAE';
        } elseif ($brand) {
            $optimizedAlt = $brand->name . ' Laboratory Equipment - MaxMed UAE';
        } else {
            $optimizedAlt = 'Laboratory Equipment by MaxMed UAE';
        }
    }
    
    // Generate title attribute for better UX
    $titleAttr = $title ?: $optimizedAlt;
    
    // Generate sizes attribute
    $sizesAttr = $sizes ?: '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
    
    // Determine loading strategy
    $loadingAttr = $priority ? 'eager' : 'lazy';
    
    // Generate structured data for images
    $imageSchema = null;
    if ($schema && ($product || $category)) {
        $imageSchema = [
            "@context" => "https://schema.org",
            "@type" => "ImageObject",
            "url" => $src,
            "description" => $optimizedAlt,
            "name" => $titleAttr
        ];
        
        if ($width && $height) {
            $imageSchema["width"] = $width;
            $imageSchema["height"] = $height;
        }
        
        if ($product) {
            $imageSchema["author"] = [
                "@type" => "Organization",
                "name" => "MaxMed UAE"
            ];
            $imageSchema["copyrightHolder"] = [
                "@type" => "Organization", 
                "name" => "MaxMed UAE"
            ];
        }
    }
@endphp

<figure class="seo-image-figure {{ $class }}" 
        @if($imageSchema) itemscope itemtype="https://schema.org/ImageObject" @endif>
    
    <img src="{{ $src }}"
         alt="{{ $optimizedAlt }}"
         title="{{ $titleAttr }}"
         @if($width) width="{{ $width }}" @endif
         @if($height) height="{{ $height }}" @endif
         @if($sizes) sizes="{{ $sizesAttr }}" @endif
         loading="{{ $loadingAttr }}"
         decoding="async"
         class="seo-optimized-image"
         @if($imageSchema) itemprop="contentUrl" @endif
         onerror="this.onerror=null; this.src='/images/placeholder.jpg'; this.alt='Image not available - {{ $optimizedAlt }}';"
         {{ $attributes }}>
    
    @if($caption)
        <figcaption class="image-caption text-sm text-gray-600 mt-2 italic"
                    @if($imageSchema) itemprop="caption" @endif>
            {{ $caption }}
        </figcaption>
    @endif
    
    @if($imageSchema)
        <!-- Hidden metadata for search engines -->
        <meta itemprop="description" content="{{ $optimizedAlt }}">
        <meta itemprop="name" content="{{ $titleAttr }}">
        @if($width && $height)
            <meta itemprop="width" content="{{ $width }}">
            <meta itemprop="height" content="{{ $height }}">
        @endif
        <meta itemprop="author" content="MaxMed UAE">
        <meta itemprop="copyrightHolder" content="MaxMed UAE">
    @endif
</figure>

@if($imageSchema && $schema)
    <!-- JSON-LD for Image Object -->
    <script type="application/ld+json">
        {!! json_encode($imageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

@push('styles')
<style>
    .seo-image-figure {
        margin: 0;
        position: relative;
    }
    
    .seo-optimized-image {
        max-width: 100%;
        height: auto;
        transition: opacity 0.3s ease-in-out;
    }
    
    .seo-optimized-image[loading="lazy"] {
        opacity: 0;
    }
    
    .seo-optimized-image.loaded {
        opacity: 1;
    }
    
    .image-caption {
        padding: 0.5rem 0;
        line-height: 1.4;
    }
    
    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        .seo-optimized-image {
            transition: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize image loading observers
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.seo-optimized-image[loading="lazy"]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        
                        img.addEventListener('load', function() {
                            this.classList.add('loaded');
                        });
                        
                        img.addEventListener('error', function() {
                            this.classList.add('loaded');
                        });
                        
                        // If image is already loaded (cached)
                        if (img.complete && img.naturalHeight !== 0) {
                            img.classList.add('loaded');
                        }
                        
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.1
            });
            
            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for browsers without IntersectionObserver
            images.forEach(img => {
                img.classList.add('loaded');
            });
        }
    });
</script>
@endpush 