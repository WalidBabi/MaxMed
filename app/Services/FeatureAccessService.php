<?php

namespace App\Services;

use App\Models\User;

class FeatureAccessService
{
    /**
     * Define feature access matrix for all roles
     * Each feature maps to the required permissions
     */
    public static function getFeaturePermissions(): array
    {
        return [
            // Dashboard & Analytics
            'dashboard.view' => ['dashboard.view'],
            'analytics.view' => ['analytics.view'],
            
            // User Management
            'users.index' => ['users.view'],
            'users.view' => ['users.view'],
            'users.create' => ['users.create'],
            'users.edit' => ['users.edit'],
            'users.delete' => ['users.delete'],
            
            // Role Management
            'roles.index' => ['roles.view'],
            'roles.view' => ['roles.view'],
            'roles.create' => ['roles.create'],
            'roles.edit' => ['roles.edit'],
            'roles.delete' => ['roles.delete'],
            
            // Product Management
            'products.index' => ['products.view'],
            'products.view' => ['products.view'],
            'products.create' => ['products.create'],
            'products.edit' => ['products.edit'],
            'products.delete' => ['products.delete'],
            'products.manage_inventory' => ['products.manage_inventory'],
            
            // Category Management
            'categories.index' => ['categories.view'],
            'categories.view' => ['categories.view'],
            'categories.create' => ['categories.create'],
            'categories.edit' => ['categories.edit'],
            'categories.delete' => ['categories.delete'],
            
            // Brand Management
            'brands.index' => ['brands.view'],
            'brands.view' => ['brands.view'],
            'brands.create' => ['brands.create'],
            'brands.edit' => ['brands.edit'],
            'brands.delete' => ['brands.delete'],
            
            // Order Management
            'orders.index' => ['orders.view_all'],
            'orders.create' => ['orders.create'],
            'orders.edit' => ['orders.edit'],
            'orders.delete' => ['orders.delete'],
            'orders.manage_status' => ['orders.manage_status'],
            
            // Customer Management
            'customers.index' => ['customers.view'],
            'customers.create' => ['customers.create'],
            'customers.edit' => ['customers.edit'],
            'customers.delete' => ['customers.delete'],
            
            // Supplier Management
            'suppliers.index' => ['suppliers.view'],
            'suppliers.view' => ['suppliers.view'],
            'suppliers.create' => ['suppliers.create'],
            'suppliers.edit' => ['suppliers.edit'],
            'suppliers.delete' => ['suppliers.delete'],
            'suppliers.performance' => ['suppliers.view_performance'],
            
            // Purchase Order Management
            'purchase_orders.index' => ['purchase_orders.view'],
            'purchase_orders.view' => ['purchase_orders.view'],
            'purchase_orders.create' => ['purchase_orders.create'],
            'purchase_orders.edit' => ['purchase_orders.edit'],
            'purchase_orders.delete' => ['purchase_orders.delete'],
            'purchase_orders.send' => ['purchase_orders.send'],
            'purchase_orders.manage_status' => ['purchase_orders.manage_status'],
            
            // Quotation Management
            'quotations.index' => ['quotations.view'],
            'quotations.view' => ['quotations.view'],
            'quotations.create' => ['quotations.create'],
            'quotations.edit' => ['quotations.edit'],
            'quotations.delete' => ['quotations.delete'],
            'quotations.compare' => ['quotations.compare'],
            
            // Invoice Management
            'invoices.index' => ['invoices.view'],
            'invoices.create' => ['invoices.create'],
            'invoices.edit' => ['invoices.edit'],
            'invoices.delete' => ['invoices.delete'],
            'invoices.send' => ['invoices.send'],
            
            // Delivery Management
            'deliveries.index' => ['deliveries.view'],
            'deliveries.view' => ['deliveries.view'],
            'deliveries.create' => ['deliveries.create'],
            'deliveries.edit' => ['deliveries.edit'],
            'deliveries.track' => ['deliveries.track'],
            
            // Cash Receipt Management
            'cash_receipts.index' => ['cash_receipts.view'],
            'cash_receipts.view' => ['cash_receipts.view'],
            'cash_receipts.create' => ['cash_receipts.create'],
            'cash_receipts.edit' => ['cash_receipts.edit'],
            'cash_receipts.delete' => ['cash_receipts.delete'],
            
            // Inquiry Management
            'inquiries.index' => ['inquiries.view'],
            'inquiries.view' => ['inquiries.view'],
            'inquiries.create' => ['inquiries.create'],
            'inquiries.edit' => ['inquiries.edit'],
            'inquiries.delete' => ['inquiries.delete'],
            'inquiries.broadcast' => ['inquiries.broadcast'],
            'inquiries.forward' => ['inquiries.forward'],
            
            // CRM System
            'crm.access' => ['crm.access'],
            'crm.leads.index' => ['crm.leads.view'],
            'crm.leads.view' => ['crm.leads.view'],
            'crm.leads.create' => ['crm.leads.create'],
            'crm.leads.edit' => ['crm.leads.edit'],
            'crm.leads.delete' => ['crm.leads.delete'],
            'crm.contacts.index' => ['crm.contacts.view'],
            'crm.contacts.view' => ['crm.contacts.view'],
            'crm.contacts.create' => ['crm.contacts.create'],
            'crm.contacts.edit' => ['crm.contacts.edit'],
            'crm.activities.index' => ['crm.activities.view'],
            'crm.activities.view' => ['crm.activities.view'],
            'crm.activities.create' => ['crm.activities.create'],
            'crm.contact-submissions.index' => ['crm.contact-submissions.view'],
            'crm.contact-submissions.view' => ['crm.contact-submissions.view'],
            'crm.quotation-requests.index' => ['crm.quotation-requests.view'],
            'crm.quotation-requests.view' => ['crm.quotation-requests.view'],
            
            // Feedback Management
            'feedback.index' => ['feedback.view'],
            'feedback.view' => ['feedback.view'],
            'feedback.create' => ['feedback.create'],
            'supplier.feedback.create' => ['supplier.feedback.create'],
            
            // Sales Targets
            'sales_targets.index' => ['sales_targets.view'],
            'sales_targets.view' => ['sales_targets.view'],
            'sales_targets.create' => ['sales_targets.create'],
            'sales_targets.edit' => ['sales_targets.edit'],
            
            // Content Management
            'news.index' => ['news.view'],
            'news.create' => ['news.create'],
            'news.edit' => ['news.edit'],
            'news.delete' => ['news.delete'],
            
            // User Behavior Analytics
            'user_behavior.index' => ['analytics.view'],
            'analytics.index' => ['analytics.view'],
        ];
    }
    
    /**
     * Check if user can access a specific feature
     */
    public static function canAccess(User $user, string $feature): bool
    {
        // Super admin has access to everything
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        $featurePermissions = self::getFeaturePermissions();
        
        if (!isset($featurePermissions[$feature])) {
            return false;
        }
        
        $requiredPermissions = $featurePermissions[$feature];
        
        foreach ($requiredPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get all accessible features for a user
     */
    public static function getAccessibleFeatures(User $user): array
    {
        $featurePermissions = self::getFeaturePermissions();
        
        // Super admin has access to all features
        if ($user->hasRole('super_admin')) {
            return array_keys($featurePermissions);
        }
        
        $features = [];
        
        foreach ($featurePermissions as $feature => $permissions) {
            if (self::canAccess($user, $feature)) {
                $features[] = $feature;
            }
        }
        
        return $features;
    }
    
    /**
     * Get feature categories for navigation organization
     */
    public static function getFeatureCategories(): array
    {
        return [
            'Dashboard & Analytics' => [
                'dashboard.view',
                'analytics.view',
            ],
            'User Management' => [
                'users.index',
                'users.create',
                'users.edit',
                'users.delete',
                'roles.index',
                'roles.create',
                'roles.edit',
                'roles.delete',
            ],
            'Product Management' => [
                'products.index',
                'products.create',
                'products.edit',
                'products.delete',
                'products.manage_inventory',
                'categories.index',
                'categories.create',
                'categories.edit',
                'categories.delete',
                'brands.index',
                'brands.create',
                'brands.edit',
                'brands.delete',
            ],
            'Sales & Orders' => [
                'orders.index',
                'orders.create',
                'orders.edit',
                'orders.delete',
                'orders.manage_status',
                'quotations.index',
                'quotations.create',
                'quotations.edit',
                'quotations.delete',
                'quotations.compare',
                'invoices.index',
                'invoices.create',
                'invoices.edit',
                'invoices.delete',
                'invoices.send',
                'cash_receipts.index',
                'cash_receipts.create',
                'cash_receipts.edit',
                'cash_receipts.delete',
            ],
            'Purchasing & Procurement' => [
                'purchase_orders.index',
                'purchase_orders.create',
                'purchase_orders.edit',
                'purchase_orders.delete',
                'purchase_orders.send',
                'purchase_orders.manage_status',
                'suppliers.index',
                'suppliers.create',
                'suppliers.edit',
                'suppliers.delete',
                'suppliers.performance',
                'deliveries.index',
                'deliveries.create',
                'deliveries.edit',
                'deliveries.track',
            ],
            'Customer Relations' => [
                'customers.index',
                'customers.create',
                'customers.edit',
                'customers.delete',
                'inquiries.index',
                'inquiries.create',
                'inquiries.edit',
                'inquiries.delete',
                'inquiries.broadcast',
                'inquiries.forward',
            ],
            'CRM System' => [
                'crm.access',
                'crm.leads.index',
                'crm.leads.create',
                'crm.leads.edit',
                'crm.leads.delete',
                'crm.contacts.index',
                'crm.contacts.create',
                'crm.contacts.edit',
                'crm.activities.index',
                'crm.activities.create',
                'crm.tasks.index',
                'crm.tasks.create',
            ],
            'Feedback & Support' => [
                'feedback.index',
                'feedback.create',
                'supplier.feedback.create',
            ],
            'Sales Management' => [
                'sales_targets.index',
                'sales_targets.create',
                'sales_targets.edit',
            ],
        ];
    }
}
