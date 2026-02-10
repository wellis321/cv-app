<?php
/**
 * Delete Feedback API
 * Allows super admins to delete feedback submissions
 */

require_once __DIR__ . '/../../php/helpers.php';

header('Content-Type: application/json');

// Require super admin access
if (!isLoggedIn() || !isSuperAdmin()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
$token = post(CSRF_TOKEN_NAME);
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$feedbackId = sanitizeInput(post('id') ?? '');

if (empty($feedbackId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Feedback ID is required']);
    exit;
}

try {
    // Check if feedback exists
    $existing = db()->fetchOne(
        "SELECT id FROM user_feedback WHERE id = ?",
        [$feedbackId]
    );
    
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Feedback not found']);
        exit;
    }
    
    // Delete feedback
    db()->delete('user_feedback', 'id = ?', [$feedbackId]);
    
    // Log the deletion
    error_log("Feedback deleted: ID={$feedbackId}, Admin=" . getUserId());
    
    echo json_encode([
        'success' => true,
        'message' => 'Feedback deleted successfully'
    ]);
} catch (Exception $e) {
    error_log("Delete feedback error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete feedback'
    ]);
}
