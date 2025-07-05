-- Fix greenvalleyherbs.local domain issue
-- This ensures the domain is exactly 'greenvalleyherbs.local'

-- First, let's see what we have
SELECT id, name, slug, domain, status FROM companies WHERE slug LIKE '%greenvalley%' OR domain LIKE '%greenvalley%';

-- Update the domain to the correct value
UPDATE companies 
SET domain = 'greenvalleyherbs.local' 
WHERE slug = 'greenvalleyherbs' OR name = 'Green Valley Herbs';

-- Also check for any trailing spaces or issues
UPDATE companies 
SET domain = TRIM(domain) 
WHERE domain LIKE '%greenvalley%';

-- Verify the fix
SELECT id, name, slug, domain, status FROM companies ORDER BY id;
