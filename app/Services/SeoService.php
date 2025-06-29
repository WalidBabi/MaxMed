<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;

class SeoService
{
    // Core MaxMed keywords for all pages
    private $coreKeywords = [
        'laboratory equipment', 'medical equipment', 'Dubai', 'UAE', 'MaxMed',
        'scientific instruments', 'diagnostic tools', 'lab supplies'
    ];

    // High-value keywords from search console data
    private $primaryKeywords = [
        'laboratory equipment Dubai', 'lab instruments UAE', 'medical equipment supplier',
        'diagnostic equipment', 'scientific equipment', 'PCR equipment', 'microscopes',
        'centrifuge machines', 'analytical instruments', 'rapid test kits',
        'fume hood suppliers', 'dental consumables', 'veterinary diagnostics',
        'point of care testing equipment', 'laboratory sterilization'
    ];

    // High-impression, low-CTR keywords from search console that need optimization
    private $targetKeywords = [
        'fume hood suppliers in uae',
        'dental consumables',
        'rapid veterinary diagnostics uae',
        'point of care testing equipment',
        'laboratory equipment sterilization',
        'pcr machine suppliers uae',
        'veterinary diagnostic kits available in dubai',
        'lab consumables',
        'dental supplies uae',
        'chromatography clinical consumables',
        'laboratory centrifuge suppliers',
        'benchtop autoclave',
        'medical testing equipment',
        'laboratory refrigerator',
        'veterinary biotech solutions uae'
    ];

    public function generateProductMeta(Product $product): array
    {
        $title = $this->generateProductTitle($product);
        $description = $this->generateProductDescription($product);
        $keywords = $this->generateProductKeywords($product);

        return [
            'title' => $title,
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'og_title' => $title,
            'og_description' => $description,
            'canonical_url' => route('product.show', $product)
        ];
    }

    public function generateCategoryMeta(Category $category): array
    {
        $title = $this->generateCategoryTitle($category);
        $description = $this->generateCategoryDescription($category);
        $keywords = $this->generateCategoryKeywords($category);

        return [
            'title' => $title,
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'og_title' => $title,
            'og_description' => $description,
            'canonical_url' => $this->getCategoryCanonicalUrl($category)
        ];
    }

    public function generateNewsMeta(News $news): array
    {
        $title = $this->generateNewsTitle($news);
        $description = $this->generateNewsDescription($news);
        $keywords = $this->generateNewsKeywords($news);

        return [
            'title' => $title,
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'og_title' => $title,
            'og_description' => $description,
            'canonical_url' => route('news.show', $news)
        ];
    }

    public function generateHomeMeta(): array
    {
        return [
            'title' => 'MaxMed UAE - Laboratory Equipment & Medical Supplies Dubai',
            'meta_description' => '🔬 Leading lab equipment supplier in Dubai! PCR machines, centrifuges, fume hoods, autoclaves & more. ✅ Same-day quotes ☎️ +971 55 460 2500 🚚 Fast UAE delivery',
            'meta_keywords' => implode(', ', array_merge($this->primaryKeywords, $this->targetKeywords, [
                'MaxMed UAE', 'laboratory supplier Dubai', 'medical equipment UAE',
                'PCR machine Dubai', 'centrifuge supplier', 'microscope UAE',
                'diagnostic equipment Dubai', 'lab technology', '+971 55 460 2500'
            ])),
            'og_title' => 'MaxMed UAE - #1 Laboratory Equipment Supplier in Dubai',
            'og_description' => 'Leading lab equipment supplier in Dubai! PCR machines, centrifuges, fume hoods, autoclaves & more. Same-day quotes, fast UAE delivery.',
            'canonical_url' => url('/')
        ];
    }

    private function generateProductTitle(Product $product): string
    {
        $baseName = $product->name;
        $category = $product->category ? $product->category->name : 'Laboratory Equipment';
        $brand = $product->brand ? $product->brand->name : '';

        // Optimize title for search engines while keeping it natural
        $title = "{$baseName}";
        
        if ($brand && !str_contains($baseName, $brand)) {
            $title = "{$brand} {$baseName}";
        }

        // Add category if not already in title
        if (!str_contains(strtolower($title), strtolower($category))) {
            $title .= " - {$category}";
        }

        // Add location for local SEO
        if (!str_contains($title, 'Dubai') && !str_contains($title, 'UAE')) {
            $title .= " | Dubai UAE";
        }

        $title .= " | MaxMed";

        return Str::limit($title, 60);
    }

    private function generateProductDescription(Product $product): string
    {
        $description = strip_tags($product->description ?? '');
        $category = $product->category ? $product->category->name : 'laboratory equipment';
        $brand = $product->brand ? $product->brand->name : 'MaxMed';

        if (empty($description)) {
            $description = "Professional {$category} from {$brand}. High-quality scientific equipment available in Dubai, UAE.";
        }

        // Enhance description with SEO elements
        $enhancement = " Contact MaxMed UAE at +971 55 460 2500 for pricing and availability. Fast delivery across UAE.";
        
        $fullDescription = Str::limit($description, 120) . $enhancement;
        
        return Str::limit($fullDescription, 160);
    }

    private function generateProductKeywords(Product $product): string
    {
        $keywords = $this->coreKeywords;
        
        // Add product-specific keywords
        $keywords[] = $product->name;
        
        if ($product->category) {
            $keywords[] = $product->category->name;
            $keywords[] = $product->category->name . ' Dubai';
        }
        
        if ($product->brand) {
            $keywords[] = $product->brand->name;
            $keywords[] = $product->brand->name . ' UAE';
        }

        // Add product name variations
        $productWords = explode(' ', $product->name);
        foreach ($productWords as $word) {
            if (strlen($word) > 3) {
                $keywords[] = $word . ' Dubai';
                $keywords[] = $word . ' UAE';
            }
        }

        return implode(', ', array_unique($keywords));
    }

    private function generateCategoryTitle(Category $category): string
    {
        $title = $category->name;
        
        // Add descriptive context
        if (!str_contains($title, 'Equipment') && !str_contains($title, 'Instruments')) {
            $title .= " Equipment";
        }
        
        $title .= " in Dubai UAE | Laboratory Supplies | MaxMed";
        
        return Str::limit($title, 60);
    }

    private function generateCategoryDescription(Category $category): string
    {
        $productCount = $category->products()->count();
        $description = "Explore our comprehensive range of {$category->name} equipment in Dubai, UAE.";
        
        if ($productCount > 0) {
            $description .= " {$productCount}+ quality products available.";
        }
        
        $description .= " Professional laboratory and medical equipment from trusted brands. Contact MaxMed at +971 55 460 2500 for expert consultation and competitive pricing.";
        
        return Str::limit($description, 160);
    }

    private function generateCategoryKeywords(Category $category): string
    {
        $keywords = $this->coreKeywords;
        
        $keywords[] = $category->name;
        $keywords[] = $category->name . ' Dubai';
        $keywords[] = $category->name . ' UAE';
        $keywords[] = $category->name . ' equipment';
        $keywords[] = $category->name . ' supplier';
        
        return implode(', ', array_unique($keywords));
    }

    private function generateNewsTitle(News $news): string
    {
        $title = $news->title;
        
        if (!str_contains($title, 'MaxMed') && !str_contains($title, 'UAE')) {
            $title .= " | MaxMed UAE";
        }
        
        return Str::limit($title, 60);
    }

    private function generateNewsDescription(News $news): string
    {
        $description = strip_tags($news->content ?? $news->excerpt ?? '');
        
        if (empty($description)) {
            $description = "Latest news and updates from MaxMed UAE - Leading laboratory and medical equipment supplier in Dubai.";
        }
        
        return Str::limit($description, 160);
    }

    private function generateNewsKeywords(News $news): string
    {
        $keywords = $this->coreKeywords;
        $keywords[] = 'news';
        $keywords[] = 'updates';
        $keywords[] = 'MaxMed news';
        
        return implode(', ', array_unique($keywords));
    }

    private function getCategoryCanonicalUrl(Category $category): string
    {
        return route('categories.show', $category);
    }

    public function generateContactMeta(): array
    {
        return [
            'title' => 'Contact MaxMed UAE - Lab Equipment Supplier | +971 55 460 2500',
            'meta_description' => '📞 Contact MaxMed UAE for lab equipment quotes! Call +971 55 460 2500 or email sales@maxmedme.com ⚡ Fast response ✅ Expert consultation 🚚 UAE delivery',
            'meta_keywords' => implode(', ', array_merge($this->coreKeywords, [
                'contact MaxMed', '+971 55 460 2500', 'sales@maxmedme.com',
                'laboratory equipment contact', 'Dubai medical equipment supplier',
                'UAE scientific equipment contact', 'lab equipment quote Dubai'
            ])),
            'canonical_url' => route('contact')
        ];
    }

    public function generateAboutMeta(): array
    {
        return [
            'title' => 'About MaxMed UAE - Leading Lab Equipment Supplier Since 2010',
            'meta_description' => '🏆 MaxMed UAE: 14+ years supplying premium lab equipment in Dubai. ✅ Trusted by 500+ labs ⭐ ISO certified suppliers 🔬 PCR, centrifuge, microscope experts',
            'meta_keywords' => implode(', ', array_merge($this->coreKeywords, [
                'about MaxMed', 'company profile', 'laboratory equipment supplier UAE',
                'medical equipment company Dubai', 'scientific equipment provider',
                'lab equipment history Dubai', 'MaxMed experience'
            ])),
            'canonical_url' => route('about')
        ];
    }

    public function generateIndustryMeta(): array
    {
        return [
            'title' => 'Industries We Serve - Lab Equipment Solutions | MaxMed UAE',
            'meta_description' => '🏥 MaxMed serves hospitals, clinics, universities & research labs in UAE. Complete lab equipment solutions ⚡ Expert consultation ☎️ +971 55 460 2500',
            'meta_keywords' => implode(', ', array_merge($this->primaryKeywords, [
                'healthcare industry UAE', 'research facilities Dubai', 'university lab equipment',
                'hospital supplies', 'clinic equipment', 'diagnostic centers', 'pharmaceutical industry',
                'biotechnology equipment', 'academic research', 'industrial testing'
            ])),
            'og_title' => 'Industries We Serve - MaxMed UAE',
            'og_description' => 'Complete lab equipment solutions for healthcare, research, and educational institutions across UAE.',
            'canonical_url' => route('industry.index')
        ];
    }

    /**
     * Generate FAQ schema for SEO
     */
    public function generateFAQSchema(array $faqs): array
    {
        $faqSchema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => []
        ];

        foreach ($faqs as $faq) {
            $faqSchema["mainEntity"][] = [
                "@type" => "Question",
                "name" => $faq['question'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $faq['answer']
                ]
            ];
        }

        return $faqSchema;
    }

    /**
     * Generate internal linking suggestions based on content
     */
    public function generateInternalLinks(string $content, string $currentUrl = ''): array
    {
        $suggestions = [];
        $content = strtolower($content);

        // Define internal linking opportunities
        $linkingOpportunities = [
            'laboratory equipment' => route('products.index'),
            'medical supplies' => route('products.index') . '?category=medical',
            'diagnostic tools' => route('products.index') . '?category=diagnostic',
            'pcr machine' => route('products.index') . '?search=pcr',
            'microscope' => route('products.index') . '?search=microscope',
            'centrifuge' => route('products.index') . '?search=centrifuge',
            'contact us' => route('contact'),
            'about maxmed' => route('about'),
            'our partners' => route('partners.index'),
            'latest news' => route('news.index'),
        ];

        foreach ($linkingOpportunities as $keyword => $url) {
            if (strpos($content, $keyword) !== false && $url !== $currentUrl) {
                $suggestions[] = [
                    'keyword' => $keyword,
                    'url' => $url,
                    'anchor_text' => ucwords($keyword)
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Generate related products for cross-linking
     */
    public function getRelatedProducts(Product $product, int $limit = 4): array
    {
        $related = Product::where('id', '!=', $product->id)
            ->where(function($query) use ($product) {
                if ($product->category_id) {
                    $query->where('category_id', $product->category_id);
                }
                if ($product->brand_id) {
                    $query->orWhere('brand_id', $product->brand_id);
                }
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        return $related->map(function($item) {
            return [
                'name' => $item->name,
                'url' => route('product.show', $item),
                'image' => $item->image_url,
                'price' => $item->price_aed ?? $item->price
            ];
        })->toArray();
    }

    /**
     * Generate category navigation breadcrumbs
     */
    public function generateCategoryBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('welcome')]
        ];

        // Add parent categories
        $parents = [];
        $current = $category;
        while ($current->parent) {
            $parents[] = $current->parent;
            $current = $current->parent;
        }

        // Reverse to get correct order
        $parents = array_reverse($parents);
        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'name' => $parent->name,
                'url' => route('categories.show', $parent)
            ];
        }

        // Add current category
        $breadcrumbs[] = [
            'name' => $category->name,
            'url' => route('categories.show', $category)
        ];

        return $breadcrumbs;
    }

    /**
     * Generate product breadcrumbs
     */
    public function generateProductBreadcrumbs(Product $product): array
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('welcome')],
            ['name' => 'Products', 'url' => route('products.index')]
        ];

        if ($product->category) {
            $breadcrumbs = array_merge($breadcrumbs, $this->generateCategoryBreadcrumbs($product->category));
        }

        $breadcrumbs[] = [
            'name' => $product->name,
            'url' => route('product.show', $product)
        ];

        return $breadcrumbs;
    }

    /**
     * Generate page-specific FAQs
     */
    public function getPageFAQs(string $pageType, $entity = null): array
    {
        $faqs = [];

        switch ($pageType) {
            case 'home':
                $faqs = [
                    [
                        'question' => 'What types of laboratory equipment does MaxMed UAE supply?',
                        'answer' => 'MaxMed UAE supplies a comprehensive range of laboratory equipment including PCR machines, microscopes, centrifuges, analytical instruments, diagnostic tools, and scientific instruments for healthcare and research facilities across UAE.'
                    ],
                    [
                        'question' => 'Do you provide installation and maintenance services?',
                        'answer' => 'Yes, MaxMed UAE provides complete installation, training, and maintenance services for all laboratory equipment. Our certified technicians ensure proper setup and ongoing support.'
                    ],
                    [
                        'question' => 'What areas do you serve in UAE?',
                        'answer' => 'MaxMed UAE serves all emirates including Dubai, Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, and Umm Al Quwain. We also serve other GCC countries.'
                    ],
                    [
                        'question' => 'How can I request a quotation?',
                        'answer' => 'You can request a quotation by calling +971 55 460 2500, emailing sales@maxmedme.com, or using our online quotation form on any product page.'
                    ]
                ];
                break;

            case 'product':
                if ($entity) {
                    $faqs = [
                        [
                            'question' => "What is the warranty period for {$entity->name}?",
                            'answer' => "All MaxMed UAE products come with comprehensive warranty coverage. The specific warranty period for {$entity->name} varies by manufacturer. Please contact us at +971 55 460 2500 for detailed warranty information."
                        ],
                        [
                            'question' => "Is installation included with {$entity->name}?",
                            'answer' => "Yes, MaxMed UAE provides professional installation services for {$entity->name}. Our certified technicians ensure proper setup and provide training on equipment operation."
                        ],
                        [
                            'question' => "What is the delivery time for {$entity->name}?",
                            'answer' => "Delivery time for {$entity->name} typically ranges from 2-7 business days within UAE, depending on stock availability and location. For urgent requirements, please contact us for expedited delivery options."
                        ]
                    ];
                }
                break;

            case 'contact':
                $faqs = [
                    [
                        'question' => 'What are MaxMed UAE business hours?',
                        'answer' => 'MaxMed UAE is open Monday through Friday from 9:00 AM to 6:00 PM. For urgent inquiries outside business hours, please email sales@maxmedme.com.'
                    ],
                    [
                        'question' => 'How can I get technical support?',
                        'answer' => 'For technical support, please call our technical team at +971 55 460 2500 or email support@maxmedme.com. We provide comprehensive technical assistance for all our equipment.'
                    ],
                    [
                        'question' => 'Do you offer training programs?',
                        'answer' => 'Yes, MaxMed UAE offers comprehensive training programs for laboratory equipment operation, maintenance, and safety protocols. Contact us to schedule training sessions.'
                    ]
                ];
                break;
        }

        return $faqs;
    }

    /**
     * Generate specific landing page content for high-impression keywords
     */
    public function generateSpecificKeywordMeta(string $keyword): array
    {
        $keywordMetas = [
            'fume hood suppliers in uae' => [
                'title' => 'Fume Hood Suppliers UAE - Laboratory Safety | MaxMed Dubai',
                'meta_description' => '🔬 #1 Fume Hood Suppliers in UAE! Chemical, biological & radioisotope fume hoods ✅ CE certified ⚡ Installation included ☎️ +971 55 460 2500',
                'keywords' => 'fume hood suppliers UAE, laboratory fume hoods Dubai, chemical fume hood, biological safety cabinet, lab ventilation UAE'
            ],
            'dental consumables' => [
                'title' => 'Dental Consumables UAE - Dental Supplies Dubai | MaxMed',
                'meta_description' => '🦷 Premium dental consumables UAE! Impression materials, dental burs, disposables & more ✅ Same-day delivery Dubai ☎️ +971 55 460 2500',
                'keywords' => 'dental consumables UAE, dental supplies Dubai, dental materials, impression materials UAE, dental burs Dubai'
            ],
            'pcr machine suppliers uae' => [
                'title' => 'PCR Machine Suppliers UAE - Real-Time PCR Dubai | MaxMed',
                'meta_description' => '🧬 Top PCR machine suppliers UAE! Real-time PCR, thermal cyclers, qPCR systems ✅ Installation & training ⚡ Same-day quotes ☎️ +971 55 460 2500',
                'keywords' => 'PCR machine suppliers UAE, real-time PCR Dubai, thermal cycler UAE, qPCR systems, molecular diagnostics UAE'
            ],
            'laboratory centrifuge suppliers' => [
                'title' => 'Laboratory Centrifuge Suppliers UAE - Centrifuge Dubai | MaxMed',
                'meta_description' => '🔬 Premium centrifuge suppliers UAE! Benchtop, floor-standing, refrigerated centrifuges ✅ Service & calibration ☎️ +971 55 460 2500',
                'keywords' => 'laboratory centrifuge suppliers UAE, centrifuge Dubai, benchtop centrifuge, refrigerated centrifuge UAE'
            ],
            'benchtop autoclave' => [
                'title' => 'Benchtop Autoclave UAE - Steam Sterilizer Dubai | MaxMed',
                'meta_description' => '⚡ Benchtop autoclave UAE! Class B steam sterilizers, dental autoclaves ✅ Fast sterilization ⚡ Installation & training ☎️ +971 55 460 2500',
                'keywords' => 'benchtop autoclave UAE, steam sterilizer Dubai, dental autoclave, Class B autoclave UAE'
            ]
        ];

        return $keywordMetas[$keyword] ?? [
            'title' => 'Laboratory Equipment UAE - ' . ucwords($keyword) . ' | MaxMed Dubai',
            'meta_description' => "Premium " . $keyword . " available at MaxMed UAE. Contact +971 55 460 2500 for quotes and expert consultation.",
            'keywords' => $keyword . ', laboratory equipment UAE, scientific instruments Dubai'
        ];
    }
} 