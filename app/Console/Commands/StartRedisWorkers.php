<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartRedisWorkers extends Command
{
    protected $signature = 'redis:start-workers {--all : Start workers for all queues}';
    protected $description = 'Start Redis queue workers for MaxMed application';

    public function handle()
    {
        $all = $this->option('all');
        
        if ($all) {
            $this->info('🚀 Starting Redis workers for all queues...');
            
            $this->info('📧 Starting email queue worker...');
            $this->startWorker('emails', 3);
            
            $this->info('🔔 Starting notifications queue worker...');
            $this->startWorker('notifications', 2);
            
            $this->info('📈 Starting campaigns queue worker...');
            $this->startWorker('campaigns', 1);
            
            $this->info('⚡ Starting realtime queue worker...');
            $this->startWorker('realtime', 2);
            
        } else {
            $queue = $this->choice(
                'Which queue would you like to start?',
                ['emails', 'notifications', 'campaigns', 'realtime', 'all'],
                'all'
            );
            
            if ($queue === 'all') {
                return $this->call('redis:start-workers', ['--all' => true]);
            }
            
            $processes = $this->ask("How many processes for {$queue} queue?", 1);
            $this->startWorker($queue, $processes);
        }
        
        $this->info('✅ Workers started successfully!');
        $this->info('💡 Use `php artisan queue:monitor` to monitor queue status');
        $this->info('🔍 Use `php artisan queue:failed` to check failed jobs');
        
        return 0;
    }
    
    private function startWorker($queue, $processes = 1)
    {
        $timeout = match($queue) {
            'campaigns' => 600,  // 10 minutes for bulk campaigns
            'emails' => 300,     // 5 minutes for emails
            'notifications' => 120, // 2 minutes for notifications
            'realtime' => 60,    // 1 minute for realtime
            default => 300
        };
        
        $command = "php artisan queue:work redis --queue={$queue} --tries=3 --timeout={$timeout} --sleep=3";
        
        $this->info("Starting {$processes} worker(s) for {$queue} queue...");
        $this->line("Command: {$command}");
        
        // For development, suggest running manually
        $this->warn("⚠️  Run this command manually in separate terminal(s):");
        $this->line("   {$command}");
        
        if ($processes > 1) {
            $this->line("   (Run {$processes} times in different terminals)");
        }
        
        $this->line('');
    }
} 