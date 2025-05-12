-- Create a storage bucket for profile photos if it doesn't exist
INSERT INTO storage.buckets (id, name, public)
VALUES ('profile_photos', 'profile_photos', true)
ON CONFLICT (id) DO NOTHING;

-- Set up CORS for the profile_photos bucket
UPDATE storage.buckets
SET cors = '[{"origin":"*","methods":["GET"]}]'
WHERE id = 'profile_photos';

-- Allow authenticated users to upload and delete their own profile photos
CREATE POLICY "Users can upload their own profile photos"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (
    bucket_id = 'profile_photos' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

CREATE POLICY "Users can update their own profile photos"
ON storage.objects FOR UPDATE
TO authenticated
USING (
    bucket_id = 'profile_photos' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

CREATE POLICY "Users can delete their own profile photos"
ON storage.objects FOR DELETE
TO authenticated
USING (
    bucket_id = 'profile_photos' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

-- Allow public access to read profile photos (needed for sharing CVs)
CREATE POLICY "Public read access to profile photos"
ON storage.objects FOR SELECT
TO public
USING (bucket_id = 'profile_photos');