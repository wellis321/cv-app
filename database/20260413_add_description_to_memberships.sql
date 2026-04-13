-- Add description column to professional_memberships and cv_variant_memberships
ALTER TABLE professional_memberships
    ADD COLUMN description TEXT NULL AFTER end_date;

ALTER TABLE cv_variant_memberships
    ADD COLUMN description TEXT NULL AFTER end_date;
