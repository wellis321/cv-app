-- Add Remote Work Story Submissions Table
-- Migration: 20250118_add_remote_work_stories
-- Description: Creates table to store remote work story submissions

CREATE TABLE IF NOT EXISTS remote_work_stories (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NULL,
    category VARCHAR(100) NULL,
    story TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'featured') DEFAULT 'pending',
    reviewed_by VARCHAR(36) NULL,
    review_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_email (email),
    FOREIGN KEY (reviewed_by) REFERENCES profiles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

