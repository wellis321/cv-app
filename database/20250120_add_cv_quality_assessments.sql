-- Add CV Quality Assessments Table
-- Migration: 20250120_add_cv_quality_assessments
-- Description: Creates table for storing AI-generated CV quality assessments

CREATE TABLE IF NOT EXISTS cv_quality_assessments (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    cv_variant_id VARCHAR(36) NULL,
    overall_score INT,
    ats_score INT,
    content_score INT,
    formatting_score INT,
    keyword_match_score INT,
    recommendations JSON,
    strengths JSON,
    weaknesses JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_cv_variant_id (cv_variant_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

