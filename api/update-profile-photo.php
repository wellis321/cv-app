<?php
/**
 * API endpoint for updating profile photo (upload and delete)
 */

// Set content type to JSON first to ensure proper response
header('Content-Type: application/json');

// Disable error display for production (but still log)
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../php/helpers.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check authentication (don't redirect, return JSON error)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();

// Verify CSRF token
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$action = $_POST['action'] ?? 'upload';

if ($action === 'delete') {
    // Delete photo
    try {
        $profile = db()->fetchOne("SELECT photo_url FROM profiles WHERE id = ?", [$userId]);

        if (!empty($profile['photo_url'])) {
            // Extract path from URL
            $url = $profile['photo_url'];
            $path = str_replace(STORAGE_URL . '/', '', $url);
            $fullPath = STORAGE_PATH . '/' . $path;

            // Delete file if it exists
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        // Update database
        db()->update('profiles', [
            'photo_url' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$userId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("Photo delete error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to delete photo']);
    }
} else {
    // Upload file
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
        exit;
    }

    $file = $_FILES['photo'];

    // Upload file using storage function
    $result = uploadFile($file, $userId, 'profiles');

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $result['error']]);
        exit;
    }

    // Delete old photo if exists
    try {
        $profile = db()->fetchOne("SELECT photo_url FROM profiles WHERE id = ?", [$userId]);

        if (!empty($profile['photo_url'])) {
            $oldUrl = $profile['photo_url'];
            $oldPath = str_replace(STORAGE_URL . '/', '', $oldUrl);
            $oldFullPath = STORAGE_PATH . '/' . $oldPath;

            if (file_exists($oldFullPath)) {
                @unlink($oldFullPath);
            }
        }

        // Update profile with new photo URL
        db()->update('profiles', [
            'photo_url' => $result['url'],
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$userId]);

        echo json_encode([
            'success' => true,
            'url' => $result['url']
        ]);
    } catch (Exception $e) {
        error_log("Photo update error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
    }
}
