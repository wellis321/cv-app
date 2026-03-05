<?php
/**
 * AI Interview Task Help Generation API
 * Generates help for interview tasks (questions, assignments, presentations, case studies)
 * using job description and CV. For questions: practice answers. For assignments: outline/structure.
 */

ob_start();
set_time_limit(120);

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
$applicationId = $_POST['application_id'] ?? null;
$taskId = $_POST['task_id'] ?? null;
$cvVariantId = $_POST['cv_variant_id'] ?? null;
$browserAiResult = trim($_POST['ai_suggestions'] ?? '');

if (!$applicationId || !$taskId) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Application ID and task ID are required']);
    exit;
}

$job = getJobApplication($applicationId, $userId);
if (!$job) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Job application not found']);
    exit;
}

$tasks = getJobInterviewTasks($applicationId, $userId);
$task = null;
foreach ($tasks as $t) {
    if ($t['id'] === $taskId) {
        $task = $t;
        break;
    }
}
if (!$task) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Interview task not found']);
    exit;
}

// Allow client to pass current user_notes (e.g. unsaved edits) for this request
$postUserNotes = trim($_POST['user_notes'] ?? '');
if ($postUserNotes !== '') {
    $task['user_notes'] = $postUserNotes;
}

// If client sends browser AI result, save and return
if ($browserAiResult !== '') {
    $updated = updateJobInterviewTaskFields($taskId, $userId, ['ai_suggestions' => $browserAiResult]);
    if (!$updated['success']) {
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => $updated['error'] ?? 'Failed to save suggestions']);
        exit;
    }
    ob_end_clean();
    echo json_encode(['success' => true, 'ai_suggestions' => $browserAiResult]);
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
$result = $aiService->generateInterviewTaskHelp($cvData, $job, $task);

if (!$result['success']) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Failed to generate help']);
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
        'task_id' => $taskId,
        'application_id' => $applicationId,
    ]);
    exit;
}

$suggestionsText = $result['suggestions_text'] ?? '';
updateJobInterviewTaskFields($taskId, $userId, ['ai_suggestions' => $suggestionsText]);

ob_end_clean();
echo json_encode(['success' => true, 'ai_suggestions' => $suggestionsText]);
