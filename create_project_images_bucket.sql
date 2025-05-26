-- Add image_url column to projects table
ALTER TABLE projects
ADD COLUMN image_url TEXT DEFAULT NULL;

-- Create a new storage bucket for project images if it doesn't exist
INSERT INTO storage.buckets (id, name, public)
VALUES ('project-images', 'project-images', true)
ON CONFLICT (id) DO NOTHING;

-- Allow authenticated users to upload their own project images
CREATE POLICY "Users can upload their own project images"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (
    bucket_id = 'project-images' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

-- Allow users to view their own project images
CREATE POLICY "Users can view their own project images"
ON storage.objects FOR SELECT
TO authenticated
USING (
    bucket_id = 'project-images' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

-- Allow users to delete their own project images
CREATE POLICY "Users can delete their own project images"
ON storage.objects FOR DELETE
TO authenticated
USING (
    bucket_id = 'project-images' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

-- Allow users to update their own project images
CREATE POLICY "Users can update their own project images"
ON storage.objects FOR UPDATE
TO authenticated
USING (
    bucket_id = 'project-images' AND
    (storage.foldername(name))[1] = auth.uid()::text
);

-- Allow public access to read project images (needed for sharing CVs)
CREATE POLICY "Public read access to project images"
ON storage.objects FOR SELECT
TO public
USING (bucket_id = 'project-images');