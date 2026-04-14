-- Add description column to professional_memberships and cv_variant_memberships
-- Also make start_date nullable (end_date was already nullable; start_date should be optional too)

ALTER TABLE professional_memberships
    MODIFY COLUMN start_date DATE NULL,
    ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER end_date;

ALTER TABLE cv_variant_memberships
    MODIFY COLUMN start_date DATE NULL,
    ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER end_date;
