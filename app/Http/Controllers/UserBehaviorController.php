<?php

namespace App\Http\Controllers;

use App\Models\UserBehavior;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class UserBehaviorController extends Controller
{
    /**
     * Track user behavior events
     */
    public function track(Request $request): JsonResponse
    {
        Log::info('UserBehaviorController@track - Incoming request', ['payload' => $request->all()]);
        try {
            // Validate the request
            $validated = $request->validate([
                'event_type' => 'required|string|in:page_view,click,scroll,mouse_move,form_interaction,time_on_page,exit_intent,error,cookie_consent,resize',
                'page_url' => 'required|string|max:2048',
                'referrer_url' => 'nullable|string|max:2048',
                'event_data' => 'nullable|array',
                'duration' => 'nullable|integer|min:0',
                'scroll_depth' => 'nullable|integer|min:0|max:100',
                'mouse_position' => 'nullable|array',
                'click_target' => 'nullable|array',
                'interaction_path' => 'nullable|array',
                'device_info' => 'nullable|array',
                'location_data' => 'nullable|array',
                'timestamp' => 'nullable|date',
            ]);

            // Check if user has given cookie consent
            if ($validated['event_type'] !== 'cookie_consent' && !$this->hasCookieConsent($request)) {
                Log::warning('Rejected event due to missing cookie consent', [
                    'event_type' => $validated['event_type'],
                    'cookies' => $request->cookies->all(),
                    'headers' => $request->headers->all(),
                    'session_id' => session()->getId(),
                ]);
                return response()->json(['message' => 'Cookie consent required'], 403);
            }

            // Create the user behavior record
            $userBehavior = UserBehavior::create([
                'event_type' => $validated['event_type'],
                'page_url' => $validated['page_url'],
                'referrer_url' => $validated['referrer_url'] ?? null,
                'event_data' => $validated['event_data'] ?? [],
                'duration' => $validated['duration'] ?? null,
                'scroll_depth' => $validated['scroll_depth'] ?? null,
                'mouse_position' => $validated['mouse_position'] ?? null,
                'click_target' => $validated['click_target'] ?? null,
                'interaction_path' => $validated['interaction_path'] ?? null,
                'device_info' => $validated['device_info'] ?? null,
                'location_data' => $validated['location_data'] ?? null,
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'timestamp' => $this->normalizeEventTimestamp($validated['timestamp'] ?? null),
            ]);
            
            // Store cookie consent in session as fallback
            if ($validated['event_type'] === 'cookie_consent') {
                session(['cookie_consent' => 'accepted']);
            }

            // Log the event for debugging
            Log::info('User behavior tracked', [
                'event_type' => $validated['event_type'],
                'page_url' => $validated['page_url'],
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event tracked successfully',
                'event_id' => $userBehavior->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('UserBehaviorController@track - Validation error', ['errors' => $ve->errors(), 'payload' => $request->all()]);
            throw $ve;
        } catch (\Exception $e) {
            Log::error('User behavior tracking error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track event',
            ], 500);
        }
    }

    /**
     * Track multiple events in batch
     */
    public function trackBatch(Request $request): JsonResponse
    {
        Log::info('UserBehaviorController@trackBatch - Incoming request', ['payload' => $request->all()]);
        try {
            // Validate the request
            $validated = $request->validate([
                'events' => 'required|array|max:100',
                'events.*.event_type' => 'required|string|in:page_view,click,scroll,mouse_move,form_interaction,time_on_page,exit_intent,error,cookie_consent,resize',
                'events.*.page_url' => 'required|string|max:2048',
                'events.*.referrer_url' => 'nullable|string|max:2048',
                'events.*.event_data' => 'nullable|array',
                'events.*.duration' => 'nullable|integer|min:0',
                'events.*.scroll_depth' => 'nullable|integer|min:0|max:100',
                'events.*.mouse_position' => 'nullable|array',
                'events.*.click_target' => 'nullable|array',
                'events.*.interaction_path' => 'nullable|array',
                'events.*.device_info' => 'nullable|array',
                'events.*.location_data' => 'nullable|array',
                'events.*.timestamp' => 'nullable|date',
            ]);

            // Check if user has given cookie consent
            if (!$this->hasCookieConsent($request)) {
                return response()->json(['message' => 'Cookie consent required'], 403);
            }

            $events = [];
            $sessionId = session()->getId();
            $userId = Auth::id();
            $userAgent = $request->userAgent();
            $ipAddress = $request->ip();
            $deviceInfo = $this->getDeviceInfo($request);

            foreach ($validated['events'] as $event) {
                $events[] = [
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'page_url' => $event['page_url'],
                    'referrer_url' => $event['referrer_url'] ?? $request->header('referer'),
                    'user_agent' => $userAgent,
                    'ip_address' => $ipAddress,
                    'event_type' => $event['event_type'],
                    'event_data' => json_encode($event['event_data'] ?? []),
                    'timestamp' => $this->normalizeEventTimestamp($event['timestamp'] ?? null),
                    'duration' => $event['duration'] ?? null,
                    'scroll_depth' => $event['scroll_depth'] ?? null,
                    'mouse_position' => json_encode($event['mouse_position'] ?? null),
                    'click_target' => json_encode($event['click_target'] ?? null),
                    'interaction_path' => json_encode($event['interaction_path'] ?? null),
                    'device_info' => json_encode($event['device_info'] ?? $deviceInfo),
                    'location_data' => json_encode($event['location_data'] ?? null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert all events at once
            UserBehavior::insert($events);

            Log::info('User behavior batch tracked', [
                'event_count' => count($events),
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Events tracked successfully',
                'event_count' => count($events),
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('UserBehaviorController@trackBatch - Validation error', ['errors' => $ve->errors(), 'payload' => $request->all()]);
            throw $ve;
        } catch (\Exception $e) {
            Log::error('User behavior batch tracking error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track events',
            ], 500);
        }
    }

    /**
     * Normalize incoming event timestamps to a MySQL-friendly datetime string.
     */
    private function normalizeEventTimestamp($value): string
    {
        // If missing or empty, use now()
        if (empty($value)) {
            return now()->toDateTimeString();
        }

        try {
            // Handle numeric epochs (seconds or milliseconds)
            if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                $intVal = (int) $value;
                // Milliseconds if 13+ digits
                if (strlen((string) $intVal) >= 13) {
                    return Carbon::createFromTimestampMs($intVal)->toDateTimeString();
                }
                // Seconds
                return Carbon::createFromTimestamp($intVal)->toDateTimeString();
            }

            // Accept ISO 8601 and other common formats from clients
            // Carbon::parse handles "2025-11-02T07:45:35.123Z" and returns in app timezone
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $e) {
            // Fallback to now() on parse failure to avoid DB errors
            Log::warning('UserBehaviorController@normalizeEventTimestamp - Failed to parse timestamp, using now()', [
                'original' => $value,
                'error' => $e->getMessage(),
            ]);
            return now()->toDateTimeString();
        }
    }

    /**
     * Get analytics data for admin dashboard
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $pageUrl = $request->get('page_url');

            $data = [
                'total_events' => UserBehavior::recent($days)->count(),
                'page_views' => UserBehavior::recent($days)->pageViews()->count(),
                'clicks' => UserBehavior::recent($days)->clicks()->count(),
                'form_interactions' => UserBehavior::recent($days)->formInteractions()->count(),
                'exit_intents' => UserBehavior::recent($days)->exitIntents()->count(),
                'average_time_on_page' => UserBehavior::getAverageTimeOnPage($pageUrl, $days),
                'most_clicked_elements' => UserBehavior::getMostClickedElements($days, 10),
                'scroll_depth_stats' => UserBehavior::getScrollDepthStats($pageUrl, $days),
                // Enhanced analytics:
                'event_type_breakdown' => UserBehavior::getEventTypeBreakdown($days),
                'device_stats' => UserBehavior::getDeviceStats($days),
                'browser_stats' => UserBehavior::getBrowserStats($days),
                'os_stats' => UserBehavior::getOSStats($days),
                'top_pages' => UserBehavior::getTopPages($days, 10),
                'top_referrers' => UserBehavior::getTopReferrers($days, 10),
                'recent_events' => UserBehavior::getRecentEvents($days, 50),
                'export_data' => UserBehavior::getExportData($days),
            ];

            if ($pageUrl) {
                $data['heatmap_data'] = UserBehavior::getHeatmapData($pageUrl, $days);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('UserBehaviorController@analytics - Error', ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get analytics data',
            ], 500);
        }
    }

    /**
     * Get user journey for a specific session
     */
    public function userJourney(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->get('session_id');
            
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID required',
                ], 400);
            }

            $journey = UserBehavior::getUserJourney($sessionId);

            return response()->json([
                'success' => true,
                'data' => $journey,
            ]);

        } catch (\Exception $e) {
            Log::error('User journey error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get user journey',
            ], 500);
        }
    }

    /**
     * Return all user behavior data for admin review
     */
    public function all(Request $request): JsonResponse
    {
        // TODO: Add admin auth middleware in routes
        $data = \App\Models\UserBehavior::orderByDesc('timestamp')->limit(1000)->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Check if user has given cookie consent
     */
    private function hasCookieConsent(Request $request): bool
    {
        // Method 1: Check Laravel's parsed cookies
        if ($request->cookies->get('cookie_consent') === 'accepted') {
            return true;
        }
        
        // Method 2: Check raw cookie header if Laravel parsing fails
        $rawCookieHeader = $request->headers->get('cookie');
        if ($rawCookieHeader && strpos($rawCookieHeader, 'cookie_consent=accepted') !== false) {
            return true;
        }
        
        // Method 3: Check session storage as fallback
        if (session('cookie_consent') === 'accepted') {
            return true;
        }
        
        return false;
    }

    /**
     * Get device information from request
     */
    private function getDeviceInfo(Request $request): array
    {
        $userAgent = $request->userAgent();
        
        return [
            'user_agent' => $userAgent,
            'screen_width' => $request->header('X-Screen-Width'),
            'screen_height' => $request->header('X-Screen-Height'),
            'viewport_width' => $request->header('X-Viewport-Width'),
            'viewport_height' => $request->header('X-Viewport-Height'),
            'device_type' => $this->detectDeviceType($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'os' => $this->detectOS($userAgent),
        ];
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Detect browser from user agent
     */
    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome/', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }

    /**
     * Detect OS from user agent
     */
    private function detectOS(string $userAgent): string
    {
        if (preg_match('/Windows/', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            return 'iOS';
        } else {
            return 'Other';
        }
    }
} 