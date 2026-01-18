-- Add custom CV template fields to profiles table
-- Migration: 20250123_add_cv_template_customization
-- Description: Adds columns for storing AI-generated custom CV templates

ALTER TABLE profiles 
ADD COLUMN custom_cv_template_html TEXT NULL AFTER template_preference,
ADD COLUMN custom_cv_template_css TEXT NULL AFTER custom_cv_template_html,
ADD COLUMN custom_cv_template_active BOOLEAN DEFAULT FALSE AFTER custom_cv_template_css,
ADD COLUMN custom_cv_template_description TEXT NULL AFTER custom_cv_template_active;

