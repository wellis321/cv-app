<?php
/**
 * LLMfit Recommend API
 * Localhost-only: runs `llmfit recommend --json --limit 10` to get model recommendations.
 * When exec/shell_exec is disabled or llmfit not in PATH, returns available: false and message to use manual paste.
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Only allow from localhost
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
$allowed = in_array($remoteAddr, ['127.0.0.1', '::1'])
    || $remoteAddr === 'localhost'
    || preg_match('/^127\.\d+\.\d+\.\d+$/', $remoteAddr);

if (!$allowed) {
    echo json_encode([
        'available' => false,
        'message' => 'LLMfit auto-detect is only available when running locally. Run <code>llmfit recommend --json --limit 20</code> in Terminal and paste the output in the Model Guide.',
    ]);
    exit;
}

// Check if shell_exec is available
$disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
if (in_array('shell_exec', $disabled) || !function_exists('shell_exec')) {
    echo json_encode([
        'available' => false,
        'message' => 'Server cannot run shell commands. Run <code>llmfit recommend --json --limit 20</code> in Terminal and paste the output below.',
    ]);
    exit;
}

$limit = 20;
$cmd = sprintf('llmfit recommend --json --limit %d 2>/dev/null', (int) $limit);
$output = @shell_exec($cmd);

if ($output === null || trim($output) === '') {
    echo json_encode([
        'available' => false,
        'message' => 'LLMfit not found or produced no output. Install with <code>brew install llmfit</code> if needed, then run <code>llmfit recommend --json --limit 20</code> and paste the output below.',
    ]);
    exit;
}

$parsed = json_decode(trim($output), true);
if (!is_array($parsed)) {
    echo json_encode([
        'available' => false,
        'message' => 'LLMfit output was not valid JSON. Run <code>llmfit recommend --json --limit 20</code> and paste the output below.',
    ]);
    exit;
}

$models = [];
if (isset($parsed['models']) && is_array($parsed['models'])) {
    $models = $parsed['models'];
} elseif (isset($parsed['recommendations']) && is_array($parsed['recommendations'])) {
    $models = $parsed['recommendations'];
} elseif (isset($parsed[0]) && is_array($parsed[0])) {
    $models = $parsed;
}

$result = [];
$sc = null;
foreach (array_slice($models, 0, $limit) as $m) {
    $name = $m['name'] ?? $m['model'] ?? $m['model_id'] ?? $m['id'] ?? null;
    if ($name === null && is_string($m)) {
        $name = $m;
    } elseif (is_array($name)) {
        $name = $name['display'] ?? $name['name'] ?? json_encode($name);
    }
    $sc = $m['score_components'] ?? $m['scores'] ?? null;
    $result[] = [
        'name' => (string) $name,
        'quality' => $sc['quality'] ?? $m['quality'] ?? null,
        'speed' => $sc['speed'] ?? $m['speed'] ?? null,
        'fit' => $sc['fit'] ?? $m['fit'] ?? null,
        'score' => $m['score'] ?? null,
    ];
}

echo json_encode([
    'success' => true,
    'models' => $result,
]);
