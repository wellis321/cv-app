<?php
/**
 * API endpoint to get guidance for a section
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../php/helpers.php';
require_once __DIR__ . '/../../php/section-guidance.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$sectionId = $_GET['section_id'] ?? '';

if (empty($sectionId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Section ID required']);
    exit;
}

try {
    $guidance = getSectionGuidance($sectionId);
    echo json_encode(['success' => true, 'guidance' => $guidance]);
} catch (Exception $e) {
    error_log("Get guidance error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load guidance']);
}
