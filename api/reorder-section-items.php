<?php
/**
 * API endpoint for reordering items within a CV section (education, skills, projects,
 * professional memberships, interests, qualification equivalence).
 *
 * Work experience and certifications already have their own dedicated endpoints
 * (reorder-work-experience.php, reorder-certifications.php) - these six share one
 * generic endpoint since their reorder logic is identical.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../php/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();

$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

// Fixed whitelist - $table is only ever taken from here, never directly from user input.
$sectionTables = [
    'education' => 'education',
    'skills' => 'skills',
    'projects' => 'projects',
    'memberships' => 'professional_memberships',
    'interests' => 'interests',
    'qualification-equivalence' => 'professional_qualification_equivalence',
];

$sectionId = $_POST['section_id'] ?? '';
if (!isset($sectionTables[$sectionId])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid section']);
    exit;
}
$table = $sectionTables[$sectionId];

$orderedIds = json_decode($_POST['ordered_ids'] ?? '[]', true);
if (empty($orderedIds) || !is_array($orderedIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid order data']);
    exit;
}

try {
    $placeholders = implode(',', array_fill(0, count($orderedIds), '?'));
    $existing = db()->fetchAll(
        "SELECT id FROM {$table} WHERE id IN ($placeholders) AND profile_id = ?",
        array_merge($orderedIds, [$userId])
    );
    if (count($existing) !== count($orderedIds)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid item IDs']);
        exit;
    }

    foreach ($orderedIds as $index => $id) {
        db()->update(
            $table,
            ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
            'id = ? AND profile_id = ?',
            [$id, $userId]
        );
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('reorder-section-items error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to reorder']);
}
