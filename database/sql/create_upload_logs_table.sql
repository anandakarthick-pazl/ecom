-- Manual SQL to create upload_logs table if migration fails
-- Database: ecom_saas
-- Run this in your MySQL client or phpMyAdmin

USE ecom_saas;

CREATE TABLE IF NOT EXISTS `upload_logs` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `file_name` varchar(255) NOT NULL,
    `original_name` varchar(255) NOT NULL,
    `file_path` varchar(255) NOT NULL,
    `file_size` bigint DEFAULT NULL,
    `mime_type` varchar(255) DEFAULT NULL,
    `storage_type` enum('local','s3') NOT NULL DEFAULT 'local',
    `upload_type` enum('product','category','banner','general') NOT NULL DEFAULT 'general',
    `source_id` bigint unsigned DEFAULT NULL,
    `source_type` varchar(255) DEFAULT NULL,
    `uploaded_by` bigint unsigned DEFAULT NULL,
    `meta_data` json DEFAULT NULL,
    `company_id` bigint unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `upload_logs_upload_type_source_id_index` (`upload_type`,`source_id`),
    KEY `upload_logs_company_id_upload_type_index` (`company_id`,`upload_type`),
    KEY `upload_logs_uploaded_by_index` (`uploaded_by`),
    KEY `upload_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign keys only if the referenced tables exist
-- Uncomment these lines after confirming users and companies tables exist:

-- ALTER TABLE `upload_logs` 
-- ADD CONSTRAINT `upload_logs_uploaded_by_foreign` 
-- FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `upload_logs` 
-- ADD CONSTRAINT `upload_logs_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

-- Verify table creation
SELECT 'upload_logs table created successfully' as status;
DESCRIBE upload_logs;
