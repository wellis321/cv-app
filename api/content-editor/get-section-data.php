<?php
/**
 * API endpoint to get section data (entries list)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../php/helpers.php';
require_once __DIR__ . '/../../php/cv-data.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$sectionId = $_GET['section_id'] ?? '';

if (empty($sectionId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Section ID required']);
    exit;
}

try {
    $data = [];
    
    switch ($sectionId) {
        case 'professional-summary':
            $summary = db()->fetchOne("SELECT * FROM professional_summary WHERE profile_id = ?", [$userId]);
            if ($summary) {
                $summary['strengths'] = db()->fetchAll(
                    "SELECT * FROM professional_summary_strengths WHERE professional_summary_id = ? ORDER BY sort_order ASC",
                    [$summary['id']]
                );
            }
            $data = $summary ?: null;
            break;
            
        case 'work-experience':
            $data = db()->fetchAll(
                "SELECT * FROM work_experience WHERE profile_id = ? ORDER BY sort_order ASC, start_date DESC",
                [$userId]
            );
            // Load responsibilities for each
            foreach ($data as &$work) {
                $categories = db()->fetchAll(
                    "SELECT * FROM responsibility_categories WHERE work_experience_id = ? ORDER BY sort_order ASC",
                    [$work['id']]
                );
                foreach ($categories as &$category) {
                    $category['items'] = db()->fetchAll(
                        "SELECT * FROM responsibility_items WHERE category_id = ? ORDER BY sort_order ASC",
                        [$category['id']]
                    );
                }
                $work['responsibility_categories'] = $categories;
            }
            break;
            
        case 'education':
            $data = db()->fetchAll(
                "SELECT * FROM education WHERE profile_id = ? ORDER BY start_date DESC",
                [$userId]
            );
            break;
            
        case 'skills':
            $data = db()->fetchAll(
                "SELECT * FROM skills WHERE profile_id = ? ORDER BY category ASC, name ASC",
                [$userId]
            );
            break;
            
        case 'projects':
            $data = db()->fetchAll(
                "SELECT * FROM projects WHERE profile_id = ? ORDER BY start_date DESC",
                [$userId]
            );
            break;
            
        case 'certifications':
            $data = db()->fetchAll(
                "SELECT * FROM certifications WHERE profile_id = ? ORDER BY date_obtained DESC",
                [$userId]
            );
            break;
            
        case 'memberships':
            $data = db()->fetchAll(
                "SELECT * FROM professional_memberships WHERE profile_id = ? ORDER BY start_date DESC",
                [$userId]
            );
            break;
            
        case 'interests':
            $data = db()->fetchAll(
                "SELECT * FROM interests WHERE profile_id = ? ORDER BY created_at ASC",
                [$userId]
            );
            break;
            
        case 'qualification-equivalence':
            $data = db()->fetchAll(
                "SELECT * FROM professional_qualification_equivalence WHERE profile_id = ? ORDER BY created_at ASC",
                [$userId]
            );
            // Load evidence for each
            foreach ($data as &$qual) {
                $qual['evidence'] = db()->fetchAll(
                    "SELECT * FROM supporting_evidence WHERE qualification_equivalence_id = ? ORDER BY sort_order ASC",
                    [$qual['id']]
                );
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid section ID']);
            exit;
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    error_log("Get section data error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load section data']);
}
