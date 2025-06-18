<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize 
                            {--source=public/Images : Source directory for images}
                            {--output=public/Images/optimized : Output directory for optimized images}
                            {--quality=85 : JPEG quality (1-100)}
                            {--webp=true : Generate WebP versions}
                            {--avif=true : Generate AVIF versions}
                            {--resize=true : Resize large images}
                            {--max-width=1920 : Maximum image width}
                            {--max-height=1080 : Maximum image height}';

    protected $description = 'Optimize images for better SEO and performance';

    private $processedCount = 0;
    private $savedBytes = 0;

    public function handle()
    {
        $this->info('🖼️ Starting Image Optimization for MaxMed...');
        
        $sourceDir = $this->option('source');
        $outputDir = $this->option('output');
        $quality = (int) $this->option('quality');
        $generateWebP = $this->option('webp') === 'true';
        $generateAVIF = $this->option('avif') === 'true';
        $resize = $this->option('resize') === 'true';
        $maxWidth = (int) $this->option('max-width');
        $maxHeight = (int) $this->option('max-height');

        // Ensure output directory exists
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
            $this->info("Created output directory: {$outputDir}");
        }

        // Get all image files
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $images = collect();

        foreach ($imageExtensions as $ext) {
            $files = File::glob($sourceDir . '/**/*.' . $ext);
            $images = $images->concat($files);
        }

        if ($images->isEmpty()) {
            $this->warn('No images found in the source directory.');
            return 1;
        }

        $this->info("Found {$images->count()} images to optimize...");

        // Progress bar
        $progressBar = $this->output->createProgressBar($images->count());
        $progressBar->start();

        foreach ($images as $imagePath) {
            try {
                $this->optimizeImage($imagePath, $outputDir, $quality, $generateWebP, $generateAVIF, $resize, $maxWidth, $maxHeight);
                $this->processedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to optimize {$imagePath}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->displaySummary();
        $this->generateHtaccessRules($outputDir);
        
        return 0;
    }

    private function optimizeImage($imagePath, $outputDir, $quality, $generateWebP, $generateAVIF, $resize, $maxWidth, $maxHeight)
    {
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        
        // Get original file size
        $originalSize = filesize($imagePath);
        
        // Load and process image
        $image = Image::make($imagePath);
        
        // Resize if needed
        if ($resize && ($image->width() > $maxWidth || $image->height() > $maxHeight)) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Save optimized original format
        $outputPath = $outputDir . '/' . $filename;
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image->save($outputPath . '.jpg', $quality);
                break;
            case 'png':
                $image->save($outputPath . '.png', 9); // PNG compression level 0-9
                break;
            case 'gif':
                $image->save($outputPath . '.gif');
                break;
            default:
                $image->save($outputPath . '.' . $extension);
        }

        // Generate WebP version
        if ($generateWebP) {
            try {
                $webpPath = $outputPath . '.webp';
                $image->save($webpPath, $quality, 'webp');
            } catch (\Exception $e) {
                $this->warn("Could not generate WebP for {$filename}: " . $e->getMessage());
            }
        }

        // Generate AVIF version (requires libavif)
        if ($generateAVIF) {
            try {
                $avifPath = $outputPath . '.avif';
                // Note: AVIF support depends on your Image library configuration
                $image->save($avifPath, $quality, 'avif');
            } catch (\Exception $e) {
                // AVIF might not be supported, skip silently
            }
        }

        // Calculate space saved
        $newSize = filesize($outputPath . '.' . $extension);
        $this->savedBytes += ($originalSize - $newSize);

        // Free memory
        $image->destroy();
    }

    private function displaySummary()
    {
        $this->info('✅ Image Optimization Complete!');
        $this->info("📊 Processed: {$this->processedCount} images");
        
        if ($this->savedBytes > 0) {
            $savedMB = round($this->savedBytes / 1024 / 1024, 2);
            $this->info("💾 Space saved: {$savedMB} MB");
        }
        
        $this->info('🚀 Benefits:');
        $this->line('  • Faster page loading times');
        $this->line('  • Better Core Web Vitals scores');
        $this->line('  • Improved SEO rankings');
        $this->line('  • Better user experience');
        
        $this->info('📝 Next Steps:');
        $this->line('  1. Update your image references to use optimized versions');
        $this->line('  2. Implement <picture> tags with WebP/AVIF sources');
        $this->line('  3. Add proper alt tags to all images');
        $this->line('  4. Test your site\'s loading speed');
    }

    private function generateHtaccessRules($outputDir)
    {
        $htaccessContent = "# Image Optimization Rules\n";
        $htaccessContent .= "# Auto-serve WebP/AVIF images when supported\n\n";
        
        $htaccessContent .= "<IfModule mod_rewrite.c>\n";
        $htaccessContent .= "    RewriteEngine On\n\n";
        
        $htaccessContent .= "    # Serve AVIF images if supported\n";
        $htaccessContent .= "    RewriteCond %{HTTP_ACCEPT} image/avif\n";
        $htaccessContent .= "    RewriteCond %{REQUEST_FILENAME} \\.(jpe?g|png)$\n";
        $htaccessContent .= "    RewriteCond %{REQUEST_FILENAME}.avif -f\n";
        $htaccessContent .= "    RewriteRule (.+) \$1.avif [T=image/avif,E=accept:1]\n\n";
        
        $htaccessContent .= "    # Serve WebP images if supported and AVIF not available\n";
        $htaccessContent .= "    RewriteCond %{HTTP_ACCEPT} image/webp\n";
        $htaccessContent .= "    RewriteCond %{REQUEST_FILENAME} \\.(jpe?g|png)$\n";
        $htaccessContent .= "    RewriteCond %{REQUEST_FILENAME}.webp -f\n";
        $htaccessContent .= "    RewriteRule (.+) \$1.webp [T=image/webp,E=accept:1]\n";
        $htaccessContent .= "</IfModule>\n\n";
        
        $htaccessContent .= "<IfModule mod_headers.c>\n";
        $htaccessContent .= "    Header append Vary Accept env=REDIRECT_accept\n";
        $htaccessContent .= "</IfModule>\n\n";
        
        $htaccessContent .= "# Cache optimized images\n";
        $htaccessContent .= "<IfModule mod_expires.c>\n";
        $htaccessContent .= "    ExpiresByType image/webp \"access plus 1 month\"\n";
        $htaccessContent .= "    ExpiresByType image/avif \"access plus 1 month\"\n";
        $htaccessContent .= "</IfModule>\n";

        $htaccessPath = $outputDir . '/.htaccess';
        File::put($htaccessPath, $htaccessContent);
        
        $this->info("Generated .htaccess rules at: {$htaccessPath}");
    }
} 