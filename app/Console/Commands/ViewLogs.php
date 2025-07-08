<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ViewLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:view {--lines=50 : Number of lines to display} {--file=laravel.log : Log file to view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View Laravel logs with proper encoding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lines = $this->option('lines');
        $file = $this->option('file');
        $logPath = storage_path('logs/' . $file);

        if (!file_exists($logPath)) {
            $this->error("Log file not found: {$logPath}");
            return 1;
        }

        $this->info("Viewing last {$lines} lines of {$file}:");
        $this->line(str_repeat('-', 60));

        // Read the file and display the last N lines
        $content = file_get_contents($logPath);
        
        // Ensure UTF-8 encoding
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }

        $allLines = explode("\n", $content);
        $lastLines = array_slice($allLines, -$lines);

        foreach ($lastLines as $line) {
            if (trim($line)) {
                $this->line($line);
            }
        }

        $this->line(str_repeat('-', 60));
        $this->info("Total file size: " . formatBytes(filesize($logPath)));
        
        return 0;
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
} 