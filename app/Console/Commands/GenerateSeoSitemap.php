<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GenerateSeoSitemap extends Command
{
    protected $signature = 'sitemap:generate 
                            {--include-images=true : Include image sitemap}
                            {--include-news=true : Include news sitemap}
                            {--include-videos=false : Include video sitemap}
                            {--output=public : Output directory}';

    protected $description = 'Generate comprehensive SEO sitemaps with images and news';

    private $baseUrl;
    private $outputDir;

    public function handle()
    {
        $this->info('🗺️ Generating SEO-optimized sitemaps for MaxMed...');
        
        $this->baseUrl = rtrim(config('app.url'), '/');
        $this->outputDir = $this->option('output');
        
        $includeImages = $this->option('include-images') === 'true';
        $includeNews = $this->option('include-news') === 'true';
        $includeVideos = $this->option('include-videos') === 'true';

        // Generate individual sitemaps
        $sitemaps = [];
        
        $sitemaps['main'] = $this->generateMainSitemap();
        $sitemaps['products'] = $this->generateProductSitemap($includeImages);
        $sitemaps['categories'] = $this->generateCategorySitemap($includeImages);
        
        if ($includeNews) {
            $sitemaps['news'] = $this->generateNewsSitemap($includeImages);
        }
        
        if ($includeImages) {
            $sitemaps['images'] = $this->generateImageSitemap();
        }
        
        if ($includeVideos) {
            $sitemaps['videos'] = $this->generateVideoSitemap();
        }

        // Generate sitemap index
        $this->generateSitemapIndex($sitemaps);
        
        $this->info('✅ SEO sitemaps generated successfully!');
        $this->displaySummary($sitemaps);
        
        return 0;
    }

    private function generateMainSitemap()
    {
        $this->info('📄 Generating main sitemap...');
        
        $urls = [
            ['loc' => $this->baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $this->baseUrl . '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $this->baseUrl . '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $this->baseUrl . '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $this->baseUrl . '/categories', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => $this->baseUrl . '/industries', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => $this->baseUrl . '/partners', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $this->baseUrl . '/news', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => $this->baseUrl . '/quotation/form', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        $xml = $this->createSitemapXML($urls);
        $filename = 'sitemap-main.xml';
        File::put($this->outputDir . '/' . $filename, $xml);
        
        return ['filename' => $filename, 'count' => count($urls)];
    }

    private function generateProductSitemap($includeImages = true)
    {
        $this->info('🔬 Generating product sitemap...');
        
        $products = Product::with(['category', 'images', 'brand'])
                          ->where('status', 'active')
                          ->orderBy('updated_at', 'desc')
                          ->get();

        $urls = [];
        foreach ($products as $product) {
            $url = [
                'loc' => $this->baseUrl . '/product/' . $product->id,
                'lastmod' => $product->updated_at->format('c'),
                'priority' => '0.8',
                'changefreq' => 'weekly'
            ];

            // Add image information
            if ($includeImages) {
                $images = [];
                
                // Main product image
                if ($product->image_url) {
                    $images[] = [
                        'loc' => $product->image_url,
                        'caption' => $product->name . ' - ' . ($product->category ? $product->category->name : 'Laboratory Equipment'),
                        'title' => $product->name
                    ];
                }
                
                // Additional product images
                if ($product->images) {
                    foreach ($product->images as $image) {
                        $images[] = [
                            'loc' => $image->image_url,
                            'caption' => $product->name . ' - Additional Image',
                            'title' => $product->name
                        ];
                    }
                }
                
                if (!empty($images)) {
                    $url['images'] = $images;
                }
            }

            $urls[] = $url;
        }

        $xml = $this->createSitemapXML($urls, true);
        $filename = 'sitemap-products.xml';
        File::put($this->outputDir . '/' . $filename, $xml);
        
        return ['filename' => $filename, 'count' => count($urls)];
    }

    private function generateCategorySitemap($includeImages = true)
    {
        $this->info('📂 Generating category sitemap...');
        
        $categories = Category::with(['subcategories', 'products'])
                             ->orderBy('updated_at', 'desc')
                             ->get();

        $urls = [];
        foreach ($categories as $category) {
            $url = [
                'loc' => $this->baseUrl . '/categories/' . $category->id,
                'lastmod' => $category->updated_at->format('c'),
                'priority' => '0.7',
                'changefreq' => 'weekly'
            ];

            // Add category image
            if ($includeImages && $category->image_url) {
                $url['images'] = [[
                    'loc' => $category->image_url,
                    'caption' => $category->name . ' - Laboratory Equipment Category',
                    'title' => $category->name
                ]];
            }

            $urls[] = $url;

            // Add subcategories
            if ($category->subcategories) {
                foreach ($category->subcategories as $subcategory) {
                    $subUrl = [
                        'loc' => $this->baseUrl . '/categories/' . $category->id . '/' . $subcategory->id,
                        'lastmod' => $subcategory->updated_at->format('c'),
                        'priority' => '0.6',
                        'changefreq' => 'weekly'
                    ];

                    if ($includeImages && $subcategory->image_url) {
                        $subUrl['images'] = [[
                            'loc' => $subcategory->image_url,
                            'caption' => $subcategory->name . ' - Laboratory Equipment Subcategory',
                            'title' => $subcategory->name
                        ]];
                    }

                    $urls[] = $subUrl;
                }
            }
        }

        $xml = $this->createSitemapXML($urls, true);
        $filename = 'sitemap-categories.xml';
        File::put($this->outputDir . '/' . $filename, $xml);
        
        return ['filename' => $filename, 'count' => count($urls)];
    }

    private function generateNewsSitemap($includeImages = true)
    {
        $this->info('📰 Generating news sitemap...');
        
        $news = News::where('status', 'published')
                   ->where('created_at', '>=', now()->subDays(730)) // Last 2 years for news
                   ->orderBy('created_at', 'desc')
                   ->get();

        $urls = [];
        foreach ($news as $article) {
            $url = [
                'loc' => $this->baseUrl . '/news/' . $article->slug,
                'lastmod' => $article->updated_at->format('c'),
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'news' => [
                    'publication_date' => $article->created_at->format('c'),
                    'title' => $article->title,
                    'keywords' => $this->extractKeywords($article->content ?? ''),
                    'language' => 'en'
                ]
            ];

            // Add news image
            if ($includeImages && $article->image_url) {
                $url['images'] = [[
                    'loc' => $article->image_url,
                    'caption' => $article->title,
                    'title' => $article->title
                ]];
            }

            $urls[] = $url;
        }

        $xml = $this->createNewsSitemapXML($urls);
        $filename = 'sitemap-news.xml';
        File::put($this->outputDir . '/' . $filename, $xml);
        
        return ['filename' => $filename, 'count' => count($urls)];
    }

    private function generateImageSitemap()
    {
        $this->info('🖼️ Generating image sitemap...');
        
        $images = [];
        
        // Collect images from products
        $products = Product::with('images')->get();
        foreach ($products as $product) {
            if ($product->image_url) {
                $images[] = [
                    'loc' => $product->image_url,
                    'caption' => $product->name . ' - Laboratory Equipment by MaxMed UAE',
                    'title' => $product->name,
                    'license' => $this->baseUrl . '/terms-of-use'
                ];
            }
            
            if ($product->images) {
                foreach ($product->images as $image) {
                    $images[] = [
                        'loc' => $image->image_url,
                        'caption' => $product->name . ' - Additional View',
                        'title' => $product->name,
                        'license' => $this->baseUrl . '/terms-of-use'
                    ];
                }
            }
        }

        // Collect images from categories
        $categories = Category::whereNotNull('image_url')->get();
        foreach ($categories as $category) {
            $images[] = [
                'loc' => $category->image_url,
                'caption' => $category->name . ' Category - Laboratory Equipment',
                'title' => $category->name,
                'license' => $this->baseUrl . '/terms-of-use'
            ];
        }

        $xml = $this->createImageSitemapXML($images);
        $filename = 'sitemap-images.xml';
        File::put($this->outputDir . '/' . $filename, $xml);
        
        return ['filename' => $filename, 'count' => count($images)];
    }

    private function generateVideoSitemap()
    {
        $this->info('🎥 Generating video sitemap...');
        
        // This is a placeholder - implement based on your video content
        $videos = [];
        
        $xml = $this->createVideoSitemapXML($videos);
        $filename = 'sitemap-videos.xml';
        File::put($this->outputDir . '/' . $filename, $xml);
        
        return ['filename' => $filename, 'count' => count($videos)];
    }

    private function generateSitemapIndex($sitemaps)
    {
        $this->info('📋 Generating sitemap index...');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($sitemaps as $sitemap) {
            $xml .= '    <sitemap>' . "\n";
            $xml .= '        <loc>' . $this->baseUrl . '/' . $sitemap['filename'] . '</loc>' . "\n";
            $xml .= '        <lastmod>' . now()->format('c') . '</lastmod>' . "\n";
            $xml .= '    </sitemap>' . "\n";
        }
        
        $xml .= '</sitemapindex>' . "\n";
        
        File::put($this->outputDir . '/sitemap.xml', $xml);
    }

    private function createSitemapXML($urls, $includeImages = false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        
        if ($includeImages) {
            $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        }
        
        $xml .= '>' . "\n";
        
        foreach ($urls as $url) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            
            if (isset($url['lastmod'])) {
                $xml .= '        <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            }
            
            if (isset($url['changefreq'])) {
                $xml .= '        <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            }
            
            if (isset($url['priority'])) {
                $xml .= '        <priority>' . $url['priority'] . '</priority>' . "\n";
            }
            
            // Add images
            if (isset($url['images']) && $includeImages) {
                foreach ($url['images'] as $image) {
                    $xml .= '        <image:image>' . "\n";
                    $xml .= '            <image:loc>' . htmlspecialchars($image['loc']) . '</image:loc>' . "\n";
                    if (isset($image['caption'])) {
                        $xml .= '            <image:caption>' . htmlspecialchars($image['caption']) . '</image:caption>' . "\n";
                    }
                    if (isset($image['title'])) {
                        $xml .= '            <image:title>' . htmlspecialchars($image['title']) . '</image:title>' . "\n";
                    }
                    $xml .= '        </image:image>' . "\n";
                }
            }
            
            $xml .= '    </url>' . "\n";
        }
        
        $xml .= '</urlset>' . "\n";
        
        return $xml;
    }

    private function createNewsSitemapXML($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            
            if (isset($url['news'])) {
                $news = $url['news'];
                $xml .= '        <news:news>' . "\n";
                $xml .= '            <news:publication>' . "\n";
                $xml .= '                <news:name>MaxMed UAE</news:name>' . "\n";
                $xml .= '                <news:language>' . $news['language'] . '</news:language>' . "\n";
                $xml .= '            </news:publication>' . "\n";
                $xml .= '            <news:publication_date>' . $news['publication_date'] . '</news:publication_date>' . "\n";
                $xml .= '            <news:title>' . htmlspecialchars($news['title']) . '</news:title>' . "\n";
                if (isset($news['keywords'])) {
                    $xml .= '            <news:keywords>' . htmlspecialchars($news['keywords']) . '</news:keywords>' . "\n";
                }
                $xml .= '        </news:news>' . "\n";
            }
            
            // Add images
            if (isset($url['images'])) {
                foreach ($url['images'] as $image) {
                    $xml .= '        <image:image>' . "\n";
                    $xml .= '            <image:loc>' . htmlspecialchars($image['loc']) . '</image:loc>' . "\n";
                    $xml .= '            <image:caption>' . htmlspecialchars($image['caption']) . '</image:caption>' . "\n";
                    $xml .= '        </image:image>' . "\n";
                }
            }
            
            $xml .= '    </url>' . "\n";
        }
        
        $xml .= '</urlset>' . "\n";
        
        return $xml;
    }

    private function createImageSitemapXML($images)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        // Group images by page (if they have a source page)
        $groupedImages = collect($images)->groupBy(function ($image) {
            return $this->baseUrl; // For now, associate all with homepage
        });
        
        foreach ($groupedImages as $pageUrl => $pageImages) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . $pageUrl . '</loc>' . "\n";
            
            foreach ($pageImages as $image) {
                $xml .= '        <image:image>' . "\n";
                $xml .= '            <image:loc>' . htmlspecialchars($image['loc']) . '</image:loc>' . "\n";
                $xml .= '            <image:caption>' . htmlspecialchars($image['caption']) . '</image:caption>' . "\n";
                $xml .= '            <image:title>' . htmlspecialchars($image['title']) . '</image:title>' . "\n";
                if (isset($image['license'])) {
                    $xml .= '            <image:license>' . htmlspecialchars($image['license']) . '</image:license>' . "\n";
                }
                $xml .= '        </image:image>' . "\n";
            }
            
            $xml .= '    </url>' . "\n";
        }
        
        $xml .= '</urlset>' . "\n";
        
        return $xml;
    }

    private function createVideoSitemapXML($videos)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";
        
        foreach ($videos as $video) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . htmlspecialchars($video['page_url']) . '</loc>' . "\n";
            $xml .= '        <video:video>' . "\n";
            $xml .= '            <video:thumbnail_loc>' . htmlspecialchars($video['thumbnail']) . '</video:thumbnail_loc>' . "\n";
            $xml .= '            <video:title>' . htmlspecialchars($video['title']) . '</video:title>' . "\n";
            $xml .= '            <video:description>' . htmlspecialchars($video['description']) . '</video:description>' . "\n";
            $xml .= '            <video:content_loc>' . htmlspecialchars($video['content_loc']) . '</video:content_loc>' . "\n";
            $xml .= '            <video:duration>' . $video['duration'] . '</video:duration>' . "\n";
            $xml .= '        </video:video>' . "\n";
            $xml .= '    </url>' . "\n";
        }
        
        $xml .= '</urlset>' . "\n";
        
        return $xml;
    }

    private function extractKeywords($content)
    {
        $content = strip_tags($content);
        $words = str_word_count(strtolower($content), 1);
        $keywords = array_count_values($words);
        arsort($keywords);
        
        $topKeywords = array_slice(array_keys($keywords), 0, 10);
        return implode(', ', $topKeywords);
    }

    private function displaySummary($sitemaps)
    {
        $this->info("\n" . str_repeat('=', 50));
        $this->info('📊 SITEMAP GENERATION SUMMARY');
        $this->info(str_repeat('=', 50));
        
        $totalUrls = 0;
        foreach ($sitemaps as $name => $data) {
            $this->line("📄 {$data['filename']}: {$data['count']} URLs");
            $totalUrls += $data['count'];
        }
        
        $this->info("\n✅ Total URLs: {$totalUrls}");
        $this->info("📍 Sitemap Index: {$this->baseUrl}/sitemap.xml");
        
        $this->info("\n🚀 SEO Benefits:");
        $this->line("  • Better search engine indexing");
        $this->line("  • Faster discovery of new content");
        $this->line("  • Image and news content optimization");
        $this->line("  • Improved crawl efficiency");
        
        $this->info("\n📋 Next Steps:");
        $this->line("  1. Submit sitemap to Google Search Console");
        $this->line("  2. Add sitemap URL to robots.txt");
        $this->line("  3. Monitor sitemap processing in GSC");
        $this->line("  4. Set up automated sitemap regeneration");
    }
} 