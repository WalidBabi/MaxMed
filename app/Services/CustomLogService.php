<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class CustomLogService
{
    private $logPath;
    
    public function __construct()
    {
        $this->logPath = storage_path('logs/custom.log');
    }
    
    public function info($message, array $context = [])
    {
        $this->writeLog('INFO', $message, $context);
    }
    
    public function error($message, array $context = [])
    {
        $this->writeLog('ERROR', $message, $context);
    }
    
    public function warning($message, array $context = [])
    {
        $this->writeLog('WARNING', $message, $context);
    }
    
    public function debug($message, array $context = [])
    {
        $this->writeLog('DEBUG', $message, $context);
    }
    
    private function writeLog($level, $message, array $context = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        
        File::append($this->logPath, $logEntry);
    }
} 