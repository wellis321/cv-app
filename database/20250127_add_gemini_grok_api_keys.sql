-- Add Gemini and Grok API Keys Support
-- Migration: 20250127_add_gemini_grok_api_keys
-- Description: Adds columns for user-provided Gemini and Grok API keys (encrypted)

ALTER TABLE profiles 
ADD COLUMN gemini_api_key TEXT NULL COMMENT 'Encrypted Google Gemini API key (user-provided)',
ADD COLUMN grok_api_key TEXT NULL COMMENT 'Encrypted xAI Grok API key (user-provided)';

