-- Synchronize profile schema between development and production
-- Fix email update issue and ensure photo_url is consistent

-- 1. First make username nullable to allow updates
ALTER TABLE profiles ALTER COLUMN username DROP NOT NULL;

-- 2. Ensure photo_url column exists (if not, add it)
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                  WHERE table_name = 'profiles' AND column_name = 'photo_url') THEN
        ALTER TABLE profiles ADD COLUMN photo_url TEXT;
    END IF;
END $$;

-- 3. Ensure profile_photo_url and profile_photo_path columns exist for production compatibility
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                  WHERE table_name = 'profiles' AND column_name = 'profile_photo_url') THEN
        ALTER TABLE profiles ADD COLUMN profile_photo_url TEXT;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                  WHERE table_name = 'profiles' AND column_name = 'profile_photo_path') THEN
        ALTER TABLE profiles ADD COLUMN profile_photo_path TEXT;
    END IF;
END $$;

-- 4. For existing profiles, copy photo_url to profile_photo_url if one exists and the other doesn't
UPDATE profiles
SET profile_photo_url = photo_url
WHERE photo_url IS NOT NULL AND profile_photo_url IS NULL;

UPDATE profiles
SET photo_url = profile_photo_url
WHERE profile_photo_url IS NOT NULL AND photo_url IS NULL;

-- 5. Create a trigger to ensure photo_url and profile_photo_url stay in sync
CREATE OR REPLACE FUNCTION sync_profile_photo_urls()
RETURNS TRIGGER AS $$
BEGIN
    -- If photo_url was updated but profile_photo_url wasn't, sync profile_photo_url
    IF (NEW.photo_url IS DISTINCT FROM OLD.photo_url AND
        (NEW.profile_photo_url IS NOT DISTINCT FROM OLD.profile_photo_url OR NEW.profile_photo_url IS NULL)) THEN
        NEW.profile_photo_url := NEW.photo_url;
    END IF;

    -- If profile_photo_url was updated but photo_url wasn't, sync photo_url
    IF (NEW.profile_photo_url IS DISTINCT FROM OLD.profile_photo_url AND
        (NEW.photo_url IS NOT DISTINCT FROM OLD.photo_url OR NEW.photo_url IS NULL)) THEN
        NEW.photo_url := NEW.profile_photo_url;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Drop the trigger if it exists
DROP TRIGGER IF EXISTS profile_photo_sync_trigger ON profiles;

-- Create trigger to synchronize photo URLs
CREATE TRIGGER profile_photo_sync_trigger
BEFORE UPDATE ON profiles
FOR EACH ROW
EXECUTE FUNCTION sync_profile_photo_urls();

-- 6. Fix the RLS policies for profiles to ensure they work in both environments

-- First drop all existing policies to avoid conflicts
DROP POLICY IF EXISTS "Users can update their own profile" ON profiles;
DROP POLICY IF EXISTS "Users can view their own profile" ON profiles;
DROP POLICY IF EXISTS "Ensure email always matches auth email" ON profiles;
DROP POLICY IF EXISTS "Users can insert their own profile" ON profiles;
DROP POLICY IF EXISTS "Users can delete their own profile" ON profiles;

-- Create comprehensive policies that work in both environments
-- Allow users to view their own profile
CREATE POLICY "Users can view their own profile"
    ON profiles FOR SELECT
    TO authenticated
    USING (auth.uid() = id);

-- Allow users to update their own profile
CREATE POLICY "Users can update their own profile"
    ON profiles FOR UPDATE
    TO authenticated
    USING (auth.uid() = id);

-- Allow users to insert their own profile
CREATE POLICY "Users can insert their own profile"
    ON profiles FOR INSERT
    TO authenticated
    WITH CHECK (auth.uid() = id);

-- Allow users to delete their own profile
CREATE POLICY "Users can delete their own profile"
    ON profiles FOR DELETE
    TO authenticated
    USING (auth.uid() = id);