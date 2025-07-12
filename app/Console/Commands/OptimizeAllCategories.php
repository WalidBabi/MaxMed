<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;
use App\Services\SeoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OptimizeAllCategories extends Command
{
    protected $signature = 'seo:optimize-all-categories {--dry-run : Show what would be changed without making changes}';
    
    protected $description = 'Optimize SEO for all major categories to improve search rankings across the entire website';

    private $seoService;

    public function __construct(SeoService $seoService)
    {
        parent::__construct();
        $this->seoService = $seoService;
    }

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Starting comprehensive SEO optimization for all categories...');
        
        // Get all main categories (parent_id is null)
        $mainCategories = Category::whereNull('parent_id')->get();
        
        $this->info("📊 Found {$mainCategories->count()} main categories to optimize");
        
        $totalOptimizations = 0;
        $optimizedCategories = 0;
        
        foreach ($mainCategories as $category) {
            $this->line("\n📁 Processing: {$category->name}");
            
            $categoryOptimizations = $this->optimizeCategory($category, $isDryRun);
            $totalOptimizations += $categoryOptimizations;
            
            if ($categoryOptimizations > 0) {
                $optimizedCategories++;
            }
        }
        
        // Optimize subcategories
        $subcategories = Category::whereNotNull('parent_id')->get();
        $this->info("\n📊 Found {$subcategories->count()} subcategories to optimize");
        
        foreach ($subcategories as $subcategory) {
            $this->line("\n📁 Processing subcategory: {$subcategory->name}");
            
            $subcategoryOptimizations = $this->optimizeCategory($subcategory, $isDryRun);
            $totalOptimizations += $subcategoryOptimizations;
            
            if ($subcategoryOptimizations > 0) {
                $optimizedCategories++;
            }
        }
        
        // Generate enhanced sitemaps
        // $this->info("\n🗺️ Generating enhanced sitemaps...");
        // $this->call('seo:generate-comprehensive-sitemap');
        
        // Clear caches
        if (!$isDryRun) {
            $this->info("\n🧹 Clearing caches...");
            $this->call('view:clear');
            $this->call('config:clear');
            $this->call('cache:clear');
        }
        
        $this->info("\n✅ SEO optimization completed!");
        $this->info("📈 Total optimizations: {$totalOptimizations}");
        $this->info("📁 Categories optimized: {$optimizedCategories}");
        
        if (!$isDryRun) {
            $this->info("\n🎯 Next steps for production:");
            $this->info("1. Submit updated sitemap to Google Search Console");
            $this->info("2. Monitor search rankings for the next 2-4 weeks");
            $this->info("3. Check Google Analytics for improved organic traffic");
            $this->info("4. Review search console for new keyword opportunities");
        }
    }

    private function optimizeCategory(Category $category, bool $isDryRun): int
    {
        $optimizations = 0;
        
        // Generate optimized SEO metadata
        $seoData = $this->seoService->generateCategoryMeta($category);
        
        $this->line("   📝 Generated optimized title: " . Str::limit($seoData['title'], 80));
        $this->line("   📄 Generated optimized description: " . Str::limit($seoData['meta_description'], 100));
        
        // Check if category needs description update
        if (empty($category->description) || strlen($category->description) < 100) {
            $enhancedDescription = $this->generateCategoryDescription($category);
            
            if (!$isDryRun) {
                $category->update(['description' => $enhancedDescription]);
            }
            
            $this->line("   ✏️ Enhanced category description");
            $optimizations++;
        }
        
        // Optimize products in this category
        $products = $category->products()->limit(20)->get();
        
        foreach ($products as $product) {
            $productOptimizations = $this->optimizeProduct($product, $category, $isDryRun);
            $optimizations += $productOptimizations;
        }
        
        $this->line("   📦 Optimized {$products->count()} products in category");
        
        return $optimizations;
    }

    private function optimizeProduct(Product $product, Category $category, bool $isDryRun): int
    {
        $optimizations = 0;
        
        // Generate product-specific SEO metadata
        $seoData = $this->seoService->generateProductMeta($product);
        
        // Check if product description needs enhancement
        if (empty($product->description) || strlen($product->description) < 150) {
            $enhancedDescription = $this->generateProductDescription($product, $category);
            
            if (!$isDryRun) {
                $product->update(['description' => $enhancedDescription]);
            }
            
            $optimizations++;
        }
        
        // Add location keywords to product name if needed
        if (!str_contains($product->name, 'Dubai') && !str_contains($product->name, 'UAE')) {
            $enhancedName = $this->enhanceProductName($product, $category);
            
            if (!$isDryRun) {
                $product->update(['name' => $enhancedName]);
            }
            
            $optimizations++;
        }
        
        return $optimizations;
    }

    private function generateCategoryDescription(Category $category): string
    {
        $productCount = $category->products()->count();
        
        $descriptions = [
            'Lab Equipment' => "Professional laboratory equipment and scientific instruments in Dubai UAE. Complete solutions for analytical chemistry, materials testing, quality control, and research applications. Premium equipment from leading manufacturers for universities, research institutions, and industrial laboratories.",
            
            'Medical Consumables' => "High-quality medical consumables and healthcare supplies in Dubai UAE. Sterile products, diagnostic kits, medical devices, and clinical supplies for healthcare facilities, hospitals, and medical centers. Professional-grade medical equipment and consumables for patient care and clinical applications.",
            
            'Life Science & Research' => "Comprehensive life science and research equipment in Dubai UAE. Advanced instruments for biological and chemical analysis, cell culture, protein research, and molecular biology applications. Professional research equipment for universities, biotechnology companies, and research institutions.",
            
            'Technology & AI Solutions' => "Cutting-edge technology and AI solutions for laboratory automation in Dubai UAE. Smart laboratory equipment, automated systems, and artificial intelligence applications for research and diagnostic laboratories. Advanced technology solutions for modern laboratory operations.",
            
            'Lab Consumables' => "Laboratory consumables and supplies in Dubai UAE. Professional-grade lab glassware, chemicals, reagents, and essential supplies for research and analytical work. Complete range of laboratory consumables for accurate and reliable laboratory operations.",
            
            'Molecular & Clinical Diagnostics' => "Advanced molecular and clinical diagnostic equipment in Dubai UAE. Professional PCR machines, real-time PCR systems, thermal cyclers, and molecular analysis instruments for accurate medical testing and research applications. Trusted by hospitals, research laboratories, and diagnostic centers across the Middle East.",
            
            'Rapid Test Kits RDT' => "Rapid diagnostic test kits and point-of-care testing solutions in Dubai UAE. Professional rapid test kits for infectious diseases, cardiac markers, tumor markers, and various medical conditions. High-quality diagnostic tools for healthcare facilities and clinical laboratories.",
            
            'Analytical Instruments' => "Advanced analytical instruments and laboratory analysis equipment in Dubai UAE. Precision instruments for chemical analysis, spectroscopy, chromatography, and quality control applications. Professional analytical equipment for research laboratories and industrial testing.",
            
            'Thermal & Process Equipment' => "Thermal process equipment and laboratory heating solutions in Dubai UAE. Professional incubators, ovens, autoclaves, and thermal processing equipment for research and industrial applications. High-quality thermal equipment for precise temperature control and sterilization processes.",
            
            'Mixing & Shaking Equipment' => "Professional mixing and shaking equipment in Dubai UAE. Laboratory mixers, shakers, centrifuges, and agitation equipment for research and industrial applications. High-quality mixing equipment for sample preparation and processing.",
            
            'Veterinary' => "Veterinary equipment and animal diagnostic tools in Dubai UAE. Professional veterinary diagnostic equipment, animal testing kits, and veterinary supplies for veterinary clinics and animal research facilities. Complete veterinary solutions for animal healthcare and research.",
            
            'PPE & Safety Gear' => "Personal protective equipment and safety gear in Dubai UAE. Professional PPE including lab coats, gloves, masks, safety glasses, and protective clothing for laboratory and healthcare environments. High-quality safety equipment for workplace protection.",
            
            'Dental Consumables' => "Dental consumables and dental supplies in Dubai UAE. Professional dental materials, impression materials, dental burs, disposables, and dental equipment for dental clinics and laboratories. High-quality dental supplies for dental healthcare professionals.",
            
            'Chemical & Reagents' => "Chemical reagents and laboratory chemicals in Dubai UAE. Professional-grade chemicals, reagents, standards, and analytical chemicals for research and industrial applications. High-quality chemical supplies for accurate laboratory analysis.",
            
            'Centrifuges' => "Laboratory centrifuges and centrifugation equipment in Dubai UAE. Professional benchtop centrifuges, floor-standing centrifuges, and refrigerated centrifuges for sample processing and separation. High-quality centrifugation equipment for laboratory applications.",
            
            'UV-Vis Spectrophotometers' => "UV-Vis spectrophotometers and analytical instruments in Dubai UAE. Professional spectrophotometers for chemical analysis, quality control, and research applications. High-precision analytical instruments for accurate measurements.",
            
            'Incubators & Ovens' => "Laboratory incubators and ovens in Dubai UAE. Professional CO2 incubators, drying ovens, and temperature-controlled equipment for research and industrial applications. High-quality heating equipment for precise temperature control.",
            
            'Microbiology Equipment' => "Microbiology equipment and microbial analysis tools in Dubai UAE. Professional equipment for bacterial culture, microbial testing, and microbiological research applications. Advanced microbiology tools for research and diagnostic laboratories.",
            
            'Advanced Motion & Scientific Imaging Systems' => "Cutting-edge advanced motion analysis and scientific imaging systems in Dubai UAE. Professional high-speed cameras, motion capture systems, digital image correlation (DIC), and scientific imaging equipment for research, industrial testing, and material analysis. Advanced motion analysis technology for universities, research institutions, and industrial laboratories."
        ];
        
        // Check for exact match
        if (isset($descriptions[$category->name])) {
            $description = $descriptions[$category->name];
        } else {
            // Check for partial matches
            $description = null;
            foreach ($descriptions as $key => $desc) {
                if (str_contains($category->name, $key) || str_contains($key, $category->name)) {
                    $description = $desc;
                    break;
                }
            }
            
            if (!$description) {
                $description = "Professional {$category->name} equipment and supplies in Dubai UAE. High-quality products for research, analysis, and industrial applications. Trusted by laboratories, research institutions, and healthcare facilities across the Middle East.";
            }
        }
        
        if ($productCount > 0) {
            $description .= " {$productCount}+ products available. Contact MaxMed at +971 55 460 2500 for expert consultation and competitive pricing.";
        } else {
            $description .= " Contact MaxMed at +971 55 460 2500 for expert consultation and competitive pricing.";
        }
        
        return Str::limit($description, 500);
    }

    private function generateProductDescription(Product $product, Category $category): string
    {
        $categoryName = $category->name;
        $productName = $product->name;
        
        $description = "Professional {$productName} for {$categoryName} applications in Dubai UAE. ";
        
        // Add category-specific details
        if (str_contains($categoryName, 'Lab Equipment')) {
            $description .= "High-quality laboratory equipment for research and analysis applications. ";
        } elseif (str_contains($categoryName, 'Medical')) {
            $description .= "Medical-grade equipment for healthcare and clinical applications. ";
        } elseif (str_contains($categoryName, 'Life Science')) {
            $description .= "Advanced life science equipment for biological research and analysis. ";
        } elseif (str_contains($categoryName, 'Analytical')) {
            $description .= "Precision analytical instruments for accurate measurements and analysis. ";
        } elseif (str_contains($categoryName, 'Consumables')) {
            $description .= "Professional consumables and supplies for laboratory operations. ";
        }
        
        $description .= "Trusted by research institutions, hospitals, and laboratories across the UAE. Contact MaxMed at +971 55 460 2500 for expert consultation and competitive pricing.";
        
        return Str::limit($description, 300);
    }

    private function enhanceProductName(Product $product, Category $category): string
    {
        $name = $product->name;
        
        // Add location keywords if not present
        if (!str_contains($name, 'Dubai') && !str_contains($name, 'UAE')) {
            $name .= " - Dubai UAE";
        }
        
        return Str::limit($name, 100);
    }
} 