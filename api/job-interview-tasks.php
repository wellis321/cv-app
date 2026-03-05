<?php
/**
 * Job Interview Tasks API
 * CRUD for interview tasks (questions, assignments, presentations, case studies)
 */

require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/job-applications.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'];
$input = [];
if ($method === 'GET') {
    $applicationId = $_GET['application_id'] ?? null;
} else {
    $raw = file_get_contents('php://input');
    if ($raw !== false) {
        $decoded = json_decode($raw, true);
        $input = is_array($decoded) ? $decoded : [];
    }
    if (empty($input)) {
        if ($method === 'POST' || $method === 'PATCH' || $method === 'PUT') {
            $input = $_POST;
        }
    }
    $applicationId = $input['application_id'] ?? null;
    $taskId = $input['task_id'] ?? null;
    $taskDescription = $input['task_description'] ?? null;
    $taskType = $input['task_type'] ?? 'question';
    $title = isset($input['title']) ? trim($input['title']) : null;
    $deadline = isset($input['deadline']) ? trim($input['deadline']) : null;
    $userNotes = isset($input['user_notes']) ? $input['user_notes'] : null;
    $aiSuggestions = isset($input['ai_suggestions']) ? $input['ai_suggestions'] : null;
    $sortOrder = isset($input['sort_order']) ? (int) $input['sort_order'] : 0;
}

$csrfToken = $input['csrf_token'] ?? $_GET['csrf_token'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if (!$applicationId) {
                http_response_code(400);
                echo json_encode(['error' => 'application_id required']);
                exit;
            }
            $job = getJobApplication($applicationId, $userId);
            if (!$job) {
                http_response_code(404);
                echo json_encode(['error' => 'Application not found']);
                exit;
            }
            $tasks = getJobInterviewTasks($applicationId, $userId);
            echo json_encode(['success' => true, 'tasks' => $tasks]);
            break;

        case 'POST':
            if (!verifyCsrfToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
            if (!$applicationId || $taskDescription === null || trim($taskDescription) === '') {
                http_response_code(400);
                echo json_encode(['error' => 'application_id and task_description required']);
                exit;
            }
            $result = addJobInterviewTask($applicationId, $userId, trim($taskDescription), [
                'task_type' => $taskType,
                'title' => $title,
                'deadline' => $deadline ?: null,
                'sort_order' => $sortOrder,
            ]);
            if (!$result['success']) {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
                exit;
            }
            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $result['id']]);
            break;

        case 'PATCH':
        case 'PUT':
            if (!verifyCsrfToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
            if (!$taskId) {
                http_response_code(400);
                echo json_encode(['error' => 'task_id required']);
                exit;
            }
            $fields = [];
            if (array_key_exists('title', $input)) {
                $fields['title'] = $input['title'];
            }
            if (array_key_exists('task_description', $input)) {
                $fields['task_description'] = $input['task_description'];
            }
            if (array_key_exists('task_type', $input)) {
                $fields['task_type'] = $input['task_type'];
            }
            if (array_key_exists('deadline', $input)) {
                $fields['deadline'] = $input['deadline'];
            }
            if (array_key_exists('user_notes', $input)) {
                $fields['user_notes'] = $input['user_notes'];
            }
            if (array_key_exists('ai_suggestions', $input)) {
                $fields['ai_suggestions'] = $input['ai_suggestions'];
            }
            if (array_key_exists('sort_order', $input)) {
                $fields['sort_order'] = (int) $input['sort_order'];
            }
            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['error' => 'Provide at least one field to update']);
                exit;
            }
            $result = updateJobInterviewTaskFields($taskId, $userId, $fields);
            if (!$result['success']) {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
                exit;
            }
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            if (!verifyCsrfToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
            $tid = $taskId ?? ($input['task_id'] ?? $_GET['task_id'] ?? null);
            if (!$tid) {
                http_response_code(400);
                echo json_encode(['error' => 'task_id required']);
                exit;
            }
            $result = deleteJobInterviewTask($tid, $userId);
            if (!$result['success']) {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
                exit;
            }
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
