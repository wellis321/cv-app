-- Add show_photo field to profiles table
-- This migration adds a boolean field to control photo visibility on CV

-- Check if column exists before adding (MySQL doesn't support IF NOT EXISTS for ALTER TABLE)
-- Run this manually or use a migration tool

ALTER TABLE profiles ADD COLUMN show_photo BOOLEAN DEFAULT TRUE;

-- If the above fails because column already exists, you can ignore the error
-- Or use this safer approach in your migration tool:
--
-- SET @dbname = DATABASE();
-- SET @tablename = "profiles";
-- SET @columnname = "show_photo";
-- SET @preparedStatement = (SELECT IF(
--   (
--     SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
--     WHERE
--       (table_name = @tablename)
--       AND (table_schema = @dbname)
--       AND (column_name = @columnname)
--   ) > 0,
--   "SELECT 1",
--   CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " BOOLEAN DEFAULT TRUE")
-- ));
-- PREPARE alterIfNotExists FROM @preparedStatement;
-- EXECUTE alterIfNotExists;
-- DEALLOCATE PREPARE alterIfNotExists;
