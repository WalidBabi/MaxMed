<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class TestRedisConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis connection and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Redis connection...');

        try {
            // Test basic Redis connection
            $this->info('1. Testing basic Redis connection...');
            Redis::set('test_key', 'Hello Redis from MaxMed!');
            $value = Redis::get('test_key');
            
            if ($value === 'Hello Redis from MaxMed!') {
                $this->info('✅ Basic Redis connection: SUCCESS');
            } else {
                $this->error('❌ Basic Redis connection: FAILED');
                return 1;
            }

            // Test Cache facade
            $this->info('2. Testing Cache facade...');
            Cache::put('cache_test', 'Cache working!', 60);
            $cacheValue = Cache::get('cache_test');
            
            if ($cacheValue === 'Cache working!') {
                $this->info('✅ Cache facade: SUCCESS');
            } else {
                $this->error('❌ Cache facade: FAILED');
                return 1;
            }

            // Test different Redis databases
            $this->info('3. Testing different Redis databases...');
            $defaultConnection = Redis::connection('default');
            $cacheConnection = Redis::connection('cache');
            
            $defaultConnection->set('db_test', 'default_db');
            $cacheConnection->set('db_test', 'cache_db');
            
            $defaultValue = $defaultConnection->get('db_test');
            $cacheValue = $cacheConnection->get('db_test');
            
            if ($defaultValue === 'default_db' && $cacheValue === 'cache_db') {
                $this->info('✅ Multiple Redis databases: SUCCESS');
            } else {
                $this->error('❌ Multiple Redis databases: FAILED');
            }

            // Test Redis info
            $this->info('4. Redis server information...');
            $info = Redis::info();
            $this->info("Redis version: " . $info['redis_version']);
            $this->info("Used memory: " . $info['used_memory_human']);
            $this->info("Connected clients: " . $info['connected_clients']);

            // Clean up test keys
            Redis::del('test_key');
            Redis::connection('default')->del('db_test');
            Redis::connection('cache')->del('db_test');
            Cache::forget('cache_test');

            $this->info('');
            $this->info('🎉 All Redis tests passed! Redis is properly configured.');
            
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Redis connection failed: ' . $e->getMessage());
            $this->info('');
            $this->info('Make sure:');
            $this->info('1. Redis server is running');
            $this->info('2. Redis configuration in .env is correct');
            $this->info('3. Predis package is installed');
            
            return 1;
        }
    }
} 