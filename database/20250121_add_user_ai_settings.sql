-- Add user AI settings for local Ollama configuration
-- Migration: 20250121_add_user_ai_settings
-- Description: Adds columns to profiles table for user-specific AI/Ollama settings
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE ADD COLUMN
-- If columns already exist, you'll get an error - that's okay, just ignore it

ALTER TABLE profiles 
ADD COLUMN ai_service_preference VARCHAR(20) DEFAULT NULL COMMENT 'User preferred AI service: ollama, openai, anthropic, or null for default',
ADD COLUMN ollama_base_url VARCHAR(255) DEFAULT NULL COMMENT 'User-specific Ollama base URL (e.g., http://localhost:11434)',
ADD COLUMN ollama_model VARCHAR(100) DEFAULT NULL COMMENT 'User-specific Ollama model name (e.g., llama3.2)';
