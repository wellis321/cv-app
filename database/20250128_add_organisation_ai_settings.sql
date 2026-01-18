-- Add Organisation-Level AI Settings
-- Migration: 20250128_add_organisation_ai_settings
-- Description: Adds AI service configuration columns to organisations table
--              Allows organizations to provide AI access to all members

ALTER TABLE organisations
ADD COLUMN org_ai_service_preference VARCHAR(50) NULL COMMENT 'AI service type (openai, anthropic, gemini, grok, ollama, browser)',
ADD COLUMN org_openai_api_key TEXT NULL COMMENT 'Encrypted OpenAI API key (organisation-provided)',
ADD COLUMN org_anthropic_api_key TEXT NULL COMMENT 'Encrypted Anthropic API key (organisation-provided)',
ADD COLUMN org_gemini_api_key TEXT NULL COMMENT 'Encrypted Google Gemini API key (organisation-provided)',
ADD COLUMN org_grok_api_key TEXT NULL COMMENT 'Encrypted xAI Grok API key (organisation-provided)',
ADD COLUMN org_ollama_base_url VARCHAR(255) NULL COMMENT 'Ollama server URL for organisation',
ADD COLUMN org_ollama_model VARCHAR(100) NULL COMMENT 'Ollama model name for organisation',
ADD COLUMN org_browser_ai_model VARCHAR(100) NULL COMMENT 'Browser AI model preference for organisation',
ADD COLUMN org_ai_enabled BOOLEAN DEFAULT FALSE COMMENT 'Enable/disable organisation AI for members';

-- Add index for faster lookups
ALTER TABLE organisations
ADD INDEX idx_org_ai_enabled (org_ai_enabled);

