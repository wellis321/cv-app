<?php
/**
 * Storage proxy - serve uploaded files
 */

require_once __DIR__ . '/../php/config.php';

// Get the file path from URL parameter
$path = $_GET['path'] ?? '';

// Clean the path
$path = ltrim($path, '/');
$filePath = STORAGE_PATH . '/' . $path;

// Security check - ensure file is within storage directory
$realStoragePath = realpath(STORAGE_PATH);
$realFilePath = realpath($filePath);

if (!$realFilePath || strpos($realFilePath, $realStoragePath) !== 0) {
    http_response_code(403);
    die('Access denied');
}

// Check if file exists
if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    die('File not found');
}

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

// Set headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000'); // Cache for 1 year

// Output file
readfile($filePath);
exit;
