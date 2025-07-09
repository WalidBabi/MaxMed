<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Log\LogManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->make(LogManager::class)->extend('safe_single', function ($app, $config) {
            $config['driver'] = 'monolog';
            $config['handler'] = StreamHandler::class;
            
            // Try to use the file path, but fallback to stderr if there are permission issues
            try {
                $config['with'] = [
                    'stream' => $config['path'],
                ];
            } catch (\Exception $e) {
                $config['with'] = [
                    'stream' => 'php://stderr',
                ];
            }
            
            return new Logger('safe_single', [
                new StreamHandler(
                    $config['with']['stream'],
                    $config['level'] ?? 'debug',
                    $config['bubble'] ?? true,
                    $config['permission'] ?? null,
                    $config['locking'] ?? false
                )
            ]);
        });
    }
} 