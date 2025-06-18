@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'decoding' => 'async',
    'sizes' => null,
    'srcset' => null,
    'placeholder' => '/images/placeholder.jpg',
    'webp' => null,
    'avif' => null,
    'priority' => false
])

@php
    $loadingAttribute = $priority ? 'eager' : $loading;
    $classes = "lazy-image transition-opacity duration-300 " . $class;
    
    // Generate srcset for different formats if paths provided
    $srcsetValue = $srcset;
    if ($webp || $avif) {
        $srcsetValue = '';
        if ($avif) {
            $srcsetValue .= $avif . ' ';
        }
        if ($webp) {
            $srcsetValue .= ($srcsetValue ? ', ' : '') . $webp . ' ';
        }
        $srcsetValue .= ($srcsetValue ? ', ' : '') . $src;
    }
    
    // Generate sizes attribute if not provided
    $sizesValue = $sizes ?: '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
@endphp

<picture class="lazy-picture">
    @if($avif)
        <source srcset="{{ $avif }}" type="image/avif">
    @endif
    
    @if($webp)
        <source srcset="{{ $webp }}" type="image/webp">
    @endif
    
    <img 
        src="{{ $src }}" 
        alt="{{ $alt }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($srcsetValue) srcset="{{ $srcsetValue }}" @endif
        @if($sizes) sizes="{{ $sizesValue }}" @endif
        loading="{{ $loadingAttribute }}"
        decoding="{{ $decoding }}"
        class="{{ $classes }}"
        onerror="this.onerror=null; this.src='{{ $placeholder }}'; this.alt='Image not available - {{ $alt }}';"
        {{ $attributes }}
    >
</picture>

@push('styles')
<style>
    .lazy-image {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    
    .lazy-image.loaded {
        opacity: 1;
    }
    
    .lazy-picture {
        display: block;
        overflow: hidden;
    }
    
    /* Skeleton loading animation */
    .lazy-image:not(.loaded) {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Intersection Observer for lazy loading
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Check if image is already loaded
                    if (img.complete && img.naturalHeight !== 0) {
                        img.classList.add('loaded');
                    } else {
                        // Add load event listener
                        img.addEventListener('load', function() {
                            this.classList.add('loaded');
                        });
                        
                        // Add error handling
                        img.addEventListener('error', function() {
                            this.classList.add('loaded'); // Still show it to prevent layout shift
                        });
                    }
                    
                    observer.unobserve(img);
                }
            });
        }, {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        });
        
        // Observe all lazy images
        document.querySelectorAll('.lazy-image').forEach(img => {
            imageObserver.observe(img);
        });
    });
</script>
@endpush 