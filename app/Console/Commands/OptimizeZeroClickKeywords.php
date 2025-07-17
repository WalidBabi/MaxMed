<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SeoService;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OptimizeZeroClickKeywords extends Command
{
    protected $signature = 'seo:optimize-zero-click-keywords {--keyword= : Specific keyword to optimize} {--strategy= : Specific strategy to implement}';
    protected $description = 'Optimize zero-click keywords from Google Search Console data';

    private $seoService;

    public function __construct(SeoService $seoService)
    {
        parent::__construct();
        $this->seoService = $seoService;
    }

    public function handle()
    {
        $this->info('🔍 Starting Zero-Click Keyword Optimization...');
        
        $specificKeyword = $this->option('keyword');
        $specificStrategy = $this->option('strategy');
        
        if ($specificKeyword) {
            $this->optimizeSpecificKeyword($specificKeyword, $specificStrategy);
        } else {
            $this->optimizeAllZeroClickKeywords();
        }
        
        $this->info('✅ Zero-Click Keyword Optimization Complete!');
    }

    private function optimizeSpecificKeyword(string $keyword, ?string $strategy = null)
    {
        $this->info("🎯 Optimizing keyword: {$keyword}");
        
        $optimization = $this->seoService->generateZeroClickOptimization($keyword);
        
        if (empty($optimization)) {
            $this->error("❌ No optimization strategy found for keyword: {$keyword}");
            return;
        }
        
        $this->displayOptimizationStrategy($keyword, $optimization);
        
        if ($strategy) {
            $this->implementSpecificStrategy($keyword, $strategy, $optimization);
        } else {
            $this->implementAllStrategies($keyword, $optimization);
        }
    }

    private function optimizeAllZeroClickKeywords()
    {
        $zeroClickKeywords = $this->seoService->getZeroClickKeywords();
        
        $this->info("📊 Found " . count($zeroClickKeywords) . " zero-click keywords to optimize");
        
        $report = $this->seoService->generateZeroClickSeoReport();
        
        // Display summary table
        $this->displaySummaryTable($report);
        
        // Ask user which keywords to optimize
        $keywordsToOptimize = $this->askUserForKeywords($zeroClickKeywords);
        
        foreach ($keywordsToOptimize as $keyword) {
            $this->optimizeSpecificKeyword($keyword);
        }
        
        // Generate comprehensive report
        $this->generateOptimizationReport($report);
    }

    private function displaySummaryTable(array $report)
    {
        $headers = ['Keyword', 'Impressions', 'Position', 'Priority', 'Potential Clicks', 'Revenue Potential'];
        $rows = [];
        
        foreach ($report as $keyword => $data) {
            $rows[] = [
                $keyword,
                $data['current_performance']['impressions'],
                $data['current_performance']['position'],
                $data['priority_level'],
                $data['estimated_impact']['potential_clicks'],
                '$' . number_format($data['estimated_impact']['revenue_potential'])
            ];
        }
        
        $this->table($headers, $rows);
    }

    private function askUserForKeywords(array $zeroClickKeywords): array
    {
        $this->info("\n🎯 Select keywords to optimize:");
        
        $choices = [];
        foreach ($zeroClickKeywords as $keyword => $data) {
            $priority = $data['impressions'] >= 100 ? 'HIGH' : ($data['impressions'] >= 50 ? 'MEDIUM' : 'LOW');
            $choices[] = "{$keyword} ({$priority} priority - {$data['impressions']} impressions)";
        }
        
        $selected = $this->choice(
            'Choose keywords to optimize (comma-separated for multiple):',
            $choices,
            null,
            null,
            true
        );
        
        $keywords = [];
        foreach ($selected as $choice) {
            $keyword = explode(' (', $choice)[0];
            $keywords[] = $keyword;
        }
        
        return $keywords;
    }

    private function displayOptimizationStrategy(string $keyword, array $optimization)
    {
        $this->info("\n📋 Optimization Strategy for '{$keyword}':");
        
        if (isset($optimization['title'])) {
            $this->line("📝 Title: {$optimization['title']}");
        }
        
        if (isset($optimization['description'])) {
            $this->line("📄 Description: {$optimization['description']}");
        }
        
        if (isset($optimization['content_focus'])) {
            $this->line("🎯 Content Focus: {$optimization['content_focus']}");
        }
        
        if (isset($optimization['cta_strategy'])) {
            $this->line("🔗 CTA Strategy: {$optimization['cta_strategy']}");
        }
        
        if (isset($optimization['add_sections'])) {
            $this->line("📑 Add Sections:");
            foreach ($optimization['add_sections'] as $section) {
                $this->line("   • {$section}");
            }
        }
        
        if (isset($optimization['cta_improvements'])) {
            $this->line("🔗 CTA Improvements:");
            foreach ($optimization['cta_improvements'] as $improvement) {
                $this->line("   • {$improvement}");
            }
        }
        
        if (isset($optimization['content_additions'])) {
            $this->line("📝 Content Additions:");
            foreach ($optimization['content_additions'] as $addition) {
                $this->line("   • {$addition}");
            }
        }
    }

    private function implementSpecificStrategy(string $keyword, string $strategy, array $optimization)
    {
        $this->info("🔧 Implementing strategy: {$strategy}");
        
        switch ($strategy) {
            case 'create_landing_page':
                $this->createDedicatedLandingPage($keyword, $optimization);
                break;
            case 'enhance_category':
                $this->enhanceCategoryPage($keyword, $optimization);
                break;
            case 'create_guide':
                $this->createGuideContent($keyword, $optimization);
                break;
            case 'local_seo':
                $this->implementLocalSeo($keyword, $optimization);
                break;
            case 'b2b_optimization':
                $this->implementB2bOptimization($keyword, $optimization);
                break;
            default:
                $this->error("❌ Unknown strategy: {$strategy}");
        }
    }

    private function implementAllStrategies(string $keyword, array $optimization)
    {
        $this->info("🔧 Implementing all strategies for '{$keyword}'");
        
        // Create dedicated landing page if needed
        if (isset($optimization['title']) && isset($optimization['description'])) {
            $this->createDedicatedLandingPage($keyword, $optimization);
        }
        
        // Enhance category pages
        if (isset($optimization['add_sections'])) {
            $this->enhanceCategoryPage($keyword, $optimization);
        }
        
        // Create guide content
        if (isset($optimization['sections'])) {
            $this->createGuideContent($keyword, $optimization);
        }
        
        // Implement local SEO
        if (isset($optimization['local_keywords'])) {
            $this->implementLocalSeo($keyword, $optimization);
        }
        
        // Implement B2B optimization
        if (isset($optimization['b2b_keywords'])) {
            $this->implementB2bOptimization($keyword, $optimization);
        }
    }

    private function createDedicatedLandingPage(string $keyword, array $optimization)
    {
        $this->info("📄 Creating dedicated landing page for '{$keyword}'");
        
        $slug = str_replace(' ', '-', strtolower($keyword));
        $viewPath = "resources/views/seo/{$slug}.blade.php";
        
        if (File::exists($viewPath)) {
            $this->warn("⚠️  Landing page already exists: {$viewPath}");
            return;
        }
        
        $content = $this->generateLandingPageContent($keyword, $optimization);
        File::put($viewPath, $content);
        
        $this->info("✅ Created landing page: {$viewPath}");
        
        // Add route for the landing page
        $this->addLandingPageRoute($keyword, $slug);
    }

    private function generateLandingPageContent(string $keyword, array $optimization): string
    {
        $title = $optimization['title'] ?? ucfirst($keyword) . ' Dubai UAE | MaxMed';
        $description = $optimization['description'] ?? "🔬 Premium {$keyword} in Dubai UAE! ✅ Quality guaranteed ⚡ Fast delivery ☎️ +971 55 460 2500";
        
        return <<<BLADE
@extends('layouts.app')

@section('title', '{$title}')
@section('meta_description', '{$description}')
@section('meta_keywords', '{$keyword}, {$keyword} dubai, {$keyword} uae, laboratory equipment, medical supplies, MaxMed UAE')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h1 class="page-title">{{ ucfirst('{$keyword}') }} Dubai UAE</h1>
                <div class="breadcrumbs mb-3">
                    <a href="{{ route('home') }}">Home</a> &gt;
                    <span>{{ ucfirst('{$keyword}') }}</span>
                </div>
            </div>
            
            <div class="hero-section bg-primary text-white p-5 rounded mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2>Premium {{ ucfirst('{$keyword}') }} in Dubai UAE</h2>
                        <p class="lead">🔬 Professional {$keyword} from leading manufacturers. ✅ Quality guaranteed ⚡ Fast delivery across UAE.</p>
                        <div class="d-flex gap-3">
                            <a href="tel:+971554602500" class="btn btn-light btn-lg">
                                <i class="fas fa-phone"></i> Call +971 55 460 2500
                            </a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-envelope"></i> Request Quote
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-flask fa-5x"></i>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="content-section mb-4">
                        <h3>About {{ ucfirst('{$keyword}') }}</h3>
                        <p>MaxMed UAE offers premium {$keyword} for laboratories, hospitals, and research facilities across Dubai and the UAE. Our comprehensive range includes the latest technology and equipment from world-renowned manufacturers.</p>
                        
                        <h4>Key Features:</h4>
                        <ul>
                            <li>✅ Premium quality from leading manufacturers</li>
                            <li>✅ Fast delivery across UAE</li>
                            <li>✅ Professional installation and support</li>
                            <li>✅ Comprehensive warranty coverage</li>
                            <li>✅ Technical training and maintenance</li>
                        </ul>
                    </div>
                    
                    <div class="cta-section text-center p-4 bg-light rounded">
                        <h4>Ready to Get Started?</h4>
                        <p>Contact MaxMed UAE today for expert consultation and competitive pricing.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="tel:+971554602500" class="btn btn-primary btn-lg">
                                <i class="fas fa-phone"></i> Call Now
                            </a>
                            <a href="https://wa.me/971554602500" class="btn btn-success btn-lg">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="sidebar-section">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>Quick Contact</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Phone:</strong><br>+971 55 460 2500</p>
                                <p><strong>Email:</strong><br>sales@maxmedme.com</p>
                                <p><strong>Address:</strong><br>Dubai, UAE</p>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>Why Choose MaxMed?</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li>✅ 14+ Years Experience</li>
                                    <li>✅ 500+ Labs Trust Us</li>
                                    <li>✅ ISO Certified</li>
                                    <li>✅ Same Day Quotes</li>
                                    <li>✅ Fast UAE Delivery</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    private function addLandingPageRoute(string $keyword, string $slug)
    {
        $routeContent = <<<PHP
// Zero-click keyword landing page
Route::get('/{$slug}', function () {
    return view('seo.{$slug}');
})->name('seo.{$slug}');

PHP;
        
        $routesPath = base_path('routes/web.php');
        $routesContent = File::get($routesPath);
        
        // Add route before the closing PHP tag
        if (strpos($routesContent, $routeContent) === false) {
            $routesContent = str_replace('?>', $routeContent . "\n?>", $routesContent);
            File::put($routesPath, $routesContent);
            $this->info("✅ Added route for /{$slug}");
        }
    }

    private function enhanceCategoryPage(string $keyword, array $optimization)
    {
        $this->info("🔧 Enhancing category page for '{$keyword}'");
        
        // Find relevant category
        $category = Category::where('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->first();
        
        if (!$category) {
            $this->warn("⚠️  No category found for keyword: {$keyword}");
            return;
        }
        
        $this->info("✅ Found category: {$category->name}");
        
        // Update category description with optimization content
        if (isset($optimization['content_additions'])) {
            $enhancedDescription = $category->description . "\n\n" . implode("\n", $optimization['content_additions']);
            $category->update(['description' => $enhancedDescription]);
            $this->info("✅ Enhanced category description");
        }
    }

    private function createGuideContent(string $keyword, array $optimization)
    {
        $this->info("📚 Creating guide content for '{$keyword}'");
        
        $guidePath = "resources/views/guides/{$keyword}.blade.php";
        
        if (File::exists($guidePath)) {
            $this->warn("⚠️  Guide already exists: {$guidePath}");
            return;
        }
        
        $content = $this->generateGuideContent($keyword, $optimization);
        File::put($guidePath, $content);
        
        $this->info("✅ Created guide: {$guidePath}");
    }

    private function generateGuideContent(string $keyword, array $optimization): string
    {
        $title = $optimization['title'] ?? ucfirst($keyword) . ' Guide Dubai UAE';
        $sections = $optimization['sections'] ?? ['Overview', 'Selection Guide', 'Installation', 'Maintenance'];
        
        $sectionsHtml = '';
        foreach ($sections as $section) {
            $sectionsHtml .= <<<HTML
            <div class="section mb-4">
                <h3>{$section}</h3>
                <p>Comprehensive information about {$section} for {$keyword} in Dubai UAE. Contact MaxMed UAE at +971 55 460 2500 for expert consultation.</p>
            </div>
HTML;
        }
        
        return <<<BLADE
@extends('layouts.app')

@section('title', '{$title}')
@section('meta_description', 'Complete guide to {$keyword} in Dubai UAE. Expert advice, selection tips, and professional consultation from MaxMed UAE. ☎️ +971 55 460 2500')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h1 class="page-title">{$title}</h1>
                <div class="breadcrumbs mb-3">
                    <a href="{{ route('home') }}">Home</a> &gt;
                    <a href="{{ route('categories.index') }}">Guides</a> &gt;
                    <span>{$keyword}</span>
                </div>
            </div>
            
            <div class="guide-content">
                {$sectionsHtml}
                
                <div class="cta-section text-center p-4 bg-primary text-white rounded mt-4">
                    <h4>Need Expert Consultation?</h4>
                    <p>Contact MaxMed UAE for professional advice on {$keyword} selection and implementation.</p>
                    <a href="tel:+971554602500" class="btn btn-light btn-lg">
                        <i class="fas fa-phone"></i> Call +971 55 460 2500
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    private function implementLocalSeo(string $keyword, array $optimization)
    {
        $this->info("📍 Implementing local SEO for '{$keyword}'");
        
        if (isset($optimization['local_keywords'])) {
            $this->info("✅ Local keywords identified: " . implode(', ', $optimization['local_keywords']));
        }
        
        if (isset($optimization['local_schema'])) {
            $this->info("✅ Local schema types: " . implode(', ', $optimization['local_schema']));
        }
    }

    private function implementB2bOptimization(string $keyword, array $optimization)
    {
        $this->info("🏢 Implementing B2B optimization for '{$keyword}'");
        
        if (isset($optimization['b2b_keywords'])) {
            $this->info("✅ B2B keywords identified: " . implode(', ', $optimization['b2b_keywords']));
        }
        
        if (isset($optimization['content_focus'])) {
            $this->info("✅ B2B content focus areas: " . implode(', ', $optimization['content_focus']));
        }
    }

    private function generateOptimizationReport(array $report)
    {
        $this->info("📊 Generating optimization report...");
        
        $reportPath = storage_path('app/seo/zero-click-optimization-report.json');
        Storage::makeDirectory('seo');
        
        $reportData = [
            'generated_at' => now()->toISOString(),
            'total_keywords' => count($report),
            'high_priority' => count(array_filter($report, fn($data) => $data['priority_level'] === 'HIGH')),
            'medium_priority' => count(array_filter($report, fn($data) => $data['priority_level'] === 'MEDIUM')),
            'low_priority' => count(array_filter($report, fn($data) => $data['priority_level'] === 'LOW')),
            'total_potential_clicks' => array_sum(array_column($report, 'estimated_impact.potential_clicks')),
            'total_revenue_potential' => array_sum(array_column($report, 'estimated_impact.revenue_potential')),
            'keywords' => $report
        ];
        
        Storage::put('seo/zero-click-optimization-report.json', json_encode($reportData, JSON_PRETTY_PRINT));
        
        $this->info("✅ Optimization report saved to: {$reportPath}");
        
        // Display summary
        $this->info("\n📈 Optimization Summary:");
        $this->line("• Total Keywords: " . $reportData['total_keywords']);
        $this->line("• High Priority: " . $reportData['high_priority']);
        $this->line("• Medium Priority: " . $reportData['medium_priority']);
        $this->line("• Low Priority: " . $reportData['low_priority']);
        $this->line("• Total Potential Clicks: " . $reportData['total_potential_clicks']);
        $this->line("• Total Revenue Potential: $" . number_format($reportData['total_revenue_potential']));
    }
} 