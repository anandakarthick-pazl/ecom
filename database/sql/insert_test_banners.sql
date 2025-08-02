-- Insert a test banner if none exist
-- Run this in your MySQL client or phpMyAdmin

USE ecom_saas;

-- Insert a test banner for company_id = 1 (adjust if needed)
INSERT INTO banners (
    title, 
    image, 
    position, 
    is_active, 
    sort_order, 
    company_id,
    alt_text,
    created_at,
    updated_at
) VALUES (
    'Welcome Banner',
    'banners/test-banner.jpg',
    'top',
    1,
    1,
    1,
    'Welcome to our store',
    NOW(),
    NOW()
);

-- Insert another test banner
INSERT INTO banners (
    title, 
    image, 
    position, 
    is_active, 
    sort_order, 
    company_id,
    alt_text,
    link_url,
    created_at,
    updated_at
) VALUES (
    'Special Offer',
    'banners/offer-banner.jpg',
    'top',
    1,
    2,
    1,
    'Special offers available',
    '/products',
    NOW(),
    NOW()
);

-- Verify the inserts
SELECT * FROM banners WHERE company_id = 1 ORDER BY sort_order;
