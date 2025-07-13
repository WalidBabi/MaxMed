<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SimpleXMLElement;
use DOMDocument;
use DOMXPath;

class GenerateBingTextSitemaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate-bing-text {--max-urls=50000} {--output-dir=public}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate text file sitemaps for Bing Webmaster Tools from existing XML sitemaps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maxUrls = $this->option('max-urls');
        $outputDir = $this->option('output-dir');
        
        $this->info('Starting Bing text sitemap generation...');
        
        // Create output directory if it doesn't exist
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        $allUrls = $this->extractAllUrls();
        
        if (empty($allUrls)) {
            $this->error('No URLs found in XML sitemaps!');
            return 1;
        }
        
        $this->info('Found ' . count($allUrls) . ' unique URLs');
        
        // Split URLs into chunks based on max URLs per file
        $urlChunks = array_chunk($allUrls, $maxUrls);
        
        $this->info('Creating ' . count($urlChunks) . ' text sitemap files...');
        
        foreach ($urlChunks as $index => $urlChunk) {
            $filename = $outputDir . '/sitemap-bing-' . ($index + 1) . '.txt';
            $this->createTextSitemap($filename, $urlChunk);
            $this->info('Created: ' . $filename . ' (' . count($urlChunk) . ' URLs)');
        }
        
        // Create a main sitemap index file
        $this->createSitemapIndex($outputDir, count($urlChunks));
        
        $this->info('Bing text sitemap generation completed successfully!');
        $this->info('You can now submit these files to Bing Webmaster Tools:');
        
        for ($i = 1; $i <= count($urlChunks); $i++) {
            $this->line('- ' . $outputDir . '/sitemap-bing-' . $i . '.txt');
        }
        
        return 0;
    }
    
    /**
     * Extract all URLs from XML sitemaps
     */
    private function extractAllUrls(): array
    {
        $urls = [];
        $publicDir = public_path();
        
        // Get all XML sitemap files
        $sitemapFiles = glob($publicDir . '/sitemap*.xml');
        
        foreach ($sitemapFiles as $sitemapFile) {
            $this->line('Processing: ' . basename($sitemapFile));
            
            try {
                $xmlContent = file_get_contents($sitemapFile);
                
                if (!$xmlContent) {
                    $this->warn('Could not read: ' . basename($sitemapFile));
                    continue;
                }
                
                // Check if it's a sitemap index
                if (strpos($xmlContent, '<sitemapindex') !== false) {
                    $urls = array_merge($urls, $this->extractUrlsFromSitemapIndex($xmlContent));
                } else {
                    $urls = array_merge($urls, $this->extractUrlsFromSitemap($xmlContent));
                }
                
            } catch (\Exception $e) {
                $this->warn('Error processing ' . basename($sitemapFile) . ': ' . $e->getMessage());
            }
        }
        
        // Remove duplicates and sort
        $urls = array_unique($urls);
        sort($urls);
        
        return $urls;
    }
    
    /**
     * Extract URLs from a sitemap index file
     */
    private function extractUrlsFromSitemapIndex(string $xmlContent): array
    {
        $urls = [];
        
        try {
            $xml = new SimpleXMLElement($xmlContent);
            $xml->registerXPathNamespace('sitemap', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            
            foreach ($xml->sitemap as $sitemap) {
                $sitemapUrl = (string)$sitemap->loc;
                
                // Fetch and process the individual sitemap
                $sitemapContent = $this->fetchSitemapContent($sitemapUrl);
                if ($sitemapContent) {
                    $urls = array_merge($urls, $this->extractUrlsFromSitemap($sitemapContent));
                }
            }
        } catch (\Exception $e) {
            $this->warn('Error processing sitemap index: ' . $e->getMessage());
        }
        
        return $urls;
    }
    
    /**
     * Extract URLs from a regular sitemap file
     */
    private function extractUrlsFromSitemap(string $xmlContent): array
    {
        $urls = [];
        
        try {
            $xml = new SimpleXMLElement($xmlContent);
            $xml->registerXPathNamespace('url', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            
            foreach ($xml->url as $url) {
                $urlString = (string)$url->loc;
                if (!empty($urlString)) {
                    $urls[] = $urlString;
                }
            }
        } catch (\Exception $e) {
            $this->warn('Error extracting URLs from sitemap: ' . $e->getMessage());
        }
        
        return $urls;
    }
    
    /**
     * Fetch sitemap content from URL
     */
    private function fetchSitemapContent(string $url): ?string
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'MaxMed-Sitemap-Generator/1.0'
                ]
            ]);
            
            $content = file_get_contents($url, false, $context);
            return $content ?: null;
        } catch (\Exception $e) {
            $this->warn('Could not fetch sitemap from: ' . $url);
            return null;
        }
    }
    
    /**
     * Create a text sitemap file
     */
    private function createTextSitemap(string $filename, array $urls): void
    {
        $content = implode("\n", $urls);
        file_put_contents($filename, $content);
    }
    
    /**
     * Create a sitemap index file for the text sitemaps
     */
    private function createSitemapIndex(string $outputDir, int $count): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        for ($i = 1; $i <= $count; $i++) {
            $xml .= '    <sitemap>' . "\n";
            $xml .= '        <loc>https://maxmedme.com/sitemap-bing-' . $i . '.txt</loc>' . "\n";
            $xml .= '        <lastmod>' . date('c') . '</lastmod>' . "\n";
            $xml .= '    </sitemap>' . "\n";
        }
        
        $xml .= '</sitemapindex>';
        
        file_put_contents($outputDir . '/sitemap-bing-index.xml', $xml);
        $this->info('Created sitemap index: ' . $outputDir . '/sitemap-bing-index.xml');
    }
} 