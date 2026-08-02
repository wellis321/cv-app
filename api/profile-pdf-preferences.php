<?php
/**
 * API endpoint for master-CV PDF preferences (per-section PDF inclusion, accent
 * colour, photo/QR PDF defaults, "include responsibilities in PDF").
 * GET: returns saved preferences for the logged-in user's profile
 * POST: saves preferences for the logged-in user's profile
 *
 * Deliberately narrower than api/variant-pdf-preferences.php: template selection
 * has its own profiles.preferred_template_id column, and section/responsibilities
 * online-CV visibility goes through api/save-profile-sections-online.php - both
 * already exist and shouldn't be duplicated into this JSON blob.
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'];

$validSections = [
    'profile', 'summary', 'work', 'education', 'areasOfExpertise', 'skills', 'projects',
    'certifications', 'memberships', 'interests', 'qualificationEquivalence'
];

if ($method === 'GET') {
    $profile = db()->fetchOne("SELECT pdf_preferences FROM profiles WHERE id = ?", [$userId]);
    echo json_encode([
        'preferences' => getPdfPreferencesForCv($profile),
        'csrf_token' => csrfToken()
    ]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input && ($_SERVER['CONTENT_TYPE'] ?? '') === 'application/x-www-form-urlencoded') {
        $input = $_POST;
    }
    $csrf = $input[CSRF_TOKEN_NAME] ?? post(CSRF_TOKEN_NAME) ?? '';
    if (!verifyCsrfToken($csrf)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid security token']);
        exit;
    }

    $prefs = [];
    if (isset($input['sections']) && is_array($input['sections'])) {
        $prefs['sections'] = [];
        foreach ($validSections as $s) {
            $prefs['sections'][$s] = isset($input['sections'][$s]) ? (bool) $input['sections'][$s] : true;
        }
    }
    if (isset($input['colour_preset']) && is_string($input['colour_preset'])) {
        $prefs['colour_preset'] = trim($input['colour_preset']);
    }
    if (isset($input['custom_accent_hex']) && preg_match('/^#[0-9A-Fa-f]{6}$/', trim($input['custom_accent_hex'] ?? ''))) {
        $prefs['custom_accent_hex'] = trim($input['custom_accent_hex']);
    }
    if (isset($input['include_photo'])) {
        $prefs['include_photo'] = (bool) $input['include_photo'];
    }
    if (isset($input['include_qr'])) {
        $prefs['include_qr'] = (bool) $input['include_qr'];
    }
    if (isset($input['show_responsibilities_in_pdf'])) {
        $prefs['show_responsibilities_in_pdf'] = (bool) $input['show_responsibilities_in_pdf'];
    }

    if (empty($prefs)) {
        http_response_code(400);
        echo json_encode(['error' => 'No valid preferences provided']);
        exit;
    }

    $profile = db()->fetchOne("SELECT pdf_preferences FROM profiles WHERE id = ?", [$userId]);
    $existing = [];
    if (!empty($profile['pdf_preferences'])) {
        $decoded = is_string($profile['pdf_preferences'])
            ? json_decode($profile['pdf_preferences'], true)
            : $profile['pdf_preferences'];
        if (is_array($decoded)) {
            $existing = $decoded;
        }
    }

    $merged = $existing;
    foreach ($prefs as $k => $v) {
        $merged[$k] = $v;
    }
    $json = json_encode($merged);

    db()->update('profiles', ['pdf_preferences' => $json], 'id = ?', [$userId]);

    echo json_encode([
        'success' => true,
        'preferences' => $merged,
        'csrf_token' => csrfToken()
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
