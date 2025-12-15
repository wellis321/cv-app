<?php
/**
 * File storage functions
 */

require_once __DIR__ . '/config.php';

/**
 * Ensure storage directory exists
 */
function ensureStorageDir($path) {
    $fullPath = STORAGE_PATH . '/' . $path;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
    }
    return $fullPath;
}

/**
 * Upload a file
 */
function uploadFile($file, $userId, $bucket = 'uploads') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    // Validate file size
    if ($file['size'] > STORAGE_MAX_SIZE) {
        return ['success' => false, 'error' => 'File too large'];
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Get file extension
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ALLOWED_IMAGE_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file extension'];
    }

    // Create storage path
    $storageDir = ensureStorageDir($bucket . '/' . $userId);
    $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $filePath = $storageDir . '/' . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => false, 'error' => 'Failed to save file'];
    }

    // Generate URL
    $relativePath = $bucket . '/' . $userId . '/' . $fileName;
    $url = STORAGE_URL . '/' . $relativePath;

    return [
        'success' => true,
        'url' => $url,
        'path' => $relativePath,
        'filename' => $fileName
    ];
}

/**
 * Delete a file
 */
function deleteFile($path) {
    $fullPath = STORAGE_PATH . '/' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Get file URL
 */
function getFileUrl($path) {
    return STORAGE_URL . '/' . $path;
}

/**
 * Serve file (for storage-proxy functionality)
 */
function serveFile($path) {
    $fullPath = STORAGE_PATH . '/' . $path;

    if (!file_exists($fullPath) || !is_file($fullPath)) {
        http_response_code(404);
        die('File not found');
    }

    // Get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fullPath);
    finfo_close($finfo);

    // Set headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($fullPath));
    header('Cache-Control: public, max-age=3600');
    header('Content-Disposition: inline');

    // Output file
    readfile($fullPath);
    exit;
}
