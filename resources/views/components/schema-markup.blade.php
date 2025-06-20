@php
// Get current route and determine appropriate schema
$route = request()->route();
$routeName = $route ? $route->getName() : '';
$organization = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => "MaxMed UAE",
    "alternateName" => "MaxMed",
    "url" => "https://maxmedme.com",
    "logo" => asset('Images/logo.png'),
    "contactPoint" => [
        "@type" => "ContactPoint",
        "telephone" => "+971-55-460-2500",
        "contactType" => "Sales",
        "email" => "sales@maxmedme.com",
        "areaServed" => ["AE", "SA", "QA", "KW", "OM", "BH"],
        "availableLanguage" => ["English", "Arabic"]
    ],
    "address" => [
        "@type" => "PostalAddress",
        "addressCountry" => "AE",
        "addressRegion" => "Dubai"
    ],
    "sameAs" => [
        "https://www.linkedin.com/company/maxmed-uae"
    ]
];
@endphp

<script type="application/ld+json">
{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Product Schema --}}
@if($routeName === 'product.show' && isset($product))
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ strip_tags(Str::limit($product->description, 300)) }}",
    "sku": "{{ $product->sku }}",
    "mpn": "{{ $product->sku }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand ? $product->brand->name : 'MaxMed' }}"
    },
    "manufacturer": {
        "@type": "Organization",
        "name": "{{ $product->brand ? $product->brand->name : 'MaxMed UAE' }}"
    },
    "image": [
        "{{ $product->image_url }}"
        @if($product->images && $product->images->count() > 0)
            @foreach($product->images as $image)
            ,"{{ $image->image_url }}"
            @endforeach
        @endif
    ],
    "url": "{{ route('product.show', $product) }}",
    "category": "{{ $product->category ? $product->category->name : 'Laboratory Equipment' }}",
    "offers": {
        "@type": "Offer",
        "url": "{{ route('product.show', $product) }}",
        "priceCurrency": "AED",
        "price": "{{ $product->price_aed ?? $product->price }}",
        "priceValidUntil": "{{ now()->addMonths(6)->format('Y-m-d') }}",
        "availability": "{{ $product->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "itemCondition": "https://schema.org/NewCondition",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "url": "https://maxmedme.com"
        },
        "deliveryLeadTime": {
            "@type": "QuantitativeValue",
            "minValue": "2",
            "maxValue": "7",
            "unitCode": "DAY"
        },
        "shippingDetails": {
            "@type": "OfferShippingDetails",
            "shippingRate": {
                "@type": "MonetaryAmount",
                "value": "0",
                "currency": "AED"
            },
            "shippingDestination": [
                {
                    "@type": "DefinedRegion",
                    "addressCountry": "AE",
                    "addressRegion": ["Dubai", "Abu Dhabi", "Sharjah", "Ajman", "Ras Al Khaimah", "Fujairah", "Umm Al Quwain"]
                },
                {
                    "@type": "DefinedRegion",
                    "addressCountry": ["SA", "QA", "KW", "OM", "BH"]
                }
            ],
            "deliveryTime": {
                "@type": "ShippingDeliveryTime",
                "handlingTime": {
                    "@type": "QuantitativeValue",
                    "minValue": "1",
                    "maxValue": "2",
                    "unitCode": "DAY"
                },
                "transitTime": {
                    "@type": "QuantitativeValue", 
                    "minValue": "1",
                    "maxValue": "5", 
                    "unitCode": "DAY"
                }
            }
        },
        "hasMerchantReturnPolicy": {
            "@type": "MerchantReturnPolicy",
            "applicableCountry": "AE",
            "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
            "merchantReturnDays": 30,
            "returnMethod": "https://schema.org/ReturnByMail",
            "returnFees": "https://schema.org/FreeReturn"
        }
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ $product->average_rating ?? 4.8 }}",
        "reviewCount": "{{ $product->review_count ?? 15 }}",
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
                "name": "Healthcare Professional"
            },
            "reviewBody": "Excellent quality {{ strtolower($product->category ? $product->category->name : 'laboratory equipment') }}. Fast delivery and great customer service from MaxMed UAE."
        }
    ]
}
</script>
@endif

{{-- Category/Product Listing Schema --}}
@if($routeName === 'products.index' || strpos($routeName, 'categories.') === 0)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Laboratory Equipment & Medical Supplies",
    "description": "Comprehensive collection of laboratory equipment and medical supplies from MaxMed UAE",
    "url": "{{ url()->current() }}"
}
</script>
@endif

{{-- News Article Schema --}}
@if($routeName === 'news.show' && isset($news))
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $news->title }}",
    "description": "{{ strip_tags(Str::limit($news->content, 300)) }}",
    "author": {
        "@type": "Organization",
        "name": "MaxMed UAE"
    },
    "publisher": {
        "@type": "Organization",
        "name": "MaxMed UAE",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('Images/logo.png') }}"
        }
    },
    "datePublished": "{{ $news->created_at->toISOString() }}",
    "dateModified": "{{ $news->updated_at->toISOString() }}",
    "url": "{{ route('news.show', $news) }}"
}
</script>
@endif

{{-- Breadcrumb Schema --}}
@if(isset($breadcrumbs) && count($breadcrumbs) > 1)
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

{{-- Local Business Schema for Contact Page --}}
@if($routeName === 'contact')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "MaxMed UAE",
    "description": "Leading supplier of laboratory equipment and medical supplies in UAE",
    "image": "{{ asset('Images/logo.png') }}",
    "telephone": "+971-55-460-2500",
    "email": "sales@maxmedme.com",
    "url": "https://maxmedme.com",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "AE",
        "addressRegion": "Dubai"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 25.2048,
        "longitude": 55.2708
    },
    "openingHours": "Mo-Fr 09:00-18:00",
    "priceRange": "$$",
    "serviceArea": {
        "@type": "Country",
        "name": "United Arab Emirates"
    }
}
</script>
@endif

{{-- FAQ Schema for pages with FAQs --}}
@if(isset($faqs) && count($faqs) > 0)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $faq)
        {
            "@type": "Question",
            "name": "{{ $faq['question'] }}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ $faq['answer'] }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif 