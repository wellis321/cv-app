-- Add enhanced_recommendations column to cv_quality_assessments table
-- Migration: 20250122_add_enhanced_recommendations
-- Description: Adds column to store enhanced recommendations with examples and AI-generated improvements

-- Check if column exists before adding (MySQL doesn't support IF NOT EXISTS for ALTER TABLE)
-- Run this migration manually if needed, or use a tool that handles IF NOT EXISTS

ALTER TABLE cv_quality_assessments 
ADD COLUMN enhanced_recommendations JSON NULL 
AFTER recommendations;

