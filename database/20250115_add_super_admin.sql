-- Add super admin support
-- Migration: 20250115_add_super_admin
-- Description: Adds is_super_admin field to profiles table for system administrators

-- =====================================================
-- Add is_super_admin column to profiles table
-- =====================================================

SET @dbname = DATABASE();
SET @tablename = 'profiles';
SET @columnname = 'is_super_admin';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' BOOLEAN DEFAULT FALSE NOT NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add index for super admin lookups
SET @indexname = 'idx_profiles_super_admin';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  'SELECT 1',
  CONCAT('CREATE INDEX ', @indexname, ' ON ', @tablename, '(', @columnname, ')')
));
PREPARE createIndexIfNotExists FROM @preparedStatement;
EXECUTE createIndexIfNotExists;
DEALLOCATE PREPARE createIndexIfNotExists;

