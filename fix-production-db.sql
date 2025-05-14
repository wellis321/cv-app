-- Script to fix ONLY the production database to match your working development environment
-- Copy and paste this into the Supabase SQL Editor on your production project

-- 1. Ensure we have the photo_url column that works in development
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                  WHERE table_name = 'profiles' AND column_name = 'photo_url') THEN
        ALTER TABLE profiles ADD COLUMN photo_url TEXT;
    END IF;
END $$;

-- 2. If profile_photo_url contains data but photo_url doesn't, copy it over
UPDATE profiles
SET photo_url = profile_photo_url
WHERE profile_photo_url IS NOT NULL AND photo_url IS NULL;

-- 3. Make username nullable to allow updates (matching development)
ALTER TABLE profiles ALTER COLUMN username DROP NOT NULL;

-- 4. Fix the RLS policies to match your working development environment
-- First drop all existing policies that might be causing issues
DROP POLICY IF EXISTS "Users can update their own profile" ON profiles;
DROP POLICY IF EXISTS "Users can view their own profile" ON profiles;
DROP POLICY IF EXISTS "Ensure email always matches auth email" ON profiles;
DROP POLICY IF EXISTS "Users can insert their own profile" ON profiles;
DROP POLICY IF EXISTS "Users can delete their own profile" ON profiles;

-- Create the simple policies that work in your development environment
CREATE POLICY "Users can view their own profile"
    ON profiles FOR SELECT
    TO authenticated
    USING (auth.uid() = id);

CREATE POLICY "Users can update their own profile"
    ON profiles FOR UPDATE
    TO authenticated
    USING (auth.uid() = id);

CREATE POLICY "Users can insert their own profile"
    ON profiles FOR INSERT
    TO authenticated
    WITH CHECK (auth.uid() = id);

CREATE POLICY "Users can delete their own profile"
    ON profiles FOR DELETE
    TO authenticated
    USING (auth.uid() = id);