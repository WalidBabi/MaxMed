@props([
    'product' => null,
    'category' => null,
    'type' => 'product'
])

@php
    $aiSeoService = app(\App\Services\AiSeoService::class);
    
    if ($product) {
        $aiContent = $aiSeoService->generateAiOptimizedContent($product);
        $entityData = $aiContent['knowledge_base_format'];
        $schema = $aiContent['ai_friendly_schema'];
        $relationships = $aiContent['entity_relationships'];
    } elseif ($category) {
        $aiContent = $aiSeoService->generateAiCategoryContent($category);
        $entityData = $aiContent['knowledge_structure'];
        $schema = $aiContent['ai_schema'];
        $relationships = $aiContent['semantic_relationships'];
    }
@endphp

@if(isset($schema))
{{-- AI-Enhanced Schema Markup --}}
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

{{-- Knowledge Graph Schema for AI Understanding --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "KnowledgeGraph",
    "mainEntity": {
        "@type": "{{ $type === 'product' ? 'Product' : 'Category' }}",
        "name": "{{ $product ? $product->name : ($category ? $category->name : 'MaxMed UAE') }}",
        "description": "{{ isset($entityData['product_description']) ? $entityData['product_description'] : (isset($entityData['description']) ? $entityData['description'] : 'Laboratory equipment and medical supplies from MaxMed UAE') }}",
        "provider": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "legalName": "MaxMed Scientific & Laboratory Equipment Trading Co L.L.C",
            "description": "Leading laboratory equipment supplier in United Arab Emirates",
            "url": "https://maxmedme.com",
            "telephone": "+971-55-460-2500",
            "email": "sales@maxmedme.com",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "AE",
                "addressRegion": "Dubai",
                "addressLocality": "Dubai"
            },
            "areaServed": {
                "@type": "Country",
                "name": "United Arab Emirates"
            },
            "foundingLocation": {
                "@type": "Place",
                "name": "Dubai, UAE"
            },
            "knowsAbout": [
                "Laboratory Equipment",
                "Medical Equipment", 
                "Scientific Instruments",
                "Diagnostic Tools",
                "Research Equipment",
                "Laboratory Supplies",
                "Medical Supplies",
                "Healthcare Equipment"
            ],
            "serviceType": [
                "Laboratory Equipment Supply",
                "Medical Equipment Distribution",
                "Scientific Instrument Sales", 
                "Technical Support",
                "Equipment Installation",
                "Training Services",
                "Maintenance Services"
            ]
        },
        @if(isset($relationships))
        "relatedTo": {!! json_encode(array_values($relationships)) !!},
        @endif
        @if(isset($entityData['target_industries']))
        "industry": {!! json_encode($entityData['target_industries']) !!},
        @endif
        @if(isset($entityData['key_applications']))
        "applicationCategory": {!! json_encode($entityData['key_applications']) !!},
        @endif
        "keywords": "{{ implode(', ', $aiContent['semantic_keywords'] ?? []) }}",
        "sameAs": [
            "https://maxmedme.com",
            "https://www.linkedin.com/company/maxmed-uae"
        ]
    }
}
</script>

{{-- Entity Relationship Schema for AI Context --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "Organization",
            "@id": "https://maxmedme.com/#organization",
            "name": "MaxMed UAE",
            "legalName": "MaxMed Scientific & Laboratory Equipment Trading Co L.L.C",
            "url": "https://maxmedme.com",
            "description": "Leading laboratory equipment and medical supplies distributor in Dubai, United Arab Emirates. Serving hospitals, research institutions, universities, and healthcare facilities across the Middle East since establishment.",
            "foundingLocation": "Dubai, UAE",
            "areaServed": ["UAE", "Dubai", "Abu Dhabi", "Sharjah", "Middle East", "GCC"],
            "hasOfferCatalog": {
                "@type": "OfferCatalog",
                "name": "Laboratory & Medical Equipment Catalog",
                "numberOfItems": "1000+",
                "itemListElement": [
                    {
                        "@type": "OfferCatalog",
                        "name": "Laboratory Equipment",
                        "description": "Complete range of laboratory equipment including PCR machines, centrifuges, microscopes"
                    },
                    {
                        "@type": "OfferCatalog", 
                        "name": "Medical Equipment",
                        "description": "Medical diagnostic and healthcare equipment for hospitals and clinics"
                    },
                    {
                        "@type": "OfferCatalog",
                        "name": "Scientific Instruments", 
                        "description": "Precision scientific instruments for research and analysis"
                    }
                ]
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+971-55-460-2500",
                "email": "sales@maxmedme.com",
                "contactType": "sales",
                "areaServed": "UAE",
                "availableLanguage": ["English", "Arabic"]
            }
        },
        @if($product)
        {
            "@type": "Product",
            "@id": "{{ route('product.show', $product) }}#product",
            "name": "{{ $product->name }}",
            "manufacturer": {
                "@id": "https://maxmedme.com/#organization"
            },
            "brand": {
                "@type": "Brand",
                "name": "{{ $product->brand->name ?? 'MaxMed UAE' }}"
            },
            "category": "{{ $product->category->name ?? 'Laboratory Equipment' }}",
            "isPartOf": {
                "@id": "https://maxmedme.com/#organization"
            }
        }
        @elseif($category)
        {
            "@type": "CollectionPage",
            "@id": "{{ route('categories.show', $category) }}#category",
            "name": "{{ $category->name }}",
            "isPartOf": {
                "@id": "https://maxmedme.com/#organization"
            },
            "about": {
                "@type": "Thing",
                "name": "{{ $category->name }}",
                "description": "{{ $category->description ?? $category->name . ' equipment and supplies' }}"
            }
        }
        @endif
    ]
}
</script>

{{-- FAQ Schema for Voice Search & AI Assistants --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What is {{ $product ? $product->name : ($category ? $category->name : 'MaxMed UAE') }}?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ isset($entityData['product_description']) ? strip_tags($entityData['product_description']) : (isset($entityData['description']) ? strip_tags($entityData['description']) : 'MaxMed UAE is the leading laboratory equipment supplier in Dubai, United Arab Emirates, providing professional equipment to hospitals, research institutions, and healthcare facilities.') }}"
            }
        },
        @if($product)
        {
            "@type": "Question", 
            "name": "Where can I buy {{ $product->name }} in Dubai?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ $product->name }} is available from MaxMed UAE, the leading laboratory equipment supplier in Dubai. Contact us at +971 55 460 2500 for pricing and availability. We provide same-day quotes and fast delivery across UAE."
            }
        },
        {
            "@type": "Question",
            "name": "What are the applications of {{ $product->name }}?",
            "acceptedAnswer": {
                "@type": "Answer", 
                "text": "{{ $product->name }} is used in {{ implode(', ', $entityData['key_applications'] ?? ['laboratory testing', 'research applications', 'quality control']) }}. It serves {{ implode(', ', $entityData['target_industries'] ?? ['healthcare', 'research', 'pharmaceutical']) }} industries."
            }
        }
        @elseif($category)
        {
            "@type": "Question",
            "name": "What {{ $category->name }} products does MaxMed UAE offer?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "MaxMed UAE offers a comprehensive range of {{ $category->name }} equipment and supplies. We are the leading distributor in Dubai, UAE, serving hospitals, research institutions, and healthcare facilities with professional-grade equipment."
            }
        }
        @endif
    ]
}
</script>

{{-- Semantic Web Data for AI Training --}}
<script type="application/ld+json">
{
    "@context": {
        "@vocab": "https://schema.org/",
        "maxmed": "https://maxmedme.com/vocab/",
        "lab": "https://schema.org/",
        "medical": "https://schema.org/"
    },
    "@type": "Dataset",
    "name": "MaxMed UAE {{ $product ? 'Product' : 'Category' }} Data",
    "description": "Structured data about {{ $product ? $product->name : ($category ? $category->name : 'MaxMed UAE') }} for AI understanding and search optimization",
    "publisher": {
        "@type": "Organization",
        "name": "MaxMed UAE"
    },
    "about": {
        "@type": "Thing",
        "name": "{{ $product ? $product->name : ($category ? $category->name : 'Laboratory Equipment') }}",
        "description": "{{ isset($entityData['product_description']) ? $entityData['product_description'] : (isset($entityData['description']) ? $entityData['description'] : 'Professional laboratory and medical equipment') }}",
        @if(isset($entityData['entity_type']))
        "additionalType": "{{ $entityData['entity_type'] }}",
        @endif
        @if(isset($entityData['supplier_name']))
        "provider": "{{ $entityData['supplier_name'] }}",
        @endif
        @if(isset($entityData['supplier_location']))
        "location": "{{ $entityData['supplier_location'] }}",
        @endif
        @if(isset($entityData['geographic_coverage']))
        "areaServed": {!! json_encode($entityData['geographic_coverage']) !!},
        @endif
        @if(isset($entityData['support_services']))
        "serviceType": {!! json_encode($entityData['support_services']) !!},
        @endif
        "keywords": "{{ implode(', ', $aiContent['semantic_keywords'] ?? []) }}"
    },
    "license": "https://creativecommons.org/licenses/by/4.0/",
    "inLanguage": "en-US"
}
</script>
@endif

{{-- Meta tags for AI crawlers --}}
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

{{-- AI-specific meta tags --}}
<meta name="ai-content-type" content="{{ $type }}">
<meta name="ai-entity-name" content="{{ $product ? $product->name : ($category ? $category->name : 'MaxMed UAE') }}">
<meta name="ai-supplier" content="MaxMed UAE">
<meta name="ai-location" content="Dubai, UAE">
@if(isset($entityData['entity_category']))
<meta name="ai-category" content="{{ $entityData['entity_category'] }}">
@endif
@if(isset($entityData['entity_brand']))
<meta name="ai-brand" content="{{ $entityData['entity_brand'] }}">
@endif

{{-- Structured markup for AI understanding --}}
<div style="display: none;" class="ai-structured-data">
    <span itemscope itemtype="https://schema.org/{{ $product ? 'Product' : 'Thing' }}">
        <span itemprop="name">{{ $product ? $product->name : ($category ? $category->name : 'MaxMed UAE') }}</span>
        <span itemprop="description">{{ isset($entityData['product_description']) ? strip_tags($entityData['product_description']) : (isset($entityData['description']) ? strip_tags($entityData['description']) : 'Laboratory equipment supplier') }}</span>
        <span itemscope itemtype="https://schema.org/Organization" itemprop="manufacturer">
            <span itemprop="name">MaxMed UAE</span>
            <span itemprop="telephone">+971-55-460-2500</span>
            <span itemprop="email">sales@maxmedme.com</span>
            <span itemprop="url">https://maxmedme.com</span>
            <span itemscope itemtype="https://schema.org/PostalAddress" itemprop="address">
                <span itemprop="addressCountry">AE</span>
                <span itemprop="addressRegion">Dubai</span>
            </span>
        </span>
        @if($product && $product->category)
        <span itemprop="category">{{ $product->category->name }}</span>
        @endif
        @if(isset($entityData['key_applications']))
        @foreach($entityData['key_applications'] as $application)
        <span itemprop="applicationCategory">{{ $application }}</span>
        @endforeach
        @endif
    </span>
</div> 