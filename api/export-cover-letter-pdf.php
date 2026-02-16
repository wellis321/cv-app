<?php
/**
 * Export Cover Letter as PDF
 * Returns cover letter data formatted for PDF generation
 */

define('SKIP_CANONICAL_REDIRECT', true);
require_once __DIR__ . '/../php/helpers.php';
require_once __DIR__ . '/../php/cover-letters.php';
require_once __DIR__ . '/../php/job-applications.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$coverLetterId = $_GET['cover_letter_id'] ?? null;

if (!$coverLetterId) {
    echo json_encode(['success' => false, 'error' => 'Cover letter ID required']);
    exit;
}

// Get cover letter
$coverLetter = getCoverLetter($coverLetterId, $user['id']);
if (!$coverLetter) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Cover letter not found']);
    exit;
}

// Get job application details
$jobApplication = getJobApplication($coverLetter['job_application_id'], $user['id']);
if (!$jobApplication) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Job application not found']);
    exit;
}

// Get user profile for name/contact info and photo
$profile = db()->fetchOne("SELECT full_name, email, phone, location, linkedin_url, photo_url, show_photo_pdf FROM profiles WHERE id = ?", [$user['id']]);

// Get professional title from most recent work experience
$latestRole = db()->fetchOne("SELECT position FROM work_experience WHERE profile_id = ? ORDER BY start_date DESC LIMIT 1", [$user['id']]);
$professionalTitle = $latestRole['position'] ?? null;

// Format date
$date = date('F j, Y');

// Photo URL for cover letter: use profile photo if user has one and show_photo_pdf is enabled
$includePhoto = (!isset($profile['show_photo_pdf']) || $profile['show_photo_pdf']) && !empty($profile['photo_url']);
$photoUrl = $includePhoto ? $profile['photo_url'] : null;

// Return data for client-side PDF generation
echo json_encode([
    'success' => true,
    'cover_letter' => [
        'text' => $coverLetter['cover_letter_text'],
        'company_name' => $jobApplication['company_name'],
        'job_title' => $jobApplication['job_title'],
        'date' => $date,
        'applicant_name' => $profile['full_name'] ?? 'Applicant',
        'applicant_email' => $profile['email'] ?? '',
        'applicant_phone' => $profile['phone'] ?? '',
        'applicant_location' => $profile['location'] ?? '',
        'professional_title' => $professionalTitle,
        'photo_url' => $photoUrl,
        'app_url' => defined('APP_URL') ? APP_URL : ''
    ]
]);

