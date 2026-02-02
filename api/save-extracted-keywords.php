<?php
/**
 * API endpoint to save extracted keywords (used by browser AI execution)
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
    $extractedKeywords = post('extracted_keywords'); // JSON array
    
    if (!$applicationId) {
        throw new Exception('Application ID is required');
    }
    
    // Verify job application belongs to user
    $job = db()->fetchOne(
        "SELECT id FROM job_applications WHERE id = ? AND user_id = ?",
        [$applicationId, $userId]
    );
    
    if (!$job) {
        throw new Exception('Job application not found');
    }
    
    // Parse and validate extracted keywords
    if (is_string($extractedKeywords)) {
        $extractedKeywords = json_decode($extractedKeywords, true);
    }
    if (!is_array($extractedKeywords)) {
        $extractedKeywords = [];
    }
    
    // Save extracted keywords
    db()->update(
        'job_applications',
        ['extracted_keywords' => json_encode(array_values($extractedKeywords))],
        'id = ? AND user_id = ?',
        [$applicationId, $userId]
    );
    
    echo json_encode([
        'success' => true,
        'keywords' => $extractedKeywords
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
