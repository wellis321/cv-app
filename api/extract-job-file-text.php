<?php
/**
 * API endpoint for extracting text from job application files
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_time_limit(120);

$extractResponseSent = false;
$debugLogPath = dirname(__DIR__) . '/.cursor/debug.log';
$extractLog = function ($step) use ($debugLogPath) {
    if (!(defined('DEBUG') && DEBUG)) return;
    if (!is_dir(dirname($debugLogPath))) return;
    @file_put_contents($debugLogPath, date('c') . ' extract: ' . $step . "\n", FILE_APPEND);
};
register_shutdown_function(function () use (&$extractResponseSent, $debugLogPath) {
    if ($extractResponseSent) return;
    if (defined('DEBUG') && DEBUG && is_dir(dirname($debugLogPath))) {
        @file_put_contents($debugLogPath, date('c') . ' extract: shutdown (no response sent)' . "\n", FILE_APPEND);
    }
    @ob_end_clean();
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    echo json_encode(['success' => false, 'error' => 'Request failed. Try again or uncheck "Format with AI" when extracting.']);
});

require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/document-extractor.php';

/**
 * If the given string is JSON (object with string values), convert to plain text:
 * "Section Name\n\nContent\n\n" for each key so the job description field shows readable text.
 */
function flattenJsonJobDescriptionToPlainText($text) {
    $trimmed = trim($text);
    if ($trimmed === '' || $trimmed[0] !== '{') {
        return $text;
    }
    $decoded = json_decode($trimmed, true);
    if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
        return $text;
    }
    $parts = [];
    foreach ($decoded as $heading => $content) {
        $heading = is_string($heading) ? trim($heading) : '';
        $content = is_string($content) ? trim($content) : (is_scalar($content) ? (string) $content : '');
        $parts[] = $heading !== '' ? $heading . "\n\n" . $content : $content;
    }
    return implode("\n\n", $parts);
}

/**
 * If the content looks like our own API response (e.g. starts with {"text":" or { "text": ")
 * unwrap it so the description field gets plain text only, not the JSON wrapper.
 * Handles both valid JSON and malformed (e.g. unescaped quotes in content) by stripping prefix/suffix.
 */
function unwrapApiTextResponse($text) {
    $trimmed = trim($text);
    if ($trimmed === '' || $trimmed[0] !== '{') return $text;

    $decoded = json_decode($trimmed, true);
    if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE && isset($decoded['text']) && is_string($decoded['text'])) {
        return $decoded['text'];
    }

    // Fallback: content may be malformed JSON (e.g. unescaped " inside the value). Strip literal prefix and suffix.
    $prefixes = ['{"text":"', '{ "text": "', "{\"text\":\"", "{ \"text\": \""];
    $start = null;
    foreach ($prefixes as $prefix) {
        if (strpos($trimmed, $prefix) === 0) {
            $start = strlen($prefix);
            break;
        }
    }
    if ($start === null) return $text;

    $suffixes = ['" }', '" }', '"}'];
    $end = false;
    foreach ($suffixes as $suffix) {
        $pos = strrpos($trimmed, $suffix);
        if ($pos !== false && $pos > $start) {
            if ($end === false || $pos > $end) $end = $pos;
        }
    }
    if ($end === false) return $text;

    $inner = substr($trimmed, $start, $end - $start);
    $inner = str_replace(['\\n', '\\r', '\\t', '\\"', '\\\\'], ["\n", "\r", "\t", '"', '\\'], $inner);
    return $inner;
}

$extractLog('loaded');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $extractResponseSent = true;
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    $extractResponseSent = true;
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$extractLog('auth_ok');

$token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($token)) {
    $extractResponseSent = true;
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

try {
    $fileId = $_POST['file_id'] ?? null;
    $applicationId = $_POST['application_id'] ?? null;
    
    if (!$fileId && !$applicationId) {
        throw new Exception('Either file_id or application_id is required');
    }
    
    // Get file information
    if ($fileId) {
        $file = db()->fetchOne(
            "SELECT * FROM job_application_files WHERE id = ? AND user_id = ?",
            [$fileId, $userId]
        );
        
        if (!$file) {
            throw new Exception('File not found');
        }
    } else {
        // Get first file for the application with purpose 'other' (job description)
        $file = db()->fetchOne(
            "SELECT * FROM job_application_files WHERE application_id = ? AND user_id = ? ORDER BY uploaded_at DESC LIMIT 1",
            [$applicationId, $userId]
        );
        
        if (!$file) {
            throw new Exception('No files found for this application');
        }
    }
    
    // Build file path
    $filePath = STORAGE_PATH . '/' . $file['stored_name'];
    
    if (!file_exists($filePath)) {
        throw new Exception('File not found on disk');
    }
    
    $extractLog('file_ok');
    
    // Extract text (use file extension as fallback if MIME is generic)
    $mimeType = $file['mime_type'] ?? '';
    $extractionResult = extractDocumentText($filePath, $mimeType, $file['original_name'] ?? '');
    
    $extractLog('extract_done');
    
    if (!$extractionResult['success']) {
        $extractResponseSent = true;
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'error' => $extractionResult['error'] ?? 'Failed to extract text'
        ]);
        exit;
    }
    
    $text = $extractionResult['text'];
    
    // Optionally format with AI for clearer sections and paragraphs (cap length to avoid timeout).
    // Skip AI formatting when extraction already contains HTML tables so we don't strip them.
    if (strpos($text, '<table') !== false && !empty($_POST['format_with_ai'])) {
        $extractLog('ai_format_skipped_has_tables');
    }
    if (!empty($_POST['format_with_ai']) && function_exists('getAIService') && strpos($text, '<table') === false) {
        $extractLog('ai_format_start');
        try {
            $aiService = getAIService($userId);
            if ($aiService && method_exists($aiService, 'formatJobDescriptionText')) {
                $maxLen = 12000;
                $textToFormat = strlen($text) > $maxLen ? substr($text, 0, $maxLen) . "\n\n[... truncated for formatting ...]" : $text;
                $formatResult = $aiService->formatJobDescriptionText($textToFormat);
                if (!empty($formatResult['text'])) {
                    $text = strlen($text) > $maxLen ? $formatResult['text'] . "\n\n" . substr($text, $maxLen) : $formatResult['text'];
                }
            }
        } catch (Exception $e) {
            // Keep raw text on any error
            error_log("Format job description with AI: " . $e->getMessage());
        }
        $extractLog('ai_format_done');
        // If the AI returned JSON (section keys -> content), flatten to plain text for the description field
        $text = flattenJsonJobDescriptionToPlainText($text);
    }
    
    // Ensure we never send a "text" value that is itself JSON like {"text":"..."} (unwrap if present)
    $text = unwrapApiTextResponse($text);
    
    $extractResponseSent = true;
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'text' => $text,
        'file_id' => $file['id'],
        'file_name' => $file['original_name']
    ]);
    
} catch (Exception $e) {
    $extractResponseSent = true;
    ob_end_clean();
    http_response_code(500);
    error_log("Extract file text error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => DEBUG ? $e->getMessage() : 'Failed to extract text'
    ]);
}

