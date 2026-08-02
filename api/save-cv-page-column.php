<?php
/**
 * API: Save which column (left/right) a section sits in on cv.php's Edit Mode.
 * Merges onto whatever's already saved so moving one section doesn't reset
 * everyone else's column assignment.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

// 'profile' (the header) is intentionally excluded - it never participates in section
// dragging, matching the same rule already enforced everywhere else.
$validSectionIds = [
    'professional-summary', 'work-experience', 'education', 'projects', 'skills',
    'certifications', 'qualification-equivalence', 'memberships', 'interests',
];

$sectionId = $_POST['section_id'] ?? '';
$column = $_POST['column'] ?? '';

if (!in_array($sectionId, $validSectionIds, true) && !preg_match('/^custom-[0-9a-f\-]{36}$/', $sectionId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid section ID']);
    exit;
}
if (!in_array($column, ['left', 'right'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid column']);
    exit;
}

$userId = getUserId();

try {
    $existing = [];
    $row = db()->fetchOne("SELECT cv_page_columns FROM profiles WHERE id = ?", [$userId]);
    if ($row && !empty($row['cv_page_columns'])) {
        $decoded = json_decode($row['cv_page_columns'], true);
        if (is_array($decoded)) $existing = $decoded;
    }
    $existing[$sectionId] = $column;

    db()->update('profiles', ['cv_page_columns' => json_encode($existing)], 'id = ?', [$userId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('save-cv-page-column error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save']);
}
