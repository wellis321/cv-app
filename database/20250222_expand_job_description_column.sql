-- Migration: 20250222_expand_job_description_column
-- Description: Expand job_description from TEXT (~64KB) to MEDIUMTEXT (~16MB) to support
--              large extracted content (e.g. full recruitment packs with tables)

ALTER TABLE job_applications MODIFY COLUMN job_description MEDIUMTEXT;
