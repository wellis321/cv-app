<?php
/**
 * API endpoint for reordering certification entries
 */

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../php/helpers.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
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
$variantId = trim($_POST['variant_id'] ?? '');

try {
    if ($action === 'reorder') {
        $orderedIds = json_decode($_POST['ordered_ids'] ?? '[]', true);

        if (empty($orderedIds) || !is_array($orderedIds)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid order data']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($orderedIds), '?'));

        if ($variantId) {
            $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
            if (!$variant) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid variant']);
                exit;
            }
            $existing = db()->fetchAll(
                "SELECT id FROM cv_variant_certifications WHERE id IN ($placeholders) AND cv_variant_id = ?",
                array_merge($orderedIds, [$variantId])
            );
            if (count($existing) !== count($orderedIds)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid certification IDs']);
                exit;
            }
            foreach ($orderedIds as $index => $id) {
                db()->update(
                    'cv_variant_certifications',
                    ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
                    'id = ? AND cv_variant_id = ?',
                    [$id, $variantId]
                );
            }
        } else {
            $existing = db()->fetchAll(
                "SELECT id FROM certifications WHERE id IN ($placeholders) AND profile_id = ?",
                array_merge($orderedIds, [$userId])
            );
            if (count($existing) !== count($orderedIds)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid certification IDs']);
                exit;
            }
            foreach ($orderedIds as $index => $id) {
                db()->update(
                    'certifications',
                    ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
                    'id = ? AND profile_id = ?',
                    [$id, $userId]
                );
            }
        }

        echo json_encode(['success' => true]);

    } elseif ($action === 'reset') {
        if ($variantId) {
            $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
            if (!$variant) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid variant']);
                exit;
            }
            $certs = db()->fetchAll(
                "SELECT id FROM cv_variant_certifications WHERE cv_variant_id = ? ORDER BY date_obtained DESC, created_at DESC",
                [$variantId]
            );
            foreach ($certs as $index => $cert) {
                db()->update(
                    'cv_variant_certifications',
                    ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
                    'id = ? AND cv_variant_id = ?',
                    [$cert['id'], $variantId]
                );
            }
        } else {
            $certs = db()->fetchAll(
                "SELECT id FROM certifications WHERE profile_id = ? ORDER BY date_obtained DESC, created_at DESC",
                [$userId]
            );
            foreach ($certs as $index => $cert) {
                db()->update(
                    'certifications',
                    ['sort_order' => $index, 'updated_at' => date('Y-m-d H:i:s')],
                    'id = ? AND profile_id = ?',
                    [$cert['id'], $userId]
                );
            }
        }

        echo json_encode(['success' => true]);

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Reorder certifications error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to reorder certifications']);
}
