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

$token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($token)) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

try {
    $fileId = $_POST['file_id'] ?? null;
    
    if (!$fileId) {
        throw new Exception('File ID is required');
    }
    
    // Get file information and verify ownership
    $file = db()->fetchOne(
        "SELECT * FROM job_application_files WHERE id = ? AND user_id = ?",
        [$fileId, $userId]
    );
    
    if (!$file) {
        throw new Exception('File not found');
    }
    
    // Delete file from storage
    $filePath = STORAGE_PATH . '/' . $file['stored_name'];
    if (file_exists($filePath)) {
        @unlink($filePath);
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
    ob_end_clean();
    http_response_code(500);
    error_log("Delete job application file error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => DEBUG ? $e->getMessage() : 'Failed to delete file'
    ]);
}

