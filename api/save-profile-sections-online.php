<?php
/**
 * Save profile-level sections_online (for master CV online view)
 * POST: saves sections_online to profiles table
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input && ($_SERVER['CONTENT_TYPE'] ?? '') === 'application/x-www-form-urlencoded') {
    $input = $_POST;
}

$csrf = $input[CSRF_TOKEN_NAME] ?? post(CSRF_TOKEN_NAME) ?? '';
if (!verifyCsrfToken($csrf)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$userId = getUserId();
$validSections = [
    'profile', 'summary', 'work', 'education', 'areasOfExpertise', 'skills', 'projects',
    'certifications', 'memberships', 'interests', 'qualificationEquivalence'
];

$hasSectionsInput = isset($input['sections_online']) && is_array($input['sections_online']);
$hasPhotoInput = isset($input['show_photo_online']);
if (!$hasSectionsInput && !isset($input['show_responsibilities_online']) && !$hasPhotoInput) {
    http_response_code(400);
    echo json_encode(['error' => 'sections_online, show_responsibilities_online, or show_photo_online required']);
    exit;
}

// Merge onto whatever is already saved so a caller that only knows about one
// preference (e.g. a single section's own form) can't silently reset the rest.
$existing = [];
$existingRow = db()->fetchOne("SELECT sections_online FROM profiles WHERE id = ?", [$userId]);
if ($existingRow && !empty($existingRow['sections_online'])) {
    $decoded = json_decode($existingRow['sections_online'], true);
    if (is_array($decoded)) {
        $existing = $decoded;
    }
}

$sections = [];
foreach ($validSections as $s) {
    if ($hasSectionsInput && array_key_exists($s, $input['sections_online'])) {
        $sections[$s] = (bool) $input['sections_online'][$s];
    } elseif (array_key_exists($s, $existing)) {
        $sections[$s] = (bool) $existing[$s];
    } else {
        $sections[$s] = true;
    }
}
// Extra online display preferences stored alongside section booleans.
// (Kept here to avoid additional schema changes.)
if (isset($input['show_responsibilities_online'])) {
    $sections['show_responsibilities_online'] = (bool) $input['show_responsibilities_online'];
} elseif (array_key_exists('show_responsibilities_online', $existing)) {
    $sections['show_responsibilities_online'] = (bool) $existing['show_responsibilities_online'];
}

$json = json_encode($sections);

// show_photo/show_qr_code are plain profiles columns (read directly by cv.php), not
// part of the sections_online JSON blob - kept as raw columns here, just persisted
// through this same "online CV settings" endpoint.
$updateData = ['sections_online' => $json];
if ($hasPhotoInput) {
    $updateData['show_photo'] = $input['show_photo_online'] ? 1 : 0;
    $updateData['show_qr_code'] = $input['show_photo_online'] ? 0 : 1;
}

try {
    db()->update('profiles', $updateData, 'id = ?', [$userId]);
} catch (Exception $e) {
    error_log('save-profile-sections-online: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save']);
    exit;
}

echo json_encode([
    'success' => true,
    'sections_online' => $sections,
    'csrf_token' => csrfToken()
]);
