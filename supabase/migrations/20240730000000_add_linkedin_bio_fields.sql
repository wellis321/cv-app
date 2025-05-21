-- Add linkedin_url and bio columns to profiles table
ALTER TABLE public.profiles
ADD COLUMN IF NOT EXISTS linkedin_url TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL;

-- Make sure RLS policies are correctly set
-- (existing policies should automatically apply to new columns)
-- But explicitly update them to make sure
ALTER POLICY "Users can view their own profiles"
ON public.profiles
USING (auth.uid() = id);

ALTER POLICY "Users can update their own profiles"
ON public.profiles
USING (auth.uid() = id)
WITH CHECK (auth.uid() = id);