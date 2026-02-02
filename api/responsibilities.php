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
$variantId = $_GET['variant_id'] ?? $_POST['variant_id'] ?? null;

// Helper: verify ownership when using variant tables
$isVariantContext = !empty($variantId);
if ($isVariantContext) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if (!$variant) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid variant']);
        exit;
    }
}

// Handle GET requests (for fetching categories)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $workExperienceId = $_GET['work_experience_id'] ?? '';
    $action = $_GET['action'] ?? '';

    if ($action === 'get' && !empty($workExperienceId)) {
        if ($isVariantContext) {
            // Variant: work_experience_id is cv_variant_work_experience.id; ownership already verified via variant
            $workExp = db()->fetchOne(
                "SELECT w.id FROM cv_variant_work_experience w
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE w.id = ? AND v.user_id = ?",
                [$workExperienceId, $userId]
            );
            if (!$workExp) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid work experience']);
                exit;
            }
            $categories = db()->fetchAll(
                "SELECT * FROM cv_variant_responsibility_categories WHERE work_experience_id = ? ORDER BY sort_order ASC",
                [$workExperienceId]
            );
            $categoryIds = array_column($categories, 'id');
            $items = [];
            if (!empty($categoryIds)) {
                $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
                $items = db()->fetchAll(
                    "SELECT * FROM cv_variant_responsibility_items WHERE category_id IN ($placeholders) ORDER BY sort_order ASC",
                    $categoryIds
                );
            }
        } else {
            // Master: work_experience table
            $workExp = db()->fetchOne(
                "SELECT id FROM work_experience WHERE id = ? AND profile_id = ?",
                [$workExperienceId, $userId]
            );
            if (!$workExp) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid work experience']);
                exit;
            }
            $categories = db()->fetchAll(
                "SELECT * FROM responsibility_categories WHERE work_experience_id = ? ORDER BY sort_order ASC",
                [$workExperienceId]
            );
            $categoryIds = array_column($categories, 'id');
            $items = [];
            if (!empty($categoryIds)) {
                $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
                $items = db()->fetchAll(
                    "SELECT * FROM responsibility_items WHERE category_id IN ($placeholders) ORDER BY sort_order ASC",
                    $categoryIds
                );
            }
        }

        // Group items by category
        foreach ($categories as &$category) {
            $category['items'] = array_filter($items, function($item) use ($category) {
                return $item['category_id'] === $category['id'];
            });
            $category['items'] = array_values($category['items']);
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

        if ($isVariantContext) {
            $workExp = db()->fetchOne(
                "SELECT w.id FROM cv_variant_work_experience w
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE w.id = ? AND v.user_id = ?",
                [$workExperienceId, $userId]
            );
            if (!$workExp) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid work experience']);
                exit;
            }
            $maxOrder = db()->fetchOne(
                "SELECT MAX(sort_order) as max_order FROM cv_variant_responsibility_categories WHERE work_experience_id = ?",
                [$workExperienceId]
            );
            $nextOrder = ($maxOrder && $maxOrder['max_order'] !== null) ? (int)$maxOrder['max_order'] + 1 : 0;
            $categoryId = generateUuid();
            db()->insert('cv_variant_responsibility_categories', [
                'id' => $categoryId,
                'work_experience_id' => $workExperienceId,
                'name' => sanitizeInput($name),
                'sort_order' => $nextOrder,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $workExp = db()->fetchOne(
                "SELECT id FROM work_experience WHERE id = ? AND profile_id = ?",
                [$workExperienceId, $userId]
            );
            if (!$workExp) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid work experience']);
                exit;
            }
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
        }

        echo json_encode(['success' => true, 'id' => $categoryId]);
    } elseif ($action === 'update_category') {
        $categoryId = $_POST['category_id'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if (empty($categoryId) || empty($name)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        if ($isVariantContext) {
            $category = db()->fetchOne(
                "SELECT rc.id FROM cv_variant_responsibility_categories rc
                 JOIN cv_variant_work_experience w ON rc.work_experience_id = w.id
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE rc.id = ? AND v.user_id = ?",
                [$categoryId, $userId]
            );
            if (!$category) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid category']);
                exit;
            }
            db()->update('cv_variant_responsibility_categories', ['name' => sanitizeInput($name)], 'id = ?', [$categoryId]);
        } else {
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
            db()->update('responsibility_categories', ['name' => sanitizeInput($name)], 'id = ?', [$categoryId]);
        }

        echo json_encode(['success' => true]);
    } elseif ($action === 'delete_category') {
        $categoryId = $_POST['category_id'] ?? '';

        if (empty($categoryId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing category ID']);
            exit;
        }

        if ($isVariantContext) {
            $category = db()->fetchOne(
                "SELECT rc.id FROM cv_variant_responsibility_categories rc
                 JOIN cv_variant_work_experience w ON rc.work_experience_id = w.id
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE rc.id = ? AND v.user_id = ?",
                [$categoryId, $userId]
            );
            if (!$category) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid category']);
                exit;
            }
            db()->delete('cv_variant_responsibility_categories', 'id = ?', [$categoryId]);
        } else {
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
            db()->delete('responsibility_categories', 'id = ?', [$categoryId]);
        }

        echo json_encode(['success' => true]);
    } elseif ($action === 'add_item') {
        $categoryId = $_POST['category_id'] ?? '';
        $content = trim($_POST['content'] ?? '');

        if (empty($categoryId) || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        if ($isVariantContext) {
            $category = db()->fetchOne(
                "SELECT rc.id FROM cv_variant_responsibility_categories rc
                 JOIN cv_variant_work_experience w ON rc.work_experience_id = w.id
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE rc.id = ? AND v.user_id = ?",
                [$categoryId, $userId]
            );
            if (!$category) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid category']);
                exit;
            }
            $maxOrder = db()->fetchOne(
                "SELECT MAX(sort_order) as max_order FROM cv_variant_responsibility_items WHERE category_id = ?",
                [$categoryId]
            );
            $nextOrder = ($maxOrder && $maxOrder['max_order'] !== null) ? (int)$maxOrder['max_order'] + 1 : 0;
            $itemId = generateUuid();
            db()->insert('cv_variant_responsibility_items', [
                'id' => $itemId,
                'category_id' => $categoryId,
                'content' => strip_tags(trim($content)),
                'sort_order' => $nextOrder,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
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
        }

        echo json_encode(['success' => true, 'id' => $itemId]);
    } elseif ($action === 'update_item') {
        $itemId = $_POST['item_id'] ?? '';
        $content = trim($_POST['content'] ?? '');

        if (empty($itemId) || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        if ($isVariantContext) {
            $item = db()->fetchOne(
                "SELECT ri.id FROM cv_variant_responsibility_items ri
                 JOIN cv_variant_responsibility_categories rc ON ri.category_id = rc.id
                 JOIN cv_variant_work_experience w ON rc.work_experience_id = w.id
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE ri.id = ? AND v.user_id = ?",
                [$itemId, $userId]
            );
            if (!$item) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid item']);
                exit;
            }
            db()->update('cv_variant_responsibility_items', ['content' => strip_tags(trim($content))], 'id = ?', [$itemId]);
        } else {
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
            db()->update('responsibility_items', ['content' => strip_tags(trim($content))], 'id = ?', [$itemId]);
        }

        echo json_encode(['success' => true]);
    } elseif ($action === 'delete_item') {
        $itemId = $_POST['item_id'] ?? '';

        if (empty($itemId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            exit;
        }

        if ($isVariantContext) {
            $item = db()->fetchOne(
                "SELECT ri.id FROM cv_variant_responsibility_items ri
                 JOIN cv_variant_responsibility_categories rc ON ri.category_id = rc.id
                 JOIN cv_variant_work_experience w ON rc.work_experience_id = w.id
                 JOIN cv_variants v ON w.cv_variant_id = v.id
                 WHERE ri.id = ? AND v.user_id = ?",
                [$itemId, $userId]
            );
            if (!$item) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid item']);
                exit;
            }
            db()->delete('cv_variant_responsibility_items', 'id = ?', [$itemId]);
        } else {
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
        }

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
