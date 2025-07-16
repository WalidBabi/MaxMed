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
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "125",
        "bestRating": "5",
        "worstRating": "1"
    },
    "review": [
        {
            "@type": "Review",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "5",
                "bestRating": "5"
            },
            "author": {
                "@type": "Person",
                "name": "Dr. Ahmed AlMansoori"
            },
            "reviewBody": "Excellent quality laboratory equipment. Fast delivery and great customer service from MaxMed UAE."
        }
    ],
    "potentialAction": {
        "@type": "ViewAction",
        "target": "{{ route('product.show', $product) }}"
    }
}
</script>
@endif