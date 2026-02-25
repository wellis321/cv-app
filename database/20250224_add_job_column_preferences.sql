-- Migration: 20250224_add_job_column_preferences
-- Description: Add job applications column visibility preferences to profiles for cross-device persistence

ALTER TABLE profiles
ADD COLUMN job_applications_column_visibility JSON NULL DEFAULT NULL;
