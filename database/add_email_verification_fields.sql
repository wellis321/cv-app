-- Add email verification fields to profiles table
ALTER TABLE profiles
ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS verification_token VARCHAR(64) NULL,
ADD COLUMN IF NOT EXISTS verification_token_expires_at DATETIME NULL,
ADD INDEX idx_profiles_verification_token (verification_token);
