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

if (!isset($input['sections_online']) || !is_array($input['sections_online'])) {
    http_response_code(400);
    echo json_encode(['error' => 'sections_online required']);
    exit;
}

$sections = [];
foreach ($validSections as $s) {
    $sections[$s] = isset($input['sections_online'][$s]) ? (bool) $input['sections_online'][$s] : true;
}

$json = json_encode($sections);

try {
    db()->update('profiles', ['sections_online' => $json], 'id = ?', [$userId]);
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
