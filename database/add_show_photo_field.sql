-- Add show_photo field to profiles table
-- This migration adds a boolean field to control photo visibility on CV
-- This uses a safer approach that checks if the column exists before adding it
-- (MySQL doesn't support IF NOT EXISTS for ALTER TABLE)

SET @dbname = DATABASE();
SET @tablename = "profiles";
SET @columnname = "show_photo";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " BOOLEAN DEFAULT TRUE")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
