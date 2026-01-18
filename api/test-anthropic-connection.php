<?php
/**
 * Test Anthropic Connection
 * Tests user-provided Anthropic API key
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

// Validate API key format
if (!preg_match('/^sk-ant-[a-zA-Z0-9\-_]{95,}$/', $apiKey)) {
    echo json_encode(['success' => false, 'error' => 'Invalid API key format']);
    exit;
}

try {
    // Test connection by making a simple API call (list messages - minimal request)
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'claude-3-haiku-20240307', // Use smallest model for testing
            'max_tokens' => 10,
            'messages' => [
                ['role' => 'user', 'content' => 'test']
            ]
        ]),
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
        echo json_encode([
            'success' => true,
            'message' => 'Connection successful! Anthropic API key is valid.'
        ]);
    } elseif ($httpCode === 401) {
        echo json_encode(['success' => false, 'error' => 'Invalid API key. Please check your key and try again.']);
    } else {
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error']['message'] ?? 'Connection failed. HTTP ' . $httpCode;
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    }
} catch (Exception $e) {
    error_log("Anthropic connection test error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Connection test failed: ' . $e->getMessage()]);
}

