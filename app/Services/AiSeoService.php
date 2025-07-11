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
} 