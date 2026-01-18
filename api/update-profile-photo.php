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

        // Delete responsive versions if they exist
        if (!empty($profile['photo_responsive'])) {
            $responsive = json_decode($profile['photo_responsive'], true);
            if (is_array($responsive)) {
                foreach ($responsive as $size => $data) {
                    if (isset($data['path'])) {
                        $responsivePath = STORAGE_PATH . '/' . $data['path'];
                        if (file_exists($responsivePath)) {
                            @unlink($responsivePath);
                        }
                    }
                }
            }
        }
        
        // Update database
        db()->update('profiles', [
            'photo_url' => null,
            'photo_responsive' => null,
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

    // Upload file using storage function (with responsive generation)
    $result = uploadFile($file, $userId, 'profiles', true);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $result['error']]);
        exit;
    }

    // Delete old photo and responsive versions if exists
    try {
        $profile = db()->fetchOne("SELECT photo_url, photo_responsive FROM profiles WHERE id = ?", [$userId]);

        if (!empty($profile['photo_url'])) {
            $oldUrl = $profile['photo_url'];
            $oldPath = str_replace(STORAGE_URL . '/', '', $oldUrl);
            $oldFullPath = STORAGE_PATH . '/' . $oldPath;

            if (file_exists($oldFullPath)) {
                @unlink($oldFullPath);
            }
            
            // Delete responsive versions if they exist
            if (!empty($profile['photo_responsive'])) {
                $responsive = json_decode($profile['photo_responsive'], true);
                if (is_array($responsive)) {
                    foreach ($responsive as $size => $data) {
                        if (isset($data['path'])) {
                            $responsivePath = STORAGE_PATH . '/' . $data['path'];
                            if (file_exists($responsivePath)) {
                                @unlink($responsivePath);
                            }
                        }
                    }
                }
            }
        }

        // Update profile with new photo URL and responsive data
        $updateData = [
            'photo_url' => $result['url'],
            'photo_responsive' => !empty($result['responsive']) ? json_encode($result['responsive']) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $updateResult = db()->update('profiles', $updateData, 'id = ?', [$userId]);
        
        // Log the update attempt
        error_log("Photo update attempt - User ID: {$userId}, URL: {$result['url']}, Rows affected: {$updateResult}");

        // Verify the update worked by querying the database
        $updatedProfile = db()->fetchOne("SELECT photo_url FROM profiles WHERE id = ?", [$userId]);
        
        if (empty($updatedProfile['photo_url'])) {
            error_log("ERROR: Photo URL not found after update. User ID: {$userId}, Expected URL: {$result['url']}, Rows affected: {$updateResult}");
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Photo uploaded but database update failed. Please try again.',
                'debug' => DEBUG ? ['rows_affected' => $updateResult, 'expected_url' => $result['url']] : null
            ]);
            exit;
        }
        
        // Double-check the URL matches
        if ($updatedProfile['photo_url'] !== $result['url']) {
            error_log("WARNING: Photo URL mismatch. Expected: {$result['url']}, Got: {$updatedProfile['photo_url']}");
        }

        echo json_encode([
            'success' => true,
            'url' => $updatedProfile['photo_url'], // Return the actual stored URL
            'responsive' => !empty($result['responsive']) ? $result['responsive'] : null,
            'rows_affected' => $updateResult
        ]);
    } catch (Exception $e) {
        error_log("Photo update error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
    }
}
