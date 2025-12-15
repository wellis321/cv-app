<?php
/**
 * API endpoint for updating profile (AJAX)
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

// Only allow POST requests
if (!isPost()) {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

// Require authentication
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Authentication required'], 401);
}

// Verify CSRF token
$token = post(CSRF_TOKEN_NAME);
if (!verifyCsrfToken($token)) {
    jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
}

$userId = getUserId();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    jsonResponse(['success' => false, 'error' => 'Invalid JSON'], 400);
}

// Validate that user can only update their own profile
if (isset($input['id']) && $input['id'] !== $userId) {
    jsonResponse(['success' => false, 'error' => 'You can only update your own profile'], 403);
}

// Prepare update data
$data = [];
$allowedFields = ['full_name', 'phone', 'location', 'linkedin_url', 'bio', 'username'];

foreach ($allowedFields as $field) {
    if (isset($input[$field])) {
        $value = sanitizeInput($input[$field]);

        // Check for XSS
        if (checkForXss($value)) {
            jsonResponse(['success' => false, 'error' => "Invalid content in {$field}"], 400);
        }

        $data[$field] = $value;
    }
}

// Validate username if provided
if (isset($data['username'])) {
    if (!preg_match('/^[a-z0-9][a-z0-9\-_]+$/', $data['username'])) {
        jsonResponse(['success' => false, 'error' => 'Invalid username format'], 400);
    }

    // Check if username is taken
    $existing = db()->fetchOne(
        "SELECT id FROM profiles WHERE username = ? AND id != ?",
        [$data['username'], $userId]
    );

    if ($existing) {
        jsonResponse(['success' => false, 'error' => 'Username is already taken'], 400);
    }
}

// Validate LinkedIn URL if provided
if (isset($data['linkedin_url']) && !empty($data['linkedin_url']) && !validateUrl($data['linkedin_url'])) {
    jsonResponse(['success' => false, 'error' => 'Invalid LinkedIn URL'], 400);
}

// Update profile
try {
    $data['updated_at'] = date('Y-m-d H:i:s');
    db()->update('profiles', $data, 'id = ?', [$userId]);

    // Fetch updated profile
    $profile = db()->fetchOne("SELECT * FROM profiles WHERE id = ?", [$userId]);

    jsonResponse([
        'success' => true,
        'profile' => $profile
    ]);
} catch (Exception $e) {
    if (DEBUG) {
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to update profile'], 500);
    }
}
