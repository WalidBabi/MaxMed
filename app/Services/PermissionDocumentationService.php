<?php

namespace App\Services;

class PermissionDocumentationService
{
    /**
     * Get comprehensive documentation for all permissions
     */
    public static function getAllPermissionDocumentation(): array
    {
        return [
            // Dashboard & Analytics
            'dashboard.view' => [
                'title' => 'View Dashboard',
                'description' => 'Access the main dashboard interface',
                'impact' => 'Allows users to view the primary dashboard with system overview, statistics, and quick access to key features.',
                'modules' => ['Dashboard', 'Analytics', 'Overview'],
                'related_permissions' => ['dashboard.analytics', 'dashboard.admin'],
                'security_level' => 'Low',
                'business_impact' => 'Essential for basic system navigation and overview.'
            ],
            'dashboard.analytics' => [
                'title' => 'View Analytics',
                'description' => 'Access analytics and reporting features',
                'impact' => 'Enables viewing of system analytics, performance metrics, and business intelligence reports.',
                'modules' => ['Analytics', 'Reports', 'Dashboard'],
                'related_permissions' => ['dashboard.view', 'analytics.view', 'reports.generate'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for data-driven decision making and performance monitoring.'
            ],
            'dashboard.admin' => [
                'title' => 'Admin Dashboard',
                'description' => 'Access admin-specific dashboard features',
                'impact' => 'Provides access to administrative dashboard with system health, user activity, and administrative tools.',
                'modules' => ['Admin Dashboard', 'System Monitoring', 'User Management'],
                'related_permissions' => ['dashboard.view', 'system.settings', 'users.view'],
                'security_level' => 'High',
                'business_impact' => 'Essential for system administrators to monitor and manage the platform.'
            ],

            // User Management
            'users.view' => [
                'title' => 'View Users',
                'description' => 'View user profiles and user lists',
                'impact' => 'Allows browsing and viewing user information, profiles, and user directory.',
                'modules' => ['User Management', 'Profiles', 'Directory'],
                'related_permissions' => ['users.create', 'users.edit', 'users.delete'],
                'security_level' => 'Medium',
                'business_impact' => 'Necessary for user administration and team management.'
            ],
            'users.create' => [
                'title' => 'Create Users',
                'description' => 'Create new user accounts',
                'impact' => 'Enables creation of new user accounts with role assignment and profile setup.',
                'modules' => ['User Management', 'Account Creation', 'Onboarding'],
                'related_permissions' => ['users.view', 'users.edit', 'roles.view'],
                'security_level' => 'High',
                'business_impact' => 'Critical for user onboarding and team expansion.'
            ],
            'users.edit' => [
                'title' => 'Edit Users',
                'description' => 'Modify user profiles and settings',
                'impact' => 'Allows modification of user information, profile details, and account settings.',
                'modules' => ['User Management', 'Profile Management', 'Settings'],
                'related_permissions' => ['users.view', 'users.create', 'roles.edit'],
                'security_level' => 'High',
                'business_impact' => 'Essential for user account maintenance and profile updates.'
            ],
            'users.delete' => [
                'title' => 'Delete Users',
                'description' => 'Remove user accounts',
                'impact' => 'Enables permanent deletion of user accounts and associated data.',
                'modules' => ['User Management', 'Account Deletion', 'Data Management'],
                'related_permissions' => ['users.view', 'users.edit'],
                'security_level' => 'Critical',
                'business_impact' => 'High risk - permanent data loss. Use with extreme caution.'
            ],
            'users.impersonate' => [
                'title' => 'Impersonate Users',
                'description' => 'Login as other users for support purposes',
                'impact' => 'Allows administrators to temporarily assume another user\'s identity for troubleshooting.',
                'modules' => ['User Management', 'Support', 'Troubleshooting'],
                'related_permissions' => ['users.view', 'users.edit'],
                'security_level' => 'Critical',
                'business_impact' => 'Powerful support tool but requires strict oversight and logging.'
            ],
            'users.export' => [
                'title' => 'Export Users',
                'description' => 'Export user data to external formats',
                'impact' => 'Enables exporting user information, profiles, and data to CSV, Excel, or other formats.',
                'modules' => ['User Management', 'Data Export', 'Reporting'],
                'related_permissions' => ['users.view', 'reports.export'],
                'security_level' => 'High',
                'business_impact' => 'Important for data analysis, compliance, and external integrations.'
            ],

            // Role & Permission Management
            'roles.view' => [
                'title' => 'View Roles',
                'description' => 'View roles and permission assignments',
                'impact' => 'Allows viewing of role definitions, permission assignments, and role hierarchies.',
                'modules' => ['Role Management', 'Permission System', 'Access Control'],
                'related_permissions' => ['roles.create', 'roles.edit', 'permissions.manage'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for understanding and auditing access control.'
            ],
            'roles.create' => [
                'title' => 'Create Roles',
                'description' => 'Create new roles and permission sets',
                'impact' => 'Enables creation of new roles with custom permission combinations.',
                'modules' => ['Role Management', 'Permission System', 'Access Control'],
                'related_permissions' => ['roles.view', 'roles.edit', 'permissions.manage'],
                'security_level' => 'High',
                'business_impact' => 'Critical for implementing custom access control policies.'
            ],
            'roles.edit' => [
                'title' => 'Edit Roles',
                'description' => 'Modify roles and permission assignments',
                'impact' => 'Allows modification of existing roles, permission assignments, and role properties.',
                'modules' => ['Role Management', 'Permission System', 'Access Control'],
                'related_permissions' => ['roles.view', 'roles.create', 'permissions.manage'],
                'security_level' => 'High',
                'business_impact' => 'Essential for maintaining and updating access control policies.'
            ],
            'roles.delete' => [
                'title' => 'Delete Roles',
                'description' => 'Remove roles from the system',
                'impact' => 'Enables permanent deletion of roles and their permission assignments.',
                'modules' => ['Role Management', 'Permission System', 'Access Control'],
                'related_permissions' => ['roles.view', 'roles.edit'],
                'security_level' => 'Critical',
                'business_impact' => 'High risk - may affect user access. Ensure no users are assigned to role.'
            ],
            'permissions.manage' => [
                'title' => 'Manage Permissions',
                'description' => 'Create and modify system permissions',
                'impact' => 'Allows creation, modification, and management of system permissions and their definitions.',
                'modules' => ['Permission System', 'Access Control', 'System Configuration'],
                'related_permissions' => ['roles.view', 'roles.create', 'roles.edit'],
                'security_level' => 'Critical',
                'business_impact' => 'Highest level access - affects entire permission system architecture.'
            ],

            // Product Management
            'products.view' => [
                'title' => 'View Products',
                'description' => 'View product catalog and product information',
                'impact' => 'Allows browsing and viewing of product catalog, specifications, and product details.',
                'modules' => ['Product Catalog', 'Inventory', 'Specifications'],
                'related_permissions' => ['products.create', 'products.edit', 'categories.view'],
                'security_level' => 'Low',
                'business_impact' => 'Essential for product browsing and customer service.'
            ],
            'products.create' => [
                'title' => 'Create Products',
                'description' => 'Add new products to the catalog',
                'impact' => 'Enables addition of new products with specifications, pricing, and inventory data.',
                'modules' => ['Product Catalog', 'Inventory Management', 'Pricing'],
                'related_permissions' => ['products.view', 'products.edit', 'categories.view'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for catalog expansion and new product introduction.'
            ],
            'products.edit' => [
                'title' => 'Edit Products',
                'description' => 'Modify product details and specifications',
                'impact' => 'Allows modification of product information, specifications, pricing, and inventory data.',
                'modules' => ['Product Catalog', 'Inventory Management', 'Pricing'],
                'related_permissions' => ['products.view', 'products.create', 'products.manage_pricing'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for product maintenance and updates.'
            ],
            'products.delete' => [
                'title' => 'Delete Products',
                'description' => 'Remove products from the catalog',
                'impact' => 'Enables permanent removal of products from the catalog and system.',
                'modules' => ['Product Catalog', 'Inventory Management'],
                'related_permissions' => ['products.view', 'products.edit'],
                'security_level' => 'High',
                'business_impact' => 'High risk - may affect existing orders and quotations.'
            ],
            'products.approve' => [
                'title' => 'Approve Products',
                'description' => 'Approve product submissions for publication',
                'impact' => 'Controls the approval workflow for new or modified products before they go live.',
                'modules' => ['Product Catalog', 'Approval Workflow', 'Quality Control'],
                'related_permissions' => ['products.view', 'products.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for maintaining product quality and catalog standards.'
            ],
            'products.manage_inventory' => [
                'title' => 'Manage Inventory',
                'description' => 'Update stock levels and inventory data',
                'impact' => 'Allows modification of stock levels, inventory tracking, and stock management.',
                'modules' => ['Inventory Management', 'Stock Control', 'Warehouse'],
                'related_permissions' => ['products.view', 'products.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for accurate inventory tracking and order fulfillment.'
            ],
            'products.manage_pricing' => [
                'title' => 'Manage Pricing',
                'description' => 'Set and modify product prices',
                'impact' => 'Enables setting and modification of product pricing, discounts, and pricing strategies.',
                'modules' => ['Pricing Management', 'Product Catalog', 'Sales'],
                'related_permissions' => ['products.view', 'products.edit'],
                'security_level' => 'High',
                'business_impact' => 'Critical for revenue management and competitive positioning.'
            ],
            'products.manage_specifications' => [
                'title' => 'Manage Specifications',
                'description' => 'Manage technical specifications and product details',
                'impact' => 'Allows management of technical specifications, product attributes, and detailed information.',
                'modules' => ['Product Specifications', 'Technical Data', 'Product Catalog'],
                'related_permissions' => ['products.view', 'products.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for technical accuracy and customer information.'
            ],
            'products.export' => [
                'title' => 'Export Products',
                'description' => 'Export product data to external formats',
                'impact' => 'Enables exporting product information, specifications, and data to various formats.',
                'modules' => ['Product Catalog', 'Data Export', 'Reporting'],
                'related_permissions' => ['products.view', 'reports.export'],
                'security_level' => 'Low',
                'business_impact' => 'Useful for data analysis, integrations, and external catalogs.'
            ],

            // CRM System
            'crm.access' => [
                'title' => 'Access CRM',
                'description' => 'Access the CRM system and its features',
                'impact' => 'Provides basic access to the Customer Relationship Management system.',
                'modules' => ['CRM Dashboard', 'Lead Management', 'Contact Management'],
                'related_permissions' => ['crm.leads.view', 'crm.contacts.view', 'crm.activities.view'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for customer relationship management and sales processes.'
            ],
            'crm.leads.view' => [
                'title' => 'View Leads',
                'description' => 'View CRM leads and lead information',
                'impact' => 'Allows viewing of lead records, contact information, and lead status.',
                'modules' => ['Lead Management', 'CRM Dashboard', 'Sales Pipeline'],
                'related_permissions' => ['crm.access', 'crm.leads.create', 'crm.leads.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for sales team visibility and lead tracking.'
            ],
            'crm.leads.create' => [
                'title' => 'Create Leads',
                'description' => 'Add new leads to the CRM system',
                'impact' => 'Enables creation of new lead records with contact information and initial data.',
                'modules' => ['Lead Management', 'Lead Capture', 'Sales Pipeline'],
                'related_permissions' => ['crm.access', 'crm.leads.view', 'crm.leads.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for lead capture and sales pipeline growth.'
            ],
            'crm.leads.edit' => [
                'title' => 'Edit Leads',
                'description' => 'Modify lead information and status',
                'impact' => 'Allows modification of lead data, status updates, and progress tracking.',
                'modules' => ['Lead Management', 'Sales Pipeline', 'Lead Qualification'],
                'related_permissions' => ['crm.access', 'crm.leads.view', 'crm.leads.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for lead nurturing and sales process management.'
            ],
            'crm.leads.delete' => [
                'title' => 'Delete Leads',
                'description' => 'Remove leads from the CRM system',
                'impact' => 'Enables permanent deletion of lead records and associated data.',
                'modules' => ['Lead Management', 'Data Management'],
                'related_permissions' => ['crm.access', 'crm.leads.view', 'crm.leads.edit'],
                'security_level' => 'High',
                'business_impact' => 'High risk - permanent data loss. Use with caution.'
            ],
            'crm.leads.assign' => [
                'title' => 'Assign Leads',
                'description' => 'Assign leads to sales representatives',
                'impact' => 'Allows assignment of leads to specific sales team members for follow-up.',
                'modules' => ['Lead Management', 'Sales Team', 'Workflow Management'],
                'related_permissions' => ['crm.access', 'crm.leads.view', 'crm.leads.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for sales team coordination and lead distribution.'
            ],
            'crm.leads.convert' => [
                'title' => 'Convert Leads',
                'description' => 'Convert leads to customers or opportunities',
                'impact' => 'Enables conversion of qualified leads into customers or sales opportunities.',
                'modules' => ['Lead Management', 'Sales Pipeline', 'Customer Conversion'],
                'related_permissions' => ['crm.access', 'crm.leads.view', 'crm.leads.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for sales conversion and revenue generation.'
            ],
            'crm.contacts.view' => [
                'title' => 'View Contacts',
                'description' => 'View CRM contacts and contact information',
                'impact' => 'Allows viewing of contact records, communication history, and contact details.',
                'modules' => ['Contact Management', 'CRM Dashboard', 'Communication'],
                'related_permissions' => ['crm.access', 'crm.contacts.create', 'crm.contacts.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for customer relationship management and communication.'
            ],
            'crm.contacts.create' => [
                'title' => 'Create Contacts',
                'description' => 'Add new contacts to the CRM system',
                'impact' => 'Enables creation of new contact records with personal and business information.',
                'modules' => ['Contact Management', 'Contact Capture', 'CRM Database'],
                'related_permissions' => ['crm.access', 'crm.contacts.view', 'crm.contacts.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for expanding customer database and contact management.'
            ],
            'crm.contacts.edit' => [
                'title' => 'Edit Contacts',
                'description' => 'Modify contact information and details',
                'impact' => 'Allows modification of contact data, communication preferences, and contact history.',
                'modules' => ['Contact Management', 'CRM Database', 'Communication'],
                'related_permissions' => ['crm.access', 'crm.contacts.view', 'crm.contacts.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for maintaining accurate contact information.'
            ],
            'crm.contacts.delete' => [
                'title' => 'Delete Contacts',
                'description' => 'Remove contacts from the CRM system',
                'impact' => 'Enables permanent deletion of contact records and associated data.',
                'modules' => ['Contact Management', 'Data Management'],
                'related_permissions' => ['crm.access', 'crm.contacts.view', 'crm.contacts.edit'],
                'security_level' => 'High',
                'business_impact' => 'High risk - permanent data loss. Use with caution.'
            ],
            'crm.activities.view' => [
                'title' => 'View Activities',
                'description' => 'View CRM activities and interactions',
                'impact' => 'Allows viewing of activity logs, interactions, and communication history.',
                'modules' => ['Activity Management', 'Communication History', 'CRM Dashboard'],
                'related_permissions' => ['crm.access', 'crm.activities.create', 'crm.activities.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for tracking customer interactions and relationship history.'
            ],
            'crm.activities.create' => [
                'title' => 'Create Activities',
                'description' => 'Log new activities and interactions',
                'impact' => 'Enables logging of new activities, calls, meetings, and customer interactions.',
                'modules' => ['Activity Management', 'Communication Logging', 'CRM Database'],
                'related_permissions' => ['crm.access', 'crm.activities.view', 'crm.activities.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for maintaining comprehensive interaction records.'
            ],
            'crm.activities.edit' => [
                'title' => 'Edit Activities',
                'description' => 'Modify activity records and details',
                'impact' => 'Allows modification of activity records, notes, and interaction details.',
                'modules' => ['Activity Management', 'Communication History', 'CRM Database'],
                'related_permissions' => ['crm.access', 'crm.activities.view', 'crm.activities.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for maintaining accurate activity records.'
            ],
            'crm.tasks.view' => [
                'title' => 'View Tasks',
                'description' => 'View CRM tasks and task assignments',
                'impact' => 'Allows viewing of task lists, assignments, and task status.',
                'modules' => ['Task Management', 'CRM Dashboard', 'Workflow'],
                'related_permissions' => ['crm.access', 'crm.tasks.create', 'crm.tasks.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for task tracking and workflow management.'
            ],
            'crm.tasks.create' => [
                'title' => 'Create Tasks',
                'description' => 'Create new tasks and assignments',
                'impact' => 'Enables creation of new tasks with assignments, deadlines, and priorities.',
                'modules' => ['Task Management', 'Workflow', 'Project Management'],
                'related_permissions' => ['crm.access', 'crm.tasks.view', 'crm.tasks.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for task management and team coordination.'
            ],
            'crm.tasks.edit' => [
                'title' => 'Edit Tasks',
                'description' => 'Modify tasks and task details',
                'impact' => 'Allows modification of task details, status updates, and assignments.',
                'modules' => ['Task Management', 'Workflow', 'Project Management'],
                'related_permissions' => ['crm.access', 'crm.tasks.view', 'crm.tasks.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for task management and progress tracking.'
            ],
            'crm.tasks.assign' => [
                'title' => 'Assign Tasks',
                'description' => 'Assign tasks to team members',
                'impact' => 'Enables assignment of tasks to specific team members with deadlines and priorities.',
                'modules' => ['Task Management', 'Team Coordination', 'Workflow'],
                'related_permissions' => ['crm.access', 'crm.tasks.view', 'crm.tasks.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for team coordination and task distribution.'
            ],

            // Order Management
            'orders.view' => [
                'title' => 'View Orders',
                'description' => 'View order information and order details',
                'impact' => 'Allows viewing of order information, status, and order history.',
                'modules' => ['Order Management', 'Sales', 'Customer Service'],
                'related_permissions' => ['orders.create', 'orders.edit', 'customers.view'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for order tracking and customer service.'
            ],
            'orders.view_all' => [
                'title' => 'View All Orders',
                'description' => 'View all system orders across all users',
                'impact' => 'Provides access to view all orders in the system, regardless of ownership.',
                'modules' => ['Order Management', 'Sales Management', 'Analytics'],
                'related_permissions' => ['orders.view', 'orders.manage_status', 'analytics.view'],
                'security_level' => 'High',
                'business_impact' => 'Critical for management oversight and system-wide order visibility.'
            ],
            'orders.view_own' => [
                'title' => 'View Own Orders',
                'description' => 'View only orders created by the user',
                'impact' => 'Restricts order viewing to only orders created or assigned to the current user.',
                'modules' => ['Order Management', 'Personal Sales', 'Customer Service'],
                'related_permissions' => ['orders.view', 'orders.create', 'orders.edit'],
                'security_level' => 'Low',
                'business_impact' => 'Standard permission for sales representatives and customer service.'
            ],
            'orders.create' => [
                'title' => 'Create Orders',
                'description' => 'Place new orders in the system',
                'impact' => 'Enables creation of new orders with products, quantities, and customer information.',
                'modules' => ['Order Management', 'Sales', 'Customer Service'],
                'related_permissions' => ['orders.view', 'orders.edit', 'customers.view'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for order processing and sales operations.'
            ],
            'orders.edit' => [
                'title' => 'Edit Orders',
                'description' => 'Modify order details and information',
                'impact' => 'Allows modification of order details, quantities, pricing, and customer information.',
                'modules' => ['Order Management', 'Sales', 'Customer Service'],
                'related_permissions' => ['orders.view', 'orders.create', 'orders.manage_status'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for order modifications and customer service.'
            ],
            'orders.delete' => [
                'title' => 'Delete Orders',
                'description' => 'Cancel or remove orders from the system',
                'impact' => 'Enables cancellation or deletion of orders, with appropriate business logic.',
                'modules' => ['Order Management', 'Sales', 'Customer Service'],
                'related_permissions' => ['orders.view', 'orders.edit'],
                'security_level' => 'High',
                'business_impact' => 'High risk - affects revenue and customer satisfaction.'
            ],
            'orders.manage_status' => [
                'title' => 'Manage Order Status',
                'description' => 'Update order statuses and workflow',
                'impact' => 'Allows updating of order statuses, workflow progression, and fulfillment tracking.',
                'modules' => ['Order Management', 'Workflow', 'Fulfillment'],
                'related_permissions' => ['orders.view', 'orders.edit', 'orders.process'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for order fulfillment and customer communication.'
            ],
            'orders.process' => [
                'title' => 'Process Orders',
                'description' => 'Process and fulfill orders',
                'impact' => 'Enables order processing, fulfillment, and completion workflows.',
                'modules' => ['Order Management', 'Fulfillment', 'Warehouse'],
                'related_permissions' => ['orders.view', 'orders.manage_status', 'deliveries.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for order fulfillment and customer satisfaction.'
            ],
            'orders.export' => [
                'title' => 'Export Orders',
                'description' => 'Export order data to external formats',
                'impact' => 'Enables exporting of order information, reports, and data to various formats.',
                'modules' => ['Order Management', 'Data Export', 'Reporting'],
                'related_permissions' => ['orders.view', 'reports.export'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for reporting, analysis, and external integrations.'
            ],

            // Customer Management
            'customers.view' => [
                'title' => 'View Customers',
                'description' => 'View customer information and profiles',
                'impact' => 'Allows viewing of customer profiles, contact information, and customer history.',
                'modules' => ['Customer Management', 'CRM', 'Customer Service'],
                'related_permissions' => ['customers.create', 'customers.edit', 'orders.view'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for customer service and relationship management.'
            ],
            'customers.create' => [
                'title' => 'Create Customers',
                'description' => 'Add new customers to the system',
                'impact' => 'Enables creation of new customer records with profile information and preferences.',
                'modules' => ['Customer Management', 'Customer Onboarding', 'CRM'],
                'related_permissions' => ['customers.view', 'customers.edit'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for customer acquisition and database growth.'
            ],
            'customers.edit' => [
                'title' => 'Edit Customers',
                'description' => 'Modify customer information and profiles',
                'impact' => 'Allows modification of customer data, preferences, and profile information.',
                'modules' => ['Customer Management', 'Profile Management', 'CRM'],
                'related_permissions' => ['customers.view', 'customers.create'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for maintaining accurate customer information.'
            ],
            'customers.delete' => [
                'title' => 'Delete Customers',
                'description' => 'Remove customer records from the system',
                'impact' => 'Enables permanent deletion of customer records and associated data.',
                'modules' => ['Customer Management', 'Data Management'],
                'related_permissions' => ['customers.view', 'customers.edit'],
                'security_level' => 'High',
                'business_impact' => 'High risk - permanent data loss. Use with caution.'
            ],
            'customers.view_sensitive' => [
                'title' => 'View Sensitive Data',
                'description' => 'Access sensitive customer information',
                'impact' => 'Provides access to sensitive customer data such as financial information, personal details.',
                'modules' => ['Customer Management', 'Sensitive Data', 'Compliance'],
                'related_permissions' => ['customers.view', 'customers.edit'],
                'security_level' => 'Critical',
                'business_impact' => 'High risk - requires strict access controls and compliance monitoring.'
            ],
            'customers.export' => [
                'title' => 'Export Customers',
                'description' => 'Export customer data to external formats',
                'impact' => 'Enables exporting of customer information, reports, and data to various formats.',
                'modules' => ['Customer Management', 'Data Export', 'Reporting'],
                'related_permissions' => ['customers.view', 'reports.export'],
                'security_level' => 'High',
                'business_impact' => 'Important for reporting and analysis, but requires data protection compliance.'
            ],

            // System Administration
            'system.settings' => [
                'title' => 'System Settings',
                'description' => 'Manage system configuration and settings',
                'impact' => 'Allows modification of system-wide settings, configurations, and preferences.',
                'modules' => ['System Configuration', 'Settings', 'Administration'],
                'related_permissions' => ['system.maintenance', 'system.logs'],
                'security_level' => 'Critical',
                'business_impact' => 'Highest level access - affects entire system behavior.'
            ],
            'system.maintenance' => [
                'title' => 'System Maintenance',
                'description' => 'Perform system maintenance operations',
                'impact' => 'Enables system maintenance tasks, updates, and administrative operations.',
                'modules' => ['System Maintenance', 'Updates', 'Administration'],
                'related_permissions' => ['system.settings', 'system.logs', 'system.backup'],
                'security_level' => 'Critical',
                'business_impact' => 'Critical for system health and performance.'
            ],
            'system.logs' => [
                'title' => 'View System Logs',
                'description' => 'Access system logs and audit trails',
                'impact' => 'Provides access to system logs, audit trails, and debugging information.',
                'modules' => ['System Logs', 'Audit Trail', 'Debugging'],
                'related_permissions' => ['system.settings', 'system.maintenance'],
                'security_level' => 'High',
                'business_impact' => 'Important for troubleshooting, security monitoring, and compliance.'
            ],
            'system.backup' => [
                'title' => 'System Backup',
                'description' => 'Create and restore system backups',
                'impact' => 'Enables creation, management, and restoration of system backups.',
                'modules' => ['Backup Management', 'Data Protection', 'Disaster Recovery'],
                'related_permissions' => ['system.settings', 'system.maintenance'],
                'security_level' => 'Critical',
                'business_impact' => 'Critical for data protection and business continuity.'
            ],
            'system.notifications' => [
                'title' => 'Manage Notifications',
                'description' => 'Configure system notifications and alerts',
                'impact' => 'Allows configuration of system notifications, alerts, and communication settings.',
                'modules' => ['Notification System', 'Alerts', 'Communication'],
                'related_permissions' => ['system.settings'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for system communication and user engagement.'
            ],

            // API Access
            'api.access' => [
                'title' => 'API Access',
                'description' => 'Access API endpoints and services',
                'impact' => 'Provides basic access to system APIs and external integrations.',
                'modules' => ['API Gateway', 'External Integrations', 'System APIs'],
                'related_permissions' => ['api.read', 'api.write'],
                'security_level' => 'High',
                'business_impact' => 'Essential for system integrations and external connectivity.'
            ],
            'api.read' => [
                'title' => 'API Read',
                'description' => 'Read data via API endpoints',
                'impact' => 'Enables read-only access to system data through API endpoints.',
                'modules' => ['API Gateway', 'Data Access', 'External Integrations'],
                'related_permissions' => ['api.access'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for data sharing and external system integration.'
            ],
            'api.write' => [
                'title' => 'API Write',
                'description' => 'Write data via API endpoints',
                'impact' => 'Enables write access to system data through API endpoints.',
                'modules' => ['API Gateway', 'Data Modification', 'External Integrations'],
                'related_permissions' => ['api.access', 'api.read'],
                'security_level' => 'High',
                'business_impact' => 'High risk - allows external systems to modify data.'
            ],
            'api.admin' => [
                'title' => 'API Admin',
                'description' => 'Full API administrative access',
                'impact' => 'Provides full administrative access to API management and configuration.',
                'modules' => ['API Gateway', 'API Management', 'System Administration'],
                'related_permissions' => ['api.access', 'api.read', 'api.write'],
                'security_level' => 'Critical',
                'business_impact' => 'Highest level API access - affects all API operations.'
            ],

            // Analytics & Reports
            'analytics.view' => [
                'title' => 'View Analytics',
                'description' => 'Access analytics dashboard and reports',
                'impact' => 'Provides access to analytics dashboards, metrics, and business intelligence.',
                'modules' => ['Analytics Dashboard', 'Business Intelligence', 'Metrics'],
                'related_permissions' => ['reports.generate', 'reports.export'],
                'security_level' => 'Medium',
                'business_impact' => 'Critical for data-driven decision making and performance monitoring.'
            ],
            'analytics.advanced' => [
                'title' => 'Advanced Analytics',
                'description' => 'Access detailed analytics and advanced features',
                'impact' => 'Provides access to advanced analytics features, detailed reports, and complex queries.',
                'modules' => ['Advanced Analytics', 'Complex Reports', 'Data Mining'],
                'related_permissions' => ['analytics.view', 'reports.generate'],
                'security_level' => 'High',
                'business_impact' => 'Important for advanced business analysis and strategic planning.'
            ],
            'reports.generate' => [
                'title' => 'Generate Reports',
                'description' => 'Create custom reports and analytics',
                'impact' => 'Enables creation of custom reports, analytics, and business intelligence outputs.',
                'modules' => ['Report Generation', 'Custom Analytics', 'Business Intelligence'],
                'related_permissions' => ['analytics.view', 'reports.export'],
                'security_level' => 'Medium',
                'business_impact' => 'Essential for business reporting and analysis.'
            ],
            'reports.export' => [
                'title' => 'Export Reports',
                'description' => 'Export report data to external formats',
                'impact' => 'Enables exporting of reports, analytics, and data to various external formats.',
                'modules' => ['Report Export', 'Data Export', 'External Sharing'],
                'related_permissions' => ['reports.generate', 'analytics.view'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for sharing data and external analysis.'
            ],
            'reports.schedule' => [
                'title' => 'Schedule Reports',
                'description' => 'Schedule automated reports and notifications',
                'impact' => 'Allows scheduling of automated reports, notifications, and regular data exports.',
                'modules' => ['Report Scheduling', 'Automation', 'Notifications'],
                'related_permissions' => ['reports.generate', 'system.notifications'],
                'security_level' => 'Medium',
                'business_impact' => 'Important for automated reporting and regular data distribution.'
            ],
        ];
    }

    /**
     * Get documentation for a specific permission
     */
    public static function getPermissionDocumentation(string $permissionName): ?array
    {
        $documentation = self::getAllPermissionDocumentation();
        return $documentation[$permissionName] ?? null;
    }

    /**
     * Get security level color for UI display
     */
    public static function getSecurityLevelColor(string $level): string
    {
        return match($level) {
            'Basic' => 'text-green-600',
            'Standard' => 'text-blue-600',
            'High' => 'text-yellow-600',
            'Critical' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get security level icon for UI display
     */
    public static function getSecurityLevelIcon(string $level): string
    {
        return match($level) {
            'Low' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'Medium' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
            'High' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Critical' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        };
    }

    /**
     * Get security level background color class
     */
    public static function getSecurityLevelBgColor(string $level): string
    {
        return match($level) {
            'Basic' => 'bg-green-50',
            'Standard' => 'bg-blue-50',
            'High' => 'bg-yellow-50', 
            'Critical' => 'bg-red-50',
            default => 'bg-gray-50'
        };
    }
} 