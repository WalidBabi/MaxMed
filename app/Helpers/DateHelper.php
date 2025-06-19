<?php

if (!function_exists('formatDubaiDate')) {
    /**
     * Format a date to Dubai timezone
     */
    function formatDubaiDate($date, $format = 'M d, Y H:i')
    {
        if (!$date) {
            return null;
        }
        
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->setTimezone('Asia/Dubai')->format($format);
    }
}

if (!function_exists('formatDubaiDateForHumans')) {
    /**
     * Format a date to Dubai timezone for humans
     */
    function formatDubaiDateForHumans($date)
    {
        if (!$date) {
            return null;
        }
        
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->setTimezone('Asia/Dubai')->diffForHumans();
    }
}

if (!function_exists('nowDubai')) {
    /**
     * Get current time in Dubai timezone
     */
    function nowDubai($format = null)
    {
        $now = \Carbon\Carbon::now('Asia/Dubai');
        
        return $format ? $now->format($format) : $now;
    }
} 