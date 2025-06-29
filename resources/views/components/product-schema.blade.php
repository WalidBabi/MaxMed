<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ $product->description }}",
    "image": "{{ $product->image_url ?? asset(\"/Images/placeholder.jpg\") }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand->name ?? \"MaxMed UAE\" }}"
    },
    "manufacturer": {
        "@type": "Organization",
        "name": "MaxMed UAE"
    },
    "offers": {
        "@type": "Offer",
        "url": "{{ route(\"product.show\", $product) }}",
        "priceCurrency": "AED",
        "availability": "https://schema.org/InStock",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE"
        }
    }
}
</script>