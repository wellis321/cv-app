-- Add photo_responsive column to profiles table for responsive profile photos
-- This will fail if the column already exists, which is fine - just means it's already been run
ALTER TABLE profiles ADD COLUMN photo_responsive JSON NULL AFTER photo_url;

