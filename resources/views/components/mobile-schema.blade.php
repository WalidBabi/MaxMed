@props(['product' => null])

{{-- Mobile-optimized product schema --}}
@if($product)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ $product->description }}",
    "image": "{{ $product->image_url }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand->name ?? 'MaxMed UAE' }}"
    },
    "offers": {
        "@type": "Offer",
        "priceCurrency": "AED",
        "availability": "https://schema.org/InStock",
        "url": "{{ route('product.show', $product) }}",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "telephone": "+971554602500"
        }
    },
    "potentialAction": {
        "@type": "ViewAction",
        "target": "{{ route('product.show', $product) }}"
    }
}
</script>
@endif