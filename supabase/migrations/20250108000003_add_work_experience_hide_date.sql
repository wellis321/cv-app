-- Add hide_date field to work_experience table
ALTER TABLE work_experience ADD COLUMN hide_date BOOLEAN NOT NULL DEFAULT FALSE;

-- Create index for better performance when filtering
CREATE INDEX idx_work_experience_hide_date ON work_experience(profile_id, hide_date);
