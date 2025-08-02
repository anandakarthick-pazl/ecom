-- Fix existing banner paths in the database
-- Run this in your MySQL client or phpMyAdmin

USE ecom_saas;

-- First, let's see what paths we currently have
SELECT 
    id, 
    title, 
    image as original_path,
    company_id,
    is_active
FROM banners;

-- Update banner paths to fix common issues
-- Remove 'public/' prefix if it exists
UPDATE banners 
SET image = REPLACE(image, 'public/', '')
WHERE image LIKE 'public/%';

-- Fix duplicate banners folder (banners/banners/ -> banners/)
UPDATE banners 
SET image = REPLACE(image, 'banners/banners/', 'banners/')
WHERE image LIKE '%banners/banners/%';

-- Fix banner/banners/ to just banners/
UPDATE banners 
SET image = REPLACE(image, 'banner/banners/', 'banners/')
WHERE image LIKE '%banner/banners/%';

-- Ensure all images start with 'banners/' if they don't already
UPDATE banners 
SET image = CONCAT('banners/', SUBSTRING_INDEX(image, '/', -1))
WHERE image NOT LIKE 'banners/%' 
  AND image IS NOT NULL 
  AND image != '';

-- Check the results after fixes
SELECT 
    id, 
    title, 
    image as fixed_path,
    company_id,
    is_active
FROM banners;
