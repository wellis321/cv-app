<?php
/**
 * API endpoint for uploading job application files
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
    require_once __DIR__ . '/../php/storage.php';
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
    error_log("Unexpected output in upload-job-application-file.php: " . substr($output, 0, 200));
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

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = 'No file uploaded';
    if (isset($_FILES['file']['error'])) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMsg = $uploadErrors[$_FILES['file']['error']] ?? 'Upload error: ' . $_FILES['file']['error'];
    }
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

$applicationId = $_POST['application_id'] ?? null;
$filePurpose = $_POST['file_purpose'] ?? 'other';
$customName = $_POST['custom_name'] ?? null;

// Validate file purpose
$validPurposes = ['resume', 'cover_letter', 'portfolio', 'other'];
if (!in_array($filePurpose, $validPurposes)) {
    $filePurpose = 'other';
}

// Upload the file
$uploadResult = uploadDocumentFile($_FILES['file'], $userId, 'job-applications', $applicationId);

if (!$uploadResult['success']) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $uploadResult['error']]);
    exit;
}

// Save file metadata to database
try {
    require_once __DIR__ . '/../php/job-applications.php';
    
    $fileId = generateUuid();
    $fileData = [
        'id' => $fileId,
        'user_id' => $userId,
        'application_id' => $applicationId,
        'original_name' => $uploadResult['original_name'],
        'stored_name' => $uploadResult['path'],
        'file_name' => $uploadResult['filename'],
        'custom_name' => $customName ? sanitizeInput($customName) : null,
        'mime_type' => $uploadResult['mime_type'],
        'size' => $uploadResult['size'],
        'file_purpose' => $filePurpose,
        'uploaded_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    db()->insert('job_application_files', $fileData);
    
    // Log activity if user is in an organisation
    $org = getUserOrganisation();
    if ($org) {
        logActivity('job_application_file.uploaded', null, [
            'file_id' => $fileId,
            'application_id' => $applicationId,
            'file_name' => $uploadResult['original_name']
        ], $org['organisation_id']);
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'file' => [
            'id' => $fileId,
            'original_name' => $uploadResult['original_name'],
            'custom_name' => $customName,
            'file_name' => $uploadResult['filename'],
            'mime_type' => $uploadResult['mime_type'],
            'size' => $uploadResult['size'],
            'file_purpose' => $filePurpose,
            'url' => $uploadResult['url'],
            'path' => $uploadResult['path'],
            'uploaded_at' => $fileData['uploaded_at']
        ]
    ]);
    
} catch (Exception $e) {
    // Clean up uploaded file if database insert fails
    if (isset($uploadResult['path'])) {
        $fullPath = STORAGE_PATH . '/' . $uploadResult['path'];
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
    
    ob_clean();
    http_response_code(500);
    error_log("Upload job application file error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => DEBUG ? $e->getMessage() : 'Failed to save file metadata'
    ]);
}

