-- Ensure companies exist with correct domains for multi-tenant setup
-- Run this after setting up the database

-- Update existing companies or insert new ones
INSERT INTO companies (id, name, slug, domain, email, phone, status, created_at, updated_at) VALUES
(1, 'Green Valley Herbs', 'greenvalleyherbs', 'greenvalleyherbs.local', 'admin@greenvalleyherbs.local', '1234567890', 'active', NOW(), NOW()),
(2, 'Organic Nature', 'organicnature', 'organicnature.local', 'admin@organicnature.local', '0987654321', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    domain = VALUES(domain),
    email = VALUES(email),
    status = 'active',
    updated_at = NOW();

-- Ensure super admin user exists
INSERT INTO users (id, name, email, password, role, company_id, created_at, updated_at) VALUES
(1, 'Super Admin', 'superadmin@localhost.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    role = 'super_admin',
    company_id = NULL,
    updated_at = NOW();

-- Ensure admin users exist for each company
INSERT INTO users (name, email, password, role, company_id, created_at, updated_at) VALUES
('Green Valley Admin', 'admin@greenvalleyherbs.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW()),
('Organic Nature Admin', 'admin@organicnature.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 2, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    role = 'admin',
    updated_at = NOW();

-- Update any products, categories, etc. to ensure they have company_id
UPDATE products SET company_id = 1 WHERE company_id IS NULL LIMIT 50;
UPDATE categories SET company_id = 1 WHERE company_id IS NULL LIMIT 20;

-- Create some sample data for company 2
INSERT INTO categories (name, slug, company_id, is_active, created_at, updated_at) 
SELECT CONCAT(name, ' - Organic'), CONCAT(slug, '-organic'), 2, is_active, NOW(), NOW()
FROM categories WHERE company_id = 1 LIMIT 5;

INSERT INTO products (name, slug, category_id, price, stock, company_id, is_active, created_at, updated_at)
SELECT CONCAT(name, ' Organic'), CONCAT(slug, '-organic'), 
    (SELECT id FROM categories WHERE company_id = 2 LIMIT 1),
    price * 1.2, stock, 2, is_active, NOW(), NOW()
FROM products WHERE company_id = 1 LIMIT 10;

-- Display results
SELECT 'Companies:' as '';
SELECT id, name, domain, email, status FROM companies;

SELECT '' as '', 'Users:' as '';
SELECT id, name, email, role, company_id FROM users WHERE role IN ('super_admin', 'admin');

SELECT '' as '', 'Default Password for all users: password' as Info;
