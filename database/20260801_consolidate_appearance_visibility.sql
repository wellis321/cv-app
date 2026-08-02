-- Consolidate CV appearance/visibility settings (Phase 2)
-- Migration: 20260801_consolidate_appearance_visibility

-- Part 1: This database predates migration 20241216_add_template_preferences.sql and never
-- picked up `preferred_template_id` - it still has the older `template_preference` column
-- (VARCHAR(50) DEFAULT 'classic'). The app code (preview-cv.php, php/cover-letter-styles.php,
-- content-editor.php) reads `preferred_template_id` exclusively. Add it and backfill from the
-- legacy column so no existing template choice appears to reset. `template_preference` itself
-- is left in place (unread by any current code, but not dropped here - a separate decision).
ALTER TABLE profiles ADD COLUMN preferred_template_id VARCHAR(50) DEFAULT 'minimal';
UPDATE profiles SET preferred_template_id = template_preference WHERE template_preference IS NOT NULL;

-- Part 2: master-CV equivalent of cv_variants.pdf_preferences, for settings that currently have
-- no master persistence at all (PDF section toggles, accent colour, photo/QR PDF defaults).
-- Does NOT duplicate preferred_template_id or sections_online, which already have their own
-- dedicated columns.
ALTER TABLE profiles ADD COLUMN pdf_preferences JSON NULL DEFAULT NULL;

-- Part 3: the "Include Photo in PDF" default (previously profiles.show_photo_pdf, editable only
-- on the old profile.php Photo tab / preview-cv.php Photo & QR panel) now lives in this JSON blob
-- instead, as `include_photo`, so it's reachable from the new Visibility section. Backfill each
-- profile's existing choice so nobody's saved preference (e.g. someone who deliberately hid their
-- photo from PDFs) silently resets to the default of "shown" once the old toggle is removed.
UPDATE profiles
SET pdf_preferences = JSON_SET(COALESCE(pdf_preferences, '{}'), '$.include_photo', CAST(show_photo_pdf AS UNSIGNED) = 1)
WHERE JSON_EXTRACT(COALESCE(pdf_preferences, '{}'), '$.include_photo') IS NULL;

-- Note: template_accent_color / template_font_size / template_spacing / template_customization_json
-- (from 20241216_add_template_preferences.sql) do not exist on this database and are not created
-- here - no cleanup needed locally. If a production database has them, that's a separate action.
