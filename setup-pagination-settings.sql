INSERT INTO app_settings (key, value, type, group_key, created_at, updated_at) VALUES
('frontend_pagination_enabled', 'true', 'boolean', 'pagination', NOW(), NOW()),
('admin_pagination_enabled', 'true', 'boolean', 'pagination', NOW(), NOW()),
('frontend_records_per_page', '12', 'integer', 'pagination', NOW(), NOW()),
('admin_records_per_page', '20', 'integer', 'pagination', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
value = VALUES(value),
updated_at = NOW();