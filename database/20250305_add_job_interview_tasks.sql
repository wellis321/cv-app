-- Add Interview Tasks to Job Applications
-- Migration: 20250305_add_job_interview_tasks
-- Description: Tasks, questions, or assignments given after progressing (e.g. when interviewing). AI helps prepare.

CREATE TABLE IF NOT EXISTS job_interview_tasks (
    id VARCHAR(36) PRIMARY KEY,
    job_application_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    task_type ENUM('question', 'assignment', 'presentation', 'case_study', 'other') DEFAULT 'question',
    title VARCHAR(255) NULL,
    task_description TEXT NOT NULL,
    deadline TIMESTAMP NULL,
    user_notes TEXT NULL,
    ai_suggestions TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_job_application_id (job_application_id),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
