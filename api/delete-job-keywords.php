<?php
/**
 * API endpoint to delete/clear extracted and selected keywords for a job application
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

try {
    $userId = getUserId();
    $applicationId = post('application_id');

    if (!$applicationId) {
        throw new Exception('Application ID is required');
    }

    $job = db()->fetchOne(
        "SELECT id FROM job_applications WHERE id = ? AND user_id = ?",
        [$applicationId, $userId]
    );

    if (!$job) {
        throw new Exception('Job application not found');
    }

    db()->update(
        'job_applications',
        [
            'extracted_keywords' => null,
            'selected_keywords' => null
        ],
        'id = ? AND user_id = ?',
        [$applicationId, $userId]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Keywords cleared'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
