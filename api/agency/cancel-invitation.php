<?php
/**
 * API endpoint for cancelling invitations (candidate or team)
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../php/helpers.php';

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    } else {
        setFlash('error', 'Method not allowed');
        redirect('/agency/candidates.php');
    }
    exit;
}

// Check authentication and organisation access
$org = requireOrganisationAccess('admin');
$user = getCurrentUser();

// Verify CSRF token
$csrfToken = post(CSRF_TOKEN_NAME) ?? $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    ob_end_clean();
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    } else {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/agency/candidates.php');
    }
    exit;
}

try {
    $invitationId = sanitizeInput(post('invitation_id'));
    $type = sanitizeInput(post('type')); // 'candidate' or 'team'
    
    if (empty($invitationId)) {
        throw new Exception('Invitation ID is required');
    }
    
    if (empty($type) || !in_array($type, ['candidate', 'team'])) {
        throw new Exception('Invalid invitation type. Must be "candidate" or "team"');
    }
    
    $organisationId = $org['organisation_id'];
    
    // Cancel the appropriate invitation type
    if ($type === 'candidate') {
        $result = cancelCandidateInvitation($invitationId, $organisationId);
    } else {
        $result = cancelTeamInvitation($invitationId, $organisationId);
    }
    
    if (!$result['success']) {
        throw new Exception($result['error'] ?? 'Failed to cancel invitation');
    }
    
    // Log activity
    logActivity('team.invitation_cancelled', null, [
        'invitation_id' => $invitationId,
        'type' => $type
    ], $organisationId);
    
    ob_end_clean();
    
    // Return JSON for AJAX requests, redirect for form submissions
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Invitation cancelled successfully'
        ]);
    } else {
        // Determine redirect URL based on invitation type
        $redirectUrl = ($type === 'candidate') ? '/agency/candidates/create.php' : '/agency/team/create.php';
        setFlash('success', 'Invitation cancelled successfully');
        redirect($redirectUrl);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    error_log("Cancel invitation error: " . $e->getMessage());
    
    // Return JSON for AJAX requests, redirect for form submissions
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } else {
        // Determine redirect URL based on invitation type
        $type = sanitizeInput(post('type') ?? 'candidate');
        $redirectUrl = ($type === 'candidate') ? '/agency/candidates/create.php' : '/agency/team/create.php';
        setFlash('error', $e->getMessage());
        redirect($redirectUrl);
    }
}

