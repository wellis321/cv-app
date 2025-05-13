-- Add photo_url column to profiles table
ALTER TABLE profiles ADD COLUMN IF NOT EXISTS photo_url TEXT;

-- Optional: Add a comment to explain what this column is for
COMMENT ON COLUMN profiles.photo_url IS 'URL to the user''s profile photo stored in Supabase storage';