<?php
/**
 * Get Feedback Details API
 * Returns detailed feedback information for admin view
 */

require_once __DIR__ . '/../../php/helpers.php';

header('Content-Type: application/json');

// Require super admin access
if (!isLoggedIn() || !isSuperAdmin()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$feedbackId = sanitizeInput(get('id') ?? '');

if (empty($feedbackId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Feedback ID is required']);
    exit;
}

try {
    // Determine which column name to use (user_id or profile_id)
    $userIdColumn = 'user_id';
    try {
        $columns = db()->fetchAll("SHOW COLUMNS FROM user_feedback LIKE 'user_id'");
        if (empty($columns)) {
            $columns = db()->fetchAll("SHOW COLUMNS FROM user_feedback LIKE 'profile_id'");
            if (!empty($columns)) {
                $userIdColumn = 'profile_id';
            }
        }
    } catch (Exception $e) {
        // Default to user_id if check fails
        $userIdColumn = 'user_id';
    }
    
    $feedback = db()->fetchOne(
        "SELECT uf.*, 
                p.email as user_email, 
                p.full_name as user_name,
                p.username as user_username
         FROM user_feedback uf
         LEFT JOIN profiles p ON uf.{$userIdColumn} = p.id
         WHERE uf.id = ?",
        [$feedbackId]
    );
    
    if (!$feedback) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Feedback not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'feedback' => $feedback
    ]);
} catch (Exception $e) {
    error_log("Get feedback error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to retrieve feedback'
    ]);
}
