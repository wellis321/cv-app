<?php
/**
 * API endpoint for deleting job application files
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/storage.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();

// #region agent log
$debugLogPath = __DIR__ . '/../.cursor/debug-902fb4.log';
$debugLog = function ($msg, $data = []) use ($debugLogPath) {
    $payload = array_merge(['sessionId' => '902fb4', 'location' => 'delete-job-application-file.php', 'message' => $msg, 'timestamp' => (int)(microtime(true) * 1000)], $data);
    @file_put_contents($debugLogPath, json_encode($payload) . "\n", FILE_APPEND | LOCK_EX);
};
// #endregion
$token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($token)) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

try {
    $fileId = $_POST['file_id'] ?? null;
    // #region agent log
    $debugLog('delete_api_entry', ['hypothesisId' => 'A', 'file_id' => $fileId, 'csrf_present' => !empty($_POST['csrf_token']), 'csrf_len' => strlen($_POST['csrf_token'] ?? '')]);
    // #endregion
    if (!$fileId) {
        throw new Exception('File ID is required');
    }
    
    // Get file information and verify ownership
    $file = db()->fetchOne(
        "SELECT * FROM job_application_files WHERE id = ? AND user_id = ?",
        [$fileId, $userId]
    );
    // #region agent log
    $debugLog('delete_api_db_fetch', ['hypothesisId' => 'B', 'file_found' => (bool)$file, 'stored_name' => $file['stored_name'] ?? null]);
    // #endregion
    if (!$file) {
        throw new Exception('File not found');
    }
    
    // Delete file from storage
    $filePath = STORAGE_PATH . '/' . $file['stored_name'];
    $pathExists = file_exists($filePath);
    // #region agent log
    $debugLog('delete_api_before_unlink', ['hypothesisId' => 'C', 'full_path' => $filePath, 'path_exists' => $pathExists]);
    // #endregion
    if ($pathExists) {
        $unlinkOk = @unlink($filePath);
        // #region agent log
        $debugLog('delete_api_after_unlink', ['hypothesisId' => 'D', 'unlink_ok' => $unlinkOk]);
        // #endregion
    }
    
    // Delete file record from database
    db()->delete('job_application_files', 'id = ?', [$fileId]);
    
    // Log activity if user is in an organisation
    $org = getUserOrganisation();
    if ($org) {
        logActivity('job_application_file.deleted', null, [
            'file_id' => $fileId,
            'application_id' => $file['application_id'],
            'file_name' => $file['original_name']
        ], $org['organisation_id']);
    }
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'File deleted successfully'
    ]);
    
} catch (Exception $e) {
    // #region agent log
    if (isset($debugLog)) {
        $debugLog('delete_api_exception', ['hypothesisId' => 'E', 'error' => $e->getMessage()]);
    }
    // #endregion
    ob_end_clean();
    http_response_code(500);
    error_log("Delete job application file error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => DEBUG ? $e->getMessage() : 'Failed to delete file'
    ]);
}

