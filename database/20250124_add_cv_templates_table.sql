-- Add CV Templates System
-- Migration: 20250124_add_cv_templates_table
-- Description: Creates a table for storing multiple CV templates per user (replaces single template in profiles table)

-- Create cv_templates table
CREATE TABLE IF NOT EXISTS cv_templates (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    template_name VARCHAR(255) NOT NULL DEFAULT 'Untitled Template',
    template_html TEXT NOT NULL,
    template_css TEXT NULL,
    template_description TEXT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (user_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing templates from profiles table
INSERT INTO cv_templates (id, user_id, template_name, template_html, template_css, template_description, is_active, created_at, updated_at)
SELECT 
    UUID() as id,
    id as user_id,
    'Custom Template' as template_name,
    custom_cv_template_html as template_html,
    custom_cv_template_css as template_css,
    custom_cv_template_description as template_description,
    custom_cv_template_active as is_active,
    NOW() as created_at,
    NOW() as updated_at
FROM profiles
WHERE custom_cv_template_html IS NOT NULL AND custom_cv_template_html != '';

