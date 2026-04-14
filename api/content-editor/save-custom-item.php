<?php
/**
 * API endpoint for creating, updating, and deleting custom section items
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

/**
 * Verify that the given custom_section_id belongs to the current user.
 */
function verifyCustomSectionOwnership(string $customSectionId, string $userId): bool
{
    $row = db()->fetchOne(
        "SELECT id FROM custom_sections WHERE id = ? AND profile_id = ?",
        [$customSectionId, $userId]
    );
    return !empty($row);
}

/**
 * Verify that the given item id belongs (via its section) to the current user.
 */
function verifyCustomItemOwnership(string $itemId, string $userId): ?array
{
    return db()->fetchOne(
        "SELECT csi.* FROM custom_section_items csi
         JOIN custom_sections cs ON cs.id = csi.custom_section_id
         WHERE csi.id = ? AND cs.profile_id = ?",
        [$itemId, $userId]
    );
}

try {
    if ($action === 'create') {
        $customSectionId = prepareForStorage($_POST['custom_section_id'] ?? '');
        $title           = prepareForStorage($_POST['title'] ?? '');

        if (empty($customSectionId) || empty($title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'custom_section_id and title are required']);
            exit;
        }

        if (!verifyCustomSectionOwnership($customSectionId, $userId)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Section not found']);
            exit;
        }

        $maxOrder = db()->fetchOne(
            "SELECT COALESCE(MAX(sort_order), -1) as max_order FROM custom_section_items WHERE custom_section_id = ?",
            [$customSectionId]
        );
        $sortOrder = ($maxOrder['max_order'] ?? -1) + 1;

        $id = generateUuid();
        db()->insert('custom_section_items', [
            'id'               => $id,
            'custom_section_id'=> $customSectionId,
            'title'            => $title,
            'subtitle'         => prepareForStorage($_POST['subtitle'] ?? ''),
            'item_date'        => prepareForStorage($_POST['item_date'] ?? ''),
            'url'              => prepareForStorage($_POST['url'] ?? ''),
            'description'      => prepareForStorage($_POST['description'] ?? ''),
            'sort_order'       => $sortOrder,
        ]);
        echo json_encode(['success' => true, 'id' => $id]);

    } elseif ($action === 'update') {
        $id              = prepareForStorage($_POST['id'] ?? '');
        $customSectionId = prepareForStorage($_POST['custom_section_id'] ?? '');
        $title           = prepareForStorage($_POST['title'] ?? '');

        if (empty($id) || empty($customSectionId) || empty($title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'id, custom_section_id and title are required']);
            exit;
        }

        $item = verifyCustomItemOwnership($id, $userId);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Item not found']);
            exit;
        }

        // Also verify the posted custom_section_id matches (prevents cross-section moves)
        if ($item['custom_section_id'] !== $customSectionId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Section mismatch']);
            exit;
        }

        db()->update('custom_section_items', [
            'title'       => $title,
            'subtitle'    => prepareForStorage($_POST['subtitle'] ?? ''),
            'item_date'   => prepareForStorage($_POST['item_date'] ?? ''),
            'url'         => prepareForStorage($_POST['url'] ?? ''),
            'description' => prepareForStorage($_POST['description'] ?? ''),
        ], 'id = ?', [$id]);

        echo json_encode(['success' => true]);

    } elseif ($action === 'delete') {
        $id = prepareForStorage($_POST['id'] ?? '');
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID is required']);
            exit;
        }

        $item = verifyCustomItemOwnership($id, $userId);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Item not found']);
            exit;
        }

        db()->delete('custom_section_items', 'id = ?', [$id]);
        echo json_encode(['success' => true]);

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log('save-custom-item error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
}
