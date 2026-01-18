<?php
/**
 * Handle organisation account request form submissions
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
$token = post(CSRF_TOKEN_NAME);
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Get form data
$organisationName = sanitizeInput(post('organisation_name', ''));
$contactName = sanitizeInput(post('contact_name', ''));
$contactEmail = sanitizeInput(post('contact_email', ''));
$expectedCandidates = sanitizeInput(post('expected_candidates', ''));
$expectedTeamMembers = sanitizeInput(post('expected_team_members', ''));
$organisationType = sanitizeInput(post('organisation_type', ''));
$additionalRequirements = sanitizeInput(post('additional_requirements', ''));

// Validation
$errors = [];

if (empty($organisationName)) {
    $errors[] = 'Organisation name is required';
}

if (empty($contactName)) {
    $errors[] = 'Contact name is required';
}

if (empty($contactEmail)) {
    $errors[] = 'Contact email is required';
} elseif (!validateEmail($contactEmail)) {
    $errors[] = 'Invalid email address';
}

if (empty($expectedCandidates)) {
    $errors[] = 'Expected number of candidates is required';
} elseif (!is_numeric($expectedCandidates) || $expectedCandidates < 1) {
    $errors[] = 'Expected number of candidates must be a positive number';
}

if (empty($expectedTeamMembers)) {
    $errors[] = 'Expected number of team members is required';
} elseif (!is_numeric($expectedTeamMembers) || $expectedTeamMembers < 1) {
    $errors[] = 'Expected number of team members must be a positive number';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode('. ', $errors)]);
    exit;
}

// Prepare email content
$toEmail = 'noreply@simple-job-tracker.com';
$subject = 'New Organisation Account Request: ' . $organisationName;

$message = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #2563eb; }
        .value { margin-top: 5px; padding: 10px; background-color: #f3f4f6; border-radius: 5px; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Organisation Account Request</h1>
        <p>A new organisation has requested an account setup:</p>
        
        <div class="field">
            <div class="label">Organisation Name:</div>
            <div class="value">' . htmlspecialchars($organisationName) . '</div>
        </div>
        
        <div class="field">
            <div class="label">Primary Contact Name:</div>
            <div class="value">' . htmlspecialchars($contactName) . '</div>
        </div>
        
        <div class="field">
            <div class="label">Contact Email:</div>
            <div class="value">' . htmlspecialchars($contactEmail) . '</div>
        </div>
        
        <div class="field">
            <div class="label">Expected Number of Candidates:</div>
            <div class="value">' . htmlspecialchars($expectedCandidates) . '</div>
        </div>
        
        <div class="field">
            <div class="label">Expected Number of Team Members:</div>
            <div class="value">' . htmlspecialchars($expectedTeamMembers) . '</div>
        </div>
        
        <div class="field">
            <div class="label">Organisation Type:</div>
            <div class="value">' . htmlspecialchars($organisationType ?: 'Not specified') . '</div>
        </div>';

if (!empty($additionalRequirements)) {
    $message .= '
        <div class="field">
            <div class="label">Additional Requirements:</div>
            <div class="value">' . nl2br(htmlspecialchars($additionalRequirements)) . '</div>
        </div>';
}

$message .= '
        <div class="footer">
            <p><strong>Next Steps:</strong> Please review this request and set up the organisation account. Once set up, send an invitation to ' . htmlspecialchars($contactEmail) . '.</p>
            <p>Submitted: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </div>
</body>
</html>';

// Send email
require_once __DIR__ . '/../php/email.php';
$emailSent = sendEmail($toEmail, $subject, $message, null, 'Simple CV Builder');

if ($emailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your request! We\'ve received your organisation account request and will review it shortly. You\'ll receive an email at ' . htmlspecialchars($contactEmail) . ' once your organisation account is set up.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send request. Please try again or contact us directly at noreply@simple-job-tracker.com'
    ]);
}

