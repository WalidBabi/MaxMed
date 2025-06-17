-- Complete Foreign Key Fix for MaxMed Database
-- This script creates tables in the correct order to avoid foreign key constraint errors

-- =========================================================================
-- STEP 1: Create quotation_requests table WITHOUT foreign key constraints
-- =========================================================================

-- First, drop the table if it exists (this will also drop any foreign keys referencing it)
DROP TABLE IF EXISTS `supplier_quotations`;
DROP TABLE IF EXISTS `quotation_requests`;

-- Create quotation_requests table without foreign keys first
CREATE TABLE `quotation_requests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `size` varchar(191) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `delivery_timeline` varchar(191) DEFAULT NULL,
  `status` enum('pending','forwarded','supplier_responded','quote_created','completed','cancelled') NOT NULL DEFAULT 'pending',
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `forwarded_at` timestamp NULL DEFAULT NULL,
  `supplier_responded_at` timestamp NULL DEFAULT NULL,
  `lead_id` bigint(20) UNSIGNED DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `supplier_response` enum('pending','available','not_available') NOT NULL DEFAULT 'pending',
  `supplier_notes` text DEFAULT NULL,
  `generated_quote_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- STEP 2: Create indexes for quotation_requests
-- =========================================================================
ALTER TABLE `quotation_requests`
  ADD KEY `quotation_requests_product_id_index` (`product_id`),
  ADD KEY `quotation_requests_user_id_index` (`user_id`),
  ADD KEY `quotation_requests_supplier_id_index` (`supplier_id`),
  ADD KEY `quotation_requests_lead_id_index` (`lead_id`),
  ADD KEY `quotation_requests_generated_quote_id_index` (`generated_quote_id`),
  ADD KEY `quotation_requests_status_created_at_index` (`status`, `created_at`),
  ADD KEY `quotation_requests_supplier_id_status_index` (`supplier_id`, `status`);

-- =========================================================================
-- STEP 3: Verify required tables exist before adding foreign keys
-- =========================================================================
SELECT 'Checking required tables...' as status;

-- Check tables
SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'maxmed' AND TABLE_NAME = 'products')
        THEN 'products table EXISTS'
        ELSE 'ERROR: products table MISSING - Create products table first'
    END as products_status;

SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'maxmed' AND TABLE_NAME = 'users')
        THEN 'users table EXISTS'
        ELSE 'ERROR: users table MISSING - Create users table first'
    END as users_status;

SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'maxmed' AND TABLE_NAME = 'crm_leads')
        THEN 'crm_leads table EXISTS'
        ELSE 'WARNING: crm_leads table MISSING - Skip lead_id foreign key'
    END as crm_leads_status;

SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'maxmed' AND TABLE_NAME = 'quotes')
        THEN 'quotes table EXISTS'
        ELSE 'WARNING: quotes table MISSING - Skip generated_quote_id foreign key'
    END as quotes_status;

-- =========================================================================
-- STEP 4: Add foreign key constraints for quotation_requests
-- (Uncomment only the lines for tables that exist)
-- =========================================================================

-- REQUIRED: Foreign key for product_id (uncomment if products table exists)
-- ALTER TABLE `quotation_requests`
--   ADD CONSTRAINT `quotation_requests_product_id_foreign` 
--   FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

-- REQUIRED: Foreign key for user_id (uncomment if users table exists)
-- ALTER TABLE `quotation_requests`
--   ADD CONSTRAINT `quotation_requests_user_id_foreign` 
--   FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- OPTIONAL: Foreign key for supplier_id (uncomment if users table exists)
-- ALTER TABLE `quotation_requests`
--   ADD CONSTRAINT `quotation_requests_supplier_id_foreign` 
--   FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- OPTIONAL: Foreign key for lead_id (uncomment if crm_leads table exists)
-- ALTER TABLE `quotation_requests`
--   ADD CONSTRAINT `quotation_requests_lead_id_foreign` 
--   FOREIGN KEY (`lead_id`) REFERENCES `crm_leads` (`id`) ON DELETE SET NULL;

-- OPTIONAL: Foreign key for generated_quote_id (uncomment if quotes table exists)
-- ALTER TABLE `quotation_requests`
--   ADD CONSTRAINT `quotation_requests_generated_quote_id_foreign` 
--   FOREIGN KEY (`generated_quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL;

-- =========================================================================
-- STEP 5: Create supplier_quotations table (after quotation_requests exists)
-- =========================================================================

CREATE TABLE `supplier_quotations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_request_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quotation_number` varchar(191) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'AED',
  `minimum_quantity` int(11) NOT NULL DEFAULT 1,
  `lead_time_days` int(11) DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `size` varchar(191) DEFAULT NULL,
  `specifications` json DEFAULT NULL,
  `description` text DEFAULT NULL,
  `supplier_notes` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `status` enum('draft','submitted','accepted','rejected') NOT NULL DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_quotations_quotation_number_unique` (`quotation_number`),
  KEY `supplier_quotations_quotation_request_id_index` (`quotation_request_id`),
  KEY `supplier_quotations_supplier_id_status_index` (`supplier_id`,`status`),
  KEY `supplier_quotations_status_created_at_index` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- STEP 6: Add foreign key constraints for supplier_quotations
-- (Uncomment only after verifying the referenced tables exist)
-- =========================================================================

-- This foreign key WILL work now because quotation_requests table exists with proper primary key
ALTER TABLE `supplier_quotations`
  ADD CONSTRAINT `supplier_quotations_quotation_request_id_foreign` 
  FOREIGN KEY (`quotation_request_id`) REFERENCES `quotation_requests` (`id`) ON DELETE CASCADE;

-- Uncomment these if the referenced tables exist:
-- ALTER TABLE `supplier_quotations`
--   ADD CONSTRAINT `supplier_quotations_supplier_id_foreign` 
--   FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `supplier_quotations`
--   ADD CONSTRAINT `supplier_quotations_product_id_foreign` 
--   FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

-- =========================================================================
-- FINAL MESSAGE
-- =========================================================================
SELECT 'Tables created successfully! Now uncomment and run the foreign key constraints for tables that exist.' as final_message; 