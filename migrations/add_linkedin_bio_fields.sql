-- Add linkedin_url and bio columns to profiles table
ALTER TABLE public.profiles
ADD COLUMN IF NOT EXISTS linkedin_url TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL;

-- Update RLS policies to include the new columns
ALTER POLICY "Users can view their own profiles"
ON public.profiles
USING (auth.uid() = id);

ALTER POLICY "Users can update their own profiles"
ON public.profiles
USING (auth.uid() = id)
WITH CHECK (auth.uid() = id);