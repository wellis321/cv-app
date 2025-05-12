-- Rename column from degree to qualification
ALTER TABLE IF EXISTS education
RENAME COLUMN degree TO qualification;

-- If the column already exists, this will handle the data migration
DO $$
BEGIN
    -- Check if both columns exist (in case of a partial migration)
    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_name = 'education'
        AND column_name = 'degree'
    ) AND EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_name = 'education'
        AND column_name = 'qualification'
    ) THEN
        -- Copy data from degree to qualification where qualification is null
        UPDATE education
        SET qualification = degree
        WHERE qualification IS NULL AND degree IS NOT NULL;

        -- Then drop the degree column
        ALTER TABLE education DROP COLUMN degree;
    END IF;
END$$;