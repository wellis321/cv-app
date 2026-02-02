<?php
/**
 * API endpoint to save selected keywords for a job application
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
    $selectedKeywords = post('selected_keywords'); // JSON array
    
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
    
    // Parse and validate selected keywords
    if (is_string($selectedKeywords)) {
        $selectedKeywords = json_decode($selectedKeywords, true);
    }
    if (!is_array($selectedKeywords)) {
        $selectedKeywords = [];
    }
    
    // Get extracted keywords to validate against
    $jobData = db()->fetchOne(
        "SELECT extracted_keywords FROM job_applications WHERE id = ? AND user_id = ?",
        [$applicationId, $userId]
    );
    
    $extractedKeywords = [];
    if (!empty($jobData['extracted_keywords'])) {
        $extractedKeywords = json_decode($jobData['extracted_keywords'], true);
        if (!is_array($extractedKeywords)) {
            $extractedKeywords = [];
        }
    }
    
    // Filter selected keywords to only include those that were extracted
    $validSelectedKeywords = array_intersect($selectedKeywords, $extractedKeywords);
    
    // Save selected keywords
    db()->update(
        'job_applications',
        ['selected_keywords' => json_encode(array_values($validSelectedKeywords))],
        'id = ? AND user_id = ?',
        [$applicationId, $userId]
    );
    
    echo json_encode([
        'success' => true,
        'selected_keywords' => $validSelectedKeywords
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
