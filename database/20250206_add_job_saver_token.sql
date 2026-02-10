-- Job saver token: used by browser extension (and other clients) to quick-add jobs without session.
-- User copies token from app into extension; extension sends it as Bearer token when saving a job.
-- Idempotent: safe to run multiple times (only adds column/key if missing).

DROP PROCEDURE IF EXISTS add_job_saver_token_if_missing;
DELIMITER //
CREATE PROCEDURE add_job_saver_token_if_missing()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profiles' AND COLUMN_NAME = 'job_saver_token'
  ) THEN
    ALTER TABLE profiles ADD COLUMN job_saver_token VARCHAR(64) NULL DEFAULT NULL;
    ALTER TABLE profiles ADD UNIQUE KEY idx_profiles_job_saver_token (job_saver_token);
  END IF;
END//
DELIMITER ;
CALL add_job_saver_token_if_missing();
DROP PROCEDURE add_job_saver_token_if_missing;
