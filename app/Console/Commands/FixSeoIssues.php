<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixSeoIssues extends Command
{
    protected $signature = 'seo:fix-issues 
                            {--dry-run : Show what would be fixed without making changes}
                            {--type=all : Type of issues to fix (titles, descriptions, duplicates, all)}';
    protected $description = 'Automatically fix identified SEO issues';

    public function handle()
    {
        $this->info('🔧 Starting SEO Issue Fixes for MaxMed UAE...');
        
        $isDryRun = $this->option('dry-run');
        $type = $this->option('type');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $fixes = 0;

        if ($type === 'all' || $type === 'titles') {
            $fixes += $this->fixLongTitles($isDryRun);
        }

        if ($type === 'all' || $type === 'descriptions') {
            $fixes += $this->fixLongDescriptions($isDryRun);
        }

        if ($type === 'all' || $type === 'duplicates') {
            $fixes += $this->fixDuplicateProducts($isDryRun);
        }

        if ($type === 'all' || $type === 'content') {
            $fixes += $this->createSampleNews($isDryRun);
        }

        $this->info("✅ SEO fixes completed! {$fixes} issues " . ($isDryRun ? 'would be' : 'were') . " fixed.");
        
        if (!$isDryRun && $fixes > 0) {
            $this->info('🚀 Run sitemap generation to update search engines:');
            $this->line('   php artisan sitemap:ultimate --validate');
        }

        return 0;
    }

    private function fixLongTitles($isDryRun)
    {
        $this->info('📝 Fixing long product titles...');
        
        $products = Product::whereRaw('CHAR_LENGTH(name) > 60')->get();
        $fixed = 0;

        foreach ($products as $product) {
            $originalTitle = $product->name;
            $optimizedTitle = $this->optimizeTitle($originalTitle);
            
            if ($optimizedTitle !== $originalTitle) {
                $this->line("   📦 {$originalTitle}");
                $this->line("   ➡️  {$optimizedTitle}");
                
                if (!$isDryRun) {
                    $product->update(['name' => $optimizedTitle]);
                }
                $fixed++;
            }
        }

        $this->line("   Fixed {$fixed} long titles");
        return $fixed;
    }

    private function fixLongDescriptions($isDryRun)
    {
        $this->info('📄 Optimizing meta descriptions...');
        
        $products = Product::whereRaw('CHAR_LENGTH(description) > 160 OR CHAR_LENGTH(description) < 120')
                          ->whereNotNull('description')
                          ->get();
        $fixed = 0;

        foreach ($products as $product) {
            $originalDesc = $product->description;
            $optimizedDesc = $this->optimizeDescription($originalDesc, $product->name);
            
            if ($optimizedDesc !== $originalDesc) {
                $this->line("   📦 {$product->name}");
                $this->line("   📄 Description optimized (" . strlen($optimizedDesc) . " chars)");
                
                if (!$isDryRun) {
                    $product->update(['description' => $optimizedDesc]);
                }
                $fixed++;
            }
        }

        $this->line("   Optimized {$fixed} descriptions");
        return $fixed;
    }

    private function fixDuplicateProducts($isDryRun)
    {
        $this->info('🔍 Finding and fixing duplicate products...');
        
        $duplicates = Product::select('name', DB::raw('COUNT(*) as count'))
                            ->groupBy('name')
                            ->having('count', '>', 1)
                            ->get();

        $fixed = 0;

        foreach ($duplicates as $duplicate) {
            $products = Product::where('name', $duplicate->name)->get();
            
            $this->line("   🔄 Found {$duplicate->count} products named: {$duplicate->name}");
            
            foreach ($products as $index => $product) {
                if ($index > 0) { // Keep first one, modify others
                    $newName = $product->name . ' - Model ' . $product->id;
                    $this->line("   ➡️  Renaming to: {$newName}");
                    
                    if (!$isDryRun) {
                        $product->update(['name' => $newName]);
                    }
                    $fixed++;
                }
            }
        }

        $this->line("   Fixed {$fixed} duplicate products");
        return $fixed;
    }

    private function createSampleNews($isDryRun)
    {
        $this->info('📰 Creating sample news content...');
        
        $newsCount = News::count();
        if ($newsCount >= 3) {
            $this->line("   ✅ Already have {$newsCount} news articles");
            return 0;
        }

        $sampleNews = [
            [
                'title' => 'MaxMed UAE Expands Laboratory Equipment Portfolio for 2025',
                'content' => 'MaxMed UAE continues to strengthen its position as the leading laboratory equipment supplier in the Middle East with the addition of cutting-edge analytical instruments and advanced research equipment. Our expanded portfolio now includes state-of-the-art spectrophotometers, high-precision balances, and automated laboratory systems designed to meet the evolving needs of research institutions, hospitals, and quality control laboratories across the GCC region.',
                'published' => true
            ],
            [
                'title' => 'New Partnership Brings Advanced Biotechnology Solutions to UAE Labs',
                'content' => 'We are excited to announce our strategic partnership with leading international biotechnology manufacturers, bringing the latest innovations in molecular biology, genomics, and proteomics equipment to UAE laboratories. This collaboration enables us to offer comprehensive solutions for life sciences research, clinical diagnostics, and pharmaceutical development, supporting the UAE\'s vision for becoming a global hub for scientific innovation.',
                'published' => true
            ],
            [
                'title' => 'MaxMed UAE Supports UAE National Innovation Strategy with Advanced Equipment',
                'content' => 'As part of our commitment to supporting the UAE\'s National Innovation Strategy 2071, MaxMed UAE has been actively supplying cutting-edge laboratory equipment to research institutions and universities. Our comprehensive range of analytical chemistry, materials testing, and environmental monitoring equipment helps advance scientific research and development initiatives across the country, contributing to the UAE\'s goal of becoming one of the most innovative nations in the world.',
                'published' => true
            ]
        ];

        $created = 0;
        foreach ($sampleNews as $newsItem) {
            $this->line("   📄 Creating: {$newsItem['title']}");
            
            if (!$isDryRun) {
                News::create($newsItem);
            }
            $created++;
        }

        $this->line("   Created {$created} news articles");
        return $created;
    }

    private function optimizeTitle($title)
    {
        // If title is too long, optimize it
        if (strlen($title) <= 60) {
            return $title;
        }

        // Remove redundant words and optimize for SEO
        $optimized = $title;
        
        // Remove common redundant phrases
        $redundantPhrases = [
            'Multifunctional Orbital Decolorizing',
            'Intelligent Decolorizing',
            'Multifunctional',
            'Professional',
            'High Quality',
            'Advanced'
        ];

        foreach ($redundantPhrases as $phrase) {
            $optimized = str_replace($phrase, '', $optimized);
        }

        // Clean up extra spaces
        $optimized = preg_replace('/\s+/', ' ', trim($optimized));

        // If still too long, truncate intelligently
        if (strlen($optimized) > 60) {
            $optimized = Str::limit($optimized, 57, '...');
        }

        return $optimized;
    }

    private function optimizeDescription($description, $productName)
    {
        // If description is null or empty, create one
        if (empty($description)) {
            return "High-quality {$productName} from MaxMed UAE. Professional laboratory equipment with reliable performance for research and analysis applications.";
        }

        // Strip HTML tags
        $clean = strip_tags($description);
        
        // If too long, truncate intelligently
        if (strlen($clean) > 160) {
            // Find last complete sentence within 160 chars
            $truncated = Str::limit($clean, 157, '...');
            
            // Try to end at a sentence
            $lastPeriod = strrpos(substr($truncated, 0, 157), '.');
            if ($lastPeriod && $lastPeriod > 100) {
                $truncated = substr($truncated, 0, $lastPeriod + 1);
            }
            
            return $truncated;
        }

        // If too short, enhance it
        if (strlen($clean) < 120) {
            $enhanced = $clean;
            if (!str_contains($enhanced, 'MaxMed')) {
                $enhanced .= ' Available from MaxMed UAE.';
            }
            if (!str_contains($enhanced, 'laboratory') && !str_contains($enhanced, 'research')) {
                $enhanced .= ' Professional laboratory equipment.';
            }
            return Str::limit($enhanced, 160);
        }

        return $clean;
    }
} 