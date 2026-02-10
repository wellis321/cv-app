<?php
/**
 * Update Feedback API
 * Allows super admins to update feedback status and admin notes
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
$status = sanitizeInput(post('status') ?? '');
$adminNotes = prepareForStorage(post('admin_notes') ?? '');

if (empty($feedbackId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Feedback ID is required']);
    exit;
}

// Validate status
$validStatuses = ['new', 'reviewed', 'resolved', 'closed'];
if (!empty($status) && !in_array($status, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
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
    
    // Build update array
    $updateData = [];
    if (!empty($status)) {
        $updateData['status'] = $status;
    }
    if ($adminNotes !== null) {
        $updateData['admin_notes'] = $adminNotes;
    }
    $updateData['updated_at'] = date('Y-m-d H:i:s');
    
    // Update feedback
    db()->update('user_feedback', $updateData, 'id = ?', [$feedbackId]);
    
    // Log the update
    error_log("Feedback updated: ID={$feedbackId}, Status={$status}, Admin=" . getUserId());
    
    echo json_encode([
        'success' => true,
        'message' => 'Feedback updated successfully'
    ]);
} catch (Exception $e) {
    error_log("Update feedback error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update feedback'
    ]);
}
