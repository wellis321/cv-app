-- Migration: Add Stripe webhook events table for idempotency tracking
-- This prevents duplicate processing of webhook events

CREATE TABLE IF NOT EXISTS stripe_webhook_events (
    id VARCHAR(255) PRIMARY KEY,  -- Stripe event ID (evt_xxx)
    event_type VARCHAR(100) NOT NULL,
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_processed_at (processed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clean up old events after 30 days (run periodically via cron)
-- DELETE FROM stripe_webhook_events WHERE processed_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
