-- Add subscription-related fields to profiles
ALTER TABLE profiles
    ADD COLUMN IF NOT EXISTS plan VARCHAR(32) NOT NULL DEFAULT 'free',
    ADD COLUMN IF NOT EXISTS subscription_status VARCHAR(32) DEFAULT 'inactive',
    ADD COLUMN IF NOT EXISTS subscription_current_period_end DATETIME NULL,
    ADD COLUMN IF NOT EXISTS stripe_customer_id VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS stripe_subscription_id VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS subscription_cancel_at DATETIME NULL;
