-- Migration: 20250224_add_cv_variant_pdf_preferences
-- Description: Add PDF preferences per variant (template, sections, colours) for preview and cover letter consistency

ALTER TABLE cv_variants
ADD COLUMN pdf_preferences JSON NULL DEFAULT NULL;
