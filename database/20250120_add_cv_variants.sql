-- Add CV Variants System
-- Migration: 20250120_add_cv_variants
-- Description: Creates tables for CV variants (job-specific CVs) and variant data tables

-- =====================================================
-- CV Variants Table
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variants (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    job_application_id VARCHAR(36) NULL,
    variant_name VARCHAR(255) NOT NULL,
    is_master BOOLEAN DEFAULT FALSE,
    created_from_variant_id VARCHAR(36) NULL,
    ai_generated BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE SET NULL,
    FOREIGN KEY (created_from_variant_id) REFERENCES cv_variants(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_job_application_id (job_application_id),
    INDEX idx_is_master (user_id, is_master)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Professional Summary
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_professional_summary (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cv_variant_professional_summary_strengths (
    id VARCHAR(36) PRIMARY KEY,
    professional_summary_id VARCHAR(36) NOT NULL,
    strength VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professional_summary_id) REFERENCES cv_variant_professional_summary(id) ON DELETE CASCADE,
    INDEX idx_professional_summary_id (professional_summary_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Work Experience
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_work_experience (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_work_experience_id VARCHAR(36) NULL,
    company_name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    description TEXT,
    sort_order INT DEFAULT 0,
    hide_date BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_work_experience_id) REFERENCES work_experience(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id),
    INDEX idx_sort_order (cv_variant_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cv_variant_responsibility_categories (
    id VARCHAR(36) PRIMARY KEY,
    work_experience_id VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_experience_id) REFERENCES cv_variant_work_experience(id) ON DELETE CASCADE,
    INDEX idx_work_experience_id (work_experience_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cv_variant_responsibility_items (
    id VARCHAR(36) PRIMARY KEY,
    category_id VARCHAR(36) NOT NULL,
    content TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES cv_variant_responsibility_categories(id) ON DELETE CASCADE,
    INDEX idx_category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Education
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_education (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_education_id VARCHAR(36) NULL,
    institution VARCHAR(255) NOT NULL,
    degree VARCHAR(255) NOT NULL,
    field_of_study VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_education_id) REFERENCES education(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Projects
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_projects (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_project_id VARCHAR(36) NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    url TEXT,
    image_url TEXT,
    image_path TEXT,
    image_responsive JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Skills
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_skills (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_skill_id VARCHAR(36) NULL,
    name VARCHAR(255) NOT NULL,
    level VARCHAR(50),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_skill_id) REFERENCES skills(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Certifications
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_certifications (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_certification_id VARCHAR(36) NULL,
    name VARCHAR(255) NOT NULL,
    issuer VARCHAR(255) NOT NULL,
    date_obtained DATE NOT NULL,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_certification_id) REFERENCES certifications(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Professional Memberships
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_memberships (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_membership_id VARCHAR(36) NULL,
    organisation VARCHAR(255) NOT NULL,
    role VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_membership_id) REFERENCES professional_memberships(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Interests
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_interests (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_interest_id VARCHAR(36) NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_interest_id) REFERENCES interests(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CV Variant Qualification Equivalence
-- =====================================================
CREATE TABLE IF NOT EXISTS cv_variant_qualification_equivalence (
    id VARCHAR(36) PRIMARY KEY,
    cv_variant_id VARCHAR(36) NOT NULL,
    original_qualification_id VARCHAR(36) NULL,
    level VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cv_variant_id) REFERENCES cv_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (original_qualification_id) REFERENCES professional_qualification_equivalence(id) ON DELETE SET NULL,
    INDEX idx_cv_variant_id (cv_variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cv_variant_supporting_evidence (
    id VARCHAR(36) PRIMARY KEY,
    qualification_equivalence_id VARCHAR(36) NOT NULL,
    content TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (qualification_equivalence_id) REFERENCES cv_variant_qualification_equivalence(id) ON DELETE CASCADE,
    INDEX idx_qualification_equivalence_id (qualification_equivalence_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

