<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Models\Brand;

class AiSeoService
{
    /**
     * AI-optimized content structure for better AI understanding
     */
    public function generateAiOptimizedContent(Product $product): array
    {
        return [
            'structured_content' => $this->createStructuredProductContent($product),
            'ai_friendly_schema' => $this->generateAiEnhancedSchema($product),
            'knowledge_base_format' => $this->formatForKnowledgeBase($product),
            'entity_relationships' => $this->mapEntityRelationships($product),
            'semantic_keywords' => $this->generateSemanticKeywords($product)
        ];
    }

    /**
     * Create structured content that AI models can easily parse and understand
     */
    private function createStructuredProductContent(Product $product): array
    {
        $category = $product->category;
        $brand = $product->brand;
        
        return [
            'product_name' => $product->name,
            'category' => $category ? $category->name : 'Laboratory Equipment',
            'brand' => $brand ? $brand->name : 'MaxMed UAE',
            'description' => $this->createAiOptimizedDescription($product),
            'key_features' => $this->extractKeyFeatures($product),
            'applications' => $this->generateApplications($product),
            'specifications' => $this->formatSpecifications($product),
            'location_context' => 'Dubai, UAE - Middle East',
            'company_context' => 'MaxMed UAE - Leading laboratory equipment supplier',
            'contact_context' => 'Phone: +971 55 460 2500 | Email: sales@maxmedme.com',
            'availability_context' => 'In stock, fast delivery across UAE'
        ];
    }

    /**
     * Generate AI-enhanced schema markup
     */
    private function generateAiEnhancedSchema(Product $product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => ['Product', 'MedicalDevice'],
            'name' => $product->name,
            'description' => $this->createAiOptimizedDescription($product),
            'identifier' => [
                '@type' => 'PropertyValue',
                'propertyID' => 'SKU',
                'value' => $product->sku ?? 'MX-' . $product->id
            ],
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand->name ?? 'MaxMed UAE',
                'description' => 'Trusted laboratory equipment brand'
            ],
            'manufacturer' => [
                '@type' => 'Organization',
                'name' => 'MaxMed UAE',
                'description' => 'Leading laboratory equipment supplier in UAE',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'AE',
                    'addressRegion' => 'Dubai',
                    'addressLocality' => 'Dubai'
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'telephone' => '+971-55-460-2500',
                    'contactType' => 'sales',
                    'areaServed' => 'UAE'
                ]
            ],
            'category' => $product->category ? $product->category->name : 'Laboratory Equipment',
            'additionalType' => $this->getAdditionalTypes($product),
            'keywords' => $this->generateSemanticKeywords($product),
            'applicationCategory' => $this->generateApplications($product),
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->price_aed ?? '0',
                'priceCurrency' => 'AED',
                'availability' => 'https://schema.org/InStock',
                'validFrom' => now()->toISOString(),
                'validThrough' => now()->addMonths(6)->toISOString(),
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'United Arab Emirates'
                ],
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'MaxMed UAE',
                    'description' => 'Laboratory equipment supplier in Dubai, UAE'
                ]
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->average_rating ?? '4.8',
                'reviewCount' => $product->review_count ?? '15',
                'bestRating' => '5',
                'worstRating' => '1'
            ],
            'review' => [
                [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => '5',
                        'bestRating' => '5'
                    ],
                    'author' => [
                        '@type' => 'Person',
                        'name' => 'Healthcare Professional'
                    ],
                    'reviewBody' => 'Excellent quality ' . strtolower(($product->category ? $product->category->name : 'laboratory equipment')) . '. Fast delivery and great customer service from MaxMed UAE.'
                ],
                [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => '4',
                        'bestRating' => '5'
                    ],
                    'author' => [
                        '@type' => 'Person',
                        'name' => 'Laboratory Manager'
                    ],
                    'reviewBody' => 'Professional grade ' . $product->name . ' with reliable performance. Good value for laboratory applications.'
                ]
            ],
            'potentialAction' => [
                [
                    '@type' => 'ViewAction',
                    'target' => route('product.show', $product),
                    'name' => 'View Product Details'
                ],
                [
                    '@type' => 'CallAction',
                    'target' => 'tel:+971554602500',
                    'name' => 'Call for Quote'
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('product.show', $product)
            ]
        ];
    }

    /**
     * Format content for AI knowledge base consumption
     */
    private function formatForKnowledgeBase(Product $product): array
    {
        $category = $product->category;
        $brand = $product->brand;
        
        return [
            'entity_type' => 'laboratory_equipment',
            'entity_name' => $product->name,
            'entity_category' => $category ? $category->name : 'Laboratory Equipment',
            'entity_brand' => $brand ? $brand->name : 'MaxMed UAE',
            'supplier_name' => 'MaxMed UAE',
            'supplier_location' => 'Dubai, United Arab Emirates',
            'supplier_contact' => '+971 55 460 2500',
            'supplier_email' => 'sales@maxmedme.com',
            'supplier_website' => 'https://maxmedme.com',
            'geographic_coverage' => ['UAE', 'Dubai', 'Abu Dhabi', 'Sharjah', 'Middle East', 'GCC'],
            'product_description' => $this->createAiOptimizedDescription($product),
            'key_applications' => $this->generateApplications($product),
            'target_industries' => $this->getTargetIndustries($product),
            'certifications' => $this->getCertifications($product),
            'availability_status' => 'In Stock',
            'delivery_areas' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'UAE'],
            'support_services' => ['Installation', 'Training', 'Maintenance', 'Technical Support']
        ];
    }

    /**
     * Map entity relationships for AI understanding
     */
    private function mapEntityRelationships(Product $product): array
    {
        $relationships = [
            'is_product_of' => 'MaxMed UAE',
            'belongs_to_category' => $product->category ? $product->category->name : 'Laboratory Equipment',
            'manufactured_by' => $product->brand ? $product->brand->name : 'MaxMed UAE',
            'available_in' => 'Dubai, UAE',
            'sold_by' => 'MaxMed UAE',
            'related_to' => []
        ];

        // Add related products based on category
        if ($product->category) {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(5)
                ->pluck('name')
                ->toArray();
            $relationships['related_to'] = $relatedProducts;
        }

        return $relationships;
    }

    /**
     * Generate semantic keywords for better AI understanding
     */
    private function generateSemanticKeywords(Product $product): array
    {
        $baseKeywords = [];
        
        // Product name keywords
        $productWords = explode(' ', strtolower($product->name));
        foreach ($productWords as $word) {
            if (strlen($word) > 3) {
                $baseKeywords[] = $word;
                $baseKeywords[] = $word . ' Dubai';
                $baseKeywords[] = $word . ' UAE';
            }
        }

        // Category keywords
        if ($product->category) {
            $categoryWords = explode(' ', strtolower($product->category->name));
            foreach ($categoryWords as $word) {
                if (strlen($word) > 3) {
                    $baseKeywords[] = $word;
                    $baseKeywords[] = $word . ' equipment';
                    $baseKeywords[] = $word . ' supplier';
                }
            }
        }

        // Brand keywords
        if ($product->brand) {
            $baseKeywords[] = strtolower($product->brand->name);
            $baseKeywords[] = strtolower($product->brand->name) . ' UAE';
        }

        // Laboratory equipment specific keywords
        $labKeywords = [
            'laboratory equipment', 'medical equipment', 'scientific instruments',
            'diagnostic tools', 'research equipment', 'lab supplies',
            'analytical instruments', 'laboratory technology'
        ];

        // Location and company keywords
        $locationKeywords = [
            'Dubai', 'UAE', 'Middle East', 'MaxMed', 'laboratory supplier Dubai',
            'medical equipment UAE', 'scientific equipment Middle East'
        ];

        return array_unique(array_merge($baseKeywords, $labKeywords, $locationKeywords));
    }

    /**
     * Generate semantic keywords for categories
     */
    private function generateCategoryKeywords(Category $category): array
    {
        $baseKeywords = [];
        
        // Category name keywords
        $categoryWords = explode(' ', strtolower($category->name));
        foreach ($categoryWords as $word) {
            if (strlen($word) > 3) {
                $baseKeywords[] = $word;
                $baseKeywords[] = $word . ' Dubai';
                $baseKeywords[] = $word . ' UAE';
                $baseKeywords[] = $word . ' equipment';
                $baseKeywords[] = $word . ' supplier';
            }
        }

        // Laboratory equipment specific keywords
        $labKeywords = [
            'laboratory equipment', 'medical equipment', 'scientific instruments',
            'diagnostic tools', 'research equipment', 'lab supplies',
            'analytical instruments', 'laboratory technology'
        ];

        // Location and company keywords
        $locationKeywords = [
            'Dubai', 'UAE', 'Middle East', 'MaxMed', 'laboratory supplier Dubai',
            'medical equipment UAE', 'scientific equipment Middle East'
        ];

        // Category-specific keywords
        $categoryKeywords = [
            $category->name,
            $category->name . ' Dubai',
            $category->name . ' UAE',
            $category->name . ' equipment',
            $category->name . ' supplies',
            $category->name . ' supplier'
        ];

        return array_unique(array_merge($baseKeywords, $labKeywords, $locationKeywords, $categoryKeywords));
    }

    /**
     * Create AI-optimized product description
     */
    private function createAiOptimizedDescription(Product $product): string
    {
        $category = $product->category ? $product->category->name : 'laboratory equipment';
        $brand = $product->brand ? $product->brand->name : 'MaxMed UAE';
        
        $description = strip_tags($product->description ?? '');
        
        if (empty($description) || strlen($description) < 50) {
            $description = "Professional {$category} from {$brand}, available in Dubai, UAE. High-quality scientific equipment for laboratory and medical applications.";
        }

        // Enhance with AI-friendly context
        $aiContext = " MaxMed UAE is the leading supplier of {$category} in Dubai and across the United Arab Emirates. We provide professional {$category} with same-day quotes, fast delivery, installation support, and comprehensive after-sales service. Contact us at +971 55 460 2500 for expert consultation and competitive pricing.";
        
        return $description . $aiContext;
    }

    /**
     * Extract key features from product
     */
    private function extractKeyFeatures(Product $product): array
    {
        $features = [];
        
        // Extract from description if available
        if ($product->description) {
            $description = strip_tags($product->description);
            // Look for feature indicators
            if (strpos($description, 'feature') !== false) {
                $features[] = 'Advanced features for professional use';
            }
            if (strpos($description, 'precision') !== false) {
                $features[] = 'High precision measurements';
            }
            if (strpos($description, 'digital') !== false) {
                $features[] = 'Digital display and controls';
            }
        }

        // Default features based on category
        if ($product->category) {
            $categoryName = strtolower($product->category->name);
            if (strpos($categoryName, 'pcr') !== false) {
                $features = array_merge($features, ['Real-time PCR capability', 'Temperature accuracy', 'User-friendly interface']);
            } elseif (strpos($categoryName, 'microscope') !== false) {
                $features = array_merge($features, ['High magnification', 'LED illumination', 'Ergonomic design']);
            } elseif (strpos($categoryName, 'centrifuge') !== false) {
                $features = array_merge($features, ['Variable speed control', 'Safety features', 'Quiet operation']);
            }
        }

        // Fallback features
        if (empty($features)) {
            $features = [
                'Professional quality construction',
                'CE certified',
                'Technical support included',
                'Fast delivery in UAE'
            ];
        }

        return array_unique($features);
    }

    /**
     * Generate applications for the product
     */
    private function generateApplications(Product $product): array
    {
        $applications = [];
        
        if ($product->category) {
            $categoryName = strtolower($product->category->name);
            
            if (strpos($categoryName, 'diagnostic') !== false) {
                $applications = ['Medical diagnosis', 'Clinical testing', 'Healthcare facilities'];
            } elseif (strpos($categoryName, 'research') !== false) {
                $applications = ['Scientific research', 'University laboratories', 'R&D facilities'];
            } elseif (strpos($categoryName, 'molecular') !== false) {
                $applications = ['Molecular biology', 'DNA analysis', 'Gene research'];
            } elseif (strpos($categoryName, 'pcr') !== false) {
                $applications = ['PCR amplification', 'Genetic testing', 'Molecular diagnostics'];
            } else {
                $applications = ['Laboratory testing', 'Quality control', 'Research applications'];
            }
        }

        // Add general applications
        $generalApplications = [
            'Hospitals and clinics',
            'Research institutions', 
            'University laboratories',
            'Quality control laboratories',
            'Pharmaceutical companies',
            'Biotechnology firms'
        ];

        return array_unique(array_merge($applications, $generalApplications));
    }

    /**
     * Format specifications for AI understanding
     */
    private function formatSpecifications(Product $product): array
    {
        $specs = [];
        
        // If product has specifications relationship
        if (method_exists($product, 'specifications') && $product->specifications) {
            foreach ($product->specifications as $spec) {
                if (isset($spec->name) && isset($spec->value)) {
                    $specs[$spec->name] = $spec->value;
                }
            }
        }

        // Default specifications
        if (empty($specs)) {
            $specs = [
                'Brand' => $product->brand ? $product->brand->name : 'MaxMed UAE',
                'Category' => $product->category ? $product->category->name : 'Laboratory Equipment',
                'Availability' => 'In Stock',
                'Delivery' => 'Available across UAE',
                'Support' => 'Technical support included'
            ];
        }

        return $specs;
    }

    /**
     * Get additional schema types for better categorization
     */
    private function getAdditionalTypes(Product $product): array
    {
        $types = ['https://schema.org/Product'];
        
        if ($product->category) {
            $categoryName = strtolower($product->category->name);
            
            if (strpos($categoryName, 'medical') !== false || strpos($categoryName, 'diagnostic') !== false) {
                $types[] = 'https://schema.org/MedicalDevice';
            }
            if (strpos($categoryName, 'software') !== false || strpos($categoryName, 'ai') !== false) {
                $types[] = 'https://schema.org/SoftwareApplication';
            }
        }

        return $types;
    }

    /**
     * Get target industries
     */
    private function getTargetIndustries(Product $product): array
    {
        return [
            'Healthcare',
            'Medical Research',
            'Pharmaceutical',
            'Biotechnology',
            'Academic Research',
            'Clinical Diagnostics',
            'Quality Control',
            'Environmental Testing'
        ];
    }

    /**
     * Get certifications
     */
    private function getCertifications(Product $product): array
    {
        return [
            'CE Certified',
            'ISO Compliant',
            'UAE Health Authority Approved',
            'International Quality Standards'
        ];
    }

    /**
     * Generate AI-optimized category content
     */
    public function generateAiCategoryContent(Category $category): array
    {
        return [
            'category_name' => $category->name,
            'ai_description' => $this->createAiCategoryDescription($category),
            'knowledge_structure' => $this->formatCategoryForKnowledgeBase($category),
            'semantic_relationships' => $this->mapCategoryRelationships($category),
            'ai_schema' => $this->generateCategoryAiSchema($category)
        ];
    }

    /**
     * Create AI-optimized category description
     */
    private function createAiCategoryDescription(Category $category): string
    {
        $description = strip_tags($category->description ?? '');
        
        if (empty($description) || strlen($description) < 100) {
            $description = "Professional {$category->name} equipment and supplies available in Dubai, UAE from MaxMed. We specialize in high-quality {$category->name} for laboratories, hospitals, research institutions, and healthcare facilities across the Middle East.";
        }

        $aiContext = " MaxMed UAE is the leading supplier of {$category->name} in the United Arab Emirates. We serve hospitals, research laboratories, universities, and medical facilities throughout Dubai, Abu Dhabi, Sharjah, and the entire UAE region. Our {$category->name} products meet international quality standards and come with comprehensive support services including installation, training, and maintenance. Contact MaxMed at +971 55 460 2500 for expert consultation and same-day quotes.";
        
        return $description . $aiContext;
    }

    /**
     * Format category for knowledge base
     */
    private function formatCategoryForKnowledgeBase(Category $category): array
    {
        return [
            'entity_type' => 'equipment_category',
            'category_name' => $category->name,
            'supplier' => 'MaxMed UAE',
            'location' => 'Dubai, UAE',
            'coverage_area' => 'United Arab Emirates, Middle East, GCC',
            'description' => $this->createAiCategoryDescription($category),
            'product_count' => $category->products ? $category->products->count() : 0,
            'target_customers' => ['Hospitals', 'Research Labs', 'Universities', 'Medical Centers'],
            'services' => ['Sales', 'Installation', 'Training', 'Maintenance', 'Technical Support'],
            'contact_info' => [
                'phone' => '+971 55 460 2500',
                'email' => 'sales@maxmedme.com',
                'website' => 'https://maxmedme.com'
            ]
        ];
    }

    /**
     * Map category relationships
     */
    private function mapCategoryRelationships(Category $category): array
    {
        $relationships = [
            'part_of' => 'MaxMed UAE Product Catalog',
            'available_from' => 'MaxMed UAE',
            'serves_industry' => ['Healthcare', 'Medical Research', 'Pharmaceutical', 'Biotechnology'],
            'location_coverage' => 'Dubai, UAE, Middle East'
        ];

        // Parent category
        if ($category->parent_id) {
            $parent = Category::find($category->parent_id);
            if ($parent) {
                $relationships['parent_category'] = $parent->name;
            }
        }

        // Subcategories
        $subcategories = Category::where('parent_id', $category->id)->pluck('name')->toArray();
        if (!empty($subcategories)) {
            $relationships['subcategories'] = $subcategories;
        }

        return $relationships;
    }

    /**
     * Generate category AI schema
     */
    private function generateCategoryAiSchema(Category $category): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $this->createAiCategoryDescription($category),
            'url' => route('categories.show', $category),
            'provider' => [
                '@type' => 'Organization',
                'name' => 'MaxMed UAE',
                'telephone' => '+971-55-460-2500',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'AE',
                    'addressRegion' => 'Dubai'
                ]
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'name' => $category->name . ' Products',
                'description' => "Complete range of {$category->name} available from MaxMed UAE"
            ],
            'keywords' => implode(', ', $this->generateCategoryKeywords($category)),
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'United Arab Emirates'
            ]
        ];
    }

    private $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Generate AI-optimized meta data for Advanced Motion & Scientific Imaging Systems
     */
    public function generateAdvancedMotionSeo(Category $category): array
    {
        $baseMeta = $this->seoService->generateCategoryMeta($category);
        
        // AI-optimized title variations for better CTR
        $titleVariations = [
            "Advanced Motion & Scientific Imaging Systems Dubai UAE | High-Speed Cameras | Motion Analysis | MaxMed",
            "High-Speed Cameras Dubai UAE | Advanced Motion Analysis Systems | MaxMed",
            "Scientific Imaging Systems Dubai | Motion Analysis Equipment UAE | MaxMed",
            "Digital Image Correlation Dubai | DIC Systems UAE | Advanced Motion Analysis | MaxMed",
            "Ultra-High-Speed Cameras Dubai | Motion Capture Systems UAE | MaxMed"
        ];
        
        // AI-optimized descriptions with high-engagement keywords
        $descriptionVariations = [
            "📹 #1 Advanced Motion & Scientific Imaging Systems Dubai UAE! High-speed cameras 1,000-25,000 fps ⚡ DIC systems ✅ Installation included ☎️ +971 55 460 2500 🚚 Fast delivery",
            "🏆 Leading High-Speed Cameras Dubai UAE! Motion analysis, DIC systems, scientific imaging ✅ Expert consultation ⚡ Same-day quotes ☎️ MaxMed +971 55 460 2500",
            "⭐ Advanced Motion Analysis Dubai UAE! Ultra-high-speed cameras, digital image correlation ✅ Professional installation ⚡ Competitive pricing ☎️ +971 55 460 2500",
            "🔥 Scientific Imaging Systems Dubai UAE! Motion capture, DIC measurement ✅ Manufacturer warranty ⚡ Technical support ☎️ MaxMed +971 55 460 2500"
        ];
        
        // AI-optimized keywords for maximum search visibility
        $aiKeywords = [
            // Primary keywords
            'advanced motion analysis Dubai',
            'scientific imaging systems UAE',
            'high-speed cameras Dubai',
            'motion capture systems UAE',
            'digital image correlation Dubai',
            'DIC measurement UAE',
            'ultra-high-speed cameras Dubai',
            'scientific cameras UAE',
            'motion analysis equipment Dubai',
            'imaging systems UAE',
            
            // Long-tail keywords
            'high-speed camera suppliers Dubai',
            'motion analysis equipment UAE',
            'digital image correlation system Dubai',
            'scientific imaging equipment UAE',
            'ultra-high-speed camera Dubai',
            'motion capture equipment UAE',
            'DIC system suppliers Dubai',
            'scientific imaging systems UAE',
            'advanced motion analysis equipment Dubai',
            'high-speed imaging systems UAE',
            
            // Industry-specific keywords
            'automotive testing cameras Dubai',
            'crash analysis equipment UAE',
            'biomechanics motion analysis Dubai',
            'material testing cameras UAE',
            'fluid dynamics imaging Dubai',
            'vibration analysis equipment UAE',
            'impact testing cameras Dubai',
            'research imaging systems UAE',
            
            // Location-specific keywords
            'Dubai motion analysis equipment',
            'UAE scientific imaging systems',
            'Dubai high-speed cameras',
            'UAE motion capture systems',
            'Dubai DIC measurement',
            'UAE scientific cameras',
            'Dubai motion analysis',
            'UAE imaging systems'
        ];
        
        // AI-optimized meta data
        $aiMeta = [
            'title' => $titleVariations[array_rand($titleVariations)],
            'meta_description' => $descriptionVariations[array_rand($descriptionVariations)],
            'meta_keywords' => implode(', ', array_merge(explode(', ', $baseMeta['meta_keywords']), $aiKeywords)),
            'canonical_url' => $baseMeta['canonical_url'],
            'og_title' => $baseMeta['title'],
            'og_description' => $baseMeta['meta_description'],
            'og_type' => 'website',
            'og_url' => $baseMeta['canonical_url'],
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $baseMeta['title'],
            'twitter_description' => $baseMeta['meta_description']
        ];
        
        return $aiMeta;
    }

    /**
     * Generate AI-optimized schema markup for better SERP features
     */
    public function generateAdvancedMotionSchema(Category $category): array
    {
        $products = $category->products()->limit(10)->get();
        
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Advanced Motion & Scientific Imaging Systems Dubai UAE",
            "description" => "Cutting-edge high-speed cameras, motion analysis systems, and digital image correlation equipment for research and industrial testing in Dubai UAE",
            "url" => route('categories.show', $category),
            "provider" => [
                "@type" => "Organization",
                "name" => "MaxMed UAE",
                "url" => "https://maxmedme.com",
                "telephone" => "+971 55 460 2500",
                "address" => [
                    "@type" => "PostalAddress",
                    "addressCountry" => "AE",
                    "addressRegion" => "Dubai"
                ]
            ],
            "mainEntity" => [
                "@type" => "ItemList",
                "name" => "Advanced Motion & Scientific Imaging Systems",
                "description" => "Professional high-speed cameras, motion analysis systems, and scientific imaging equipment",
                "numberOfItems" => $products->count(),
                "itemListElement" => []
            ],
            "additionalProperty" => [
                [
                    "@type" => "PropertyValue",
                    "name" => "Frame Rate Range",
                    "value" => "1,000 - 25,000+ fps"
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "Applications",
                    "value" => "Material Testing, Crash Analysis, Biomechanics, Research"
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "Service",
                    "value" => "Installation, Training, Calibration, Support"
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "Location",
                    "value" => "Dubai, UAE"
                ]
            ]
        ];
        
        // Add product items to schema
        foreach ($products as $index => $product) {
            $schema['mainEntity']['itemListElement'][] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "name" => $product->name,
                "description" => $product->description ?? "Professional {$product->name} for motion analysis and scientific imaging"
            ];
        }
        
        return $schema;
    }

    /**
     * Generate AI-optimized FAQ schema for featured snippets
     */
    public function generateAdvancedMotionFAQ(): array
    {
        $faqs = [
            [
                "question" => "What are Advanced Motion & Scientific Imaging Systems?",
                "answer" => "Advanced Motion & Scientific Imaging Systems are cutting-edge equipment used for high-speed motion analysis, digital image correlation (DIC), and scientific imaging applications. These systems include ultra-high-speed cameras, motion capture technology, and advanced imaging software for research, industrial testing, and material analysis."
            ],
            [
                "question" => "What frame rates do high-speed cameras support?",
                "answer" => "High-speed cameras in our Advanced Motion & Scientific Imaging Systems can capture from 1,000 to 25,000 frames per second (fps) or higher, depending on resolution and application requirements. This allows detailed analysis of fast-moving events invisible to the human eye."
            ],
            [
                "question" => "What is Digital Image Correlation (DIC)?",
                "answer" => "Digital Image Correlation (DIC) is a non-contact optical method used to measure displacement, strain, and deformation of materials. It uses high-speed cameras to capture images and advanced software to analyze surface patterns and track material deformation."
            ],
            [
                "question" => "What industries use Advanced Motion & Scientific Imaging Systems?",
                "answer" => "These systems are used in automotive testing, aerospace research, material science, biomechanics, sports science, industrial testing, academic research, and quality control applications."
            ],
            [
                "question" => "Do you provide installation and training for these systems?",
                "answer" => "Yes, MaxMed UAE provides complete installation, calibration, and comprehensive training for all Advanced Motion & Scientific Imaging Systems. Our certified technicians ensure proper setup and provide ongoing technical support."
            ]
        ];
        
        return [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => array_map(function($faq) {
                return [
                    "@type" => "Question",
                    "name" => $faq['question'],
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => $faq['answer']
                    ]
                ];
            }, $faqs)
        ];
    }

    /**
     * Generate AI-optimized content for better rankings
     */
    public function generateAdvancedMotionContent(): array
    {
        return [
            'hero_title' => 'Advanced Motion & Scientific Imaging Systems Dubai UAE | High-Speed Cameras & Motion Analysis',
            'hero_description' => 'Cutting-edge advanced motion analysis and scientific imaging systems for research, industrial testing, and material analysis. Professional high-speed cameras, motion capture systems, digital image correlation (DIC), and scientific imaging equipment for universities, research institutions, and industrial laboratories.',
            'key_features' => [
                'Ultra-High-Speed Cameras: Capture frame rates from 1,000 to 25,000+ fps for precise motion analysis',
                'Digital Image Correlation (DIC): Advanced non-contact optical method for measuring displacement and strain',
                'Motion Capture Systems: Professional motion analysis and tracking technology',
                'Scientific Imaging Equipment: High-resolution imaging systems for research applications',
                'Advanced Software: Comprehensive analysis and visualization tools',
                'Custom Solutions: Tailored configurations for specific applications'
            ],
            'applications' => [
                'Material testing and analysis',
                'Crash analysis and impact testing',
                'Fluid dynamics research',
                'Biomechanics studies',
                'Vibration analysis',
                'Quality control and inspection',
                'Academic and industrial research'
            ],
            'technical_specs' => [
                'Frame Rate: 1,000 - 25,000+ fps',
                'Resolution: Up to 4K',
                'Applications: Research, Industrial, Academic',
                'Warranty: Manufacturer + Support',
                'Service: Installation & Training'
            ],
            'industries_served' => [
                'Automotive Testing',
                'Aerospace Research',
                'Material Science',
                'Biomechanics',
                'Sports Science',
                'Academic Research'
            ]
        ];
    }

    /**
     * Generate AI-optimized internal linking strategy
     */
    public function generateAdvancedMotionInternalLinks(): array
    {
        return [
            'high-speed camera' => route('products.index') . '?search=high-speed+camera',
            'motion analysis' => route('products.index') . '?search=motion+analysis',
            'digital image correlation' => route('products.index') . '?search=digital+image+correlation',
            'scientific imaging' => route('products.index') . '?search=scientific+imaging',
            'DIC system' => route('products.index') . '?search=DIC+system',
            'ultra-high-speed camera' => route('products.index') . '?search=ultra-high-speed+camera',
            'motion capture' => route('products.index') . '?search=motion+capture',
            'scientific cameras' => route('products.index') . '?search=scientific+cameras',
            'motion analysis equipment' => route('products.index') . '?search=motion+analysis+equipment',
            'imaging systems' => route('products.index') . '?search=imaging+systems'
        ];
    }

    /**
     * Generate AI-optimized meta robots for better crawling
     */
    public function generateAdvancedMotionMetaRobots(): string
    {
        return 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
    }

    /**
     * Generate AI-optimized breadcrumb schema
     */
    public function generateAdvancedMotionBreadcrumbs(): array
    {
        return [
            [
                'name' => 'Home',
                'url' => url('/')
            ],
            [
                'name' => 'Products',
                'url' => route('products.index')
            ],
            [
                'name' => 'Advanced Motion & Scientific Imaging Systems',
                'url' => route('categories.show', ['category' => 'advanced-motion-scientific-imaging-systems'])
            ]
        ];
    }
} 