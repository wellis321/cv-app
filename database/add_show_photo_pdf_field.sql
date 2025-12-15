-- Add show_photo_pdf field to control PDF photo visibility separately
ALTER TABLE profiles
    ADD COLUMN IF NOT EXISTS show_photo_pdf TINYINT(1) DEFAULT 1;
