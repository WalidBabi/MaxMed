<?php

use App\Services\FeatureAccessService;
use Illuminate\Support\Facades\Auth;

if (!function_exists('canAccessFeature')) {
    /**
     * Check if the current authenticated user can access a feature
     */
    function canAccessFeature(string $feature): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return FeatureAccessService::canAccess(Auth::user(), $feature);
    }
}

if (!function_exists('getUserAccessibleFeatures')) {
    /**
     * Get all features accessible by the current user
     */
    function getUserAccessibleFeatures(): array
    {
        if (!Auth::check()) {
            return [];
        }
        
        return FeatureAccessService::getAccessibleFeatures(Auth::user());
    }
}

if (!function_exists('getFeatureCategories')) {
    /**
     * Get feature categories for navigation
     */
    function getFeatureCategories(): array
    {
        return FeatureAccessService::getFeatureCategories();
    }
}
