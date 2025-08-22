-- Add date_format_preference field to profiles table
ALTER TABLE profiles ADD COLUMN date_format_preference TEXT NOT NULL DEFAULT 'month-year' CHECK (date_format_preference IN ('month-year', 'year-only'));

-- Create index for better performance
CREATE INDEX idx_profiles_date_format_preference ON profiles(date_format_preference);

-- Update existing profiles to have the default date format preference
UPDATE profiles SET date_format_preference = 'month-year' WHERE date_format_preference IS NULL;
