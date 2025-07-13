<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IndexNowService
{
    private $apiKey = 'cb4a3e27410c45f09a6a107fbecd69ff';
    private $keyLocation = 'https://maxmedme.com/cb4a3e27410c45f09a6a107fbecd69ff.txt';
    private $host = 'maxmedme.com';
    private $indexNowUrl = 'https://api.indexnow.org/indexnow';

    /**
     * Submit a single URL to IndexNow
     */
    public function submitUrl(string $url): bool
    {
        try {
            $response = Http::post($this->indexNowUrl, [
                'host' => $this->host,
                'key' => $this->apiKey,
                'keyLocation' => $this->keyLocation,
                'urlList' => [$url]
            ]);

            $this->logSubmission($url, $response->status(), $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('IndexNow submission failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Submit multiple URLs to IndexNow
     */
    public function submitUrls(array $urls): array
    {
        $results = [];
        
        // IndexNow allows up to 10,000 URLs per request
        $chunks = array_chunk($urls, 10000);
        
        foreach ($chunks as $chunk) {
            try {
                $response = Http::post($this->indexNowUrl, [
                    'host' => $this->host,
                    'key' => $this->apiKey,
                    'keyLocation' => $this->keyLocation,
                    'urlList' => $chunk
                ]);

                $status = $response->status();
                $body = $response->body();
                
                $results[] = [
                    'status' => $status,
                    'urls_count' => count($chunk),
                    'response' => $body,
                    'success' => $response->successful()
                ];

                $this->logBulkSubmission($chunk, $status, $body);
                
                // Rate limiting - wait 1 second between requests
                if (count($chunks) > 1) {
                    sleep(1);
                }
            } catch (\Exception $e) {
                Log::error('IndexNow bulk submission failed', [
                    'urls_count' => count($chunk),
                    'error' => $e->getMessage()
                ]);
                
                $results[] = [
                    'status' => 500,
                    'urls_count' => count($chunk),
                    'response' => $e->getMessage(),
                    'success' => false
                ];
            }
        }
        
        return $results;
    }

    /**
     * Submit all sitemap URLs to IndexNow
     */
    public function submitSitemapUrls(): array
    {
        $urls = $this->getSitemapUrls();
        return $this->submitUrls($urls);
    }

    /**
     * Submit new product URLs to IndexNow
     */
    public function submitNewProducts(): array
    {
        $products = \App\Models\Product::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orWhere('updated_at', '>=', Carbon::now()->subDays(7))
            ->get();

        $urls = [];
        foreach ($products as $product) {
            $urls[] = "https://{$this->host}/products/{$product->slug}";
        }

        return $this->submitUrls($urls);
    }

    /**
     * Submit new category URLs to IndexNow
     */
    public function submitNewCategories(): array
    {
        $categories = \App\Models\Category::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orWhere('updated_at', '>=', Carbon::now()->subDays(7))
            ->get();

        $urls = [];
        foreach ($categories as $category) {
            $urls[] = "https://{$this->host}/categories/{$category->slug}";
        }

        return $this->submitUrls($urls);
    }

    /**
     * Submit new news URLs to IndexNow
     */
    public function submitNewNews(): array
    {
        $news = \App\Models\News::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orWhere('updated_at', '>=', Carbon::now()->subDays(7))
            ->get();

        $urls = [];
        foreach ($news as $article) {
            $urls[] = "https://{$this->host}/news/{$article->slug}";
        }

        return $this->submitUrls($urls);
    }

    /**
     * Get all URLs from sitemaps
     */
    private function getSitemapUrls(): array
    {
        $urls = [];
        
        // Main pages
        $urls[] = "https://{$this->host}/";
        $urls[] = "https://{$this->host}/about";
        $urls[] = "https://{$this->host}/contact";
        $urls[] = "https://{$this->host}/products";
        $urls[] = "https://{$this->host}/categories";
        $urls[] = "https://{$this->host}/news";
        $urls[] = "https://{$this->host}/quotation/form";

        // Products
        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            $urls[] = "https://{$this->host}/products/{$product->slug}";
        }

        // Categories
        $categories = \App\Models\Category::all();
        foreach ($categories as $category) {
            $urls[] = "https://{$this->host}/categories/{$category->slug}";
        }

        // News
        $news = \App\Models\News::all();
        foreach ($news as $article) {
            $urls[] = "https://{$this->host}/news/{$article->slug}";
        }

        return $urls;
    }

    /**
     * Log submission results
     */
    private function logSubmission(string $url, int $status, string $response): void
    {
        Log::info('IndexNow URL submission', [
            'url' => $url,
            'status' => $status,
            'response' => $response,
            'timestamp' => Carbon::now()->toISOString()
        ]);
    }

    /**
     * Log bulk submission results
     */
    private function logBulkSubmission(array $urls, int $status, string $response): void
    {
        Log::info('IndexNow bulk URL submission', [
            'urls_count' => count($urls),
            'status' => $status,
            'response' => $response,
            'timestamp' => Carbon::now()->toISOString()
        ]);
    }

    /**
     * Get IndexNow configuration
     */
    public function getConfig(): array
    {
        return [
            'api_key' => $this->apiKey,
            'key_location' => $this->keyLocation,
            'host' => $this->host,
            'indexnow_url' => $this->indexNowUrl
        ];
    }

    /**
     * Validate IndexNow setup
     */
    public function validateSetup(): array
    {
        $results = [];

        // Check if key file exists and is accessible
        $keyFileUrl = $this->keyLocation;
        try {
            $response = Http::get($keyFileUrl);
            $results['key_file_accessible'] = $response->successful();
            $results['key_file_content'] = $response->body() === $this->apiKey;
        } catch (\Exception $e) {
            $results['key_file_accessible'] = false;
            $results['key_file_error'] = $e->getMessage();
        }

        // Test IndexNow API connection
        try {
            $response = Http::post($this->indexNowUrl, [
                'host' => $this->host,
                'key' => $this->apiKey,
                'keyLocation' => $this->keyLocation,
                'urlList' => ["https://{$this->host}/"]
            ]);
            $results['api_connection'] = $response->successful();
            $results['api_status'] = $response->status();
        } catch (\Exception $e) {
            $results['api_connection'] = false;
            $results['api_error'] = $e->getMessage();
        }

        return $results;
    }
} 