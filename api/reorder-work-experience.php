<?php
/**
 * API endpoint for reordering work experience entries
 */

header('Content-Type: application/json');

// Disable error display for production (but still log)
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../php/helpers.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check authentication (don't redirect, return JSON error)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();

// Verify CSRF token
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'reorder') {
        // Get the ordered list of IDs
        $orderedIds = json_decode($_POST['ordered_ids'] ?? '[]', true);

        if (empty($orderedIds) || !is_array($orderedIds)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid order data']);
            exit;
        }

        // Verify all IDs belong to the user
        $placeholders = implode(',', array_fill(0, count($orderedIds), '?'));
        $existingExperiences = db()->fetchAll(
            "SELECT id FROM work_experience WHERE id IN ($placeholders) AND profile_id = ?",
            array_merge($orderedIds, [$userId])
        );

        $existingIds = array_column($existingExperiences, 'id');
        if (count($existingIds) !== count($orderedIds)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid experience IDs']);
            exit;
        }

        // Update sort_order for each experience
        foreach ($orderedIds as $index => $id) {
            db()->update(
                'work_experience',
                ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ? AND profile_id = ?',
                [$id, $userId]
            );
        }

        echo json_encode(['success' => true]);
    } elseif ($action === 'reset') {
        // Reset to date-based order
        $experiences = db()->fetchAll(
            "SELECT id FROM work_experience WHERE profile_id = ? ORDER BY start_date DESC, created_at DESC",
            [$userId]
        );

        foreach ($experiences as $index => $exp) {
            db()->update(
                'work_experience',
                ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ? AND profile_id = ?',
                [$exp['id'], $userId]
            );
        }

        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Reorder work experience error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to reorder experiences']);
}
