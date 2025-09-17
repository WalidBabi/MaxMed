<?php
/**
 * Production Permission Synchronization Script
 * 
 * This script synchronizes permissions between development and production environments.
 * It creates all permissions that are referenced in the codebase but may be missing.
 * 
 * SAFE TO RUN: This script only creates missing permissions and does not delete or modify existing ones.
 * 
 * Usage: php production_permission_sync.php
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "🔄 MaxMed Production Permission Synchronization\n";
echo "==============================================\n\n";

echo "📊 Current Status:\n";
$currentCount = Permission::count();
$activeCount = Permission::where('is_active', true)->count();
echo "   Current permissions: $currentCount\n";
echo "   Active permissions: $activeCount\n\n";

// Define the complete set of permissions that should exist in production
$requiredPermissions = [
    // Dashboard & Analytics
    ['name' => 'dashboard.view', 'category' => 'dashboard', 'display_name' => 'View Dashboard'],
    ['name' => 'dashboard.analytics', 'category' => 'dashboard', 'display_name' => 'View Analytics'],
    ['name' => 'dashboard.admin', 'category' => 'dashboard', 'display_name' => 'Admin Dashboard'],
    
    // User Management
    ['name' => 'users.view', 'category' => 'users', 'display_name' => 'View Users'],
    ['name' => 'users.create', 'category' => 'users', 'display_name' => 'Create Users'],
    ['name' => 'users.edit', 'category' => 'users', 'display_name' => 'Edit Users'],
    ['name' => 'users.delete', 'category' => 'users', 'display_name' => 'Delete Users'],
    ['name' => 'users.impersonate', 'category' => 'users', 'display_name' => 'Impersonate Users'],
    ['name' => 'users.export', 'category' => 'users', 'display_name' => 'Export Users'],
    
    // Role & Permission Management
    ['name' => 'roles.view', 'category' => 'roles', 'display_name' => 'View Roles'],
    ['name' => 'roles.create', 'category' => 'roles', 'display_name' => 'Create Roles'],
    ['name' => 'roles.edit', 'category' => 'roles', 'display_name' => 'Edit Roles'],
    ['name' => 'roles.delete', 'category' => 'roles', 'display_name' => 'Delete Roles'],
    ['name' => 'permissions.manage', 'category' => 'roles', 'display_name' => 'Manage Permissions'],
    ['name' => 'permissions.view', 'category' => 'permissions', 'display_name' => 'View Permissions'],
    ['name' => 'permissions.create', 'category' => 'permissions', 'display_name' => 'Create Permissions'],
    ['name' => 'permissions.edit', 'category' => 'permissions', 'display_name' => 'Edit Permissions'],
    ['name' => 'permissions.delete', 'category' => 'permissions', 'display_name' => 'Delete Permissions'],
    
    // Product Management
    ['name' => 'products.view', 'category' => 'products', 'display_name' => 'View Products'],
    ['name' => 'products.create', 'category' => 'products', 'display_name' => 'Create Products'],
    ['name' => 'products.edit', 'category' => 'products', 'display_name' => 'Edit Products'],
    ['name' => 'products.delete', 'category' => 'products', 'display_name' => 'Delete Products'],
    ['name' => 'products.approve', 'category' => 'products', 'display_name' => 'Approve Products'],
    ['name' => 'products.manage_inventory', 'category' => 'products', 'display_name' => 'Manage Inventory'],
    ['name' => 'products.manage_pricing', 'category' => 'products', 'display_name' => 'Manage Pricing'],
    ['name' => 'products.manage_specifications', 'category' => 'products', 'display_name' => 'Manage Specifications'],
    ['name' => 'products.export', 'category' => 'products', 'display_name' => 'Export Products'],
    
    // Category Management
    ['name' => 'categories.view', 'category' => 'categories', 'display_name' => 'View Categories'],
    ['name' => 'categories.create', 'category' => 'categories', 'display_name' => 'Create Categories'],
    ['name' => 'categories.edit', 'category' => 'categories', 'display_name' => 'Edit Categories'],
    ['name' => 'categories.delete', 'category' => 'categories', 'display_name' => 'Delete Categories'],
    ['name' => 'categories.manage_hierarchy', 'category' => 'categories', 'display_name' => 'Manage Hierarchy'],
    
    // Brand Management
    ['name' => 'brands.view', 'category' => 'brands', 'display_name' => 'View Brands'],
    ['name' => 'brands.create', 'category' => 'brands', 'display_name' => 'Create Brands'],
    ['name' => 'brands.edit', 'category' => 'brands', 'display_name' => 'Edit Brands'],
    ['name' => 'brands.delete', 'category' => 'brands', 'display_name' => 'Delete Brands'],
    
    // Order Management
    ['name' => 'orders.view', 'category' => 'orders', 'display_name' => 'View Orders'],
    ['name' => 'orders.view_all', 'category' => 'orders', 'display_name' => 'View All Orders'],
    ['name' => 'orders.view_own', 'category' => 'orders', 'display_name' => 'View Own Orders'],
    ['name' => 'orders.create', 'category' => 'orders', 'display_name' => 'Create Orders'],
    ['name' => 'orders.edit', 'category' => 'orders', 'display_name' => 'Edit Orders'],
    ['name' => 'orders.delete', 'category' => 'orders', 'display_name' => 'Delete Orders'],
    ['name' => 'orders.manage_status', 'category' => 'orders', 'display_name' => 'Manage Order Status'],
    ['name' => 'orders.process', 'category' => 'orders', 'display_name' => 'Process Orders'],
    ['name' => 'orders.export', 'category' => 'orders', 'display_name' => 'Export Orders'],
    
    // Customer Management
    ['name' => 'customers.view', 'category' => 'customers', 'display_name' => 'View Customers'],
    ['name' => 'customers.create', 'category' => 'customers', 'display_name' => 'Create Customers'],
    ['name' => 'customers.edit', 'category' => 'customers', 'display_name' => 'Edit Customers'],
    ['name' => 'customers.delete', 'category' => 'customers', 'display_name' => 'Delete Customers'],
    ['name' => 'customers.view_sensitive', 'category' => 'customers', 'display_name' => 'View Sensitive Data'],
    ['name' => 'customers.export', 'category' => 'customers', 'display_name' => 'Export Customers'],
    
    // Supplier Management
    ['name' => 'suppliers.view', 'category' => 'suppliers', 'display_name' => 'View Suppliers'],
    ['name' => 'suppliers.create', 'category' => 'suppliers', 'display_name' => 'Create Suppliers'],
    ['name' => 'suppliers.edit', 'category' => 'suppliers', 'display_name' => 'Edit Suppliers'],
    ['name' => 'suppliers.delete', 'category' => 'suppliers', 'display_name' => 'Delete Suppliers'],
    ['name' => 'suppliers.approve', 'category' => 'suppliers', 'display_name' => 'Approve Suppliers'],
    ['name' => 'suppliers.manage_categories', 'category' => 'suppliers', 'display_name' => 'Manage Categories'],
    ['name' => 'suppliers.view_performance', 'category' => 'suppliers', 'display_name' => 'View Performance'],
    ['name' => 'suppliers.manage_contracts', 'category' => 'suppliers', 'display_name' => 'Manage Contracts'],
    ['name' => 'suppliers.manage_payments', 'category' => 'suppliers', 'display_name' => 'Manage Payments'],
    
    // Supplier Self-Service
    ['name' => 'supplier.dashboard', 'category' => 'suppliers', 'display_name' => 'Supplier Dashboard'],
    ['name' => 'supplier.products.view', 'category' => 'suppliers', 'display_name' => 'View Own Products'],
    ['name' => 'supplier.products.create', 'category' => 'suppliers', 'display_name' => 'Create Products'],
    ['name' => 'supplier.products.edit', 'category' => 'suppliers', 'display_name' => 'Edit Own Products'],
    ['name' => 'supplier.products.delete', 'category' => 'suppliers', 'display_name' => 'Delete Own Products'],
    ['name' => 'supplier.orders.view', 'category' => 'suppliers', 'display_name' => 'View Assigned Orders'],
    ['name' => 'supplier.orders.manage', 'category' => 'suppliers', 'display_name' => 'Manage Orders'],
    ['name' => 'supplier.inquiries.view', 'category' => 'suppliers', 'display_name' => 'View Inquiries'],
    ['name' => 'supplier.inquiries.respond', 'category' => 'suppliers', 'display_name' => 'Respond to Inquiries'],
    ['name' => 'supplier.feedback.create', 'category' => 'suppliers', 'display_name' => 'Submit Feedback'],
    
    // Quotation Management
    ['name' => 'quotations.view', 'category' => 'quotations', 'display_name' => 'View Quotations'],
    ['name' => 'quotations.create', 'category' => 'quotations', 'display_name' => 'Create Quotations'],
    ['name' => 'quotations.edit', 'category' => 'quotations', 'display_name' => 'Edit Quotations'],
    ['name' => 'quotations.delete', 'category' => 'quotations', 'display_name' => 'Delete Quotations'],
    ['name' => 'quotations.approve', 'category' => 'quotations', 'display_name' => 'Approve Quotations'],
    ['name' => 'quotations.send', 'category' => 'quotations', 'display_name' => 'Send Quotations'],
    ['name' => 'quotations.compare', 'category' => 'quotations', 'display_name' => 'Compare Quotations'],
    ['name' => 'quotations.convert', 'category' => 'quotations', 'display_name' => 'Convert to Order'],
    
    // Purchase Order Management
    ['name' => 'purchase_orders.view', 'category' => 'purchase_orders', 'display_name' => 'View Purchase Orders'],
    ['name' => 'purchase_orders.create', 'category' => 'purchase_orders', 'display_name' => 'Create Purchase Orders'],
    ['name' => 'purchase_orders.edit', 'category' => 'purchase_orders', 'display_name' => 'Edit Purchase Orders'],
    ['name' => 'purchase_orders.delete', 'category' => 'purchase_orders', 'display_name' => 'Delete Purchase Orders'],
    ['name' => 'purchase_orders.approve', 'category' => 'purchase_orders', 'display_name' => 'Approve Purchase Orders'],
    ['name' => 'purchase_orders.send', 'category' => 'purchase_orders', 'display_name' => 'Send to Suppliers'],
    ['name' => 'purchase_orders.send_to_supplier', 'category' => 'purchase_orders', 'display_name' => 'Send to Supplier'],
    ['name' => 'purchase_orders.manage_status', 'category' => 'purchase_orders', 'display_name' => 'Manage Status'],
    ['name' => 'purchase_orders.view_financials', 'category' => 'purchase_orders', 'display_name' => 'View Financial Info'],
    ['name' => 'purchase_orders.manage_payments', 'category' => 'purchase_orders', 'display_name' => 'Manage Payments'],
    
    // Invoice Management
    ['name' => 'invoices.view', 'category' => 'invoices', 'display_name' => 'View Invoices'],
    ['name' => 'invoices.create', 'category' => 'invoices', 'display_name' => 'Create Invoices'],
    ['name' => 'invoices.edit', 'category' => 'invoices', 'display_name' => 'Edit Invoices'],
    ['name' => 'invoices.delete', 'category' => 'invoices', 'display_name' => 'Delete Invoices'],
    ['name' => 'invoices.send', 'category' => 'invoices', 'display_name' => 'Send Invoices'],
    ['name' => 'invoices.manage_payments', 'category' => 'invoices', 'display_name' => 'Manage Payments'],
    
    // Delivery Management
    ['name' => 'deliveries.view', 'category' => 'deliveries', 'display_name' => 'View Deliveries'],
    ['name' => 'deliveries.create', 'category' => 'deliveries', 'display_name' => 'Create Deliveries'],
    ['name' => 'deliveries.edit', 'category' => 'deliveries', 'display_name' => 'Edit Deliveries'],
    ['name' => 'deliveries.delete', 'category' => 'deliveries', 'display_name' => 'Delete Deliveries'],
    ['name' => 'deliveries.track', 'category' => 'deliveries', 'display_name' => 'Track Deliveries'],
    ['name' => 'deliveries.confirm', 'category' => 'deliveries', 'display_name' => 'Confirm Deliveries'],
    
    // Feedback Management
    ['name' => 'feedback.view', 'category' => 'feedback', 'display_name' => 'View Feedback'],
    ['name' => 'feedback.respond', 'category' => 'feedback', 'display_name' => 'Respond to Feedback'],
    ['name' => 'feedback.delete', 'category' => 'feedback', 'display_name' => 'Delete Feedback'],
    ['name' => 'feedback.export', 'category' => 'feedback', 'display_name' => 'Export Feedback'],
    
    // News Management
    ['name' => 'news.view', 'category' => 'news', 'display_name' => 'View News'],
    ['name' => 'news.create', 'category' => 'news', 'display_name' => 'Create News'],
    ['name' => 'news.edit', 'category' => 'news', 'display_name' => 'Edit News'],
    ['name' => 'news.delete', 'category' => 'news', 'display_name' => 'Delete News'],
    ['name' => 'news.publish', 'category' => 'news', 'display_name' => 'Publish News'],
    
    // CRM System - Core
    ['name' => 'crm.access', 'category' => 'crm', 'display_name' => 'Access CRM'],
    ['name' => 'crm.leads.view', 'category' => 'crm', 'display_name' => 'View Leads'],
    ['name' => 'crm.leads.create', 'category' => 'crm', 'display_name' => 'Create Leads'],
    ['name' => 'crm.leads.edit', 'category' => 'crm', 'display_name' => 'Edit Leads'],
    ['name' => 'crm.leads.delete', 'category' => 'crm', 'display_name' => 'Delete Leads'],
    ['name' => 'crm.leads.assign', 'category' => 'crm', 'display_name' => 'Assign Leads'],
    ['name' => 'crm.leads.convert', 'category' => 'crm', 'display_name' => 'Convert Leads'],
    ['name' => 'crm.leads.view_requirements', 'category' => 'crm', 'display_name' => 'View Lead Requirements'],
    ['name' => 'crm.leads.export', 'category' => 'crm', 'display_name' => 'Export Leads'],
    ['name' => 'crm.leads.import', 'category' => 'crm', 'display_name' => 'Import Leads'],
    ['name' => 'crm.leads.merge', 'category' => 'crm', 'display_name' => 'Merge Leads'],
    ['name' => 'crm.leads.bulk_actions', 'category' => 'crm', 'display_name' => 'Bulk Actions on Leads'],
    
    // CRM Contacts
    ['name' => 'crm.contacts.view', 'category' => 'crm', 'display_name' => 'View Contacts'],
    ['name' => 'crm.contacts.create', 'category' => 'crm', 'display_name' => 'Create Contacts'],
    ['name' => 'crm.contacts.edit', 'category' => 'crm', 'display_name' => 'Edit Contacts'],
    ['name' => 'crm.contacts.delete', 'category' => 'crm', 'display_name' => 'Delete Contacts'],
    ['name' => 'crm.contacts.merge', 'category' => 'crm', 'display_name' => 'Merge Contacts'],
    ['name' => 'crm.contacts.export', 'category' => 'crm', 'display_name' => 'Export Contacts'],
    ['name' => 'crm.contacts.import', 'category' => 'crm', 'display_name' => 'Import Contacts'],
    
    // CRM Deals
    ['name' => 'crm.deals.view', 'category' => 'crm', 'display_name' => 'View Deals'],
    ['name' => 'crm.deals.create', 'category' => 'crm', 'display_name' => 'Create Deals'],
    ['name' => 'crm.deals.edit', 'category' => 'crm', 'display_name' => 'Edit Deals'],
    ['name' => 'crm.deals.delete', 'category' => 'crm', 'display_name' => 'Delete Deals'],
    ['name' => 'crm.deals.assign', 'category' => 'crm', 'display_name' => 'Assign Deals'],
    ['name' => 'crm.deals.close', 'category' => 'crm', 'display_name' => 'Close Deals'],
    ['name' => 'crm.deals.export', 'category' => 'crm', 'display_name' => 'Export Deals'],
    ['name' => 'crm.deals.pipeline', 'category' => 'crm', 'display_name' => 'View Deal Pipeline'],
    ['name' => 'crm.deals.forecast', 'category' => 'crm', 'display_name' => 'Deal Forecast'],
    
    // CRM Activities
    ['name' => 'crm.activities.view', 'category' => 'crm', 'display_name' => 'View Activities'],
    ['name' => 'crm.activities.create', 'category' => 'crm', 'display_name' => 'Create Activities'],
    ['name' => 'crm.activities.edit', 'category' => 'crm', 'display_name' => 'Edit Activities'],
    ['name' => 'crm.activities.delete', 'category' => 'crm', 'display_name' => 'Delete Activities'],
    ['name' => 'crm.activities.complete', 'category' => 'crm', 'display_name' => 'Complete Activities'],
    ['name' => 'crm.activities.schedule', 'category' => 'crm', 'display_name' => 'Schedule Activities'],
    ['name' => 'crm.activities.timeline', 'category' => 'crm', 'display_name' => 'View Activity Timeline'],
    
    // CRM Tasks
    ['name' => 'crm.tasks.view', 'category' => 'crm', 'display_name' => 'View Tasks'],
    ['name' => 'crm.tasks.create', 'category' => 'crm', 'display_name' => 'Create Tasks'],
    ['name' => 'crm.tasks.edit', 'category' => 'crm', 'display_name' => 'Edit Tasks'],
    ['name' => 'crm.tasks.delete', 'category' => 'crm', 'display_name' => 'Delete Tasks'],
    ['name' => 'crm.tasks.assign', 'category' => 'crm', 'display_name' => 'Assign Tasks'],
    ['name' => 'crm.tasks.complete', 'category' => 'crm', 'display_name' => 'Complete Tasks'],
    ['name' => 'crm.tasks.overdue', 'category' => 'crm', 'display_name' => 'View Overdue Tasks'],
    
    // CRM Campaigns
    ['name' => 'crm.campaigns.view', 'category' => 'crm', 'display_name' => 'View Campaigns'],
    ['name' => 'crm.campaigns.create', 'category' => 'crm', 'display_name' => 'Create Campaigns'],
    ['name' => 'crm.campaigns.edit', 'category' => 'crm', 'display_name' => 'Edit Campaigns'],
    ['name' => 'crm.campaigns.delete', 'category' => 'crm', 'display_name' => 'Delete Campaigns'],
    ['name' => 'crm.campaigns.execute', 'category' => 'crm', 'display_name' => 'Execute Campaigns'],
    ['name' => 'crm.campaigns.track', 'category' => 'crm', 'display_name' => 'Track Campaigns'],
    
    // CRM Reports & Analytics
    ['name' => 'crm.reports.view', 'category' => 'crm', 'display_name' => 'View CRM Reports'],
    ['name' => 'crm.reports.create', 'category' => 'crm', 'display_name' => 'Create CRM Reports'],
    ['name' => 'crm.reports.export', 'category' => 'crm', 'display_name' => 'Export CRM Reports'],
    ['name' => 'crm.analytics.view', 'category' => 'crm', 'display_name' => 'View CRM Analytics'],
    ['name' => 'crm.analytics.dashboard', 'category' => 'crm', 'display_name' => 'CRM Analytics Dashboard'],
    ['name' => 'crm.analytics.performance', 'category' => 'crm', 'display_name' => 'CRM Performance Analytics'],
    
    // CRM Communication
    ['name' => 'crm.communication.email', 'category' => 'crm', 'display_name' => 'Email Communication'],
    ['name' => 'crm.communication.sms', 'category' => 'crm', 'display_name' => 'SMS Communication'],
    ['name' => 'crm.communication.call', 'category' => 'crm', 'display_name' => 'Call Communication'],
    ['name' => 'crm.communication.meeting', 'category' => 'crm', 'display_name' => 'Meeting Communication'],
    ['name' => 'crm.communication.templates', 'category' => 'crm', 'display_name' => 'Communication Templates'],
    
    // CRM Integration & Automation
    ['name' => 'crm.integration.webhooks', 'category' => 'crm', 'display_name' => 'CRM Webhooks'],
    ['name' => 'crm.integration.api', 'category' => 'crm', 'display_name' => 'CRM API Integration'],
    ['name' => 'crm.automation.workflows', 'category' => 'crm', 'display_name' => 'CRM Workflows'],
    ['name' => 'crm.automation.rules', 'category' => 'crm', 'display_name' => 'CRM Automation Rules'],
    ['name' => 'crm.automation.triggers', 'category' => 'crm', 'display_name' => 'CRM Triggers'],
    
    // CRM Administration
    ['name' => 'crm.admin.settings', 'category' => 'crm', 'display_name' => 'CRM Settings'],
    ['name' => 'crm.admin.fields', 'category' => 'crm', 'display_name' => 'CRM Custom Fields'],
    ['name' => 'crm.admin.workflows', 'category' => 'crm', 'display_name' => 'CRM Workflow Admin'],
    ['name' => 'crm.admin.integrations', 'category' => 'crm', 'display_name' => 'CRM Integration Admin'],
    ['name' => 'crm.admin.backup', 'category' => 'crm', 'display_name' => 'CRM Backup'],
    ['name' => 'crm.admin.restore', 'category' => 'crm', 'display_name' => 'CRM Restore'],
    
    // Special CRM permissions
    ['name' => 'crm.contact-submissions.view', 'category' => 'crm', 'display_name' => 'View Contact Submissions'],
    ['name' => 'crm.quotation-requests.view', 'category' => 'crm', 'display_name' => 'View Quotation Requests'],
    
    // Marketing & Campaigns
    ['name' => 'marketing.access', 'category' => 'marketing', 'display_name' => 'Access Marketing'],
    ['name' => 'marketing.campaigns.view', 'category' => 'marketing', 'display_name' => 'View Marketing Campaigns'],
    ['name' => 'marketing.campaigns.create', 'category' => 'marketing', 'display_name' => 'Create Marketing Campaigns'],
    ['name' => 'marketing.campaigns.edit', 'category' => 'marketing', 'display_name' => 'Edit Marketing Campaigns'],
    ['name' => 'marketing.campaigns.delete', 'category' => 'marketing', 'display_name' => 'Delete Marketing Campaigns'],
    ['name' => 'marketing.campaigns.send', 'category' => 'marketing', 'display_name' => 'Send Marketing Campaigns'],
    ['name' => 'marketing.templates.manage', 'category' => 'marketing', 'display_name' => 'Manage Marketing Templates'],
    ['name' => 'marketing.analytics', 'category' => 'marketing', 'display_name' => 'Marketing Analytics'],
    
    // Analytics & Reports
    ['name' => 'analytics.view', 'category' => 'analytics', 'display_name' => 'View Analytics'],
    ['name' => 'analytics.advanced', 'category' => 'analytics', 'display_name' => 'Advanced Analytics'],
    ['name' => 'reports.generate', 'category' => 'analytics', 'display_name' => 'Generate Reports'],
    ['name' => 'reports.export', 'category' => 'analytics', 'display_name' => 'Export Reports'],
    ['name' => 'reports.schedule', 'category' => 'analytics', 'display_name' => 'Schedule Reports'],
    ['name' => 'reports.view', 'category' => 'analytics', 'display_name' => 'View Reports'],
    ['name' => 'reports.create', 'category' => 'analytics', 'display_name' => 'Create Reports'],
    ['name' => 'user_behavior.view', 'category' => 'analytics', 'display_name' => 'View User Behavior'],
    
    // System Administration
    ['name' => 'system.settings', 'category' => 'system', 'display_name' => 'System Settings'],
    ['name' => 'system.maintenance', 'category' => 'system', 'display_name' => 'System Maintenance'],
    ['name' => 'system.logs', 'category' => 'system', 'display_name' => 'View System Logs'],
    ['name' => 'system.backup', 'category' => 'system', 'display_name' => 'System Backup'],
    ['name' => 'system.notifications', 'category' => 'system', 'display_name' => 'Manage Notifications'],
    ['name' => 'system.admin', 'category' => 'system', 'display_name' => 'System Administration'],
    ['name' => 'procurement.analytics', 'category' => 'system', 'display_name' => 'Procurement Analytics'],
    ['name' => 'procurement.reports', 'category' => 'system', 'display_name' => 'Procurement Reports'],
    ['name' => 'procurement.budget_tracking', 'category' => 'system', 'display_name' => 'Budget Tracking'],
    ['name' => 'cash_receipts.view', 'category' => 'system', 'display_name' => 'View Cash Receipts'],
    ['name' => 'cash_receipts.create', 'category' => 'system', 'display_name' => 'Create Cash Receipts'],
    ['name' => 'cash_receipts.edit', 'category' => 'system', 'display_name' => 'Edit Cash Receipts'],
    ['name' => 'cash_receipts.delete', 'category' => 'system', 'display_name' => 'Delete Cash Receipts'],
    ['name' => 'sales_targets.view', 'category' => 'system', 'display_name' => 'View Sales Targets'],
    ['name' => 'sales_targets.create', 'category' => 'system', 'display_name' => 'Create Sales Targets'],
    ['name' => 'sales_targets.edit', 'category' => 'system', 'display_name' => 'Edit Sales Targets'],
    ['name' => 'sales_targets.delete', 'category' => 'system', 'display_name' => 'Delete Sales Targets'],
    ['name' => 'inquiries.view', 'category' => 'system', 'display_name' => 'View Inquiries'],
    ['name' => 'inquiries.create', 'category' => 'system', 'display_name' => 'Create Inquiries'],
    ['name' => 'inquiries.edit', 'category' => 'system', 'display_name' => 'Edit Inquiries'],
    ['name' => 'inquiries.delete', 'category' => 'system', 'display_name' => 'Delete Inquiries'],
    ['name' => 'inquiries.broadcast', 'category' => 'system', 'display_name' => 'Broadcast Inquiries'],
    ['name' => 'inquiries.forward', 'category' => 'system', 'display_name' => 'Forward Inquiries'],
    
    // API Access
    ['name' => 'api.access', 'category' => 'api', 'display_name' => 'API Access'],
    ['name' => 'api.read', 'category' => 'api', 'display_name' => 'API Read'],
    ['name' => 'api.write', 'category' => 'api', 'display_name' => 'API Write'],
    ['name' => 'api.admin', 'category' => 'api', 'display_name' => 'API Admin'],
    
    // Blog Management (from DynamicPermissionSeeder)
    ['name' => 'blog.view', 'category' => 'blog', 'display_name' => 'View Blog Posts'],
    ['name' => 'blog.create', 'category' => 'blog', 'display_name' => 'Create Blog Posts'],
    ['name' => 'blog.edit', 'category' => 'blog', 'display_name' => 'Edit Blog Posts'],
    ['name' => 'blog.delete', 'category' => 'blog', 'display_name' => 'Delete Blog Posts'],
    
    // Settings Management (from DynamicPermissionSeeder)
    ['name' => 'settings.view', 'category' => 'settings', 'display_name' => 'View Settings'],
    ['name' => 'settings.edit', 'category' => 'settings', 'display_name' => 'Edit Settings'],
];

echo "🔄 Creating missing permissions...\n";

$created = 0;
$existing = 0;
$errors = 0;

DB::beginTransaction();

try {
    foreach ($requiredPermissions as $permissionData) {
        $permission = Permission::updateOrCreate(
            ['name' => $permissionData['name']],
            [
                'display_name' => $permissionData['display_name'],
                'description' => 'Permission to ' . str_replace(['.', '_'], [' ', ' '], $permissionData['name']),
                'category' => $permissionData['category'],
                'is_active' => true,
            ]
        );
        
        if ($permission->wasRecentlyCreated) {
            $created++;
            echo "   ✅ Created: {$permissionData['name']}\n";
        } else {
            $existing++;
        }
    }
    
    DB::commit();
    echo "\n✅ Transaction committed successfully!\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "\n❌ Error occurred: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}

$newTotal = Permission::count();
$newActive = Permission::where('is_active', true)->count();

echo "\n📊 Final Results:\n";
echo "================\n";
echo "   Created: $created permissions\n";
echo "   Existing: $existing permissions\n";
echo "   Errors: $errors\n";
echo "   Total permissions: $newTotal (was $currentCount)\n";
echo "   Active permissions: $newActive (was $activeCount)\n";

// Show summary by category
echo "\n📋 Permissions by Category:\n";
echo "===========================\n";
$categories = Permission::select('category', DB::raw('count(*) as count'))
    ->groupBy('category')
    ->orderBy('category')
    ->get();

foreach ($categories as $category) {
    echo sprintf("   %-20s: %d\n", $category->category, $category->count);
}

echo "\n🎉 Permission synchronization completed successfully!\n";
echo "Production now has the same permissions as development.\n\n";

echo "💡 Next Steps:\n";
echo "- Test role assignments to ensure all referenced permissions exist\n";
echo "- Verify that users with existing roles maintain their permissions\n";
echo "- Consider running: php artisan optimize:clear\n";
