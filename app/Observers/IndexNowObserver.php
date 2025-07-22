<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Services\IndexNowService;
use Illuminate\Support\Facades\Log;

class IndexNowObserver
{
    private $indexNowService;

    public function __construct(IndexNowService $indexNowService)
    {
        $this->indexNowService = $indexNowService;
    }

    /**
     * Handle the "created" event.
     */
    public function created($model): void
    {
        if ($model instanceof Product) {
            $this->submitProductUrl($model, 'created');
        } elseif ($model instanceof Category) {
            $this->submitCategoryUrl($model, 'created');
        } elseif ($model instanceof News) {
            $this->submitNewsUrl($model, 'created');
        }
    }

    /**
     * Handle the "updated" event.
     */
    public function updated($model): void
    {
        if ($model instanceof Product) {
            $this->submitProductUrl($model, 'updated');
        } elseif ($model instanceof Category) {
            $this->submitCategoryUrl($model, 'updated');
        } elseif ($model instanceof News) {
            $this->submitNewsUrl($model, 'updated');
        }
    }

    /**
     * Handle the Category "created" event.
     */
    public function categoryCreated(Category $category): void
    {
        $this->submitCategoryUrl($category, 'created');
    }

    /**
     * Handle the Category "updated" event.
     */
    public function categoryUpdated(Category $category): void
    {
        $this->submitCategoryUrl($category, 'updated');
    }

    /**
     * Handle the News "created" event.
     */
    public function newsCreated(News $news): void
    {
        $this->submitNewsUrl($news, 'created');
    }

    /**
     * Handle the News "updated" event.
     */
    public function newsUpdated(News $news): void
    {
        $this->submitNewsUrl($news, 'updated');
    }

    /**
     * Submit product URL to IndexNow
     */
    private function submitProductUrl(Product $product, string $action): void
    {
        try {
            $url = "https://maxmedme.com/products/{$product->slug}";
            $success = $this->indexNowService->submitUrl($url);
            
            Log::info("IndexNow: Product {$action}", [
                'product_id' => $product->id,
                'product_slug' => $product->slug,
                'url' => $url,
                'submission_success' => $success
            ]);
        } catch (\Exception $e) {
            Log::error("IndexNow: Failed to submit product URL", [
                'product_id' => $product->id,
                'product_slug' => $product->slug,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Submit category URL to IndexNow
     */
    private function submitCategoryUrl(Category $category, string $action): void
    {
        try {
            $url = "https://maxmedme.com/categories/{$category->slug}";
            $success = $this->indexNowService->submitUrl($url);
            
            Log::info("IndexNow: Category {$action}", [
                'category_id' => $category->id,
                'category_slug' => $category->slug,
                'url' => $url,
                'submission_success' => $success
            ]);
        } catch (\Exception $e) {
            Log::error("IndexNow: Failed to submit category URL", [
                'category_id' => $category->id,
                'category_slug' => $category->slug,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Submit news URL to IndexNow
     */
    private function submitNewsUrl(News $news, string $action): void
    {
        try {
            $url = "https://maxmedme.com/news/{$news->slug}";
            $success = $this->indexNowService->submitUrl($url);
            
            Log::info("IndexNow: News {$action}", [
                'news_id' => $news->id,
                'news_slug' => $news->slug,
                'url' => $url,
                'submission_success' => $success
            ]);
        } catch (\Exception $e) {
            Log::error("IndexNow: Failed to submit news URL", [
                'news_id' => $news->id,
                'news_slug' => $news->slug,
                'error' => $e->getMessage()
            ]);
        }
    }
} 