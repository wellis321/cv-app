<?php
/**
 * CV Variants API – rename and delete variants (JSON).
 * Used by the content editor CV Variants panel.
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$userId = getUserId();
$action = post('action');
$variantId = post('variant_id');

if (empty($variantId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Variant ID required']);
    exit;
}

if ($action === 'delete') {
    $result = deleteCvVariant($variantId, $userId);
    if ($result['success']) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Failed to delete variant']);
    }
    exit;
}

if ($action === 'rename') {
    $variantName = trim(post('variant_name') ?? '');
    if ($variantName === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Variant name cannot be empty']);
        exit;
    }
    $result = updateCvVariantName($variantId, $variantName, $userId);
    if ($result['success']) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Failed to rename variant']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid action. Use rename or delete.']);
