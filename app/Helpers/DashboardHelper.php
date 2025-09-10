<?php

namespace App\Helpers;

use App\Services\DashboardNamingService;

class DashboardHelper
{
    /**
     * Get dashboard name for admin
     */
    public static function adminDashboardName(): string
    {
        return DashboardNamingService::getDashboardName('admin');
    }

    /**
     * Get dashboard name for CRM
     */
    public static function crmDashboardName(): string
    {
        return DashboardNamingService::getDashboardName('crm');
    }

    /**
     * Get dashboard name for supplier
     */
    public static function supplierDashboardName(): string
    {
        return DashboardNamingService::getDashboardName('supplier');
    }

    /**
     * Get portal header name for sidebar display
     */
    public static function adminPortalHeaderName(): string
    {
        return DashboardNamingService::getPortalHeaderName('admin');
    }

    /**
     * Get CRM portal header name for sidebar display
     */
    public static function crmPortalHeaderName(): string
    {
        return DashboardNamingService::getPortalHeaderName('crm');
    }

    /**
     * Get supplier portal header name for sidebar display
     */
    public static function supplierPortalHeaderName(): string
    {
        return DashboardNamingService::getPortalHeaderName('supplier');
    }

    /**
     * Get all available dashboards for current user
     */
    public static function availableDashboards(): array
    {
        return DashboardNamingService::getAvailableDashboards();
    }
}
