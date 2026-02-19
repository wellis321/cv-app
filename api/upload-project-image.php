<?php
/**
 * API endpoint for uploading project images
 */

// Start output buffering immediately to catch any output
ob_start();

// Suppress all output before JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Prevent canonical domain redirect for API endpoints
define('SKIP_CANONICAL_REDIRECT', true);

try {
    require_once __DIR__ . '/../php/helpers.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Server configuration error: ' . $e->getMessage()]);
    exit;
}

// Clear any output that might have been generated during require
$output = ob_get_clean();
if (!empty($output)) {
    // If there was output, log it but don't include it in response
    error_log("Unexpected output in upload-project-image.php: " . substr($output, 0, 200));
}

// Start fresh output buffer for JSON response
ob_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    ob_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();

$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    ob_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

if (!isset($_FILES['project_image']) || $_FILES['project_image']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = 'No file uploaded';
    if (isset($_FILES['project_image']['error'])) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMsg = $uploadErrors[$_FILES['project_image']['error']] ?? 'Upload error: ' . $_FILES['project_image']['error'];
    }
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

$file = $_FILES['project_image'];
$result = uploadFile($file, $userId, 'projects', true); // Generate responsive versions
if (!$result['success']) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Upload failed']);
    exit;
}

// Ensure we have valid data
$response = [
    'success' => true,
    'url' => $result['url'] ?? '',
    'path' => $result['path'] ?? str_replace(STORAGE_URL . '/', '', $result['url'] ?? ''),
    'responsive' => $result['responsive'] ?? []
];

// Clear any output and send JSON
ob_clean();
echo json_encode($response);
exit;
