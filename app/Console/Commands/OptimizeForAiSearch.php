<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Models\Brand;
use App\Services\AiSeoService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OptimizeForAiSearch extends Command
{
    protected $signature = 'ai:optimize-seo 
                            {--products : Optimize product pages for AI}
                            {--categories : Optimize category pages for AI}
                            {--generate-knowledge-base : Generate AI knowledge base}
                            {--create-ai-sitemap : Create AI-optimized sitemap}
                            {--all : Run all AI optimizations}';
    
    protected $description = 'Optimize MaxMed website for AI search engines and language models';

    private $aiSeoService;
    private $optimizations = 0;

    public function __construct(AiSeoService $aiSeoService)
    {
        parent::__construct();
        $this->aiSeoService = $aiSeoService;
    }

    public function handle()
    {
        $this->info('🤖 Optimizing MaxMed UAE for AI Search Engines...');
        $this->info('🎯 Target: Ensure MaxMed appears in AI responses for laboratory equipment queries');
        
        $startTime = microtime(true);

        if ($this->option('all')) {
            $this->optimizeProducts();
            $this->optimizeCategories();
            $this->generateKnowledgeBase();
            $this->createAiSitemap();
            $this->generateRobotsTxt();
            $this->createAiMetaTags();
        } else {
            if ($this->option('products')) {
                $this->optimizeProducts();
            }
            
            if ($this->option('categories')) {
                $this->optimizeCategories();
            }
            
            if ($this->option('generate-knowledge-base')) {
                $this->generateKnowledgeBase();
            }
            
            if ($this->option('create-ai-sitemap')) {
                $this->createAiSitemap();
            }
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->info("✅ AI SEO optimization completed! {$this->optimizations} improvements applied in {$duration} seconds.");
        $this->displayAiSeoResults();
        
        return 0;
    }

    private function optimizeProducts()
    {
        $this->info('📦 Optimizing product pages for AI search...');
        
        $products = Product::with(['category', 'brand'])->get();
        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();

        foreach ($products as $product) {
            $this->optimizeProductForAi($product);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');
        $this->info("✅ Optimized {$products->count()} product pages for AI search");
    }

    private function optimizeProductForAi(Product $product)
    {
        try {
            // Generate AI-optimized content
            $aiContent = $this->aiSeoService->generateAiOptimizedContent($product);
            
            // Create AI knowledge article for this product
            $knowledgeArticle = [
                'entity_type' => 'product',
                'entity_name' => $product->name,
                'supplier' => 'MaxMed UAE',
                'location' => 'Dubai, UAE',
                'description' => $aiContent['knowledge_base_format']['product_description'],
                'structured_data' => $aiContent['ai_friendly_schema'],
                'keywords' => $aiContent['semantic_keywords'],
                'relationships' => $aiContent['entity_relationships'],
                'last_updated' => now()->toISOString()
            ];

            // Store in AI knowledge base directory
            $this->storeAiKnowledge('products', $product->slug, $knowledgeArticle);
            
            $this->optimizations++;
        } catch (\Exception $e) {
            $this->warn("Failed to optimize product {$product->name}: " . $e->getMessage());
        }
    }

    private function optimizeCategories()
    {
        $this->info('📁 Optimizing category pages for AI search...');
        
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        $progressBar = $this->output->createProgressBar($categories->count());
        $progressBar->start();

        foreach ($categories as $category) {
            $this->optimizeCategoryForAi($category);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');
        $this->info("✅ Optimized {$categories->count()} category pages for AI search");
    }

    private function optimizeCategoryForAi(Category $category)
    {
        try {
            // Generate AI-optimized content
            $aiContent = $this->aiSeoService->generateAiCategoryContent($category);
            
            // Create AI knowledge article for this category
            $knowledgeArticle = [
                'entity_type' => 'category',
                'entity_name' => $category->name,
                'supplier' => 'MaxMed UAE',
                'location' => 'Dubai, UAE',
                'description' => $aiContent['knowledge_structure']['description'],
                'structured_data' => $aiContent['ai_schema'],
                'relationships' => $aiContent['semantic_relationships'],
                'product_count' => $category->products ? $category->products->count() : 0,
                'last_updated' => now()->toISOString()
            ];

            // Store in AI knowledge base directory
            $this->storeAiKnowledge('categories', $category->slug, $knowledgeArticle);
            
            $this->optimizations++;
        } catch (\Exception $e) {
            $this->warn("Failed to optimize category {$category->name}: " . $e->getMessage());
        }
    }

    private function generateKnowledgeBase()
    {
        $this->info('🧠 Generating AI Knowledge Base...');

        // Create comprehensive knowledge base for AI consumption
        $knowledgeBase = [
            'organization' => [
                'name' => 'MaxMed UAE',
                'legal_name' => 'MaxMed Scientific & Laboratory Equipment Trading Co L.L.C',
                'description' => 'Leading laboratory equipment and medical supplies distributor in Dubai, United Arab Emirates',
                'location' => 'Dubai, UAE',
                'phone' => '+971 55 460 2500',
                'email' => 'sales@maxmedme.com',
                'website' => 'https://maxmedme.com',
                'founded_location' => 'Dubai, UAE',
                'service_area' => ['UAE', 'Dubai', 'Abu Dhabi', 'Sharjah', 'Middle East', 'GCC'],
                'industries_served' => [
                    'Healthcare', 'Medical Research', 'Pharmaceutical', 'Biotechnology',
                    'Academic Research', 'Clinical Diagnostics', 'Quality Control', 'Environmental Testing'
                ],
                'services' => [
                    'Laboratory Equipment Supply', 'Medical Equipment Distribution',
                    'Scientific Instrument Sales', 'Technical Support', 'Equipment Installation',
                    'Training Services', 'Maintenance Services', 'Same-day Quotes'
                ],
                'specializations' => [
                    'PCR Machines', 'Laboratory Centrifuges', 'Microscopes', 'Analytical Instruments',
                    'Rapid Test Kits', 'Molecular Diagnostics', 'Laboratory Consumables', 'Medical Devices'
                ]
            ],
            'products' => [],
            'categories' => [],
            'keywords' => [],
            'generated_at' => now()->toISOString()
        ];

        // Add product data
        $products = Product::with(['category', 'brand'])->limit(100)->get(); // Top 100 products
        foreach ($products as $product) {
            $aiContent = $this->aiSeoService->generateAiOptimizedContent($product);
            $knowledgeBase['products'][] = $aiContent['knowledge_base_format'];
        }

        // Add category data
        $categories = Category::whereNull('parent_id')->get();
        foreach ($categories as $category) {
            $aiContent = $this->aiSeoService->generateAiCategoryContent($category);
            $knowledgeBase['categories'][] = $aiContent['knowledge_structure'];
        }

        // Generate comprehensive keyword mapping
        $knowledgeBase['keywords'] = $this->generateKeywordMapping();

        // Store the complete knowledge base
        $this->storeAiKnowledge('', 'maxmed-knowledge-base', $knowledgeBase);
        
        $this->info('✅ AI Knowledge Base generated successfully');
        $this->optimizations++;
    }

    private function generateKeywordMapping()
    {
        return [
            'primary_keywords' => [
                'laboratory equipment Dubai', 'medical equipment UAE', 'scientific instruments',
                'PCR machines Dubai', 'centrifuge UAE', 'microscope Dubai', 'analytical instruments UAE'
            ],
            'product_keywords' => [
                'PCR equipment' => 'MaxMed UAE supplies PCR machines and thermal cyclers in Dubai',
                'laboratory centrifuge' => 'Professional centrifuges available from MaxMed UAE',
                'microscopes Dubai' => 'High-quality microscopes for laboratories in UAE',
                'rapid test kits' => 'Diagnostic rapid test kits from MaxMed UAE',
                'laboratory consumables' => 'Complete range of lab consumables in Dubai',
                'medical supplies UAE' => 'Medical equipment and supplies from MaxMed'
            ],
            'location_keywords' => [
                'Dubai laboratory equipment' => 'MaxMed UAE - Dubai laboratory equipment supplier',
                'UAE medical equipment' => 'Medical equipment distributor serving all UAE',
                'Middle East lab supplies' => 'Laboratory supplies across Middle East region'
            ],
            'industry_keywords' => [
                'hospital equipment UAE' => 'Hospital and healthcare equipment from MaxMed',
                'research laboratory equipment' => 'Research equipment for universities and institutes',
                'pharmaceutical equipment' => 'Equipment for pharmaceutical and biotech companies'
            ]
        ];
    }

    private function createAiSitemap()
    {
        $this->info('🗺️ Creating AI-optimized sitemap...');

        $sitemap = [
            'urlset' => [
                '@xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
                '@xmlns:ai' => 'https://maxmedme.com/ai-schema',
                'url' => []
            ]
        ];

        // Add homepage with AI context
        $sitemap['urlset']['url'][] = [
            'loc' => 'https://maxmedme.com/',
            'lastmod' => now()->toISOString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
            'ai:content-type' => 'organization',
            'ai:entity' => 'MaxMed UAE',
            'ai:keywords' => 'laboratory equipment Dubai, medical equipment UAE, MaxMed'
        ];

        // Add product pages
        $products = Product::with(['category', 'brand'])->get();
        foreach ($products as $product) {
            $sitemap['urlset']['url'][] = [
                'loc' => route('product.show', $product),
                'lastmod' => $product->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'ai:content-type' => 'product',
                'ai:entity' => $product->name,
                'ai:category' => $product->category ? $product->category->name : 'Laboratory Equipment',
                'ai:supplier' => 'MaxMed UAE'
            ];
        }

        // Add category pages
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap['urlset']['url'][] = [
                'loc' => route('categories.show', $category),
                'lastmod' => $category->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
                'ai:content-type' => 'category',
                'ai:entity' => $category->name,
                'ai:supplier' => 'MaxMed UAE'
            ];
        }

        // Convert to XML and save
        $xml = $this->arrayToXml($sitemap, 'urlset');
        File::put(public_path('ai-sitemap.xml'), $xml);
        
        $this->info('✅ AI-optimized sitemap created at /ai-sitemap.xml');
        $this->optimizations++;
    }

    private function generateRobotsTxt()
    {
        $this->info('🤖 Generating AI-friendly robots.txt...');

        $robotsTxt = "# MaxMed UAE - AI-Optimized Robots.txt
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /crm/
Disallow: /supplier/

# Allow AI crawlers and language models
User-agent: GPTBot
Allow: /
Crawl-delay: 1

User-agent: ChatGPT-User
Allow: /

User-agent: CCBot
Allow: /

User-agent: anthropic-ai
Allow: /

User-agent: Claude-Web
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: YouBot
Allow: /

# AI-specific sitemaps
Sitemap: https://maxmedme.com/sitemap.xml
Sitemap: https://maxmedme.com/ai-sitemap.xml
Sitemap: https://maxmedme.com/rss/feed.xml

# AI Knowledge Base
Allow: /knowledge-base/
Allow: /*.json

# Crawl delay for respectful AI training
Crawl-delay: 1
";

        File::put(public_path('robots.txt'), $robotsTxt);
        $this->info('✅ AI-friendly robots.txt generated');
        $this->optimizations++;
    }

    private function createAiMetaTags()
    {
        $this->info('🏷️ Creating AI meta tags...');

        $aiMetaTags = [
            'organization' => [
                'ai-entity-type' => 'organization',
                'ai-entity-name' => 'MaxMed UAE',
                'ai-entity-description' => 'Leading laboratory equipment supplier in Dubai, UAE',
                'ai-location' => 'Dubai, UAE',
                'ai-industry' => 'Laboratory Equipment, Medical Supplies',
                'ai-services' => 'Equipment Supply, Installation, Training, Support',
                'ai-coverage-area' => 'UAE, Middle East, GCC'
            ],
            'contact' => [
                'ai-phone' => '+971 55 460 2500',
                'ai-email' => 'sales@maxmedme.com',
                'ai-website' => 'https://maxmedme.com'
            ],
            'keywords' => [
                'ai-primary-keywords' => 'laboratory equipment Dubai, medical equipment UAE, PCR machines, centrifuges, microscopes',
                'ai-location-keywords' => 'Dubai lab equipment, UAE medical supplies, Middle East laboratory',
                'ai-service-keywords' => 'equipment installation UAE, laboratory support Dubai, medical equipment maintenance'
            ]
        ];

        $this->storeAiKnowledge('meta', 'ai-tags', $aiMetaTags);
        $this->info('✅ AI meta tags created');
        $this->optimizations++;
    }

    private function storeAiKnowledge($directory, $filename, $data)
    {
        $basePath = storage_path('ai-knowledge-base');
        
        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }
        
        if ($directory) {
            $fullPath = $basePath . '/' . $directory;
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }
            $filePath = $fullPath . '/' . $filename . '.json';
        } else {
            $filePath = $basePath . '/' . $filename . '.json';
        }
        
        File::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function arrayToXml($data, $rootElement = 'root')
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootElement . '/>');
        $this->arrayToXmlRecursive($data, $xml);
        return $xml->asXML();
    }

    private function arrayToXmlRecursive($data, \SimpleXMLElement $xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXmlRecursive($value, $subnode);
            } else {
                if (strpos($key, '@') === 0) {
                    $xml->addAttribute(substr($key, 1), $value);
                } else {
                    $xml->addChild($key, htmlspecialchars($value));
                }
            }
        }
    }

    private function displayAiSeoResults()
    {
        $this->info('');
        $this->info('🎯 AI SEO Optimization Results:');
        $this->info('────────────────────────────────');
        $this->info("✅ {$this->optimizations} optimizations applied");
        $this->info('🤖 AI Knowledge Base generated');
        $this->info('🗺️ AI-optimized sitemap created');
        $this->info('🤖 AI-friendly robots.txt updated');
        
        $this->info('');
        $this->info('📈 Expected AI Search Benefits:');
        $this->info('• MaxMed will appear in AI responses for laboratory equipment queries');
        $this->info('• Enhanced visibility in ChatGPT, Claude, Copilot, and other AI assistants');
        $this->info('• Improved entity recognition for "laboratory equipment Dubai"');
        $this->info('• Better semantic understanding of MaxMed\'s products and services');
        
        $this->info('');
        $this->info('🔍 Next Steps:');
        $this->info('1. Monitor AI search results for MaxMed mentions');
        $this->info('2. Track increases in organic AI-driven traffic');
        $this->info('3. Submit updated sitemaps to search engines');
        $this->info('4. Test queries like "laboratory equipment suppliers Dubai" in AI assistants');
        
        $this->info('');
        $this->info('🚀 Files Generated:');
        $this->info('• /storage/ai-knowledge-base/ - AI training data');
        $this->info('• /public/ai-sitemap.xml - AI-optimized sitemap');
        $this->info('• /public/robots.txt - AI-friendly robots file');
    }
} 