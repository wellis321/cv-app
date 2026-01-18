-- Add User AI API Keys Support
-- Migration: 20250125_add_user_ai_api_keys
-- Description: Adds columns for user-provided API keys (encrypted) and browser AI preferences

ALTER TABLE profiles 
ADD COLUMN openai_api_key TEXT NULL COMMENT 'Encrypted OpenAI API key (user-provided)',
ADD COLUMN anthropic_api_key TEXT NULL COMMENT 'Encrypted Anthropic API key (user-provided)',
ADD COLUMN browser_ai_model VARCHAR(100) DEFAULT NULL COMMENT 'Browser AI model preference (webllm or tensorflow.js model name)';

-- Update ai_service_preference to support 'browser' option
-- Note: This may require manual enum update if ai_service_preference is an enum type
-- If it's VARCHAR, this migration will work fine

-- Index for faster lookups
ALTER TABLE profiles
ADD INDEX idx_ai_service_preference (ai_service_preference);

