<?php
/**
 * API endpoint for submitting remote work stories
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
$token = post(CSRF_TOKEN_NAME);
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

// Rate limiting: 3 submissions per IP per hour
$ip = getClientIp();
$rateLimitKey = 'remote_story_ip_' . $ip;
$rateLimit = checkRateLimit($rateLimitKey, 3, 3600);

if (!$rateLimit['allowed']) {
    $minutesRemaining = ceil(($rateLimit['reset_at'] - time()) / 60);
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => "Too many submissions. Please try again in {$minutesRemaining} minute(s)."
    ]);
    exit;
}

// Get and validate input
// Note: We sanitize for XSS checking, but store plain text in database
// HTML encoding happens when displaying (using e() function)
$name = trim(post('name', ''));
$email = trim(post('email', ''));
$jobTitle = trim(post('job_title', ''));
$companyName = trim(post('company_name', ''));
$story = trim(post('story', ''));
$jobCategory = trim(post('job_category', ''));

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Name is required and must be at least 2 characters';
}

if (empty($email) || !validateEmail($email)) {
    $errors[] = 'Valid email address is required';
}

if (empty($jobTitle) || strlen($jobTitle) < 2) {
    $errors[] = 'Job title is required';
}

if (empty($story) || strlen($story) < 50) {
    $errors[] = 'Story must be at least 50 characters';
}

if (strlen($story) > 2000) {
    $errors[] = 'Story must be less than 2000 characters';
}

// Check for XSS
if (checkForXss($story) || checkForXss($name) || checkForXss($jobTitle) || checkForXss($companyName)) {
    $errors[] = 'Invalid content detected';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Save to database
try {
    $storyId = generateUuid();

    // Store plain text in database (PDO prepared statements prevent SQL injection)
    // HTML encoding happens when displaying using e() function
    db()->insert('remote_work_stories', [
        'id' => $storyId,
        'name' => strip_tags($name), // Remove any HTML tags
        'email' => $email, // Email is validated separately
        'job_title' => strip_tags($jobTitle), // Remove any HTML tags
        'company_name' => $companyName ? strip_tags($companyName) : null, // Remove any HTML tags
        'story' => strip_tags($story), // Remove HTML tags - store plain text
        'job_category' => $jobCategory ? strip_tags($jobCategory) : null, // Remove any HTML tags
        'approved' => false, // Requires admin approval
        'featured' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    // Optional: Send email notification to admin
    // sendEmailNotification($email, $name, $jobTitle);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for sharing your story! We\'ll review it and may feature it on our site.'
    ]);

} catch (Exception $e) {
    error_log("Remote work story submission error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to submit story. Please try again.']);
}
