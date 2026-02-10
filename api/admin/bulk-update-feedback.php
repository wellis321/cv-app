<?php
/**
 * Bulk Update Feedback API
 * Allows super admins to update status or delete multiple feedback items at once
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

$action = sanitizeInput(post('action') ?? '');
$feedbackIds = post('ids') ?? [];

if (empty($action)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Action is required']);
    exit;
}

if (empty($feedbackIds) || !is_array($feedbackIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Feedback IDs are required']);
    exit;
}

// Sanitize all IDs
$feedbackIds = array_map('sanitizeInput', $feedbackIds);
$feedbackIds = array_filter($feedbackIds); // Remove empty values

if (empty($feedbackIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid feedback IDs provided']);
    exit;
}

try {
    $db = db();
    $db->beginTransaction();
    
    $successCount = 0;
    $errorCount = 0;
    
    if ($action === 'delete') {
        // Delete multiple feedback items
        $placeholders = implode(',', array_fill(0, count($feedbackIds), '?'));
        $db->query(
            "DELETE FROM user_feedback WHERE id IN ({$placeholders})",
            $feedbackIds
        );
        $successCount = count($feedbackIds);
        
        error_log("Bulk feedback deleted: IDs=" . implode(',', $feedbackIds) . ", Admin=" . getUserId());
        
    } elseif ($action === 'update_status') {
        // Update status for multiple feedback items
        $newStatus = sanitizeInput(post('status') ?? '');
        
        if (empty($newStatus)) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Status is required']);
            exit;
        }
        
        $validStatuses = ['new', 'reviewed', 'resolved', 'closed'];
        if (!in_array($newStatus, $validStatuses)) {
            $db->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            exit;
        }
        
        $placeholders = implode(',', array_fill(0, count($feedbackIds), '?'));
        $params = array_merge([$newStatus, date('Y-m-d H:i:s')], $feedbackIds);
        
        $db->query(
            "UPDATE user_feedback SET status = ?, updated_at = ? WHERE id IN ({$placeholders})",
            $params
        );
        $successCount = count($feedbackIds);
        
        error_log("Bulk feedback status updated: IDs=" . implode(',', $feedbackIds) . ", Status={$newStatus}, Admin=" . getUserId());
        
    } else {
        $db->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => $action === 'delete' 
            ? "Successfully deleted {$successCount} feedback item(s)."
            : "Successfully updated {$successCount} feedback item(s).",
        'count' => $successCount
    ]);
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    error_log("Bulk update feedback error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to process bulk action'
    ]);
}
