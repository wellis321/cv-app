-- Migration: 20250305_add_sections_online
-- Description: Add sections_online to profiles for controlling which sections appear on the public online CV (/cv/@username)
-- Variant-level sections_online is stored in cv_variants.pdf_preferences JSON (no schema change)

ALTER TABLE profiles
ADD COLUMN sections_online JSON NULL DEFAULT NULL;
