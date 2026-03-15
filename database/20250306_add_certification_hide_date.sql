-- Add hide_date to certifications (match education/work_experience behaviour)
ALTER TABLE certifications ADD COLUMN hide_date TINYINT(1) NOT NULL DEFAULT 0;

-- Add hide_date to cv_variant_certifications for variant CVs
ALTER TABLE cv_variant_certifications ADD COLUMN hide_date TINYINT(1) NOT NULL DEFAULT 0;
