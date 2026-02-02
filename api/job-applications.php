<?php
/**
 * Job Applications API Endpoint
 * Handles CRUD operations for job applications
 */

set_time_limit(60);

$jobApiResponseSent = false;
register_shutdown_function(function () use (&$jobApiResponseSent) {
    if ($jobApiResponseSent) return;
    @ob_end_clean();
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    echo json_encode(['error' => 'Request failed. Please try again.']);
});

require_once __DIR__ . '/../php/helpers.php';

// Set JSON headers
header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn()) {
    $jobApiResponseSent = true;
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'];
$rawInput = file_get_contents('php://input');
$input = $rawInput !== false ? json_decode($rawInput, true) : null;
if (!is_array($input)) {
    $input = [];
}

// Get application ID from URL or input
$applicationId = $_GET['id'] ?? $input['id'] ?? null;

try {
    switch ($method) {
        case 'GET':
            // Get all applications or a single application
            $jobApiResponseSent = true;
            if ($applicationId) {
                $application = getJobApplication($applicationId, $userId);
                if ($application) {
                    echo json_encode($application);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Application not found']);
                }
            } else {
                $filters = [];
                if (isset($_GET['status']) && $_GET['status'] !== 'all') {
                    $filters['status'] = $_GET['status'];
                }
                if (isset($_GET['search'])) {
                    $filters['search'] = $_GET['search'];
                }
                $applications = getUserJobApplications($userId, $filters);
                echo json_encode(['applications' => $applications, 'csrf_token' => csrfToken()]);
            }
            break;
            
        case 'POST':
            // Create new application
            if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
                $jobApiResponseSent = true;
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
            
            $result = createJobApplication($input, $userId);
            $jobApiResponseSent = true;
            if ($result['success']) {
                http_response_code(201);
                echo json_encode(['success' => true, 'id' => $result['id']]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
            }
            break;
            
        case 'PATCH':
        case 'PUT':
            // Update application
            if (!$applicationId) {
                $jobApiResponseSent = true;
                http_response_code(400);
                echo json_encode(['error' => 'Application ID required']);
                exit;
            }
            
            if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
                $jobApiResponseSent = true;
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
            
            $result = updateJobApplication($applicationId, $input, $userId);
            $jobApiResponseSent = true;
            if ($result['success']) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
            }
            break;
            
        case 'DELETE':
            // Delete application
            if (!$applicationId) {
                $jobApiResponseSent = true;
                http_response_code(400);
                echo json_encode(['error' => 'Application ID required']);
                exit;
            }
            
            if (!verifyCsrfToken($input['csrf_token'] ?? $_GET['csrf_token'] ?? '')) {
                $jobApiResponseSent = true;
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
            
            $result = deleteJobApplication($applicationId, $userId);
            $jobApiResponseSent = true;
            if ($result['success']) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
            }
            break;
            
        default:
            $jobApiResponseSent = true;
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    $jobApiResponseSent = true;
    http_response_code(500);
    if (DEBUG) {
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        echo json_encode(['error' => 'Internal server error']);
    }
}

