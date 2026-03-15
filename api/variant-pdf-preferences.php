<?php
/**
 * API endpoint for variant PDF preferences (template, sections, colours)
 * GET: returns saved preferences for a variant
 * POST: saves preferences for a variant
 */

require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/cv-variants.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'];

$variantId = trim($_GET['variant_id'] ?? $_POST['variant_id'] ?? '');
if (!$variantId && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $variantId = trim($input['variant_id'] ?? '');
}

if (!$variantId) {
    http_response_code(400);
    echo json_encode(['error' => 'variant_id required']);
    exit;
}

$variant = getCvVariant($variantId, $userId);
if (!$variant) {
    http_response_code(404);
    echo json_encode(['error' => 'Variant not found']);
    exit;
}

$validSections = [
    'profile', 'summary', 'work', 'education', 'areasOfExpertise', 'skills', 'projects',
    'certifications', 'memberships', 'interests', 'qualificationEquivalence'
];

// Section keys for online CV (same set, stored separately from PDF sections)
$validSectionsOnline = $validSections;

if ($method === 'GET') {
    $prefs = null;
    if (!empty($variant['pdf_preferences'])) {
        $decoded = is_string($variant['pdf_preferences'])
            ? json_decode($variant['pdf_preferences'], true)
            : $variant['pdf_preferences'];
        if (is_array($decoded)) {
            $prefs = [
                'preferred_template_id' => $decoded['preferred_template_id'] ?? null,
                'sections' => [],
                'colour_preset' => $decoded['colour_preset'] ?? null,
                'custom_accent_hex' => $decoded['custom_accent_hex'] ?? null,
                'include_photo' => isset($decoded['include_photo']) ? (bool) $decoded['include_photo'] : null,
                'include_qr' => isset($decoded['include_qr']) ? (bool) $decoded['include_qr'] : null,
                'show_responsibilities_in_pdf' => isset($decoded['show_responsibilities_in_pdf']) ? (bool) $decoded['show_responsibilities_in_pdf'] : true,
            ];
            $sections = $decoded['sections'] ?? [];
            foreach ($validSections as $s) {
                $prefs['sections'][$s] = isset($sections[$s]) ? (bool) $sections[$s] : true;
            }
            $sectionsOnline = $decoded['sections_online'] ?? [];
            $prefs['sections_online'] = [];
            foreach ($validSectionsOnline as $s) {
                $prefs['sections_online'][$s] = isset($sectionsOnline[$s]) ? (bool) $sectionsOnline[$s] : true;
            }
        }
    }
    echo json_encode([
        'preferences' => $prefs,
        'csrf_token' => csrfToken()
    ]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input && $_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded') {
        $input = $_POST;
    }
    $csrf = $input[CSRF_TOKEN_NAME] ?? post(CSRF_TOKEN_NAME) ?? '';
    if (!verifyCsrfToken($csrf)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid security token']);
        exit;
    }

    $prefs = [];
    if (isset($input['preferred_template_id']) && is_string($input['preferred_template_id'])) {
        $prefs['preferred_template_id'] = trim($input['preferred_template_id']);
    }
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
    if (isset($input['sections_online']) && is_array($input['sections_online'])) {
        $prefs['sections_online'] = [];
        foreach ($validSectionsOnline as $s) {
            $prefs['sections_online'][$s] = isset($input['sections_online'][$s]) ? (bool) $input['sections_online'][$s] : true;
        }
    }

    $existing = [];
    if (!empty($variant['pdf_preferences'])) {
        $decoded = is_string($variant['pdf_preferences'])
            ? json_decode($variant['pdf_preferences'], true)
            : $variant['pdf_preferences'];
        if (is_array($decoded)) $existing = $decoded;
    }

    $merged = $existing;
    foreach ($prefs as $k => $v) {
        $merged[$k] = $v;
    }
    $json = json_encode($merged);

    db()->update(
        'cv_variants',
        ['pdf_preferences' => $json],
        'id = ? AND user_id = ?',
        [$variantId, $userId]
    );

    echo json_encode([
        'success' => true,
        'preferences' => $merged,
        'csrf_token' => csrfToken()
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
