<?php
/**
 * API endpoint for creating, renaming, and deleting custom CV sections
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$userId = getUserId();
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'create') {
        $title = prepareForStorage($_POST['title'] ?? '');
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Title is required']);
            exit;
        }
        $id = generateUuid();
        // Use max sort_order + 1 so new sections appear at the bottom
        $maxOrder = db()->fetchOne(
            "SELECT COALESCE(MAX(sort_order), -1) as max_order FROM custom_sections WHERE profile_id = ?",
            [$userId]
        );
        $sortOrder = ($maxOrder['max_order'] ?? -1) + 1;
        db()->insert('custom_sections', [
            'id'         => $id,
            'profile_id' => $userId,
            'title'      => $title,
            'sort_order' => $sortOrder,
        ]);
        echo json_encode(['success' => true, 'id' => $id, 'title' => $title]);

    } elseif ($action === 'rename') {
        $id    = prepareForStorage($_POST['id'] ?? '');
        $title = prepareForStorage($_POST['title'] ?? '');
        if (empty($id) || empty($title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID and title are required']);
            exit;
        }
        $section = db()->fetchOne(
            "SELECT id FROM custom_sections WHERE id = ? AND profile_id = ?",
            [$id, $userId]
        );
        if (!$section) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Section not found']);
            exit;
        }
        db()->update('custom_sections', ['title' => $title], 'id = ? AND profile_id = ?', [$id, $userId]);
        echo json_encode(['success' => true, 'title' => $title]);

    } elseif ($action === 'delete') {
        $id = prepareForStorage($_POST['id'] ?? '');
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID is required']);
            exit;
        }
        $section = db()->fetchOne(
            "SELECT id FROM custom_sections WHERE id = ? AND profile_id = ?",
            [$id, $userId]
        );
        if (!$section) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Section not found']);
            exit;
        }
        // CASCADE in the DB will remove all child items
        db()->delete('custom_sections', 'id = ? AND profile_id = ?', [$id, $userId]);
        echo json_encode(['success' => true]);

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log('save-custom-section error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
}
