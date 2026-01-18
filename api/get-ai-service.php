<?php
/**
 * Get User's AI Service Preference
 * Returns the user's current AI service configuration
 */

define('SKIP_CANONICAL_REDIRECT', true);
require_once __DIR__ . '/../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $user = getCurrentUser();
    
    // Get AI service preference
    $aiService = $user['ai_service_preference'] ?? env('AI_SERVICE', 'ollama');
    
    // Normalise service name
    $aiService = strtolower(trim($aiService));
    
    // Determine if it's a paid service
    $paidServices = ['openai', 'anthropic', 'gemini', 'grok'];
    $isPaid = in_array($aiService, $paidServices);
    
    echo json_encode([
        'success' => true,
        'service' => $aiService,
        'is_paid' => $isPaid,
        'is_free' => !$isPaid
    ]);
} catch (Exception $e) {
    error_log("Error getting AI service: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to get AI service configuration'
    ]);
}

