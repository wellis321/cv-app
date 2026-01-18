<?php
/**
 * Save Prompt Instructions API Endpoint
 * Saves user's custom CV rewrite prompt instructions
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering
ob_start();

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check authentication
$user = getCurrentUser();
if (!$user) {
    http_response_code(401);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Verify CSRF token
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

try {
    $instructions = $_POST['instructions'] ?? '';
    
    // Validate length (max 2000 characters)
    if (strlen($instructions) > 2000) {
        throw new Exception('Instructions must be 2000 characters or less');
    }
    
    // Sanitize instructions (allow newlines and basic formatting)
    $instructions = trim($instructions);
    
    // Check if column exists, if not, log warning but continue
    try {
        db()->update('profiles',
            ['cv_rewrite_prompt_instructions' => $instructions ?: null],
            'id = ?',
            [$user['id']]
        );
    } catch (Exception $e) {
        // Column might not exist yet - check if it's a column error
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            error_log("cv_rewrite_prompt_instructions column not found. Please run migration: database/20250126_add_custom_prompt_instructions.sql");
            throw new Exception('Prompt customisation feature is not available. Please contact support.');
        }
        throw $e;
    }
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Instructions saved successfully'
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log("Save Prompt Instructions Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

