<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateKeywordSitemaps extends Command
{
    protected $signature = 'sitemap:keywords {--generate-routes : Generate route definitions for keywords}';
    protected $description = 'Generate keyword-based sitemaps for maximum SEO coverage';

    private $baseUrl = 'https://maxmedme.com';
    private $keywords = [];

    public function handle()
    {
        $this->info('🔍 Generating keyword-based sitemaps for first page rankings...');
        
        // Extract keywords from products and categories
        $this->extractKeywords();
        
        // Generate keyword-based sitemaps
        $this->generateKeywordSitemaps();
        
        if ($this->option('generate-routes')) {
            $this->generateRouteDefinitions();
        }
        
        $this->displayKeywordStats();
        
        return 0;
    }

    private function extractKeywords()
    {
        $this->info('📝 Extracting keywords from products and categories...');
        
        // Medical and healthcare keywords
        $medicalKeywords = [
            // Equipment types
            'medical-equipment', 'laboratory-equipment', 'diagnostic-equipment', 
            'surgical-instruments', 'hospital-supplies', 'clinical-supplies',
            'laboratory-supplies', 'research-equipment', 'analytical-instruments',
            
            // Specialty areas
            'cardiology-equipment', 'orthopedic-supplies', 'dental-equipment',
            'ophthalmology-equipment', 'dermatology-supplies', 'neurology-equipment',
            'radiology-equipment', 'anesthesia-equipment', 'emergency-supplies',
            
            // Laboratory specialties
            'chemistry-equipment', 'biology-supplies', 'microbiology-equipment',
            'pathology-supplies', 'hematology-equipment', 'immunology-supplies',
            'molecular-biology-equipment', 'cell-culture-supplies', 'pcr-equipment',
            
            // Testing and analysis
            'diagnostic-tests', 'rapid-tests', 'blood-tests', 'urine-tests',
            'covid-tests', 'pregnancy-tests', 'drug-tests', 'genetic-tests',
            
            // Consumables
            'disposable-supplies', 'sterile-supplies', 'medical-consumables',
            'laboratory-consumables', 'surgical-consumables', 'dental-consumables',
            
            // Brands and manufacturers
            'abbott-products', 'roche-products', 'siemens-products', 'ge-healthcare',
            'philips-healthcare', 'medtronic-products', 'johnson-products',
            
            // Regional terms
            'medical-supplies-uae', 'laboratory-equipment-dubai', 'hospital-supplies-dubai',
            'medical-equipment-abu-dhabi', 'healthcare-products-sharjah',
            'medical-supplies-middle-east', 'laboratory-supplies-gcc',
        ];
        
        // Add product-based keywords
        $products = Product::select('name', 'description', 'slug')->get();
        foreach ($products as $product) {
            $keywords = $this->extractKeywordsFromText($product->name . ' ' . $product->description);
            foreach ($keywords as $keyword) {
                if (strlen($keyword) >= 3) {
                    $this->keywords[Str::slug($keyword)] = [
                        'keyword' => $keyword,
                        'priority' => '0.7',
                        'changefreq' => 'monthly',
                        'products' => [$product->slug]
                    ];
                }
            }
        }
        
        // Add category-based keywords
        $categories = Category::select('name', 'slug', 'description')->get();
        foreach ($categories as $category) {
            $keywords = $this->extractKeywordsFromText($category->name . ' ' . $category->description);
            foreach ($keywords as $keyword) {
                if (strlen($keyword) >= 3) {
                    $slug = Str::slug($keyword);
                    if (isset($this->keywords[$slug])) {
                        $this->keywords[$slug]['categories'][] = $category->slug;
                    } else {
                        $this->keywords[$slug] = [
                            'keyword' => $keyword,
                            'priority' => '0.6',
                            'changefreq' => 'monthly',
                            'categories' => [$category->slug]
                        ];
                    }
                }
            }
        }
        
        // Add predefined medical keywords
        foreach ($medicalKeywords as $keyword) {
            $this->keywords[Str::slug($keyword)] = [
                'keyword' => $keyword,
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'type' => 'medical'
            ];
        }
        
        $this->line('   ✓ Extracted ' . count($this->keywords) . ' keywords');
    }

    private function extractKeywordsFromText($text)
    {
        // Remove HTML tags and clean text
        $text = strip_tags($text);
        $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Split into words
        $words = explode(' ', strtolower($text));
        
        // Filter meaningful keywords
        $keywords = [];
        $commonWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'a', 'an', 'is', 'are', 'was', 'were', 'been', 'be', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'this', 'that', 'these', 'those', 'here', 'there', 'where', 'when', 'why', 'how', 'what', 'which', 'who', 'whom', 'whose'];
        
        foreach ($words as $word) {
            if (strlen($word) >= 3 && !in_array($word, $commonWords)) {
                $keywords[] = $word;
            }
        }
        
        // Also create compound keywords
        for ($i = 0; $i < count($words) - 1; $i++) {
            $compound = $words[$i] . ' ' . $words[$i + 1];
            if (strlen($compound) >= 6 && !in_array($words[$i], $commonWords) && !in_array($words[$i + 1], $commonWords)) {
                $keywords[] = $compound;
            }
        }
        
        return array_unique($keywords);
    }

    private function generateKeywordSitemaps()
    {
        $this->info('🗺️ Generating keyword-based sitemaps...');
        
        // Split keywords into chunks for separate sitemaps
        $chunks = array_chunk($this->keywords, 100, true);
        
        foreach ($chunks as $index => $chunk) {
            $this->generateKeywordSitemap($chunk, $index);
        }
        
        // Generate keyword index
        $this->generateKeywordIndex(count($chunks));
    }

    private function generateKeywordSitemap($keywords, $index)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($keywords as $slug => $data) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$this->baseUrl}/keywords/{$slug}</loc>\n";
            $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "        <changefreq>{$data['changefreq']}</changefreq>\n";
            $xml .= "        <priority>{$data['priority']}</priority>\n";
            $xml .= "    </url>\n";
        }
        
        $xml .= "</urlset>\n";
        
        $filename = "sitemap-keywords-{$index}.xml";
        File::put(public_path($filename), $xml);
        $this->line("   ✓ Generated {$filename} with " . count($keywords) . " keywords");
    }

    private function generateKeywordIndex($chunkCount)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        for ($i = 0; $i < $chunkCount; $i++) {
            $xml .= "    <sitemap>\n";
            $xml .= "        <loc>{$this->baseUrl}/sitemap-keywords-{$i}.xml</loc>\n";
            $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "    </sitemap>\n";
        }
        
        $xml .= "</sitemapindex>\n";
        
        File::put(public_path('sitemap-keywords.xml'), $xml);
        $this->line("   ✓ Generated keyword sitemap index");
    }

    private function generateRouteDefinitions()
    {
        $this->info('📝 Generating route definitions for keywords...');
        
        $routes = "// Auto-generated keyword routes for SEO\n";
        $routes .= "// Generated on: " . now()->toDateTimeString() . "\n\n";
        
        foreach ($this->keywords as $slug => $data) {
            $routes .= "Route::get('/keywords/{$slug}', function() {\n";
            $routes .= "    return view('keywords.show', [\n";
            $routes .= "        'keyword' => '{$data['keyword']}',\n";
            $routes .= "        'slug' => '{$slug}',\n";
            $routes .= "    ]);\n";
            $routes .= "})->name('keywords.{$slug}');\n\n";
        }
        
        File::put(base_path('routes/keyword_routes.php'), $routes);
        $this->line("   ✓ Generated route definitions in routes/keyword_routes.php");
        
        $this->info("\n📝 To use these routes, add this to your web.php:");
        $this->line("   require_once __DIR__ . '/keyword_routes.php';");
    }

    private function displayKeywordStats()
    {
        $this->info("\n📊 Keyword Sitemap Statistics:");
        $this->line("   Total keywords: " . count($this->keywords));
        
        $types = [];
        foreach ($this->keywords as $data) {
            $type = $data['type'] ?? 'general';
            $types[$type] = ($types[$type] ?? 0) + 1;
        }
        
        foreach ($types as $type => $count) {
            $this->line("   {$type}: {$count} keywords");
        }
        
        $this->info("\n🚀 Top keywords for SEO:");
        $topKeywords = array_slice($this->keywords, 0, 10);
        foreach ($topKeywords as $slug => $data) {
            $this->line("   {$data['keyword']} (priority: {$data['priority']})");
        }
        
        $this->info("\n📝 Next steps:");
        $this->line("   1. Create keyword landing pages");
        $this->line("   2. Add keyword routes to web.php");
        $this->line("   3. Submit keyword sitemaps to Google Search Console");
        $this->line("   4. Monitor keyword rankings");
    }
} 