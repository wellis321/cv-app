<?php
/**
 * AI Personal Statement Generation API
 * Generates a 500-word personal statement based on job description, CV keywords, and CV data.
 * Applies humaniser to reduce AI detection.
 */

define('SKIP_CANONICAL_REDIRECT', true);

ob_start();
@set_time_limit(300);
@ini_set('max_execution_time', '300');

require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/ai-service.php';
require_once __DIR__ . '/../php/cv-data.php';
require_once __DIR__ . '/../php/job-applications.php';
require_once __DIR__ . '/../php/cv-variants.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$userId = getUserId();
$applicationId = $_POST['job_application_id'] ?? null;
$cvVariantId = $_POST['cv_variant_id'] ?? null;
$customInstructions = trim($_POST['custom_instructions'] ?? '');
$lengthInstructions = trim($_POST['length_instructions'] ?? '');
$humanizeFurther = isset($_POST['humanize_further']) && $_POST['humanize_further'] === '1';
$browserAiResult = trim($_POST['personal_statement_text'] ?? '');

if (!$applicationId) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Job application ID is required']);
    exit;
}

$jobApplication = getJobApplication($applicationId, $userId);
if (!$jobApplication) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Job application not found']);
    exit;
}

// If client sends browser AI result, save to job application and return
if ($browserAiResult !== '') {
    $toSave = $browserAiResult;
    if (function_exists('convertToBritishSpelling')) {
        $toSave = convertToBritishSpelling($toSave);
    }
    $maxChars = $lengthInstructions ? parseAnswerCharacterLimit($lengthInstructions) : null;
    $maxWords = $lengthInstructions ? parseAnswerWordLimit($lengthInstructions) : null;
    if ($maxChars !== null && function_exists('truncateToCharacterLimit')) {
        $toSave = truncateToCharacterLimit($toSave, $maxChars);
    } elseif ($maxWords !== null && function_exists('truncateToWordLimit')) {
        $toSave = truncateToWordLimit($toSave, $maxWords);
    } else {
        $wordCount = str_word_count($toSave);
        if ($wordCount > 550) {
            $words = preg_split('/\s+/', $toSave, 501);
            $toSave = implode(' ', array_slice($words, 0, 500));
        }
    }
    $updateResult = updateJobApplication($applicationId, ['personal_statement' => $toSave], $userId);
    if (!$updateResult['success']) {
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => $updateResult['error'] ?? 'Failed to save']);
        exit;
    }
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'personal_statement_text' => $toSave,
        'message' => 'Personal statement saved',
    ]);
    exit;
}

// Load CV data
if ($cvVariantId) {
    $variant = getCvVariant($cvVariantId, $userId);
    if (!$variant) {
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => 'CV variant not found']);
        exit;
    }
    $cvData = loadCvVariantData($cvVariantId);
} else {
    $cvData = loadCvData($userId);
}

if (!$cvData || empty($cvData)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'No CV data found. Please create your CV first.']);
    exit;
}

$aiService = new AIService($userId);
$result = $aiService->generatePersonalStatement($cvData, $jobApplication, [
    'custom_instructions' => $customInstructions ?: null,
    'length_instructions' => $lengthInstructions ?: null,
    'humanize_further' => $humanizeFurther,
]);

if (!$result['success']) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Failed to generate']);
    exit;
}

if (isset($result['browser_execution']) && $result['browser_execution']) {
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'browser_execution' => true,
        'prompt' => $result['prompt'] ?? '',
        'model' => $result['model'] ?? 'llama3.2',
        'model_type' => $result['model_type'] ?? 'webllm',
        'humanize_further' => $result['humanize_further'] ?? $humanizeFurther,
        'job_application_id' => $applicationId,
        'length_instructions' => $lengthInstructions ?: null,
    ]);
    exit;
}

$text = $result['personal_statement_text'] ?? '';
if (function_exists('convertToBritishSpelling')) {
    $text = convertToBritishSpelling($text);
}

$updateResult = updateJobApplication($applicationId, ['personal_statement' => $text], $userId);
if (!$updateResult['success']) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $updateResult['error'] ?? 'Failed to save']);
    exit;
}

ob_end_clean();
echo json_encode([
    'success' => true,
    'personal_statement_text' => $text,
    'message' => 'Personal statement generated and saved',
]);
