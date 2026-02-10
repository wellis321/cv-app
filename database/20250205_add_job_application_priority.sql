-- Add priority to job applications (for quick-save and prioritisation)
-- Migration: 20250205_add_job_application_priority

ALTER TABLE job_applications
ADD COLUMN priority ENUM('low','medium','high') NULL DEFAULT NULL AFTER remote_type,
ADD INDEX idx_priority (priority);
