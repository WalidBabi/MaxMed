<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\AiSeoService;
use App\Services\SeoService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AiAdvancedMotionSeo extends Command
{
    protected $signature = 'seo:ai-advanced-motion {--dry-run : Run without making changes}';
    protected $description = 'AI-powered SEO optimization for Advanced Motion & Scientific Imaging Systems category';

    private $aiSeoService;
    private $seoService;

    public function __construct(AiSeoService $aiSeoService, SeoService $seoService)
    {
        parent::__construct();
        $this->aiSeoService = $aiSeoService;
        $this->seoService = $seoService;
    }

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('🚀 Starting AI-powered SEO optimization for Advanced Motion & Scientific Imaging Systems...');
        
        // Find the category
        $category = Category::where('name', 'Advanced Motion & Scientific Imaging Systems')->first();
        
        if (!$category) {
            $this->error('❌ Advanced Motion & Scientific Imaging Systems category not found!');
            return 1;
        }
        
        $this->info("📁 Found category: {$category->name}");
        
        $optimizations = 0;
        
        // 1. Generate AI-optimized meta data
        $this->info("\n📝 Generating AI-optimized meta data...");
        $aiMeta = $this->aiSeoService->generateAdvancedMotionSeo($category);
        
        $this->line("   Title: " . Str::limit($aiMeta['title'], 80));
        $this->line("   Description: " . Str::limit($aiMeta['meta_description'], 100));
        $this->line("   Keywords: " . Str::limit($aiMeta['meta_keywords'], 150));
        
        // 2. Generate AI-optimized schema markup
        $this->info("\n🏗️ Generating AI-optimized schema markup...");
        $schema = $this->aiSeoService->generateAdvancedMotionSchema($category);
        $this->line("   Schema generated with {$schema['mainEntity']['numberOfItems']} products");
        
        // 3. Generate AI-optimized FAQ schema
        $this->info("\n❓ Generating AI-optimized FAQ schema...");
        $faqSchema = $this->aiSeoService->generateAdvancedMotionFAQ();
        $this->line("   FAQ schema generated with " . count($faqSchema['mainEntity']) . " questions");
        
        // 4. Generate AI-optimized content
        $this->info("\n📄 Generating AI-optimized content...");
        $content = $this->aiSeoService->generateAdvancedMotionContent();
        $this->line("   Content generated with " . count($content['key_features']) . " key features");
        
        // 5. Optimize products in this category
        $this->info("\n📦 Optimizing products in category...");
        $products = $category->products()->get();
        
        foreach ($products as $product) {
            $productOptimizations = $this->optimizeProduct($product, $category, $isDryRun);
            $optimizations += $productOptimizations;
        }
        
        $this->line("   Optimized {$products->count()} products");
        
        // 6. Generate internal linking strategy
        $this->info("\n🔗 Generating internal linking strategy...");
        $internalLinks = $this->aiSeoService->generateAdvancedMotionInternalLinks();
        $this->line("   Generated " . count($internalLinks) . " internal linking opportunities");
        
        // 7. Generate AI-optimized meta robots
        $this->info("\n🤖 Generating AI-optimized meta robots...");
        $metaRobots = $this->aiSeoService->generateAdvancedMotionMetaRobots();
        $this->line("   Meta robots: {$metaRobots}");
        
        // 8. Generate breadcrumb schema
        $this->info("\n🍞 Generating breadcrumb schema...");
        $breadcrumbs = $this->aiSeoService->generateAdvancedMotionBreadcrumbs();
        $this->line("   Breadcrumb schema generated with " . count($breadcrumbs) . " levels");
        
        // 9. Create AI-optimized sitemap entry
        $this->info("\n🗺️ Creating AI-optimized sitemap entry...");
        $this->createAdvancedMotionSitemapEntry($category, $isDryRun);
        
        // 10. Generate AI-optimized knowledge base
        $this->info("\n🧠 Generating AI knowledge base...");
        $this->generateAdvancedMotionKnowledgeBase($category, $isDryRun);
        
        // 11. Create AI-optimized social media meta
        $this->info("\n📱 Generating AI-optimized social media meta...");
        $this->generateAdvancedMotionSocialMeta($category, $isDryRun);
        
        // 12. Generate AI-optimized local SEO
        $this->info("\n📍 Generating AI-optimized local SEO...");
        $this->generateAdvancedMotionLocalSeo($category, $isDryRun);
        
        $this->info("\n✅ AI-powered SEO optimization completed!");
        $this->info("📈 Total optimizations: {$optimizations}");
        
        if (!$isDryRun) {
            $this->info("\n🎯 Next steps for first-page ranking:");
            $this->info("1. Submit updated sitemap to Google Search Console");
            $this->info("2. Monitor search rankings for the next 2-4 weeks");
            $this->info("3. Check Google Analytics for improved organic traffic");
            $this->info("4. Review search console for new keyword opportunities");
            $this->info("5. Monitor featured snippet opportunities");
        }
        
        return 0;
    }

    private function optimizeProduct(Product $product, Category $category, bool $isDryRun): int
    {
        $optimizations = 0;
        
        // Generate AI-optimized product meta data
        $aiProductMeta = $this->generateAiProductMeta($product, $category);
        
        // Check if product description needs enhancement
        if (empty($product->description) || strlen($product->description) < 200) {
            $enhancedDescription = $this->generateAiProductDescription($product, $category);
            
            if (!$isDryRun) {
                $product->update(['description' => $enhancedDescription]);
            }
            
            $optimizations++;
        }
        
        // Add AI-optimized keywords to product name if needed
        if (!str_contains($product->name, 'Dubai') && !str_contains($product->name, 'UAE')) {
            $enhancedName = $this->enhanceAiProductName($product, $category);
            
            if (!$isDryRun) {
                $product->update(['name' => $enhancedName]);
            }
            
            $optimizations++;
        }
        
        return $optimizations;
    }

    private function generateAiProductMeta(Product $product, Category $category): array
    {
        $baseMeta = $this->seoService->generateProductMeta($product);
        
        // AI-optimized title variations
        $titleVariations = [
            "{$product->name} Dubai UAE | High-Speed Camera | Motion Analysis | MaxMed",
            "{$product->name} UAE | Scientific Imaging | Motion Capture | MaxMed",
            "{$product->name} Dubai | Advanced Motion Analysis | MaxMed",
            "{$product->name} UAE | DIC System | Scientific Imaging | MaxMed"
        ];
        
        // AI-optimized descriptions
        $descriptionVariations = [
            "📹 Professional {$product->name} Dubai UAE! High-speed motion analysis ✅ Expert installation ⚡ Competitive pricing ☎️ +971 55 460 2500",
            "🏆 Leading {$product->name} UAE! Scientific imaging systems ✅ Manufacturer warranty ⚡ Technical support ☎️ MaxMed +971 55 460 2500",
            "⭐ Advanced {$product->name} Dubai! Motion capture equipment ✅ Professional service ⚡ Fast delivery ☎️ +971 55 460 2500"
        ];
        
        return [
            'title' => $titleVariations[array_rand($titleVariations)],
            'meta_description' => $descriptionVariations[array_rand($descriptionVariations)],
            'meta_keywords' => $baseMeta['meta_keywords'] . ', high-speed camera Dubai, motion analysis UAE, scientific imaging Dubai, DIC system UAE',
            'canonical_url' => $baseMeta['canonical_url']
        ];
    }

    private function generateAiProductDescription(Product $product, Category $category): string
    {
        $productName = $product->name;
        
        $description = "Professional {$productName} for Advanced Motion & Scientific Imaging Systems in Dubai UAE. ";
        
        // Add AI-optimized content based on product type
        if (str_contains(strtolower($productName), 'camera')) {
            $description .= "High-speed camera with frame rates up to 25,000 fps for precise motion analysis and scientific imaging applications. ";
        } elseif (str_contains(strtolower($productName), 'dic')) {
            $description .= "Digital Image Correlation (DIC) system for non-contact measurement of displacement, strain, and deformation. ";
        } elseif (str_contains(strtolower($productName), 'motion')) {
            $description .= "Advanced motion analysis equipment for research, industrial testing, and material analysis applications. ";
        } else {
            $description .= "Cutting-edge scientific imaging equipment for research and industrial applications. ";
        }
        
        $description .= "Trusted by universities, research institutions, and industrial laboratories across the UAE. Contact MaxMed at +971 55 460 2500 for expert consultation and competitive pricing.";
        
        return Str::limit($description, 300);
    }

    private function enhanceAiProductName(Product $product, Category $category): string
    {
        $name = $product->name;
        
        // Add AI-optimized location keywords
        if (!str_contains($name, 'Dubai') && !str_contains($name, 'UAE')) {
            $name .= " - Dubai UAE";
        }
        
        return Str::limit($name, 100);
    }

    private function createAdvancedMotionSitemapEntry(Category $category, bool $isDryRun): void
    {
        $sitemapPath = public_path('sitemap-category.xml');
        
        if (file_exists($sitemapPath)) {
            $content = file_get_contents($sitemapPath);
            
            // Check if entry already exists
            if (!str_contains($content, 'advanced-motion-scientific-imaging-systems')) {
                $newEntry = "    <url>\n        <loc>https://maxmedme.com/categories/advanced-motion-scientific-imaging-systems</loc>\n        <lastmod>" . now()->toISOString() . "</lastmod>\n        <changefreq>weekly</changefreq>\n        <priority>0.9</priority>\n    </url>\n";
                
                // Insert before closing urlset tag
                $content = str_replace('</urlset>', $newEntry . '</urlset>', $content);
                
                if (!$isDryRun) {
                    file_put_contents($sitemapPath, $content);
                }
                
                $this->line("   Added high-priority sitemap entry");
            } else {
                $this->line("   Sitemap entry already exists");
            }
        }
    }

    private function generateAdvancedMotionKnowledgeBase(Category $category, bool $isDryRun): void
    {
        $knowledgeBase = [
            'category_name' => 'Advanced Motion & Scientific Imaging Systems',
            'description' => 'Cutting-edge high-speed cameras, motion analysis systems, and digital image correlation equipment for research and industrial testing in Dubai UAE',
            'keywords' => [
                'advanced motion analysis Dubai',
                'scientific imaging systems UAE',
                'high-speed cameras Dubai',
                'motion capture systems UAE',
                'digital image correlation Dubai',
                'DIC measurement UAE',
                'ultra-high-speed cameras Dubai',
                'scientific cameras UAE'
            ],
            'applications' => [
                'Material testing and analysis',
                'Crash analysis and impact testing',
                'Fluid dynamics research',
                'Biomechanics studies',
                'Vibration analysis',
                'Quality control and inspection'
            ],
            'technical_specs' => [
                'Frame Rate: 1,000 - 25,000+ fps',
                'Resolution: Up to 4K',
                'Applications: Research, Industrial, Academic',
                'Warranty: Manufacturer + Support',
                'Service: Installation & Training'
            ],
            'contact_info' => [
                'phone' => '+971 55 460 2500',
                'email' => 'sales@maxmedme.com',
                'website' => 'https://maxmedme.com'
            ]
        ];
        
        $knowledgePath = storage_path('ai-knowledge-base/categories/advanced-motion-scientific-imaging-systems-ai.json');
        
        if (!$isDryRun) {
            file_put_contents($knowledgePath, json_encode($knowledgeBase, JSON_PRETTY_PRINT));
        }
        
        $this->line("   Generated AI knowledge base");
    }

    private function generateAdvancedMotionSocialMeta(Category $category, bool $isDryRun): void
    {
        $socialMeta = [
            'og_title' => 'Advanced Motion & Scientific Imaging Systems Dubai UAE | High-Speed Cameras & Motion Analysis',
            'og_description' => '📹 Cutting-edge advanced motion analysis and scientific imaging systems in Dubai UAE. Professional high-speed cameras, motion capture systems, digital image correlation (DIC), and scientific imaging equipment.',
            'og_image' => 'https://maxmedme.com/storage/categories/3BeskJbYKmqu7bk6atO0VxsakygVSMroiT8xcZoK.jpg',
            'og_url' => 'https://maxmedme.com/categories/advanced-motion-scientific-imaging-systems',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => 'Advanced Motion & Scientific Imaging Systems Dubai UAE',
            'twitter_description' => '📹 Professional high-speed cameras, motion analysis systems, and scientific imaging equipment in Dubai UAE. Contact MaxMed at +971 55 460 2500.',
            'twitter_image' => 'https://maxmedme.com/storage/categories/3BeskJbYKmqu7bk6atO0VxsakygVSMroiT8xcZoK.jpg'
        ];
        
        $socialPath = storage_path('seo/social-meta/advanced-motion-scientific-imaging-systems.json');
        
        if (!$isDryRun) {
            if (!is_dir(dirname($socialPath))) {
                mkdir(dirname($socialPath), 0755, true);
            }
            file_put_contents($socialPath, json_encode($socialMeta, JSON_PRETTY_PRINT));
        }
        
        $this->line("   Generated social media meta");
    }

    private function generateAdvancedMotionLocalSeo(Category $category, bool $isDryRun): void
    {
        $localSeo = [
            'business_name' => 'MaxMed UAE',
            'category' => 'Advanced Motion & Scientific Imaging Systems',
            'address' => [
                'country' => 'UAE',
                'region' => 'Dubai',
                'city' => 'Dubai'
            ],
            'contact' => [
                'phone' => '+971 55 460 2500',
                'email' => 'sales@maxmedme.com',
                'website' => 'https://maxmedme.com'
            ],
            'services' => [
                'High-Speed Cameras',
                'Motion Analysis Systems',
                'Digital Image Correlation (DIC)',
                'Scientific Imaging Equipment',
                'Installation & Training',
                'Technical Support'
            ],
            'keywords' => [
                'advanced motion analysis Dubai',
                'scientific imaging systems UAE',
                'high-speed cameras Dubai',
                'motion capture systems UAE',
                'digital image correlation Dubai',
                'DIC measurement UAE'
            ]
        ];
        
        $localPath = storage_path('seo/local/advanced-motion-scientific-imaging-systems.json');
        
        if (!$isDryRun) {
            if (!is_dir(dirname($localPath))) {
                mkdir(dirname($localPath), 0755, true);
            }
            file_put_contents($localPath, json_encode($localSeo, JSON_PRETTY_PRINT));
        }
        
        $this->line("   Generated local SEO data");
    }
} 