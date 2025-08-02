-- Update Company Domain for Production
-- Run this SQL query to update your company domain

-- First, check current companies
SELECT id, name, slug, domain, status FROM companies;

-- Update the domain for RRK Crackers company
-- If you have an existing company, update it:
UPDATE companies 
SET domain = 'rrkcrackers.com' 
WHERE slug = 'rrkcrackers' OR name LIKE '%RRK%' OR name LIKE '%crackers%';

-- If no company exists, insert a new one:
INSERT INTO companies (
    name, 
    slug, 
    domain, 
    email, 
    phone,
    address,
    city,
    state,
    country,
    status, 
    trial_ends_at,
    created_at, 
    updated_at
) VALUES (
    'RRK Crackers', 
    'rrkcrackers', 
    'rrkcrackers.com', 
    'admin@rrkcrackers.com',
    '+91 9876543210',
    'Chennai',
    'Chennai',
    'Tamil Nadu',
    'India',
    'active',
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    NOW(), 
    NOW()
);

-- Verify the update
SELECT id, name, slug, domain, status FROM companies WHERE domain = 'rrkcrackers.com';
