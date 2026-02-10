-- Allow application_date to be NULL for jobs saved via extension/quick-add (not yet applied)
-- Migration: 20250207_make_job_application_date_nullable

ALTER TABLE job_applications
MODIFY COLUMN application_date TIMESTAMP NULL DEFAULT NULL;
