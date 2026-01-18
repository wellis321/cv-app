-- Add multi-tenancy support for recruitment agencies
-- Migration: 20250114_add_organisations_multitenancy
-- Description: Creates organisations, organisation_members, and candidate_invitations tables
--              Updates profiles table with organisation-related fields

-- =====================================================
-- Create organisations table (recruitment agencies)
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
-- Create organisation_members table (recruiters, admins)
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
-- Create candidate_invitations table
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
-- Create team_invitations table (for inviting recruiters/admins)
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
-- Update profiles table with organisation fields
-- =====================================================

-- Add organisation_id column
SET @dbname = DATABASE();
SET @tablename = 'profiles';
SET @columnname = 'organisation_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(36) NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add account_type column (individual = self-registered B2C user, candidate = agency-managed)
SET @columnname = 'account_type';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(20) DEFAULT ''individual''')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add managed_by column (which recruiter manages this candidate)
SET @columnname = 'managed_by';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(36) NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add cv_visibility column
SET @columnname = 'cv_visibility';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(20) DEFAULT ''public''')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add cv_status column (draft, complete, published, archived)
SET @columnname = 'cv_status';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(20) DEFAULT ''draft''')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add invitation_token column (for candidates accepting invitations)
SET @columnname = 'invitation_token';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add index for organisation_id (run separately to avoid errors if it exists)
-- Note: Run this manually if needed:
-- CREATE INDEX idx_profiles_org ON profiles(organisation_id);
-- CREATE INDEX idx_profiles_account_type ON profiles(account_type);
-- CREATE INDEX idx_profiles_managed_by ON profiles(managed_by);
-- CREATE INDEX idx_profiles_cv_status ON profiles(cv_status);

-- =====================================================
-- Create activity_log table for audit trail
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
