-- SQL commands to create required themes and packages
-- Run these in your MySQL database if automated scripts fail

-- Create default theme
INSERT INTO themes (name, slug, category, status, description, config, created_at, updated_at) 
VALUES (
    'Default Theme', 
    'default', 
    'default', 
    'active', 
    'Default theme for all stores',
    '{"primary_color":"#2d5016","secondary_color":"#6b8e23"}',
    NOW(), 
    NOW()
);

-- Create default package  
INSERT INTO packages (name, slug, price, billing_cycle, status, description, features, created_at, updated_at)
VALUES (
    'Basic Plan',
    'basic', 
    29.99, 
    'monthly', 
    'active', 
    'Basic ecommerce features',
    '{"products":100,"storage":"5GB","support":"email"}',
    NOW(), 
    NOW()
);

-- Check what was created
SELECT id, name, slug FROM themes WHERE slug = 'default';
SELECT id, name, slug FROM packages WHERE slug = 'basic';

-- Now you can create companies using these IDs
-- Replace 1 and 1 with the actual IDs from above
INSERT INTO companies (name, slug, email, domain, status, theme_id, package_id, trial_ends_at, created_by, created_at, updated_at)
VALUES (
    'Green Valley Herbs',
    'green-valley-herbs', 
    'admin@greenvalleyherbs.com',
    'greenvalleyherbs.local',
    'active',
    1, -- Replace with theme ID
    1, -- Replace with package ID  
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    1,
    NOW(),
    NOW()
);
