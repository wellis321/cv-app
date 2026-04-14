-- Make optional date fields nullable so users can save without entering dates.
-- Covers all sections where the form shows date fields without an asterisk.

-- Professional memberships (start_date was already targeted in 20260413 migration;
-- included here so a single migration run fixes everything on production)
ALTER TABLE professional_memberships
    MODIFY COLUMN start_date DATE NULL,
    MODIFY COLUMN end_date DATE NULL;

-- CV variant memberships
ALTER TABLE cv_variant_memberships
    MODIFY COLUMN start_date DATE NULL,
    MODIFY COLUMN end_date DATE NULL;

-- Certifications — date_obtained has no asterisk in the form
ALTER TABLE certifications
    MODIFY COLUMN date_obtained DATE NULL;

-- CV variant certifications
ALTER TABLE cv_variant_certifications
    MODIFY COLUMN date_obtained DATE NULL;
