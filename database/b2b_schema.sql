-- B2B CV Builder Application - Complete Database Schema
-- For recruitment agencies managing candidate CVs
--
-- Usage: mysql -u username -p database_name < database/b2b_schema.sql

-- =====================================================
-- PROFILES TABLE (User accounts with CV profile data)
-- Includes organisation-related fields for multi-tenancy
-- =====================================================
CREATE TABLE IF NOT EXISTS profiles (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    username VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(50),
    location VARCHAR(255),
    linkedin_url TEXT,
    bio TEXT,
    photo_url TEXT,
    cv_header_from_color VARCHAR(50) DEFAULT '#4338ca',
    cv_header_to_color VARCHAR(50) DEFAULT '#7e22ce',
    date_format_preference VARCHAR(20) DEFAULT 'dd/mm/yyyy',

    -- Email verification
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    email_verification_expires DATETIME,

    -- Password reset
    password_reset_token VARCHAR(255),
    password_reset_expires DATETIME,

    -- Profile photo visibility
    show_photo BOOLEAN DEFAULT TRUE,
    show_photo_pdf BOOLEAN DEFAULT TRUE,
    show_qr_code BOOLEAN DEFAULT FALSE,

    -- Subscription fields (for individual users - legacy B2C support)
    plan VARCHAR(50) DEFAULT 'free',
    subscription_status VARCHAR(50) DEFAULT 'inactive',
    stripe_customer_id VARCHAR(255),
    stripe_subscription_id VARCHAR(255),
    subscription_current_period_end DATETIME,
    subscription_cancel_at DATETIME,
    template_preference VARCHAR(50) DEFAULT 'classic',

    -- Organisation/Multi-tenancy fields
    organisation_id VARCHAR(36),                   -- Links to organisations table
    account_type VARCHAR(20) DEFAULT 'individual', -- 'individual' or 'candidate'
    managed_by VARCHAR(36),                        -- Recruiter managing this candidate
    cv_visibility VARCHAR(20) DEFAULT 'public',   -- 'private', 'organisation', 'public'
    cv_status VARCHAR(20) DEFAULT 'draft',        -- 'draft', 'complete', 'published', 'archived'
    invitation_token VARCHAR(255),                 -- For accepting invitations

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_profiles_email (email),
    INDEX idx_profiles_username (username),
    INDEX idx_profiles_organisation (organisation_id),
    INDEX idx_profiles_account_type (account_type),
    INDEX idx_profiles_managed_by (managed_by),
    INDEX idx_profiles_cv_status (cv_status),
    CONSTRAINT username_format CHECK (username REGEXP '^[a-z0-9][a-z0-9\\-_]+$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORGANISATIONS TABLE (Recruitment agencies)
-- =====================================================
CREATE TABLE IF NOT EXISTS organisations (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,           -- URL-friendly identifier e.g., 'acme-recruiting'
    custom_domain VARCHAR(255) UNIQUE,           -- e.g., 'careers.acmecorp.com'
    logo_url TEXT,
    primary_colour VARCHAR(7) DEFAULT '#4338ca', -- Branding colour
    secondary_colour VARCHAR(7) DEFAULT '#7e22ce',

    -- Subscription fields (organisation-level billing)
    plan VARCHAR(50) DEFAULT 'agency_basic',
    subscription_status VARCHAR(50) DEFAULT 'inactive',
    stripe_customer_id VARCHAR(255),
    stripe_subscription_id VARCHAR(255),
    subscription_current_period_end DATETIME,
    subscription_cancel_at DATETIME,
    max_candidates INT DEFAULT 10,               -- Limit based on plan
    max_team_members INT DEFAULT 3,              -- Limit based on plan

    -- Settings
    default_cv_visibility ENUM('private', 'organisation', 'public') DEFAULT 'organisation',
    allow_candidate_self_registration BOOLEAN DEFAULT FALSE,
    require_candidate_approval BOOLEAN DEFAULT TRUE,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_organisations_slug (slug),
    INDEX idx_organisations_custom_domain (custom_domain),
    INDEX idx_organisations_subscription_status (subscription_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORGANISATION MEMBERS TABLE (Recruiters, Admins)
-- =====================================================
CREATE TABLE IF NOT EXISTS organisation_members (
    id VARCHAR(36) PRIMARY KEY,
    organisation_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    role ENUM('owner', 'admin', 'recruiter', 'viewer') NOT NULL DEFAULT 'recruiter',
    is_active BOOLEAN DEFAULT TRUE,
    invited_by VARCHAR(36),                      -- Who invited this member
    joined_at TIMESTAMP,                         -- When they accepted the invitation
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES profiles(id) ON DELETE SET NULL,

    UNIQUE KEY unique_org_user (organisation_id, user_id),
    INDEX idx_org_members_org (organisation_id),
    INDEX idx_org_members_user (user_id),
    INDEX idx_org_members_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CANDIDATE INVITATIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS candidate_invitations (
    id VARCHAR(36) PRIMARY KEY,
    organisation_id VARCHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),                      -- Pre-fill candidate name if known
    invited_by VARCHAR(36) NOT NULL,
    assigned_recruiter VARCHAR(36),              -- Optionally assign to a recruiter
    token VARCHAR(255) UNIQUE NOT NULL,
    message TEXT,                                -- Custom invitation message
    expires_at TIMESTAMP NOT NULL,
    accepted_at TIMESTAMP,
    accepted_by VARCHAR(36),                     -- The profile ID who accepted
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_recruiter) REFERENCES profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (accepted_by) REFERENCES profiles(id) ON DELETE SET NULL,

    INDEX idx_invitations_org (organisation_id),
    INDEX idx_invitations_email (email),
    INDEX idx_invitations_token (token),
    INDEX idx_invitations_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TEAM INVITATIONS TABLE (For inviting recruiters/admins)
-- =====================================================
CREATE TABLE IF NOT EXISTS team_invitations (
    id VARCHAR(36) PRIMARY KEY,
    organisation_id VARCHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('admin', 'recruiter', 'viewer') NOT NULL DEFAULT 'recruiter',
    invited_by VARCHAR(36) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    message TEXT,
    expires_at TIMESTAMP NOT NULL,
    accepted_at TIMESTAMP,
    accepted_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (accepted_by) REFERENCES profiles(id) ON DELETE SET NULL,

    INDEX idx_team_inv_org (organisation_id),
    INDEX idx_team_inv_email (email),
    INDEX idx_team_inv_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTIVITY LOG TABLE (Audit trail)
-- =====================================================
CREATE TABLE IF NOT EXISTS activity_log (
    id VARCHAR(36) PRIMARY KEY,
    organisation_id VARCHAR(36),
    user_id VARCHAR(36),
    target_user_id VARCHAR(36),                  -- The candidate/user being acted upon
    action VARCHAR(100) NOT NULL,                -- e.g., 'candidate.invited', 'cv.viewed', 'cv.exported'
    details JSON,                                -- Additional context
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (target_user_id) REFERENCES profiles(id) ON DELETE SET NULL,

    INDEX idx_activity_org (organisation_id),
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_target (target_user_id),
    INDEX idx_activity_action (action),
    INDEX idx_activity_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- WORK EXPERIENCE TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS work_experience (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    description TEXT,
    sort_order INT DEFAULT 0,
    hide_date BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_work_experience_profile (profile_id),
    INDEX idx_work_experience_sort (profile_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RESPONSIBILITY CATEGORIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS responsibility_categories (
    id VARCHAR(36) PRIMARY KEY,
    work_experience_id VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_experience_id) REFERENCES work_experience(id) ON DELETE CASCADE,
    INDEX idx_resp_cat_work_exp (work_experience_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RESPONSIBILITY ITEMS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS responsibility_items (
    id VARCHAR(36) PRIMARY KEY,
    category_id VARCHAR(36) NOT NULL,
    content TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES responsibility_categories(id) ON DELETE CASCADE,
    INDEX idx_resp_items_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROJECTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS projects (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    url TEXT,
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_projects_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- EDUCATION TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS education (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    degree VARCHAR(255) NOT NULL,
    field_of_study VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_education_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SKILLS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS skills (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    level VARCHAR(50),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_skills_profile (profile_id),
    INDEX idx_skills_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CERTIFICATIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS certifications (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    issuer VARCHAR(255) NOT NULL,
    date_obtained DATE NOT NULL,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_certifications_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROFESSIONAL MEMBERSHIPS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS professional_memberships (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    organisation VARCHAR(255) NOT NULL,
    role VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_memberships_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INTERESTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS interests (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_interests_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROFESSIONAL QUALIFICATION EQUIVALENCE TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS professional_qualification_equivalence (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    level VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_qual_equiv_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SUPPORTING EVIDENCE TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS supporting_evidence (
    id VARCHAR(36) PRIMARY KEY,
    qualification_equivalence_id VARCHAR(36) NOT NULL,
    content TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (qualification_equivalence_id) REFERENCES professional_qualification_equivalence(id) ON DELETE CASCADE,
    INDEX idx_evidence_qual (qualification_equivalence_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROFESSIONAL SUMMARY TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS professional_summary (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_prof_summary_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROFESSIONAL SUMMARY STRENGTHS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS professional_summary_strengths (
    id VARCHAR(36) PRIMARY KEY,
    professional_summary_id VARCHAR(36) NOT NULL,
    strength VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professional_summary_id) REFERENCES professional_summary(id) ON DELETE CASCADE,
    INDEX idx_strengths_summary (professional_summary_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER FEEDBACK TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS user_feedback (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36),
    feedback_type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL,
    INDEX idx_feedback_profile (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Add foreign key for profiles.organisation_id
-- (Added after organisations table exists)
-- =====================================================
ALTER TABLE profiles ADD CONSTRAINT fk_profiles_organisation
    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE SET NULL;
