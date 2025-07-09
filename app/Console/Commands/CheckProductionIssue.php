<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\Role;

class CheckProductionIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-production-issue {--fix : Attempt to fix common issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking production environment for potential issues...');
        
        $issues = [];
        
        // Check 1: Database Connection
        $this->info('1. Checking database connection...');
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            $issues[] = 'Database connection failed';
        }
        
        // Check 2: Required Tables
        $this->info('2. Checking required database tables...');
        $requiredTables = ['users', 'roles', 'sessions', 'password_reset_tokens', 'failed_jobs'];
        foreach ($requiredTables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✅ Table '{$table}' exists");
            } else {
                $this->error("❌ Table '{$table}' missing");
                $issues[] = "Missing table: {$table}";
            }
        }
        
        // Check 3: Sessions Table
        $this->info('3. Checking sessions table...');
        if (Schema::hasTable('sessions')) {
            try {
                $sessionCount = DB::table('sessions')->count();
                $this->info("✅ Sessions table accessible ({$sessionCount} sessions)");
            } catch (\Exception $e) {
                $this->error('❌ Sessions table error: ' . $e->getMessage());
                $issues[] = 'Sessions table error';
            }
        }
        
        // Check 4: Roles and Permissions
        $this->info('4. Checking roles and permissions...');
        try {
            $roles = Role::all();
            $this->info("✅ Found {$roles->count()} roles");
            
            foreach ($roles as $role) {
                $this->info("   - {$role->name}: " . count($role->permissions ?? []) . " permissions");
            }
        } catch (\Exception $e) {
            $this->error('❌ Roles check failed: ' . $e->getMessage());
            $issues[] = 'Roles check failed';
        }
        
        // Check 5: Admin Users
        $this->info('5. Checking admin users...');
        try {
            $adminUsers = User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();
            
            if ($adminUsers->count() > 0) {
                $this->info("✅ Found {$adminUsers->count()} admin users");
                foreach ($adminUsers as $admin) {
                    $this->info("   - {$admin->email} (ID: {$admin->id})");
                }
            } else {
                $this->warn('⚠️ No admin users found');
                $issues[] = 'No admin users';
            }
        } catch (\Exception $e) {
            $this->error('❌ Admin users check failed: ' . $e->getMessage());
            $issues[] = 'Admin users check failed';
        }
        
        // Check 6: Environment Variables
        $this->info('6. Checking critical environment variables...');
        $criticalVars = [
            'APP_KEY' => 'Application encryption key',
            'APP_ENV' => 'Application environment',
            'APP_DEBUG' => 'Debug mode',
            'DB_CONNECTION' => 'Database connection',
            'DB_HOST' => 'Database host',
            'DB_DATABASE' => 'Database name',
            'DB_USERNAME' => 'Database username',
            'SESSION_DRIVER' => 'Session driver',
            'CACHE_STORE' => 'Cache store'
        ];
        
        foreach ($criticalVars as $var => $description) {
            $value = env($var);
            if ($value !== null && $value !== '') {
                $this->info("✅ {$description}: {$var} = " . ($var === 'APP_KEY' ? substr($value, 0, 10) . '...' : $value));
            } else {
                $this->error("❌ {$description}: {$var} not set");
                $issues[] = "Missing env var: {$var}";
            }
        }
        
        // Check 7: Cache Configuration
        $this->info('7. Checking cache configuration...');
        try {
            Cache::put('test_key', 'test_value', 60);
            $value = Cache::get('test_key');
            if ($value === 'test_value') {
                $this->info('✅ Cache is working');
                Cache::forget('test_key');
            } else {
                $this->error('❌ Cache test failed');
                $issues[] = 'Cache not working';
            }
        } catch (\Exception $e) {
            $this->error('❌ Cache error: ' . $e->getMessage());
            $issues[] = 'Cache error';
        }
        
        // Check 8: Storage Permissions
        $this->info('8. Checking storage permissions...');
        $storagePaths = [
            storage_path('logs') => 'Logs directory',
            storage_path('framework/cache') => 'Cache directory',
            storage_path('framework/sessions') => 'Sessions directory',
            storage_path('framework/views') => 'Views directory'
        ];
        
        foreach ($storagePaths as $path => $description) {
            if (is_dir($path) && is_writable($path)) {
                $this->info("✅ {$description} is writable");
            } else {
                $this->error("❌ {$description} is not writable");
                $issues[] = "Storage not writable: {$description}";
            }
        }
        
        // Check 9: Production-specific checks
        if (app()->environment('production')) {
            $this->info('9. Checking production-specific configurations...');
            
            // Check reCAPTCHA configuration
            $recaptchaSiteKey = config('services.recaptcha.site_key');
            $recaptchaSecretKey = config('services.recaptcha.secret_key');
            
            if ($recaptchaSiteKey && $recaptchaSecretKey) {
                $this->info('✅ reCAPTCHA configured');
            } else {
                $this->warn('⚠️ reCAPTCHA not configured (may cause validation errors)');
                $issues[] = 'reCAPTCHA not configured';
            }
            
            // Check APP_DEBUG setting
            if (config('app.debug')) {
                $this->warn('⚠️ APP_DEBUG is enabled in production (security risk)');
                $issues[] = 'APP_DEBUG enabled in production';
            } else {
                $this->info('✅ APP_DEBUG is disabled in production');
            }
        }
        
        // Check 10: Logging Configuration
        $this->info('10. Checking logging configuration...');
        try {
            $logChannel = config('logging.default');
            $logLevel = config('logging.channels.' . $logChannel . '.level', env('LOG_LEVEL', 'debug'));
            $logPath = config('logging.channels.' . $logChannel . '.path', storage_path('logs/laravel.log'));
            
            $this->info("Log Channel: {$logChannel}");
            $this->info("Log Level: {$logLevel}");
            $this->info("Log Path: {$logPath}");
            
            // Check if log directory exists and is writable
            $logDir = dirname($logPath);
            if (!is_dir($logDir)) {
                $this->error("❌ Log directory does not exist: {$logDir}");
                $issues[] = 'Log directory missing';
            } elseif (!is_writable($logDir)) {
                $this->error("❌ Log directory is not writable: {$logDir}");
                $issues[] = 'Log directory not writable';
            } else {
                $this->info("✅ Log directory is writable: {$logDir}");
            }
            
            // Check if log file exists and is writable
            if (file_exists($logPath)) {
                if (is_writable($logPath)) {
                    $fileSize = filesize($logPath);
                    $this->info("✅ Log file is writable: {$logPath} (Size: " . number_format($fileSize) . " bytes)");
                } else {
                    $this->error("❌ Log file is not writable: {$logPath}");
                    $issues[] = 'Log file not writable';
                }
            } else {
                $this->info("ℹ️ Log file does not exist yet: {$logPath}");
            }
            
            // Test logging
            $this->info('Testing logging...');
            $testMessage = 'Production diagnostic test log entry - ' . now()->toISOString();
            
            Log::emergency($testMessage . ' [EMERGENCY]');
            Log::alert($testMessage . ' [ALERT]');
            Log::critical($testMessage . ' [CRITICAL]');
            Log::error($testMessage . ' [ERROR]');
            Log::warning($testMessage . ' [WARNING]');
            Log::notice($testMessage . ' [NOTICE]');
            Log::info($testMessage . ' [INFO]');
            Log::debug($testMessage . ' [DEBUG]');
            
            // Check if the test log was written
            if (file_exists($logPath)) {
                $logContent = file_get_contents($logPath);
                if (strpos($logContent, $testMessage) !== false) {
                    $this->info('✅ Logging test successful - entries written to log file');
                } else {
                    $this->error('❌ Logging test failed - entries not found in log file');
                    $issues[] = 'Logging test failed';
                }
            } else {
                $this->error('❌ Logging test failed - log file not created');
                $issues[] = 'Log file not created';
            }
            
            // Check environment variables affecting logging
            $logEnvVars = [
                'LOG_CHANNEL' => env('LOG_CHANNEL'),
                'LOG_LEVEL' => env('LOG_LEVEL'),
                'LOG_STACK' => env('LOG_STACK'),
                'LOG_DAILY_DAYS' => env('LOG_DAILY_DAYS')
            ];
            
            $this->info('Environment variables:');
            foreach ($logEnvVars as $var => $value) {
                $this->info("  {$var} = " . ($value ?? 'not set'));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Logging check failed: ' . $e->getMessage());
            $issues[] = 'Logging check failed';
        }
        
        // Summary
        $this->newLine();
        $this->info('📊 SUMMARY:');
        
        if (empty($issues)) {
            $this->info('✅ No critical issues found. The application should work correctly.');
        } else {
            $this->error('❌ Found ' . count($issues) . ' issue(s):');
            foreach ($issues as $issue) {
                $this->error("   - {$issue}");
            }
            
            if ($this->option('fix')) {
                $this->newLine();
                $this->info('🔧 Attempting to fix common issues...');
                $this->attemptFixes($issues);
            } else {
                $this->newLine();
                $this->info('💡 Run with --fix option to attempt automatic fixes');
            }
        }
        
        return empty($issues) ? 0 : 1;
    }
    
    /**
     * Attempt to fix common issues
     */
    private function attemptFixes(array $issues): void
    {
        foreach ($issues as $issue) {
            if (str_contains($issue, 'Missing table')) {
                $this->info('Running migrations...');
                try {
                    $this->call('migrate', ['--force' => true]);
                    $this->info('✅ Migrations completed');
                } catch (\Exception $e) {
                    $this->error('❌ Migration failed: ' . $e->getMessage());
                }
            }
            
            if (str_contains($issue, 'APP_DEBUG enabled in production')) {
                $this->warn('Please set APP_DEBUG=false in your .env file');
            }
            
            if (str_contains($issue, 'No admin users')) {
                $this->info('Creating default admin user...');
                try {
                    $adminRole = Role::where('name', 'admin')->first();
                    if (!$adminRole) {
                        $adminRole = Role::create([
                            'name' => 'admin',
                            'display_name' => 'Administrator',
                            'permissions' => ['dashboard.view'],
                            'is_active' => true
                        ]);
                    }
                    
                    $admin = User::create([
                        'name' => 'Admin',
                        'email' => 'admin@maxmedme.com',
                        'password' => bcrypt('admin123'),
                        'role_id' => $adminRole->id,
                        'email_verified_at' => now()
                    ]);
                    
                    $this->info('✅ Default admin created: admin@maxmedme.com / admin123');
                } catch (\Exception $e) {
                    $this->error('❌ Admin creation failed: ' . $e->getMessage());
                }
            }
        }
    }
}
