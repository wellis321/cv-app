-- Add Missing Columns to User Feedback Table
-- Migration: 20250210_add_feedback_columns
-- Description: Adds missing columns to existing user_feedback table
--              Run this if your table was created with the old schema
--              If a column already exists, you'll get an error - just continue with the next statement

-- Add page_url column
ALTER TABLE user_feedback 
ADD COLUMN page_url TEXT NULL AFTER message;

-- Add email column
ALTER TABLE user_feedback 
ADD COLUMN email VARCHAR(255) NULL AFTER page_url;

-- Add user_agent column
ALTER TABLE user_feedback 
ADD COLUMN user_agent TEXT NULL AFTER email;

-- Add browser_info column
ALTER TABLE user_feedback 
ADD COLUMN browser_info JSON NULL AFTER user_agent;

-- Add status column
ALTER TABLE user_feedback 
ADD COLUMN status ENUM('new', 'reviewed', 'resolved', 'closed') DEFAULT 'new' AFTER browser_info;

-- Add admin_notes column
ALTER TABLE user_feedback 
ADD COLUMN admin_notes TEXT NULL AFTER status;

-- Add updated_at column
ALTER TABLE user_feedback 
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add indexes (will error if they exist - that's OK, just skip those errors)
ALTER TABLE user_feedback ADD INDEX idx_status (status);
ALTER TABLE user_feedback ADD INDEX idx_created_at (created_at);
ALTER TABLE user_feedback ADD INDEX idx_feedback_type (feedback_type);

-- Update existing records to have 'new' status
UPDATE user_feedback SET status = 'new' WHERE status IS NULL;
