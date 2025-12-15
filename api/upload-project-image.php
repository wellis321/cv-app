<?php
/**
 * API endpoint for uploading project images
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

if (!isset($_FILES['project_image']) || $_FILES['project_image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['project_image'];
$result = uploadFile($file, $userId, 'projects');

if (!$result['success']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $result['error']]);
    exit;
}

echo json_encode([
    'success' => true,
    'url' => $result['url'],
    'path' => $result['path'] ?? str_replace(STORAGE_URL . '/', '', $result['url'])
]);
exit;
