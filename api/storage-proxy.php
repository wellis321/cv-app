<?php
/**
 * Storage proxy - serve uploaded files securely
 *
 * Security features:
 * - Path traversal prevention
 * - CORS restricted to same origin
 * - Public files (profile photos, project images) accessible without auth
 * - Private files require authentication and ownership verification
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/database.php';
require_once __DIR__ . '/../php/auth.php';

// Get the file path from URL parameter
$path = $_GET['path'] ?? '';

// Clean the path - remove any leading slashes and normalize
$path = ltrim($path, '/');
$path = str_replace('..', '', $path); // Remove any directory traversal attempts

// Public paths - return 404 instead of 500 when file is missing (better for crawlers/SEO)
$isPublicPath = (
    strpos($path, 'profiles/') === 0 ||
    strpos($path, 'uploads/profile-photos/') === 0 ||
    strpos($path, 'uploads/projects/') === 0 ||
    strpos($path, 'projects/') === 0
);

try {
    // Build full file path
    $filePath = STORAGE_PATH . '/' . $path;

    // Security check - ensure file is within storage directory
    $realStoragePath = realpath(STORAGE_PATH);
    if (!$realStoragePath) {
        // Try to create storage directory if it doesn't exist
        if (!is_dir(STORAGE_PATH)) {
            require_once __DIR__ . '/../php/storage.php';
            ensureStorageDir('');
        }
        $realStoragePath = realpath(STORAGE_PATH);
        if (!$realStoragePath) {
            if ($isPublicPath) {
                http_response_code(404);
                die('File not found');
            }
            http_response_code(500);
            die('Storage path not found');
        }
    }

    // Check if file exists first
    if (!file_exists($filePath) || !is_file($filePath)) {
        http_response_code(404);
        die('File not found');
    }

    $realFilePath = realpath($filePath);
    if (!$realFilePath) {
        if ($isPublicPath) {
            http_response_code(404);
            die('File not found');
        }
        http_response_code(403);
        die('Cannot resolve file path');
    }

    // Ensure the resolved path is within the storage directory
    if (strpos($realFilePath, $realStoragePath) !== 0) {
        http_response_code(403);
        die('Access denied');
    }

    // For non-public files, require authentication and verify ownership
    if (!$isPublicPath) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isLoggedIn()) {
            http_response_code(401);
            die('Authentication required');
        }

        // For private files, verify the file belongs to the logged-in user
        $filename = basename($path);
        $userId = getUserId();

        $fileOwned = false;

        // Check job application files
        $jobFile = db()->fetchOne(
            "SELECT ja.id FROM job_application_files jaf
             JOIN job_applications ja ON jaf.job_application_id = ja.id
             WHERE jaf.file_path LIKE ? AND ja.profile_id = ?",
            ['%' . $filename, $userId]
        );
        if ($jobFile) $fileOwned = true;

        // Check document uploads (cover letters, etc.)
        if (!$fileOwned) {
            $docFile = db()->fetchOne(
                "SELECT id FROM cover_letters WHERE pdf_path LIKE ? AND profile_id = ?",
                ['%' . $filename, $userId]
            );
            if ($docFile) $fileOwned = true;
        }

        if (!$fileOwned) {
            http_response_code(403);
            die('Access denied');
        }
    }

    // Get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    // Set headers - CORS restricted to same origin only
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
    header('X-Content-Type-Options: nosniff');

    // Output file
    readfile($filePath);
    exit;

} catch (Throwable $e) {
    // For public paths (project images, profile photos), return 404 instead of 500
    // so crawlers like Ahrefs don't report broken images as server errors
    if ($isPublicPath) {
        http_response_code(404);
        header('Cache-Control: public, max-age=3600');
        die('File not found');
    }
    throw $e;
}
