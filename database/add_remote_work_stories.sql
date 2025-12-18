-- Add remote_work_stories table for collecting user success stories
CREATE TABLE IF NOT EXISTS remote_work_stories (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    story TEXT NOT NULL,
    job_category VARCHAR(100),
    salary_range VARCHAR(100),
    approved BOOLEAN DEFAULT FALSE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_stories_approved (approved),
    INDEX idx_stories_featured (featured),
    INDEX idx_stories_category (job_category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
