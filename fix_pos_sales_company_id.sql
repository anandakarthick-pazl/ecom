-- Add company_id column to pos_sales table for multi-tenant support
-- Run this script if the migration fails

-- Check if column exists before adding
SET @dbname = DATABASE();
SET @tablename = 'pos_sales';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column already exists'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN company_id BIGINT UNSIGNED NULL AFTER id")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add foreign key constraint if not exists
ALTER TABLE pos_sales 
ADD CONSTRAINT fk_pos_sales_company_id 
FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE;

-- Add index for better performance
CREATE INDEX idx_pos_sales_company_id ON pos_sales(company_id);

-- Update existing records with the first company's ID (or a specific company ID)
UPDATE pos_sales 
SET company_id = (SELECT id FROM companies LIMIT 1) 
WHERE company_id IS NULL;

-- Make company_id NOT NULL after updating existing records
ALTER TABLE pos_sales MODIFY COLUMN company_id BIGINT UNSIGNED NOT NULL;
