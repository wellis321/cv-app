-- Add cv_public field to profiles table
-- Controls whether the CV is publicly accessible at /cv/@username
ALTER TABLE profiles ADD COLUMN IF NOT EXISTS cv_public TINYINT(1) DEFAULT 1;
