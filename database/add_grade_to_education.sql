-- Add grade column to education table
ALTER TABLE education ADD COLUMN grade VARCHAR(100) NULL AFTER field_of_study;
