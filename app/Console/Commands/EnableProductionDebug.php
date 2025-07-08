<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnableProductionDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enable-production-debug {--disable : Disable debug logging}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable debug logging in production for troubleshooting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('disable')) {
            $this->disableDebugLogging();
        } else {
            $this->enableDebugLogging();
        }
    }

    /**
     * Enable debug logging
     */
    private function enableDebugLogging(): void
    {
        $this->info('🔧 Enabling production debug logging...');

        // Test debug logging
        Log::debug('Production debug logging test', [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'message' => 'Debug logging enabled'
        ]);

        $this->info('✅ Debug logging enabled');
        $this->info('📝 Debug logs will be written to: storage/logs/production-debug.log');
        $this->info('🔍 Monitor logs with: tail -f storage/logs/production-debug.log');
        $this->info('⚠️ Remember to disable debug logging after troubleshooting');
        
        $this->newLine();
        $this->info('To disable debug logging later, run:');
        $this->line('php artisan app:enable-production-debug --disable');
    }

    /**
     * Disable debug logging
     */
    private function disableDebugLogging(): void
    {
        $this->info('🔧 Disabling production debug logging...');

        // Test that regular logging still works
        Log::info('Production debug logging disabled', [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment()
        ]);

        $this->info('✅ Debug logging disabled');
        $this->info('📝 Regular logs will continue in: storage/logs/laravel.log');
    }
} 