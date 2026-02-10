-- Add User Feedback Table
-- Migration: 20250125_add_user_feedback
-- Description: Creates table to store user feedback submissions from any page

CREATE TABLE IF NOT EXISTS user_feedback (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NULL,
    feedback_type ENUM('bug', 'spelling', 'feature_request', 'personal_issue', 'other') NOT NULL,
    message TEXT NOT NULL,
    email VARCHAR(255) NULL,
    page_url TEXT NULL,
    user_agent TEXT NULL,
    browser_info JSON NULL,
    status ENUM('new', 'reviewed', 'resolved', 'closed') DEFAULT 'new',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    INDEX idx_feedback_type (feedback_type),
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
