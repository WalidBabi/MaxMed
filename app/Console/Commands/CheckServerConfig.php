<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckServerConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-server-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check server configuration for potential 500 error causes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking server configuration for potential 500 error causes...');
        $this->newLine();

        $issues = [];

        // Check 1: PHP Configuration
        $this->info('1. Checking PHP Configuration...');
        $this->checkPhpConfiguration($issues);

        // Check 2: Laravel Configuration
        $this->info('2. Checking Laravel Configuration...');
        $this->checkLaravelConfiguration($issues);

        // Check 3: Database Configuration
        $this->info('3. Checking Database Configuration...');
        $this->checkDatabaseConfiguration($issues);

        // Check 4: File Permissions
        $this->info('4. Checking File Permissions...');
        $this->checkFilePermissions($issues);

        // Check 5: Required Extensions
        $this->info('5. Checking Required Extensions...');
        $this->checkRequiredExtensions($issues);

        // Check 6: Environment Variables
        $this->info('6. Checking Environment Variables...');
        $this->checkEnvironmentVariables($issues);

        // Summary
        $this->newLine();
        $this->info('📊 SUMMARY:');
        
        if (empty($issues)) {
            $this->info('✅ No configuration issues found.');
        } else {
            $this->error('❌ Found ' . count($issues) . ' issue(s):');
            foreach ($issues as $issue) {
                $this->error("   - {$issue}");
            }
        }

        return empty($issues) ? 0 : 1;
    }

    private function checkPhpConfiguration(&$issues): void
    {
        // Check PHP version
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            $issues[] = 'PHP version ' . PHP_VERSION . ' is below recommended 8.1.0';
            $this->error("   ❌ PHP version: " . PHP_VERSION . " (recommended: 8.1.0+)");
        } else {
            $this->info("   ✅ PHP version: " . PHP_VERSION);
        }

        // Check memory limit
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = $this->returnBytes($memoryLimit);
        if ($memoryLimitBytes < 128 * 1024 * 1024) { // 128MB
            $issues[] = 'Memory limit is too low: ' . $memoryLimit;
            $this->error("   ❌ Memory limit: {$memoryLimit} (recommended: 128M+)");
        } else {
            $this->info("   ✅ Memory limit: {$memoryLimit}");
        }

        // Check max execution time
        $maxExecutionTime = ini_get('max_execution_time');
        if ($maxExecutionTime > 0 && $maxExecutionTime < 30) {
            $issues[] = 'Max execution time is too low: ' . $maxExecutionTime;
            $this->error("   ❌ Max execution time: {$maxExecutionTime}s (recommended: 30s+)");
        } else {
            $this->info("   ✅ Max execution time: {$maxExecutionTime}s");
        }

        // Check upload max filesize
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $this->info("   ℹ️  Upload max filesize: {$uploadMaxFilesize}");
    }

    private function checkLaravelConfiguration(&$issues): void
    {
        // Check if .env file exists
        if (!File::exists(base_path('.env'))) {
            $issues[] = '.env file is missing';
            $this->error("   ❌ .env file: missing");
        } else {
            $this->info("   ✅ .env file: exists");
        }

        // Check APP_KEY
        $appKey = config('app.key');
        if (empty($appKey) || $appKey === 'base64:') {
            $issues[] = 'APP_KEY is not set';
            $this->error("   ❌ APP_KEY: not set");
        } else {
            $this->info("   ✅ APP_KEY: set (" . substr($appKey, 0, 10) . "...)");
        }

        // Check APP_ENV
        $appEnv = config('app.env');
        $this->info("   ℹ️  APP_ENV: {$appEnv}");

        // Check APP_DEBUG
        $appDebug = config('app.debug');
        if ($appDebug && $appEnv === 'production') {
            $issues[] = 'APP_DEBUG is enabled in production (security risk)';
            $this->error("   ❌ APP_DEBUG: enabled in production");
        } else {
            $this->info("   ✅ APP_DEBUG: " . ($appDebug ? 'enabled' : 'disabled'));
        }
    }

    private function checkDatabaseConfiguration(&$issues): void
    {
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->info("   ✅ Database: connected");
        } catch (\Exception $e) {
            $issues[] = 'Database connection failed: ' . $e->getMessage();
            $this->error("   ❌ Database: connection failed - " . $e->getMessage());
        }

        // Check database configuration
        $dbConfig = config('database.connections.mysql');
        if ($dbConfig) {
            $this->info("   ℹ️  Database host: " . ($dbConfig['host'] ?? 'not set'));
            $this->info("   ℹ️  Database name: " . ($dbConfig['database'] ?? 'not set'));
            $this->info("   ℹ️  Database username: " . ($dbConfig['username'] ? 'set' : 'not set'));
            $this->info("   ℹ️  Database password: " . ($dbConfig['password'] ? 'set' : 'not set'));
        }

        // Check required tables
        $requiredTables = ['users', 'roles', 'sessions', 'password_reset_tokens'];
        foreach ($requiredTables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    $this->info("   ✅ Table '{$table}': exists");
                } else {
                    $issues[] = "Table '{$table}' is missing";
                    $this->error("   ❌ Table '{$table}': missing");
                }
            } catch (\Exception $e) {
                $issues[] = "Cannot check table '{$table}': " . $e->getMessage();
                $this->error("   ❌ Table '{$table}': error - " . $e->getMessage());
            }
        }
    }

    private function checkFilePermissions(&$issues): void
    {
        $paths = [
            'storage' => storage_path(),
            'storage/logs' => storage_path('logs'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($paths as $name => $path) {
            if (!File::exists($path)) {
                $issues[] = "Directory '{$name}' does not exist";
                $this->error("   ❌ {$name}: directory does not exist");
            } elseif (!is_writable($path)) {
                $issues[] = "Directory '{$name}' is not writable";
                $this->error("   ❌ {$name}: not writable");
            } else {
                $this->info("   ✅ {$name}: writable");
            }
        }
    }

    private function checkRequiredExtensions(&$issues): void
    {
        $requiredExtensions = [
            'pdo',
            'pdo_mysql',
            'mbstring',
            'openssl',
            'json',
            'tokenizer',
            'xml',
            'ctype',
            'fileinfo',
            'bcmath'
        ];

        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $issues[] = "PHP extension '{$ext}' is not loaded";
                $this->error("   ❌ {$ext}: not loaded");
            } else {
                $this->info("   ✅ {$ext}: loaded");
            }
        }
    }

    private function checkEnvironmentVariables(&$issues): void
    {
        $criticalVars = [
            'APP_NAME' => 'Application name',
            'APP_ENV' => 'Application environment',
            'APP_KEY' => 'Application key',
            'APP_URL' => 'Application URL',
            'DB_CONNECTION' => 'Database connection',
            'DB_HOST' => 'Database host',
            'DB_DATABASE' => 'Database name',
            'DB_USERNAME' => 'Database username',
            'DB_PASSWORD' => 'Database password',
            'SESSION_DRIVER' => 'Session driver',
            'CACHE_STORE' => 'Cache store'
        ];

        foreach ($criticalVars as $var => $description) {
            $value = env($var);
            if ($value === null || $value === '') {
                $issues[] = "Environment variable '{$var}' is not set";
                $this->error("   ❌ {$var}: not set");
            } else {
                $displayValue = $var === 'APP_KEY' ? substr($value, 0, 10) . '...' : $value;
                $this->info("   ✅ {$var}: {$displayValue}");
            }
        }
    }

    private function returnBytes($val): int
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int) $val;
        
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        
        return $val;
    }
} 