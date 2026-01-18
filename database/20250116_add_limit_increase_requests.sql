-- Add limit increase request system
-- Migration: 20250116_add_limit_increase_requests
-- Description: Creates table for organisation limit increase requests

-- =====================================================
-- Create limit_increase_requests table
-- =====================================================
CREATE TABLE IF NOT EXISTS limit_increase_requests (
    id VARCHAR(36) PRIMARY KEY,
    organisation_id VARCHAR(36) NOT NULL,
    requested_by VARCHAR(36) NOT NULL,
    request_type ENUM('candidates', 'team_members') NOT NULL,
    current_limit INT NOT NULL,
    requested_limit INT NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'denied', 'cancelled') DEFAULT 'pending',
    reviewed_by VARCHAR(36),
    reviewed_at DATETIME,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE,
    FOREIGN KEY (requested_by) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES profiles(id) ON DELETE SET NULL,

    INDEX idx_requests_org (organisation_id),
    INDEX idx_requests_status (status),
    INDEX idx_requests_type (request_type),
    INDEX idx_requests_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

