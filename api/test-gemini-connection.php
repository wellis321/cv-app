<?php
/**
 * Test Gemini Connection
 * Tests user-provided Google Gemini API key
 */

define('SKIP_CANONICAL_REDIRECT', true);
require_once __DIR__ . '/../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

header('Content-Type: application/json');

$apiKey = $_POST['api_key'] ?? '';

if (empty($apiKey)) {
    echo json_encode(['success' => false, 'error' => 'API key is required']);
    exit;
}

// Validate API key format (Gemini keys are typically alphanumeric, at least 20 chars)
if (strlen($apiKey) < 20) {
    echo json_encode(['success' => false, 'error' => 'Invalid API key format']);
    exit;
}

try {
    // Test connection by making a simple API call to list models
    $url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($apiKey);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        echo json_encode(['success' => false, 'error' => 'Connection error: ' . $curlError]);
        exit;
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        $modelCount = is_array($data['models'] ?? null) ? count($data['models']) : 0;
        echo json_encode([
            'success' => true,
            'message' => 'Connection successful! Gemini API key is valid.',
            'model_count' => $modelCount
        ]);
    } elseif ($httpCode === 400 || $httpCode === 401 || $httpCode === 403) {
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error']['message'] ?? 'Invalid API key. Please check your key and try again.';
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    } else {
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error']['message'] ?? 'Connection failed. HTTP ' . $httpCode;
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    }
} catch (Exception $e) {
    error_log("Gemini connection test error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Connection test failed: ' . $e->getMessage()]);
}

