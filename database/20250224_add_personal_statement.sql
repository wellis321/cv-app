-- Migration: 20250224_add_personal_statement
-- Add personal_statement column for 500-word suitability statements (e.g. for application forms)

ALTER TABLE job_applications
ADD COLUMN personal_statement MEDIUMTEXT NULL DEFAULT NULL AFTER notes;
