<?php
/**
 * CV Data Loading Functions
 * Shared functions for loading CV data across different pages
 */

require_once __DIR__ . '/database.php';

/**
 * Load all CV data for a user
 */
function loadCvData($userId) {
    $cvData = [
        'profile' => null,
        'professional_summary' => null,
        'work_experience' => [],
        'education' => [],
        'skills' => [],
        'projects' => [],
        'certifications' => [],
        'memberships' => [],
        'interests' => [],
        'qualification_equivalence' => []
    ];

    // Load profile
    $cvData['profile'] = db()->fetchOne(
        "SELECT * FROM profiles WHERE id = ?",
        [$userId]
    );

    // Load professional summary
    $summary = db()->fetchOne(
        "SELECT * FROM professional_summary WHERE profile_id = ?",
        [$userId]
    );

    if ($summary) {
        $cvData['professional_summary'] = $summary;

        // Load strengths
        $cvData['professional_summary']['strengths'] = db()->fetchAll(
            "SELECT * FROM professional_summary_strengths
             WHERE professional_summary_id = ?
             ORDER BY sort_order ASC",
            [$summary['id']]
        );
    }

    // Load work experience
    $cvData['work_experience'] = db()->fetchAll(
        "SELECT * FROM work_experience
         WHERE profile_id = ?
         ORDER BY sort_order ASC, start_date DESC",
        [$userId]
    );

    // Load responsibilities for each work experience
    foreach ($cvData['work_experience'] as &$work) {
        $categories = db()->fetchAll(
            "SELECT * FROM responsibility_categories
             WHERE work_experience_id = ?
             ORDER BY sort_order ASC",
            [$work['id']]
        );

        foreach ($categories as &$category) {
            $category['items'] = db()->fetchAll(
                "SELECT * FROM responsibility_items
                 WHERE category_id = ?
                 ORDER BY sort_order ASC",
                [$category['id']]
            );
        }

        $work['responsibility_categories'] = $categories;
    }
    unset($work, $category);

    // Load education
    $cvData['education'] = db()->fetchAll(
        "SELECT * FROM education
         WHERE profile_id = ?
         ORDER BY start_date DESC",
        [$userId]
    );

    // Load skills
    $cvData['skills'] = db()->fetchAll(
        "SELECT * FROM skills
         WHERE profile_id = ?
         ORDER BY category ASC, name ASC",
        [$userId]
    );

    // Load projects
    $cvData['projects'] = db()->fetchAll(
        "SELECT * FROM projects
         WHERE profile_id = ?
         ORDER BY start_date DESC",
        [$userId]
    );

    // Load certifications
    $cvData['certifications'] = db()->fetchAll(
        "SELECT * FROM certifications
         WHERE profile_id = ?
         ORDER BY date_obtained DESC",
        [$userId]
    );

    // Load memberships
    $cvData['memberships'] = db()->fetchAll(
        "SELECT * FROM professional_memberships
         WHERE profile_id = ?
         ORDER BY start_date DESC",
        [$userId]
    );

    // Load interests
    $cvData['interests'] = db()->fetchAll(
        "SELECT * FROM interests
         WHERE profile_id = ?
         ORDER BY name ASC",
        [$userId]
    );

    // Load qualification equivalence
    $cvData['qualification_equivalence'] = db()->fetchAll(
        "SELECT * FROM professional_qualification_equivalence
         WHERE profile_id = ?
         ORDER BY level ASC",
        [$userId]
    );

    // Load supporting evidence for each qualification
    foreach ($cvData['qualification_equivalence'] as &$qual) {
        $qual['evidence'] = db()->fetchAll(
            "SELECT * FROM supporting_evidence
             WHERE qualification_equivalence_id = ?
             ORDER BY sort_order ASC",
            [$qual['id']]
        );
    }
    unset($qual);

    return $cvData;
}
