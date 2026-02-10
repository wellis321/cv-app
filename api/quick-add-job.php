<?php
/**
 * Quick-add job API: create a job application from URL + title (e.g. from browser extension).
 * Auth: session + CSRF (in-app) or Authorization: Bearer <job_saver_token> (extension).
 * POST JSON: url (required), title (optional), closing_date (optional Y-m-d), priority (optional low|medium|high).
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

// CORS: allow browser extension only; auth is by Bearer token
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$isExtensionOrigin = $origin !== '' && (strpos($origin, 'chrome-extension://') === 0 || strpos($origin, 'moz-extension://') === 0);
if ($isExtensionOrigin) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

$method = $_SERVER['REQUEST_METHOD'] ?? '';
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = is_string($rawInput) ? json_decode($rawInput, true) : null;
if (!is_array($input)) {
    $input = [];
}

$userId = null;
$authViaToken = false;

// 1) Try Bearer token (extension)
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (preg_match('/^\s*Bearer\s+(.+)$/i', $authHeader, $m)) {
    $token = trim($m[1]);
    $userId = getUserIdFromJobSaverToken($token);
    if ($userId) {
        $authViaToken = true;
    }
}

// 2) Fall back to session + CSRF (in-app)
if (!$userId && isLoggedIn()) {
    $csrf = $input['csrf_token'] ?? '';
    if (verifyCsrfToken($csrf)) {
        $userId = getUserId();
    }
}

if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => $authViaToken ? 'Invalid or expired save token. Regenerate it in your account settings.' : 'Authentication required']);
    exit;
}

$url = isset($input['url']) ? trim((string) $input['url']) : '';
if ($url === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Job URL is required']);
    exit;
}

$title = isset($input['title']) ? trim((string) $input['title']) : '';
$closingDate = isset($input['closing_date']) ? trim((string) $input['closing_date']) : '';
$priority = isset($input['priority']) ? trim((string) $input['priority']) : '';
if ($priority !== '' && !in_array($priority, ['low', 'medium', 'high'], true)) {
    $priority = '';
}

$data = [
    'quick_add' => true,
    'application_url' => $url,
    'job_title' => $title !== '' ? $title : deriveJobTitleFromUrl($url),
    'company_name' => '—',
    'status' => 'interested',
    'next_follow_up' => $closingDate !== '' ? $closingDate : null,
];
if ($priority !== '') {
    $data['priority'] = $priority;
}
if (!$authViaToken) {
    $data['csrf_token'] = csrfToken();
}

$result = createJobApplication($data, $userId);
if ($result['success']) {
    http_response_code(201);
    echo json_encode(['success' => true, 'id' => $result['id']]);
} else {
    http_response_code(400);
    echo json_encode(['error' => $result['error'] ?? 'Failed to save job']);
}
