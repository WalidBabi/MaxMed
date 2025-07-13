<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBehavior extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'page_url',
        'referrer_url',
        'user_agent',
        'ip_address',
        'event_type',
        'event_data',
        'timestamp',
        'duration',
        'scroll_depth',
        'mouse_position',
        'click_target',
        'interaction_path',
        'device_info',
        'location_data',
    ];

    protected $casts = [
        'event_data' => 'array',
        'timestamp' => 'datetime',
        'duration' => 'integer',
        'scroll_depth' => 'integer',
        'mouse_position' => 'array',
        'click_target' => 'array',
        'interaction_path' => 'array',
        'device_info' => 'array',
        'location_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }

    // Event type constants
    const EVENT_PAGE_VIEW = 'page_view';
    const EVENT_CLICK = 'click';
    const EVENT_SCROLL = 'scroll';
    const EVENT_MOUSE_MOVE = 'mouse_move';
    const EVENT_FORM_INTERACTION = 'form_interaction';
    const EVENT_TIME_ON_PAGE = 'time_on_page';
    const EVENT_EXIT_INTENT = 'exit_intent';
    const EVENT_ERROR = 'error';
    const EVENT_COOKIE_CONSENT = 'cookie_consent';

    // Get events by type
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    // Get events for a specific session
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Get events for a specific user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Get events within a time range
    public function scopeInTimeRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    // Get recent events
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('timestamp', '>=', now()->subDays($days));
    }

    // Get click events
    public function scopeClicks($query)
    {
        return $query->where('event_type', self::EVENT_CLICK);
    }

    // Get page views
    public function scopePageViews($query)
    {
        return $query->where('event_type', self::EVENT_PAGE_VIEW);
    }

    // Get form interactions
    public function scopeFormInteractions($query)
    {
        return $query->where('event_type', self::EVENT_FORM_INTERACTION);
    }

    // Get exit intent events
    public function scopeExitIntents($query)
    {
        return $query->where('event_type', self::EVENT_EXIT_INTENT);
    }

    // Calculate average time on page
    public static function getAverageTimeOnPage($pageUrl = null, $days = 30)
    {
        $query = self::where('event_type', self::EVENT_TIME_ON_PAGE)
                     ->where('timestamp', '>=', now()->subDays($days));
        
        if ($pageUrl) {
            $query->where('page_url', $pageUrl);
        }

        return $query->avg('duration') ?? 0;
    }

    // Get most clicked elements
    public static function getMostClickedElements($days = 30, $limit = 10)
    {
        return self::where('event_type', self::EVENT_CLICK)
            ->where('timestamp', '>=', now()->subDays($days))
            ->whereNotNull('click_target')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(click_target, "$..selector")) as selector, JSON_UNQUOTE(JSON_EXTRACT(click_target, "$..text")) as text, COUNT(*) as click_count')
            ->groupBy('selector', 'text')
            ->orderByDesc('click_count')
            ->limit($limit)
            ->get();
    }

    // Get scroll depth statistics
    public static function getScrollDepthStats($pageUrl = null, $days = 30)
    {
        $query = self::where('event_type', self::EVENT_SCROLL)
                     ->where('timestamp', '>=', now()->subDays($days));
        
        if ($pageUrl) {
            $query->where('page_url', $pageUrl);
        }

        return [
            'average_depth' => $query->avg('scroll_depth') ?? 0,
            'max_depth' => $query->max('scroll_depth') ?? 0,
            'min_depth' => $query->min('scroll_depth') ?? 0,
        ];
    }

    // Get user journey for a session
    public static function getUserJourney($sessionId)
    {
        return self::where('session_id', $sessionId)
                   ->orderBy('timestamp')
                   ->get()
                   ->groupBy('event_type');
    }

    // Get heatmap data for a page
    public static function getHeatmapData($pageUrl, $days = 30)
    {
        return self::where('page_url', $pageUrl)
                   ->where('event_type', self::EVENT_CLICK)
                   ->where('timestamp', '>=', now()->subDays($days))
                   ->whereNotNull('mouse_position')
                   ->get()
                   ->map(function ($event) {
                       return [
                           'x' => $event->mouse_position['x'] ?? 0,
                           'y' => $event->mouse_position['y'] ?? 0,
                           'timestamp' => $event->timestamp,
                       ];
                   });
    }

    // Get breakdown of all event types in the last X days
    public static function getEventTypeBreakdown($days = 30)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->select('event_type', \DB::raw('COUNT(*) as count'))
            ->groupBy('event_type')
            ->pluck('count', 'event_type');
    }

    // Get device type stats
    public static function getDeviceStats($days = 30)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(device_info, "$..device_type")) as device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type');
    }

    // Get browser stats
    public static function getBrowserStats($days = 30)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(device_info, "$..browser")) as browser, COUNT(*) as count')
            ->groupBy('browser')
            ->pluck('count', 'browser');
    }

    // Get OS stats
    public static function getOSStats($days = 30)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(device_info, "$..os")) as os, COUNT(*) as count')
            ->groupBy('os')
            ->pluck('count', 'os');
    }

    // Get top visited pages
    public static function getTopPages($days = 30, $limit = 10)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->select('page_url', \DB::raw('COUNT(*) as count'))
            ->groupBy('page_url')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    // Get top referrers
    public static function getTopReferrers($days = 30, $limit = 10)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->whereNotNull('referrer_url')
            ->select('referrer_url', \DB::raw('COUNT(*) as count'))
            ->groupBy('referrer_url')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    // Get recent events (all details)
    public static function getRecentEvents($days = 30, $limit = 50)
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->orderByDesc('timestamp')
            ->limit($limit)
            ->get();
    }

    // Get all data for export
    public static function getExportData($days = 30)
    {
        return self::where('timestamp', '>=', now()->subDays($days))->get();
    }
} 