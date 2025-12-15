-- Add show_qr_code field to profiles table
-- This allows users to show a QR code linking to their CV when the photo is hidden
ALTER TABLE profiles ADD COLUMN IF NOT EXISTS show_qr_code TINYINT(1) DEFAULT 0;
