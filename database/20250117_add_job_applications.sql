-- Add Job Application Tracking System
-- Migration: 20250117_add_job_applications
-- Description: Creates tables for job application tracking (integrated from job-tracker app)

-- =====================================================
-- Applications Table
-- =====================================================
CREATE TABLE IF NOT EXISTS job_applications (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    job_description TEXT,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('applied', 'interviewing', 'offered', 'rejected', 'accepted', 'withdrawn', 'in_progress') DEFAULT 'applied',
    salary_range VARCHAR(100),
    job_location VARCHAR(255),
    remote_type ENUM('onsite', 'hybrid', 'remote') DEFAULT 'onsite',
    application_url TEXT,
    notes TEXT,
    next_follow_up TIMESTAMP NULL,
    had_interview BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_application_date (application_date),
    INDEX idx_next_follow_up (next_follow_up),
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Job Application Files Table
-- =====================================================
CREATE TABLE IF NOT EXISTS job_application_files (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    application_id VARCHAR(36) NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    custom_name VARCHAR(255),
    mime_type VARCHAR(100) NOT NULL,
    size INT NOT NULL,
    file_purpose ENUM('resume', 'cover_letter', 'portfolio', 'other') DEFAULT 'other',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_application_id (application_id),
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES job_applications(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Job Application Audit Logs Table
-- =====================================================
CREATE TABLE IF NOT EXISTS job_application_audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(36),
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id VARCHAR(36) NOT NULL,
    old_values JSON,
    new_values JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_action (user_id, action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

