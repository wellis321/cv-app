-- Add show_qr_code field to profiles table
-- This allows users to show a QR code linking to their CV when the photo is hidden
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE ADD COLUMN
-- If the column already exists, you'll get an error which can be safely ignored
ALTER TABLE profiles ADD COLUMN show_qr_code TINYINT(1) DEFAULT 0;
