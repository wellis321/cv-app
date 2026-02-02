-- Add Keywords Support to Job Applications
-- Migration: 20250201_add_job_keywords
-- Description: Adds fields to store extracted keywords and selected keywords for CV generation

ALTER TABLE job_applications 
ADD COLUMN extracted_keywords JSON NULL COMMENT 'Array of keywords extracted from job description',
ADD COLUMN selected_keywords JSON NULL COMMENT 'Array of keywords selected by user for CV generation';
