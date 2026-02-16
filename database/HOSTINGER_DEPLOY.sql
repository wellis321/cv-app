-- =====================================================
-- B2B CV Builder - Migrations for Hostinger (run AFTER base schema)
-- =====================================================
-- 
-- INSTRUCTIONS:
-- 1. In Hostinger, create a new MySQL database via hPanel
-- 2. Open phpMyAdmin, select your database
-- 3. FIRST: Run database/complete_schema_for_hostinger.sql (creates all base tables)
-- 4. THEN: Run this file (adds migrations from Feb 2025)
--
-- Alternative: Export your local DB with mysqldump and import to Hostinger
-- (see DEPLOY_TO_HOSTINGER.md for details)
-- =====================================================

SET FOREIGN_KEY_CHECKS=0;

-- =====================================================
-- Migrations 20250201 - 20250210
-- =====================================================

-- 20250201: Add Keywords Support to Job Applications
ALTER TABLE job_applications 
ADD COLUMN extracted_keywords JSON NULL COMMENT 'Array of keywords extracted from job description',
ADD COLUMN selected_keywords JSON NULL COMMENT 'Array of keywords selected by user for CV generation';

-- 20250202: Job Application Questions table
CREATE TABLE IF NOT EXISTS job_application_questions (
    id VARCHAR(36) PRIMARY KEY,
    job_application_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    question_text TEXT NOT NULL,
    answer_text TEXT NULL,
    answer_instructions TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_job_application_id (job_application_id),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20250203: (answer_instructions already in CREATE above)

-- 20250204: Status 'interested' + closing date reminders
ALTER TABLE job_applications
MODIFY COLUMN status ENUM(
    'interested',
    'in_progress',
    'applied',
    'interviewing',
    'offered',
    'rejected',
    'accepted',
    'withdrawn'
) DEFAULT 'applied';

ALTER TABLE profiles ADD COLUMN closing_date_reminder_enabled TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE profiles ADD COLUMN closing_date_reminder_days VARCHAR(50) NOT NULL DEFAULT '7,3,1';

-- 20250205: Priority on job applications
ALTER TABLE job_applications
ADD COLUMN priority ENUM('low','medium','high') NULL DEFAULT NULL AFTER remote_type,
ADD INDEX idx_priority (priority);

-- 20250206: Job saver token (for browser extension)
-- Uses procedure to avoid error if column already exists
DROP PROCEDURE IF EXISTS add_job_saver_token_if_missing;
DELIMITER //
CREATE PROCEDURE add_job_saver_token_if_missing()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profiles' AND COLUMN_NAME = 'job_saver_token'
  ) THEN
    ALTER TABLE profiles ADD COLUMN job_saver_token VARCHAR(64) NULL DEFAULT NULL;
    ALTER TABLE profiles ADD UNIQUE KEY idx_profiles_job_saver_token (job_saver_token);
  END IF;
END//
DELIMITER ;
CALL add_job_saver_token_if_missing();
DROP PROCEDURE add_job_saver_token_if_missing;

-- 20250207: application_date nullable
ALTER TABLE job_applications
MODIFY COLUMN application_date TIMESTAMP NULL DEFAULT NULL;

-- 20250210: User feedback columns (skip if columns already exist)
ALTER TABLE user_feedback ADD COLUMN page_url TEXT NULL AFTER message;
ALTER TABLE user_feedback ADD COLUMN email VARCHAR(255) NULL AFTER page_url;
ALTER TABLE user_feedback ADD COLUMN user_agent TEXT NULL AFTER email;
ALTER TABLE user_feedback ADD COLUMN browser_info JSON NULL AFTER user_agent;
ALTER TABLE user_feedback ADD COLUMN status ENUM('new', 'reviewed', 'resolved', 'closed') DEFAULT 'new' AFTER browser_info;
ALTER TABLE user_feedback ADD COLUMN admin_notes TEXT NULL AFTER status;
ALTER TABLE user_feedback ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;
ALTER TABLE user_feedback ADD INDEX idx_status (status);
ALTER TABLE user_feedback ADD INDEX idx_created_at (created_at);
ALTER TABLE user_feedback ADD INDEX idx_feedback_type (feedback_type);

SET FOREIGN_KEY_CHECKS=1;
