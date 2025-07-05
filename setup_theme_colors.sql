-- Insert default theme colors if they don't exist
-- This ensures bg-primary and other theme colors work properly

-- For company_id = 1 (greenvalleyherbs)
INSERT INTO app_settings (key, value, type, `group`, company_id, created_at, updated_at)
SELECT 'primary_color', '#2d5016', 'string', 'theme', 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM app_settings WHERE `key` = 'primary_color' AND company_id = 1
);

INSERT INTO app_settings (key, value, type, `group`, company_id, created_at, updated_at)
SELECT 'secondary_color', '#4a7c28', 'string', 'theme', 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM app_settings WHERE `key` = 'secondary_color' AND company_id = 1
);

INSERT INTO app_settings (key, value, type, `group`, company_id, created_at, updated_at)
SELECT 'sidebar_color', '#2d5016', 'string', 'theme', 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM app_settings WHERE `key` = 'sidebar_color' AND company_id = 1
);

INSERT INTO app_settings (key, value, type, `group`, company_id, created_at, updated_at)
SELECT 'theme_mode', 'light', 'string', 'theme', 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM app_settings WHERE `key` = 'theme_mode' AND company_id = 1
);

-- Show current theme settings
SELECT * FROM app_settings WHERE `group` = 'theme' AND company_id = 1;

-- Show company information
SELECT id, name, email, phone, address, city, state, postal_code, logo FROM companies WHERE id = 1;
