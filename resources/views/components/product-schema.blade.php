<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ $product->description }}",
    "image": "{{ asset($product->image) }}",
    "sku": "{{ $product->sku }}",
    "mpn": "{{ $product->sku }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand ?? 'MaxMed UAE' }}"
    },
    "category": "{{ $product->category->name ?? 'Laboratory Equipment' }}",
    "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "AED",
        "price": "{{ $product->price }}",
        "priceValidUntil": "{{ now()->addMonths(3)->format('Y-m-d') }}",
        "availability": "{{ $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "url": "https://maxmedme.com"
        },
        "itemCondition": "https://schema.org/NewCondition",
        "deliveryLeadTime": {
            "@type": "QuantitativeValue",
            "minValue": "2",
            "maxValue": "5",
            "unitCode": "DAY"
        }
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ $product->average_rating ?? 4.8 }}",
        "reviewCount": "{{ $product->review_count ?? 12 }}"
    }
}
</script> 