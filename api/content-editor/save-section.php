<?php
/**
 * Unified API endpoint for saving section data
 * Handles create, update, and delete for all CV sections
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$userId = getUserId();
$token = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$action = $_POST['action'] ?? '';
$sectionId = $_POST['section_id'] ?? '';

if (empty($action) || empty($sectionId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Action and section_id required']);
    exit;
}

$subscriptionContext = getUserSubscriptionContext($userId);

try {
    $result = null;
    
    switch ($sectionId) {
        case 'professional-summary':
            $result = handleProfessionalSummary($action, $userId, $subscriptionContext);
            break;
        case 'work-experience':
            $result = handleWorkExperience($action, $userId, $subscriptionContext);
            break;
        case 'education':
            $result = handleEducation($action, $userId, $subscriptionContext);
            break;
        case 'skills':
            $result = handleSkills($action, $userId, $subscriptionContext);
            break;
        case 'projects':
            $result = handleProjects($action, $userId, $subscriptionContext);
            break;
        case 'certifications':
            $result = handleCertifications($action, $userId, $subscriptionContext);
            break;
        case 'memberships':
            $result = handleMemberships($action, $userId, $subscriptionContext);
            break;
        case 'interests':
            $result = handleInterests($action, $userId, $subscriptionContext);
            break;
        case 'qualification-equivalence':
            $result = handleQualificationEquivalence($action, $userId, $subscriptionContext);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid section ID']);
            exit;
    }
    
    if ($result && isset($result['success'])) {
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Unknown error']);
    }
    
} catch (Exception $e) {
    error_log("Save section error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save: ' . $e->getMessage()]);
}

// Handler functions for each section

function handleProfessionalSummary($action, $userId, $subscriptionContext) {
    // Map generic 'delete' action to 'delete_strength' for professional summary
    if ($action === 'delete') {
        $action = 'delete_strength';
    }
    
    if ($action === 'save') {
        $description = trim($_POST['description'] ?? '');
        
        if (!empty($description) && checkForXss($description)) {
            return ['success' => false, 'error' => 'Invalid content in description'];
        }
        
        $description = strip_tags($description);
        if ($description && planWordLimitExceeded($subscriptionContext, 'summary_description', $description)) {
            return ['success' => false, 'error' => getPlanWordLimitMessage($subscriptionContext, 'summary_description')];
        }
        $description = $description ?: null;
        
        $summary = db()->fetchOne("SELECT * FROM professional_summary WHERE profile_id = ?", [$userId]);
        if ($summary) {
            db()->update('professional_summary', ['description' => $description, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$summary['id']]);
        } else {
            db()->insert('professional_summary', [
                'id' => generateUuid(),
                'profile_id' => $userId,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        return ['success' => true, 'message' => 'Professional summary saved successfully'];
        
    } elseif ($action === 'add_strength') {
        $summary = db()->fetchOne("SELECT * FROM professional_summary WHERE profile_id = ?", [$userId]);
        if (!$summary) {
            $summaryId = generateUuid();
            db()->insert('professional_summary', [
                'id' => $summaryId,
                'profile_id' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $summary = ['id' => $summaryId];
        }
        
        if (!planCanAddEntry($subscriptionContext, 'summary_strengths', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'summary_strengths')];
        }
        
        $strength = trim($_POST['strength'] ?? '');
        if (empty($strength)) {
            return ['success' => false, 'error' => 'Strength is required'];
        }
        if (checkForXss($strength)) {
            return ['success' => false, 'error' => 'Invalid content in strength'];
        }
        if (strlen($strength) > 255) {
            return ['success' => false, 'error' => 'Strength must be 255 characters or less'];
        }
        $strength = strip_tags($strength);
        
        $maxOrder = db()->fetchOne("SELECT MAX(sort_order) as max_order FROM professional_summary_strengths WHERE professional_summary_id = ?", [$summary['id']]);
        db()->insert('professional_summary_strengths', [
            'id' => generateUuid(),
            'professional_summary_id' => $summary['id'],
            'strength' => $strength,
            'sort_order' => ($maxOrder['max_order'] ?? 0) + 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Strength added successfully'];
        
    } elseif ($action === 'update_strength') {
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            return ['success' => false, 'error' => 'Strength ID required'];
        }
        $summary = db()->fetchOne("SELECT id FROM professional_summary WHERE profile_id = ?", [$userId]);
        if (!$summary) {
            return ['success' => false, 'error' => 'Summary not found'];
        }
        
        // Verify the strength belongs to this user's summary
        $strength = db()->fetchOne("SELECT * FROM professional_summary_strengths WHERE id = ? AND professional_summary_id = ?", [$id, $summary['id']]);
        if (!$strength) {
            return ['success' => false, 'error' => 'Strength not found'];
        }
        
        $strengthText = trim($_POST['strength'] ?? '');
        if (empty($strengthText)) {
            return ['success' => false, 'error' => 'Strength is required'];
        }
        if (checkForXss($strengthText)) {
            return ['success' => false, 'error' => 'Invalid content in strength'];
        }
        if (strlen($strengthText) > 255) {
            return ['success' => false, 'error' => 'Strength must be 255 characters or less'];
        }
        $strengthText = strip_tags($strengthText);
        
        db()->update('professional_summary_strengths', ['strength' => $strengthText], 'id = ?', [$id]);
        return ['success' => true, 'message' => 'Strength updated successfully'];
        
    } elseif ($action === 'delete_strength') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        if (empty($id)) {
            return ['success' => false, 'error' => 'Strength ID required'];
        }
        $summary = db()->fetchOne("SELECT id FROM professional_summary WHERE profile_id = ?", [$userId]);
        if (!$summary) {
            return ['success' => false, 'error' => 'Summary not found'];
        }
        db()->delete('professional_summary_strengths', 'id = ? AND professional_summary_id = ?', [$id, $summary['id']]);
        return ['success' => true, 'message' => 'Strength deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleWorkExperience($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'work_experience', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'work_experience')];
        }
        
        $companyName = trim($_POST['company_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        
        if (empty($companyName) || empty($position) || empty($startDate)) {
            return ['success' => false, 'error' => 'Company name, position, and start date are required'];
        }
        
        if (checkForXss($companyName) || checkForXss($position) || (!empty($description) && checkForXss($description))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($companyName) > 255 || strlen($position) > 255) {
            return ['success' => false, 'error' => 'Field length exceeded'];
        }
        
        $companyName = strip_tags($companyName);
        $position = strip_tags($position);
        $description = !empty($description) ? strip_tags($description) : null;
        
        if ($description && planWordLimitExceeded($subscriptionContext, 'work_description', $description)) {
            return ['success' => false, 'error' => getPlanWordLimitMessage($subscriptionContext, 'work_description')];
        }
        
        $id = generateUuid();
        db()->insert('work_experience', [
            'id' => $id,
            'profile_id' => $userId,
            'company_name' => $companyName,
            'position' => $position,
            'start_date' => $startDate,
            'end_date' => $_POST['end_date'] ?? null ?: null,
            'description' => $description,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'hide_date' => (int)($_POST['hide_date'] ?? 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Work experience added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM work_experience WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $companyName = trim($_POST['company_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        
        if (empty($companyName) || empty($position) || empty($startDate)) {
            return ['success' => false, 'error' => 'Company name, position, and start date are required'];
        }
        
        if (checkForXss($companyName) || checkForXss($position) || (!empty($description) && checkForXss($description))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        $companyName = strip_tags($companyName);
        $position = strip_tags($position);
        $description = !empty($description) ? strip_tags($description) : null;
        
        if ($description && planWordLimitExceeded($subscriptionContext, 'work_description', $description)) {
            return ['success' => false, 'error' => getPlanWordLimitMessage($subscriptionContext, 'work_description')];
        }
        
        db()->update('work_experience', [
            'company_name' => $companyName,
            'position' => $position,
            'start_date' => $startDate,
            'end_date' => $_POST['end_date'] ?? null ?: null,
            'description' => $description,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'hide_date' => (int)($_POST['hide_date'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Work experience updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('work_experience', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Work experience deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleEducation($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'education', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'education')];
        }
        
        $institution = sanitizeInput($_POST['institution'] ?? '');
        $degree = sanitizeInput($_POST['degree'] ?? '');
        $fieldOfStudy = sanitizeInput($_POST['field_of_study'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        
        if (empty($institution) || empty($degree) || empty($startDate)) {
            return ['success' => false, 'error' => 'Institution, degree, and start date are required'];
        }
        
        if (checkForXss($institution) || checkForXss($degree) || (!empty($fieldOfStudy) && checkForXss($fieldOfStudy))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($institution) > 255 || strlen($degree) > 255 || (!empty($fieldOfStudy) && strlen($fieldOfStudy) > 255)) {
            return ['success' => false, 'error' => 'Field length exceeded'];
        }
        
        $id = generateUuid();
        db()->insert('education', [
            'id' => $id,
            'profile_id' => $userId,
            'institution' => $institution,
            'degree' => $degree,
            'field_of_study' => !empty($fieldOfStudy) ? $fieldOfStudy : null,
            'start_date' => $startDate,
            'end_date' => $_POST['end_date'] ?? null ?: null,
            'hide_date' => (int)($_POST['hide_date'] ?? 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Education added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM education WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $institution = sanitizeInput($_POST['institution'] ?? '');
        $degree = sanitizeInput($_POST['degree'] ?? '');
        $fieldOfStudy = sanitizeInput($_POST['field_of_study'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        
        if (empty($institution) || empty($degree) || empty($startDate)) {
            return ['success' => false, 'error' => 'Institution, degree, and start date are required'];
        }
        
        db()->update('education', [
            'institution' => $institution,
            'degree' => $degree,
            'field_of_study' => !empty($fieldOfStudy) ? $fieldOfStudy : null,
            'start_date' => $startDate,
            'end_date' => $_POST['end_date'] ?? null ?: null,
            'hide_date' => (int)($_POST['hide_date'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Education updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('education', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Education deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleSkills($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'skills', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'skills')];
        }
        
        $name = prepareForStorage($_POST['name'] ?? '');
        $level = prepareForStorage($_POST['level'] ?? '');
        $category = prepareForStorage($_POST['category'] ?? '');
        
        if (empty($name)) {
            return ['success' => false, 'error' => 'Skill name is required'];
        }
        
        if (checkForXss($name) || (!empty($level) && checkForXss($level)) || (!empty($category) && checkForXss($category))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($name) > 255 || (!empty($level) && strlen($level) > 50) || (!empty($category) && strlen($category) > 100)) {
            return ['success' => false, 'error' => 'Field length exceeded'];
        }
        
        $id = generateUuid();
        db()->insert('skills', [
            'id' => $id,
            'profile_id' => $userId,
            'name' => $name,
            'level' => !empty($level) ? $level : null,
            'category' => !empty($category) ? $category : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Skill added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM skills WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $name = prepareForStorage($_POST['name'] ?? '');
        $level = prepareForStorage($_POST['level'] ?? '');
        $category = prepareForStorage($_POST['category'] ?? '');
        
        if (empty($name)) {
            return ['success' => false, 'error' => 'Skill name is required'];
        }
        
        db()->update('skills', [
            'name' => $name,
            'level' => !empty($level) ? $level : null,
            'category' => !empty($category) ? $category : null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Skill updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('skills', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Skill deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleProjects($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'projects', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'projects')];
        }
        
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $url = sanitizeInput($_POST['url'] ?? '');
        
        if (empty($title)) {
            return ['success' => false, 'error' => 'Project title is required'];
        }
        
        if (checkForXss($title) || (!empty($description) && checkForXss($description)) || (!empty($url) && checkForXss($url))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($title) > 255 || (!empty($url) && strlen($url) > 2048)) {
            return ['success' => false, 'error' => 'Field length exceeded'];
        }
        
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'error' => 'Invalid URL format'];
        }
        
        $description = !empty($description) ? strip_tags($description) : null;
        if ($description && planWordLimitExceeded($subscriptionContext, 'project_description', $description)) {
            return ['success' => false, 'error' => getPlanWordLimitMessage($subscriptionContext, 'project_description')];
        }
        
        $id = generateUuid();
        $insertData = [
            'id' => $id,
            'profile_id' => $userId,
            'title' => $title,
            'description' => $description,
            'start_date' => $_POST['start_date'] ?? null ?: null,
            'end_date' => $_POST['end_date'] ?? null ?: null,
            'url' => !empty($url) ? $url : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Handle image fields if provided (from upload-project-image.php)
        if (!empty($_POST['image_url'])) {
            $insertData['image_url'] = sanitizeInput($_POST['image_url']);
        }
        if (!empty($_POST['image_path'])) {
            $insertData['image_path'] = sanitizeInput($_POST['image_path']);
        }
        if (!empty($_POST['image_responsive'])) {
            $insertData['image_responsive'] = $_POST['image_responsive']; // Already JSON string from JS
        }
        
        db()->insert('projects', $insertData);
        return ['success' => true, 'message' => 'Project added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM projects WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $url = sanitizeInput($_POST['url'] ?? '');
        
        if (empty($title)) {
            return ['success' => false, 'error' => 'Project title is required'];
        }
        
        $description = !empty($description) ? strip_tags($description) : null;
        if ($description && planWordLimitExceeded($subscriptionContext, 'project_description', $description)) {
            return ['success' => false, 'error' => getPlanWordLimitMessage($subscriptionContext, 'project_description')];
        }
        
        // Get existing project to check for old image
        $project = db()->fetchOne("SELECT image_path, image_url FROM projects WHERE id = ? AND profile_id = ?", [$id, $userId]);
        
        $updateData = [
            'title' => $title,
            'description' => $description,
            'start_date' => $_POST['start_date'] ?? null ?: null,
            'end_date' => $_POST['end_date'] ?? null ?: null,
            'url' => !empty($url) ? $url : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Handle image fields
        if (isset($_POST['image_url'])) {
            // If image_url is provided (even if empty), update it
            $updateData['image_url'] = !empty($_POST['image_url']) ? sanitizeInput($_POST['image_url']) : null;
        }
        if (isset($_POST['image_path'])) {
            $updateData['image_path'] = !empty($_POST['image_path']) ? sanitizeInput($_POST['image_path']) : null;
        }
        if (isset($_POST['image_responsive'])) {
            $updateData['image_responsive'] = !empty($_POST['image_responsive']) ? $_POST['image_responsive'] : null;
        }
        
        // If image fields were cleared, remove old image file
        if (empty($updateData['image_url']) && empty($updateData['image_path']) && $project) {
            $oldPath = $project['image_path'] ?? null;
            if (empty($oldPath) && !empty($project['image_url']) && strpos($project['image_url'], STORAGE_URL) === 0) {
                $oldPath = str_replace(STORAGE_URL . '/', '', $project['image_url']);
            }
            if (!empty($oldPath)) {
                $fullPath = STORAGE_PATH . '/' . $oldPath;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }
        
        db()->update('projects', $updateData, 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Project updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('projects', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Project deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleCertifications($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'certifications', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'certifications')];
        }
        
        $name = sanitizeInput($_POST['name'] ?? '');
        $issuer = sanitizeInput($_POST['issuer'] ?? '');
        $dateObtained = trim($_POST['date_obtained'] ?? '');
        $expiryDate = $_POST['expiry_date'] ?? null ?: null;
        
        if (empty($name) || empty($issuer)) {
            return ['success' => false, 'error' => 'Name and issuer are required'];
        }
        
        $id = generateUuid();
        db()->insert('certifications', [
            'id' => $id,
            'profile_id' => $userId,
            'name' => $name,
            'issuer' => $issuer,
            'date_obtained' => $dateObtained ?: null,
            'expiry_date' => $expiryDate,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Certification added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM certifications WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $name = sanitizeInput($_POST['name'] ?? '');
        $issuer = sanitizeInput($_POST['issuer'] ?? '');
        $dateObtained = trim($_POST['date_obtained'] ?? '');
        $expiryDate = $_POST['expiry_date'] ?? null ?: null;
        
        if (empty($name) || empty($issuer)) {
            return ['success' => false, 'error' => 'Name and issuer are required'];
        }
        
        db()->update('certifications', [
            'name' => $name,
            'issuer' => $issuer,
            'date_obtained' => $dateObtained ?: null,
            'expiry_date' => $expiryDate,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Certification updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('certifications', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Certification deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleMemberships($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'memberships', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'memberships')];
        }
        
        $organisation = sanitizeInput($_POST['organisation'] ?? '');
        $role = sanitizeInput($_POST['role'] ?? '');
        $startDate = $_POST['start_date'] ?? null ?: null;
        $endDate = $_POST['end_date'] ?? null ?: null;
        $description = trim($_POST['description'] ?? '');
        
        if (empty($organisation)) {
            return ['success' => false, 'error' => 'Organisation is required'];
        }
        
        if (checkForXss($organisation) || (!empty($role) && checkForXss($role))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($organisation) > 255 || (!empty($role) && strlen($role) > 255)) {
            return ['success' => false, 'error' => 'Field length exceeded'];
        }
        
        $id = generateUuid();
        db()->insert('professional_memberships', [
            'id' => $id,
            'profile_id' => $userId,
            'organisation' => $organisation,
            'role' => !empty($role) ? $role : null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => !empty($description) ? strip_tags($description) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Membership added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM professional_memberships WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $organisation = sanitizeInput($_POST['organisation'] ?? '');
        $role = sanitizeInput($_POST['role'] ?? '');
        $startDate = $_POST['start_date'] ?? null ?: null;
        $endDate = $_POST['end_date'] ?? null ?: null;
        $description = trim($_POST['description'] ?? '');
        
        if (empty($organisation)) {
            return ['success' => false, 'error' => 'Organisation is required'];
        }
        
        db()->update('professional_memberships', [
            'organisation' => $organisation,
            'role' => !empty($role) ? $role : null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => !empty($description) ? strip_tags($description) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Membership updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('professional_memberships', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Membership deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleInterests($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'interests', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'interests')];
        }
        
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($name)) {
            return ['success' => false, 'error' => 'Interest name is required'];
        }
        
        if (checkForXss($name) || (!empty($description) && checkForXss($description))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($name) > 255) {
            return ['success' => false, 'error' => 'Interest name must be 255 characters or less'];
        }
        
        $id = generateUuid();
        db()->insert('interests', [
            'id' => $id,
            'profile_id' => $userId,
            'name' => $name,
            'description' => !empty($description) ? strip_tags($description) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Interest added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM interests WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($name)) {
            return ['success' => false, 'error' => 'Interest name is required'];
        }
        
        db()->update('interests', [
            'name' => $name,
            'description' => !empty($description) ? strip_tags($description) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Interest updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('interests', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Interest deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}

function handleQualificationEquivalence($action, $userId, $subscriptionContext) {
    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'qualification_equivalence', $userId)) {
            return ['success' => false, 'error' => getPlanLimitMessage($subscriptionContext, 'qualification_equivalence')];
        }
        
        $level = trim($_POST['level'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($level)) {
            return ['success' => false, 'error' => 'Qualification level is required'];
        }
        
        if (checkForXss($level) || (!empty($description) && checkForXss($description))) {
            return ['success' => false, 'error' => 'Invalid content detected'];
        }
        
        if (strlen($level) > 255) {
            return ['success' => false, 'error' => 'Qualification level must be 255 characters or less'];
        }
        
        $id = generateUuid();
        db()->insert('professional_qualification_equivalence', [
            'id' => $id,
            'profile_id' => $userId,
            'level' => strip_tags($level),
            'description' => !empty($description) ? strip_tags($description) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return ['success' => true, 'message' => 'Qualification added successfully', 'id' => $id];
        
    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $existing = db()->fetchOne("SELECT id FROM professional_qualification_equivalence WHERE id = ? AND profile_id = ?", [$id, $userId]);
        if (!$existing) {
            return ['success' => false, 'error' => 'Entry not found'];
        }
        
        $level = trim($_POST['level'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($level)) {
            return ['success' => false, 'error' => 'Qualification level is required'];
        }
        
        db()->update('professional_qualification_equivalence', [
            'level' => strip_tags($level),
            'description' => !empty($description) ? strip_tags($description) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Qualification updated successfully'];
        
    } elseif ($action === 'delete') {
        $id = $_POST['entry_id'] ?? $_POST['id'] ?? '';
        db()->delete('professional_qualification_equivalence', 'id = ? AND profile_id = ?', [$id, $userId]);
        return ['success' => true, 'message' => 'Qualification deleted successfully'];
    }
    
    return ['success' => false, 'error' => 'Invalid action'];
}
