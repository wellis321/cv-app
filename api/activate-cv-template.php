<?php
/**
 * Activate Custom CV Template
 */

define('SKIP_CANONICAL_REDIRECT', true);
require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/cv-templates.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit;
}

$user = getCurrentUser();
$templateId = $_POST['template_id'] ?? null;

if (!$templateId) {
    setFlash('error', 'Template ID required');
    redirect('/cv-template-customizer.php');
    exit;
}

$result = activateCvTemplate($templateId, $user['id']);

if ($result['success']) {
    setFlash('success', 'Template activated successfully');
} else {
    setFlash('error', $result['error'] ?? 'Failed to activate template');
}

redirect('/cv-template-customizer.php');

