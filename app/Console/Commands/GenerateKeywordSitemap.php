<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateKeywordSitemap extends Command
{
    protected $signature = 'sitemap:generate-keywords {category?} {--all}';
    protected $description = 'Generate keyword sitemaps for SEO optimization';

    private $keywordData = [
        'advanced-motion-scientific-imaging-systems' => [
            'main_keywords' => [
                'high-speed-cameras-dubai' => 0.9,
                'advanced-motion-analysis-dubai' => 0.9,
                'digital-image-correlation-dubai' => 0.9,
                'scientific-imaging-systems-uae' => 0.9,
                'motion-analysis-equipment-dubai' => 0.8,
                'dic-systems-uae' => 0.8,
                'high-speed-cameras-uae' => 0.8,
                'motion-analysis-dubai' => 0.8,
            ],
            'secondary_keywords' => [
                'scientific-cameras-dubai' => 0.7,
                'imaging-systems-uae' => 0.7,
                'motion-tracking-dubai' => 0.7,
                'digital-image-correlation-uae' => 0.7,
                'high-speed-imaging-dubai' => 0.6,
                'scientific-motion-analysis-uae' => 0.6,
                'motion-analysis-systems-dubai' => 0.6,
                'advanced-imaging-dubai' => 0.6,
            ],
            'long_tail_keywords' => [
                'motion-capture-dubai' => 0.5,
                'scientific-equipment-dubai' => 0.5,
                'laboratory-cameras-uae' => 0.5,
                'research-imaging-dubai' => 0.5,
                'motion-analysis-software-dubai' => 0.4,
                'high-speed-video-dubai' => 0.4,
                'scientific-imaging-equipment-uae' => 0.4,
                'motion-analysis-tools-dubai' => 0.4,
            ],
            'niche_keywords' => [
                'digital-image-processing-dubai' => 0.3,
                'scientific-motion-tracking-uae' => 0.3,
                'high-speed-photography-dubai' => 0.3,
                'motion-analysis-laboratory-dubai' => 0.3,
                'scientific-video-analysis-uae' => 0.3,
                'advanced-motion-technology-dubai' => 0.2,
                'motion-analysis-research-dubai' => 0.2,
                'scientific-motion-systems-uae' => 0.2,
                'high-speed-motion-analysis-dubai' => 0.2,
                'digital-motion-tracking-uae' => 0.2,
            ]
        ]
    ];

    public function handle()
    {
        $this->info('🗺️ Starting keyword sitemap generation...');

        if ($this->option('all')) {
            $this->generateAllKeywordSitemaps();
        } else {
            $category = $this->argument('category') ?? 'advanced-motion-scientific-imaging-systems';
            $this->generateKeywordSitemap($category);
        }

        $this->info('✅ Keyword sitemap generation completed!');
    }

    private function generateAllKeywordSitemaps()
    {
        $this->info('📁 Generating keyword sitemaps for all categories...');
        
        foreach (array_keys($this->keywordData) as $category) {
            $this->generateKeywordSitemap($category);
        }
    }

    private function generateKeywordSitemap($category)
    {
        if (!isset($this->keywordData[$category])) {
            $this->error("❌ Category '$category' not found in keyword data.");
            return;
        }

        $this->info("📝 Generating keyword sitemap for: $category");

        $keywords = $this->keywordData[$category];
        $xml = $this->generateSitemapXML($category, $keywords);

        $filename = "keyword-sitemap-{$category}.xml";
        $path = public_path($filename);

        File::put($path, $xml);

        $this->info("✅ Generated: $filename");
        $this->info("📊 Total URLs: " . $this->countUrls($keywords));
        $this->info("🎯 Priority keywords: " . count($keywords['main_keywords']));
    }

    private function generateSitemapXML($category, $keywords)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
        $xml .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n\n";

        // Main category URL
        $xml .= $this->generateUrlEntry(
            "https://maxmed.ae/categories/{$category}",
            '2025-01-20',
            'weekly',
            1.0,
            ucwords(str_replace('-', ' ', $category)) . ' - Main Category'
        );

        // Main keywords
        foreach ($keywords['main_keywords'] as $keyword => $priority) {
            $xml .= $this->generateUrlEntry(
                "https://maxmed.ae/categories/{$category}?keyword={$keyword}",
                '2025-01-20',
                'weekly',
                $priority,
                ucwords(str_replace('-', ' ', $keyword))
            );
        }

        // Secondary keywords
        foreach ($keywords['secondary_keywords'] as $keyword => $priority) {
            $xml .= $this->generateUrlEntry(
                "https://maxmed.ae/categories/{$category}?keyword={$keyword}",
                '2025-01-20',
                'weekly',
                $priority,
                ucwords(str_replace('-', ' ', $keyword))
            );
        }

        // Long tail keywords
        foreach ($keywords['long_tail_keywords'] as $keyword => $priority) {
            $xml .= $this->generateUrlEntry(
                "https://maxmed.ae/categories/{$category}?keyword={$keyword}",
                '2025-01-20',
                'weekly',
                $priority,
                ucwords(str_replace('-', ' ', $keyword))
            );
        }

        // Niche keywords
        foreach ($keywords['niche_keywords'] as $keyword => $priority) {
            $xml .= $this->generateUrlEntry(
                "https://maxmed.ae/categories/{$category}?keyword={$keyword}",
                '2025-01-20',
                'weekly',
                $priority,
                ucwords(str_replace('-', ' ', $keyword))
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function generateUrlEntry($url, $lastmod, $changefreq, $priority, $comment = '')
    {
        $entry = '';
        if ($comment) {
            $entry .= "    <!-- {$comment} -->\n";
        }
        $entry .= "    <url>\n";
        $entry .= "        <loc>{$url}</loc>\n";
        $entry .= "        <lastmod>{$lastmod}</lastmod>\n";
        $entry .= "        <changefreq>{$changefreq}</changefreq>\n";
        $entry .= "        <priority>{$priority}</priority>\n";
        $entry .= "    </url>\n\n";
        return $entry;
    }

    private function countUrls($keywords)
    {
        $count = 1; // Main category URL
        foreach ($keywords as $keywordGroup) {
            $count += count($keywordGroup);
        }
        return $count;
    }
} 