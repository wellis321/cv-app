-- Add show_photo_pdf field to control PDF photo visibility separately
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE ADD COLUMN
-- If the column already exists, you'll get an error which can be safely ignored
ALTER TABLE profiles
    ADD COLUMN show_photo_pdf TINYINT(1) DEFAULT 1;
