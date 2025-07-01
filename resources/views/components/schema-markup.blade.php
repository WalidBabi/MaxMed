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
    "name": "{{ is_object($product) && isset($product->name) ? $product->name : 'Product' }}",
    "description": "{{ is_object($product) && isset($product->description) ? strip_tags(Str::limit($product->description, 300)) : 'Laboratory equipment and medical supplies' }}",
    "sku": "{{ is_object($product) && isset($product->sku) ? $product->sku : '' }}",
    "mpn": "{{ is_object($product) && isset($product->sku) ? $product->sku : '' }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ is_object($product) && is_object($product->brand) && isset($product->brand->name) ? $product->brand->name : 'MaxMed' }}"
    },
    "manufacturer": {
        "@type": "Organization",
        "name": "{{ is_object($product) && is_object($product->brand) && isset($product->brand->name) ? $product->brand->name : 'MaxMed UAE' }}"
    },
    "image": [
        "{{ is_object($product) && isset($product->image_url) ? $product->image_url : asset('Images/logo.png') }}"
        @if(is_object($product) && isset($product->images) && $product->images && $product->images->count() > 0)
            @foreach($product->images as $image)
            ,"{{ is_object($image) && isset($image->image_url) ? $image->image_url : '' }}"
            @endforeach
        @endif
    ],
    "url": "{{ is_object($product) ? route('product.show', $product) : url()->current() }}",
    "category": "{{ is_object($product) && is_object($product->category) && isset($product->category->name) ? $product->category->name : 'Laboratory Equipment' }}",
    "offers": {
        "@type": "Offer",
        "url": "{{ is_object($product) ? route('product.show', $product) : url()->current() }}",
        "priceCurrency": "AED",
        "price": "{{ is_object($product) && isset($product->price_aed) ? $product->price_aed : (is_object($product) && isset($product->price) ? $product->price : '0') }}",
        "priceValidUntil": "{{ now()->addMonths(6)->format('Y-m-d') }}",
        "availability": "{{ is_object($product) && method_exists($product, 'inStock') ? ($product->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock') : 'https://schema.org/InStock' }}",
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
        "ratingValue": "{{ is_object($product) && isset($product->average_rating) ? $product->average_rating : 4.8 }}",
        "reviewCount": "{{ is_object($product) && isset($product->review_count) ? $product->review_count : 15 }}",
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
            "reviewBody": "Excellent quality {{ strtolower(is_object($product) && is_object($product->category) && isset($product->category->name) ? $product->category->name : 'laboratory equipment') }}. Fast delivery and great customer service from MaxMed UAE."
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
    "headline": "{{ is_object($news) && isset($news->title) ? $news->title : 'News Article' }}",
    "description": "{{ is_object($news) && isset($news->content) ? strip_tags(Str::limit($news->content, 300)) : 'Latest news and updates from MaxMed UAE' }}",
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
    "datePublished": "{{ is_object($news) && isset($news->created_at) ? $news->created_at->toISOString() : now()->toISOString() }}",
    "dateModified": "{{ is_object($news) && isset($news->updated_at) ? $news->updated_at->toISOString() : now()->toISOString() }}",
    "url": "{{ is_object($news) ? route('news.show', $news) : url()->current() }}"
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
            "name": "{{ is_array($breadcrumb) && isset($breadcrumb['name']) ? $breadcrumb['name'] : (is_string($breadcrumb) ? $breadcrumb : 'Page ' . ($index + 1)) }}",
            "item": "{{ is_array($breadcrumb) && isset($breadcrumb['url']) ? $breadcrumb['url'] : url()->current() }}"
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