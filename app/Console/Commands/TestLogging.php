<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestLogging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-logging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all logging levels to ensure they are working';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing logging system...');
        
        $testData = [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'test_id' => uniqid(),
            'user_agent' => 'Test Command',
            'ip' => '127.0.0.1'
        ];

        // Test all log levels
        Log::emergency('Test emergency log', $testData);
        Log::alert('Test alert log', $testData);
        Log::critical('Test critical log', $testData);
        Log::error('Test error log', $testData);
        Log::warning('Test warning log', $testData);
        Log::notice('Test notice log', $testData);
        Log::info('Test info log', $testData);
        Log::debug('Test debug log', $testData);

        $this->info('✅ All log levels tested');
        $this->info('📝 Check your log files:');
        $this->line('   - storage/logs/laravel.log');
        $this->line('   - storage/logs/production-debug.log');
        
        $this->newLine();
        $this->info('🔍 To view recent logs:');
        $this->line('   tail -20 storage/logs/laravel.log');
        $this->line('   tail -20 storage/logs/production-debug.log');
    }
} 