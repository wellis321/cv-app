-- Add columns used for password reset flow
ALTER TABLE profiles
    ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(64),
    ADD COLUMN IF NOT EXISTS password_reset_expires_at DATETIME;
