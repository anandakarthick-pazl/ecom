-- Quick SQL to verify and fix company domains
-- Run this to ensure companies have correct domain values

-- Check current companies
SELECT id, name, slug, domain, status FROM companies;

-- Update domains to ensure they match expected values
UPDATE companies SET domain = 'greenvalleyherbs.local' WHERE slug = 'greenvalleyherbs';
UPDATE companies SET domain = 'organicnature.local' WHERE slug = 'organicnature';

-- If companies don't exist, insert them
INSERT IGNORE INTO companies (id, name, slug, domain, email, phone, status, created_at, updated_at) VALUES
(1, 'Green Valley Herbs', 'greenvalleyherbs', 'greenvalleyherbs.local', 'admin@greenvalleyherbs.local', '1234567890', 'active', NOW(), NOW()),
(2, 'Organic Nature', 'organicnature', 'organicnature.local', 'admin@organicnature.local', '0987654321', 'active', NOW(), NOW());

-- Verify the update
SELECT id, name, slug, domain, status FROM companies;
