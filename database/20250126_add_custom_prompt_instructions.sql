-- Add Custom Prompt Instructions Support
-- Migration: 20250126_add_custom_prompt_instructions
-- Description: Adds column for user-customizable CV rewrite prompt instructions

ALTER TABLE profiles 
ADD COLUMN cv_rewrite_prompt_instructions TEXT NULL COMMENT 'User-customizable instructions for CV rewrite prompts';

