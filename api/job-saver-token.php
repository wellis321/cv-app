<?php
/**
 * Job saver token: get (masked), copy (full), regenerate.
 * For use with the "Save job" browser extension.
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'] ?? '';
$input = [];
if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    if (is_string($raw)) {
        $input = json_decode($raw, true) ?? [];
    }
    if (!verifyCsrfToken($input['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid security token']);
        exit;
    }
}

try {
    if ($method === 'GET') {
        $token = ensureJobSaverToken($userId);
        if (!$token) {
            http_response_code(500);
            $errMsg = 'Could not create token. Run database/20250206_add_job_saver_token.sql on the same database your app uses (see .env DB_NAME).';
            try {
                db()->query('SELECT job_saver_token FROM profiles LIMIT 1');
            } catch (Throwable $e) {
                $errMsg = 'Database error: ' . $e->getMessage() . '. Run database/20250206_add_job_saver_token.sql.';
            }
            echo json_encode(['error' => $errMsg]);
            exit;
        }
        echo json_encode(['masked' => maskJobSaverToken($token), 'has_token' => true]);
        exit;
    }

    if ($method === 'POST') {
        $action = $input['action'] ?? '';
        if ($action === 'regenerate') {
            $token = regenerateJobSaverToken($userId);
            if (!$token) {
                http_response_code(500);
                echo json_encode(['error' => 'Could not regenerate or save token. Run database/20250206_add_job_saver_token.sql if you have not yet.']);
                exit;
            }
            echo json_encode([
                'token' => $token,
                'masked' => maskJobSaverToken($token),
                'message' => 'Copy your new token now; it will not be shown again.',
            ]);
            exit;
        }
        if ($action === 'copy' || $action === '') {
            $token = ensureJobSaverToken($userId);
            if (!$token) {
                http_response_code(500);
                echo json_encode(['error' => 'Could not get token']);
                exit;
            }
            echo json_encode(['token' => $token]);
            exit;
        }
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} catch (Throwable $e) {
    http_response_code(500);
    $msg = 'Server error. If this persists, run the migration: database/20250206_add_job_saver_token.sql';
    if (defined('DEBUG') && DEBUG) {
        $msg .= ' [' . $e->getMessage() . ']';
    }
    if (function_exists('error_log')) {
        error_log('job-saver-token.php: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    }
    echo json_encode(['error' => $msg]);
}
