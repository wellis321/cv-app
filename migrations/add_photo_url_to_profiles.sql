-- Add photo_url column to profiles table
ALTER TABLE profiles ADD COLUMN photo_url TEXT;

-- Create storage bucket for profile photos if it doesn't exist
INSERT INTO storage.buckets (id, name, public)
VALUES ('profile_photos', 'Profile Photos', true)
ON CONFLICT (id) DO NOTHING;

-- Add policy to allow authenticated users to upload their own photos
CREATE POLICY "Users can upload their own profile photos"
ON storage.objects
FOR INSERT
TO authenticated
WITH CHECK (bucket_id = 'profile_photos' AND auth.uid()::text = (storage.foldername(name))[1]);

-- Add policy to allow users to update their own photos
CREATE POLICY "Users can update their own profile photos"
ON storage.objects
FOR UPDATE
TO authenticated
USING (bucket_id = 'profile_photos' AND auth.uid()::text = (storage.foldername(name))[1]);

-- Add policy to allow users to delete their own photos
CREATE POLICY "Users can delete their own profile photos"
ON storage.objects
FOR DELETE
TO authenticated
USING (bucket_id = 'profile_photos' AND auth.uid()::text = (storage.foldername(name))[1]);

-- Add policy to allow public access to profile photos (read-only)
CREATE POLICY "Profile photos are publicly accessible"
ON storage.objects
FOR SELECT
TO public
USING (bucket_id = 'profile_photos');