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

    // High-value keywords from our previous research
    private $primaryKeywords = [
        'laboratory equipment Dubai', 'lab instruments UAE', 'medical equipment supplier',
        'diagnostic equipment', 'scientific equipment', 'PCR equipment', 'microscopes',
        'centrifuge machines', 'analytical instruments', 'rapid test kits'
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
            'title' => 'MaxMed UAE - Premium Laboratory & Medical Equipment Supplier in Dubai',
            'meta_description' => 'Leading supplier of laboratory equipment, medical instruments, and diagnostic tools in Dubai, UAE. Contact MaxMed at +971 55 460 2500 for PCR machines, microscopes, centrifuges, rapid test kits, and scientific equipment. Serving healthcare facilities across UAE.',
            'meta_keywords' => implode(', ', array_merge($this->primaryKeywords, [
                'MaxMed UAE', 'laboratory supplier Dubai', 'medical equipment UAE',
                'PCR machine Dubai', 'centrifuge supplier', 'microscope UAE',
                'diagnostic equipment Dubai', 'lab technology', '+971 55 460 2500'
            ])),
            'og_title' => 'MaxMed UAE - Premium Laboratory & Medical Equipment Supplier',
            'og_description' => 'Leading supplier of laboratory equipment and medical instruments in Dubai, UAE. Premium quality PCR machines, microscopes, centrifuges, and diagnostic tools.',
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
            'title' => 'Contact MaxMed UAE - Laboratory Equipment Supplier | +971 55 460 2500',
            'meta_description' => 'Contact MaxMed UAE for laboratory and medical equipment in Dubai. Call +971 55 460 2500 or email sales@maxmedme.com. Expert consultation, competitive pricing, and fast delivery across UAE.',
            'meta_keywords' => implode(', ', array_merge($this->coreKeywords, [
                'contact MaxMed', '+971 55 460 2500', 'sales@maxmedme.com',
                'laboratory equipment contact', 'Dubai medical equipment supplier',
                'UAE scientific equipment contact'
            ])),
            'canonical_url' => route('contact')
        ];
    }

    public function generateAboutMeta(): array
    {
        return [
            'title' => 'About MaxMed UAE - Leading Laboratory Equipment Supplier Since [Year]',
            'meta_description' => 'Learn about MaxMed UAE, your trusted partner for laboratory and medical equipment in Dubai. Expert team, quality products, and exceptional service across the UAE. Contact us at +971 55 460 2500.',
            'meta_keywords' => implode(', ', array_merge($this->coreKeywords, [
                'about MaxMed', 'company profile', 'laboratory equipment supplier UAE',
                'medical equipment company Dubai', 'scientific equipment provider'
            ])),
            'canonical_url' => route('about')
        ];
    }

    public function generateIndustryMeta(): array
    {
        return [
            'title' => 'Industries Served | Laboratory Equipment for Healthcare, Research & More | MaxMed UAE',
            'meta_description' => 'MaxMed UAE serves diverse industries with specialized laboratory and medical equipment. Healthcare facilities, research labs, educational institutions, and more across Dubai and UAE.',
            'meta_keywords' => implode(', ', array_merge($this->coreKeywords, [
                'healthcare equipment', 'research laboratory equipment', 'educational lab equipment',
                'hospital equipment Dubai', 'clinic equipment UAE', 'university lab supplies'
            ])),
            'canonical_url' => route('industry.index')
        ];
    }
} 