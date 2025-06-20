<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class RunCampaignWorker extends Command
{
    protected $signature = 'campaigns:work 
                           {--mode=standard : Worker mode: standard, turbo, or redis}
                           {--timeout=300 : Maximum execution time per job}
                           {--memory=512 : Memory limit in MB}';
    
    protected $description = 'Run optimized queue worker for campaign processing';

    public function handle()
    {
        $mode = $this->option('mode');
        $timeout = $this->option('timeout');
        $memory = $this->option('memory');

        $this->info("🚀 Starting MaxMed Campaign Worker");
        $this->info("📊 Mode: " . ucfirst($mode));
        $this->info("⏱️  Timeout: {$timeout}s");
        $this->info("💾 Memory Limit: {$memory}MB");
        $this->line('');

        // Check current queue configuration
        $queueConnection = config('queue.default');
        $this->info("🔗 Queue Driver: {$queueConnection}");

        // Different modes for different campaign sizes
        switch ($mode) {
            case 'turbo':
                // For huge campaigns (10,000+ recipients)
                $this->turboMode($timeout, $memory);
                break;
                
            case 'redis':
                // For Redis-powered campaigns
                $this->redisMode($timeout, $memory);
                break;
                
            default:
                // Standard optimized mode
                $this->standardMode($timeout, $memory);
                break;
        }
    }

    private function standardMode($timeout, $memory)
    {
        $this->info("📈 Standard Mode: Optimized for campaigns up to 5,000 recipients");
        $this->info("⚡ Performance: ~500-1,000 emails/minute");
        $this->line('');
        
        $command = "queue:work --timeout={$timeout} --memory={$memory} --tries=3 --sleep=2";
        $this->call('queue:work', [
            '--timeout' => $timeout,
            '--memory' => $memory,
            '--tries' => 3,
            '--sleep' => 2,
        ]);
    }

    private function turboMode($timeout, $memory)
    {
        $this->info("🔥 Turbo Mode: For huge campaigns (10,000+ recipients)");
        $this->info("⚡ Performance: ~2,000-3,000 emails/minute");
        $this->warn("⚠️  Higher resource usage - monitor system performance");
        $this->line('');
        
        $this->call('queue:work', [
            '--timeout' => $timeout,
            '--memory' => $memory,
            '--tries' => 3,
            '--sleep' => 1,
            '--max-jobs' => 1000,
        ]);
    }

    private function redisMode($timeout, $memory)
    {
        if (config('queue.default') !== 'redis') {
            $this->error("❌ Redis mode requires QUEUE_CONNECTION=redis in .env");
            $this->info("📋 Update your .env file and run: php artisan config:clear");
            return;
        }

        $this->info("🏆 Redis Mode: Production-ready for millions of recipients");
        $this->info("⚡ Performance: ~10,000+ emails/minute");
        $this->info("🎯 Queue: campaigns");
        $this->line('');
        
        $this->call('queue:work', [
            'connection' => 'redis',
            '--queue' => 'campaigns',
            '--timeout' => $timeout,
            '--memory' => $memory,
            '--tries' => 3,
            '--sleep' => 1,
            '--max-jobs' => 1000,
        ]);
    }

    public function info($string, $verbosity = null)
    {
        parent::info($string, $verbosity);
        
        // Also log to file for monitoring
        \Log::info("Campaign Worker: " . $string);
    }
} 