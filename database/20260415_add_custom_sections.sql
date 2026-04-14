CREATE TABLE IF NOT EXISTS custom_sections (
    id VARCHAR(36) PRIMARY KEY,
    profile_id VARCHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_custom_sections_profile (profile_id),
    INDEX idx_custom_sections_sort (profile_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS custom_section_items (
    id VARCHAR(36) PRIMARY KEY,
    custom_section_id VARCHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    item_date VARCHAR(100),
    url TEXT,
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (custom_section_id) REFERENCES custom_sections(id) ON DELETE CASCADE,
    INDEX idx_custom_items_section (custom_section_id),
    INDEX idx_custom_items_sort (custom_section_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
