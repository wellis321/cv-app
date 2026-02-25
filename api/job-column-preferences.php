<?php
/**
 * API endpoint for job applications column visibility preferences
 * GET: returns saved preferences
 * POST: saves preferences (JSON body: { columns: { company: true, job_title: false, ... } })
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'];

$validColumns = ['company', 'job_title', 'status', 'priority', 'closing_date', 'location', 'salary', 'date_added', 'actions'];

if ($method === 'GET') {
    $row = db()->fetchOne(
        "SELECT job_applications_column_visibility FROM profiles WHERE id = ?",
        [$userId]
    );
    $prefs = null;
    if (!empty($row['job_applications_column_visibility'])) {
        $decoded = json_decode($row['job_applications_column_visibility'], true);
        if (is_array($decoded)) {
            $prefs = [];
            foreach ($validColumns as $col) {
                $prefs[$col] = isset($decoded[$col]) ? (bool) $decoded[$col] : true;
            }
        }
    }
    echo json_encode([
        'columns' => $prefs,
        'csrf_token' => csrfToken()
    ]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $csrf = $input[CSRF_TOKEN_NAME] ?? post(CSRF_TOKEN_NAME) ?? '';
    if (!verifyCsrfToken($csrf)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid security token']);
        exit;
    }
    if (!is_array($input) || !isset($input['columns'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }
    $cols = [];
    foreach ($validColumns as $col) {
        $cols[$col] = isset($input['columns'][$col]) ? (bool) $input['columns'][$col] : true;
    }
    $json = json_encode($cols);
    db()->update(
        'profiles',
        ['job_applications_column_visibility' => $json],
        'id = ?',
        [$userId]
    );
    echo json_encode([
        'success' => true,
        'columns' => $cols,
        'csrf_token' => csrfToken()
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
