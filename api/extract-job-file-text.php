<?php
/**
 * API endpoint for extracting text from job application files
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/document-extractor.php';

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
    $applicationId = $_POST['application_id'] ?? null;
    
    if (!$fileId && !$applicationId) {
        throw new Exception('Either file_id or application_id is required');
    }
    
    // Get file information
    if ($fileId) {
        $file = db()->fetchOne(
            "SELECT * FROM job_application_files WHERE id = ? AND user_id = ?",
            [$fileId, $userId]
        );
        
        if (!$file) {
            throw new Exception('File not found');
        }
    } else {
        // Get first file for the application with purpose 'other' (job description)
        $file = db()->fetchOne(
            "SELECT * FROM job_application_files WHERE application_id = ? AND user_id = ? ORDER BY uploaded_at DESC LIMIT 1",
            [$applicationId, $userId]
        );
        
        if (!$file) {
            throw new Exception('No files found for this application');
        }
    }
    
    // Build file path
    $filePath = STORAGE_PATH . '/' . $file['stored_name'];
    
    if (!file_exists($filePath)) {
        throw new Exception('File not found on disk');
    }
    
    // Extract text
    $extractionResult = extractDocumentText($filePath, $file['mime_type']);
    
    if (!$extractionResult['success']) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $extractionResult['error'] ?? 'Failed to extract text'
        ]);
        exit;
    }
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'text' => $extractionResult['text'],
        'file_id' => $file['id'],
        'file_name' => $file['original_name']
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log("Extract file text error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => DEBUG ? $e->getMessage() : 'Failed to extract text'
    ]);
}

