<?php
/**
 * Test Ollama Connection API Endpoint
 * Proxies the connection test to avoid CSP issues
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
    $baseUrl = $_POST['base_url'] ?? '';
    
    if (empty($baseUrl)) {
        throw new Exception('Base URL is required');
    }
    
    // Validate URL format
    if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid URL format');
    }
    
    // Only allow localhost connections for security
    $parsedUrl = parse_url($baseUrl);
    $host = $parsedUrl['host'] ?? '';
    
    if (!in_array($host, ['localhost', '127.0.0.1', '::1']) && !preg_match('/^127\.\d+\.\d+\.\d+$/', $host)) {
        throw new Exception('Only localhost connections are allowed for security reasons');
    }
    
    // Test connection to Ollama
    $testUrl = rtrim($baseUrl, '/') . '/api/tags';
    
    $ch = curl_init($testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception('Connection error: ' . $error);
    }
    
    if ($httpCode !== 200) {
        throw new Exception('Ollama returned HTTP ' . $httpCode);
    }
    
    $data = json_decode($response, true);
    
    if (!$data) {
        throw new Exception('Invalid response from Ollama');
    }
    
    $models = $data['models'] ?? [];
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'models' => $models,
        'model_count' => count($models),
        'message' => count($models) > 0 
            ? 'Connection successful! Found ' . count($models) . ' model(s): ' . implode(', ', array_column($models, 'name'))
            : 'Connection successful, but no models found. Make sure you\'ve downloaded a model.'
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log("Test Ollama Connection Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

