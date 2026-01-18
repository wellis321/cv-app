<?php
/**
 * Handle remote work story form submissions
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
$name = sanitizeInput(post('name', ''));
$email = sanitizeInput(post('email', ''));
$jobTitle = sanitizeInput(post('job_title', ''));
$company = sanitizeInput(post('company', ''));
$category = sanitizeInput(post('category', ''));
$story = sanitizeInput(post('story', ''));

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors[] = 'Invalid email address';
}

if (empty($jobTitle)) {
    $errors[] = 'Job title is required';
}

if (empty($story)) {
    $errors[] = 'Your story is required';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode('. ', $errors)]);
    exit;
}

try {
    // Store submission in database
    $storyId = generateUuid();
    
    db()->insert('remote_work_stories', [
        'id' => $storyId,
        'name' => $name,
        'email' => $email,
        'job_title' => $jobTitle,
        'company' => !empty($company) ? $company : null,
        'category' => !empty($category) ? $category : null,
        'story' => $story,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    // Prepare email content
    $emailSubject = 'New Remote Work Story Submission';
    $emailBody = "A new remote work story has been submitted:\n\n";
    $emailBody .= "Submission ID: " . $storyId . "\n";
    $emailBody .= "Name: " . $name . "\n";
    $emailBody .= "Email: " . $email . "\n";
    $emailBody .= "Job Title: " . $jobTitle . "\n";
    if (!empty($company)) {
        $emailBody .= "Company: " . $company . "\n";
    }
    if (!empty($category)) {
        $emailBody .= "Job Category: " . $category . "\n";
    }
    $emailBody .= "\nStory:\n" . $story . "\n";
    $emailBody .= "\n---\n";
    $emailBody .= "View all submissions: " . APP_URL . "/admin/remote-work-stories.php\n";
    
    // Send email notification
    $to = 'noreply@simple-job-tracker.com';
    $headers = "From: noreply@simple-job-tracker.com\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    mail($to, $emailSubject, $emailBody, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for sharing your story! We\'ll review it and may feature it on our site.'
    ]);
} catch (Exception $e) {
    error_log("Remote work story submission error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to submit your story. Please try again later.'
    ]);
}

