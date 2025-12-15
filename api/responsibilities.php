<?php
/**
 * API endpoint for managing responsibility categories and items
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

// Check authentication (don't redirect, return JSON error)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();

// Handle GET requests (for fetching categories)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $workExperienceId = $_GET['work_experience_id'] ?? '';
    $action = $_GET['action'] ?? '';

    if ($action === 'get' && !empty($workExperienceId)) {
        // Verify work experience belongs to user
        $workExp = db()->fetchOne(
            "SELECT id FROM work_experience WHERE id = ? AND profile_id = ?",
            [$workExperienceId, $userId]
        );

        if (!$workExp) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid work experience']);
            exit;
        }

        // Get all categories
        $categories = db()->fetchAll(
            "SELECT * FROM responsibility_categories WHERE work_experience_id = ? ORDER BY sort_order ASC",
            [$workExperienceId]
        );

        // Get all items for these categories
        $categoryIds = array_column($categories, 'id');
        $items = [];
        if (!empty($categoryIds)) {
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $items = db()->fetchAll(
                "SELECT * FROM responsibility_items WHERE category_id IN ($placeholders) ORDER BY sort_order ASC",
                $categoryIds
            );
        }

        // Group items by category
        foreach ($categories as &$category) {
            $category['items'] = array_filter($items, function($item) use ($category) {
                return $item['category_id'] === $category['id'];
            });
            $category['items'] = array_values($category['items']); // Re-index array
        }
        unset($category);

        echo json_encode(['success' => true, 'categories' => $categories]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Only allow POST requests for other actions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token for POST requests
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'add_category') {
        $workExperienceId = $_POST['work_experience_id'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if (empty($workExperienceId) || empty($name)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        // Verify work experience belongs to user
        $workExp = db()->fetchOne(
            "SELECT id FROM work_experience WHERE id = ? AND profile_id = ?",
            [$workExperienceId, $userId]
        );

        if (!$workExp) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid work experience']);
            exit;
        }

        // Get highest sort_order
        $maxOrder = db()->fetchOne(
            "SELECT MAX(sort_order) as max_order FROM responsibility_categories WHERE work_experience_id = ?",
            [$workExperienceId]
        );
        $nextOrder = ($maxOrder && $maxOrder['max_order'] !== null) ? (int)$maxOrder['max_order'] + 1 : 0;

        $categoryId = generateUuid();
        db()->insert('responsibility_categories', [
            'id' => $categoryId,
            'work_experience_id' => $workExperienceId,
            'name' => sanitizeInput($name),
            'sort_order' => $nextOrder,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => true, 'id' => $categoryId]);
    } elseif ($action === 'update_category') {
        $categoryId = $_POST['category_id'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if (empty($categoryId) || empty($name)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        // Verify category belongs to user's work experience
        $category = db()->fetchOne(
            "SELECT rc.id FROM responsibility_categories rc
             JOIN work_experience we ON rc.work_experience_id = we.id
             WHERE rc.id = ? AND we.profile_id = ?",
            [$categoryId, $userId]
        );

        if (!$category) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid category']);
            exit;
        }

        db()->update('responsibility_categories', [
            'name' => sanitizeInput($name)
        ], 'id = ?', [$categoryId]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'delete_category') {
        $categoryId = $_POST['category_id'] ?? '';

        if (empty($categoryId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing category ID']);
            exit;
        }

        // Verify category belongs to user's work experience
        $category = db()->fetchOne(
            "SELECT rc.id FROM responsibility_categories rc
             JOIN work_experience we ON rc.work_experience_id = we.id
             WHERE rc.id = ? AND we.profile_id = ?",
            [$categoryId, $userId]
        );

        if (!$category) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid category']);
            exit;
        }

        // Delete category (items will be deleted via CASCADE)
        db()->delete('responsibility_categories', 'id = ?', [$categoryId]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'add_item') {
        $categoryId = $_POST['category_id'] ?? '';
        $content = trim($_POST['content'] ?? '');

        if (empty($categoryId) || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        // Verify category belongs to user's work experience
        $category = db()->fetchOne(
            "SELECT rc.id FROM responsibility_categories rc
             JOIN work_experience we ON rc.work_experience_id = we.id
             WHERE rc.id = ? AND we.profile_id = ?",
            [$categoryId, $userId]
        );

        if (!$category) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid category']);
            exit;
        }

        // Get highest sort_order
        $maxOrder = db()->fetchOne(
            "SELECT MAX(sort_order) as max_order FROM responsibility_items WHERE category_id = ?",
            [$categoryId]
        );
        $nextOrder = ($maxOrder && $maxOrder['max_order'] !== null) ? (int)$maxOrder['max_order'] + 1 : 0;

        $itemId = generateUuid();
        db()->insert('responsibility_items', [
            'id' => $itemId,
            'category_id' => $categoryId,
            'content' => strip_tags(trim($content)),
            'sort_order' => $nextOrder,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => true, 'id' => $itemId]);
    } elseif ($action === 'update_item') {
        $itemId = $_POST['item_id'] ?? '';
        $content = trim($_POST['content'] ?? '');

        if (empty($itemId) || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        // Verify item belongs to user's work experience
        $item = db()->fetchOne(
            "SELECT ri.id FROM responsibility_items ri
             JOIN responsibility_categories rc ON ri.category_id = rc.id
             JOIN work_experience we ON rc.work_experience_id = we.id
             WHERE ri.id = ? AND we.profile_id = ?",
            [$itemId, $userId]
        );

        if (!$item) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid item']);
            exit;
        }

        db()->update('responsibility_items', [
            'content' => strip_tags(trim($content))
        ], 'id = ?', [$itemId]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'delete_item') {
        $itemId = $_POST['item_id'] ?? '';

        if (empty($itemId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            exit;
        }

        // Verify item belongs to user's work experience
        $item = db()->fetchOne(
            "SELECT ri.id FROM responsibility_items ri
             JOIN responsibility_categories rc ON ri.category_id = rc.id
             JOIN work_experience we ON rc.work_experience_id = we.id
             WHERE ri.id = ? AND we.profile_id = ?",
            [$itemId, $userId]
        );

        if (!$item) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid item']);
            exit;
        }

        db()->delete('responsibility_items', 'id = ?', [$itemId]);

        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Responsibilities API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
