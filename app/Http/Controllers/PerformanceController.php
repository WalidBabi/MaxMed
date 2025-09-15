<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
    /**
     * Get performance metrics
     */
    public function metrics()
    {
        $metrics = [
            'cache_hit_rate' => $this->getCacheHitRate(),
            'slow_queries' => $this->getSlowQueries(),
            'memory_usage' => $this->getMemoryUsage(),
            'response_times' => $this->getResponseTimes(),
            'database_connections' => $this->getDatabaseConnections(),
        ];

        return response()->json($metrics);
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate()
    {
        $hits = Cache::get('cache_hits', 0);
        $misses = Cache::get('cache_misses', 0);
        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Get slow queries from logs
     */
    private function getSlowQueries()
    {
        // This would typically read from a log file or database
        return [
            'count' => 0,
            'avg_time' => 0,
            'slowest_query' => null
        ];
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage()
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
    }

    /**
     * Get response times
     */
    private function getResponseTimes()
    {
        return [
            'avg' => Cache::get('avg_response_time', 0),
            'min' => Cache::get('min_response_time', 0),
            'max' => Cache::get('max_response_time', 0)
        ];
    }

    /**
     * Get database connection info
     */
    private function getDatabaseConnections()
    {
        try {
            $connections = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return [
                'active' => $connections[0]->Value ?? 0,
                'max' => DB::select('SHOW VARIABLES LIKE "max_connections"')[0]->Value ?? 0
            ];
        } catch (\Exception $e) {
            return ['active' => 0, 'max' => 0];
        }
    }

    /**
     * Clear performance cache
     */
    public function clearCache()
    {
        Cache::flush();
        
        return response()->json([
            'message' => 'Performance cache cleared successfully'
        ]);
    }

    /**
     * Get optimization recommendations
     */
    public function recommendations()
    {
        $recommendations = [];

        // Check cache hit rate
        $hitRate = $this->getCacheHitRate();
        if ($hitRate < 80) {
            $recommendations[] = [
                'type' => 'cache',
                'message' => 'Cache hit rate is low (' . $hitRate . '%). Consider increasing cache TTL or adding more caching.',
                'priority' => 'high'
            ];
        }

        // Check memory usage
        $memoryUsage = $this->getMemoryUsage();
        $memoryPercent = ($memoryUsage['current'] / $memoryUsage['peak']) * 100;
        if ($memoryPercent > 80) {
            $recommendations[] = [
                'type' => 'memory',
                'message' => 'High memory usage detected (' . round($memoryPercent, 2) . '%). Consider optimizing queries or increasing memory limit.',
                'priority' => 'medium'
            ];
        }

        return response()->json($recommendations);
    }
}
