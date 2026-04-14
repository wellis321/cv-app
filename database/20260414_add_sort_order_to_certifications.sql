-- Add sort_order to certifications and cv_variant_certifications for drag-and-drop reordering
ALTER TABLE certifications
    ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER expiry_date,
    ADD INDEX IF NOT EXISTS idx_certifications_sort (profile_id, sort_order);

ALTER TABLE cv_variant_certifications
    ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER expiry_date,
    ADD INDEX IF NOT EXISTS idx_cv_variant_certifications_sort (cv_variant_id, sort_order);

-- Initialise sort_order from date_obtained DESC so existing data keeps its current display order
SET @row_num = 0;
SET @prev_profile = '';

UPDATE certifications c
JOIN (
    SELECT id,
           @row_num := IF(@prev_profile = profile_id, @row_num + 1, 0) AS rn,
           @prev_profile := profile_id
    FROM certifications
    ORDER BY profile_id, date_obtained DESC, created_at DESC
) ranked ON c.id = ranked.id
SET c.sort_order = ranked.rn;
