<?php
/**
 * API: Save CV section display order for the logged-in user.
 * POST { section_order: ["professional-summary", "work-experience", ...] }
 */

require_once __DIR__ . '/../php/helpers.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$validSectionIds = [
    'professional-summary',
    'work-experience',
    'education',
    'projects',
    'skills',
    'certifications',
    'qualification-equivalence',
    'memberships',
    'interests',
];

try {
    $userId = getUserId();
    $raw = post('section_order');

    // Accept JSON string or already-decoded array
    if (is_string($raw)) {
        $sectionOrder = json_decode($raw, true);
    } else {
        $sectionOrder = $raw;
    }

    if (!is_array($sectionOrder)) {
        http_response_code(400);
        echo json_encode(['error' => 'section_order must be an array']);
        exit;
    }

    // Validate all submitted IDs are recognised (standard sections or custom-<uuid>)
    foreach ($sectionOrder as $id) {
        if (!in_array($id, $validSectionIds, true) && !preg_match('/^custom-[0-9a-f\-]{36}$/', $id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Unknown section ID: ' . $id]);
            exit;
        }
    }

    db()->update('profiles',
        ['section_order' => json_encode(array_values($sectionOrder))],
        'id = ?',
        [$userId]
    );

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
