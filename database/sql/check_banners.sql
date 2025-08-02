-- Check if banners table exists and has data
-- Run this in your MySQL client or phpMyAdmin

USE ecom_saas;

-- Check table structure
DESCRIBE banners;

-- Check all banners in the database
SELECT 
    id, 
    title, 
    image, 
    position, 
    is_active, 
    company_id,
    start_date,
    end_date,
    sort_order,
    created_at
FROM banners 
ORDER BY company_id, sort_order;

-- Check banners for a specific company (adjust company_id as needed)
SELECT 
    id, 
    title, 
    image, 
    position, 
    is_active, 
    start_date,
    end_date,
    sort_order
FROM banners 
WHERE company_id = 1 
    AND is_active = 1 
    AND position = 'top'
ORDER BY sort_order;

-- Check if there are any banners at all
SELECT COUNT(*) as total_banners FROM banners;

-- Check active banners by company
SELECT 
    company_id, 
    COUNT(*) as active_banners 
FROM banners 
WHERE is_active = 1 
GROUP BY company_id;
