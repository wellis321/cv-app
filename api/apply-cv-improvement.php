<?php
/**
 * Apply AI-Generated CV Improvement API Endpoint
 * Applies AI-generated improvements to the user's CV
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering
ob_start();

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check authentication
$user = getCurrentUser();
if (!$user) {
    http_response_code(401);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Verify CSRF token
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

try {
    $improvementType = $_POST['improvement_type'] ?? '';
    $improvementContent = $_POST['improvement_content'] ?? '';
    $cvVariantId = $_POST['cv_variant_id'] ?? null;
    
    if (empty($improvementContent)) {
        throw new Exception('No improvement content provided');
    }
    
    // Determine which CV to update
    if ($cvVariantId) {
        $variant = getCvVariant($cvVariantId, $user['id']);
        if (!$variant) {
            throw new Exception('CV variant not found');
        }
    } else {
        // Use master CV
        $cvVariantId = getOrCreateMasterVariant($user['id']);
        if (!$cvVariantId) {
            throw new Exception('Failed to get master CV variant');
        }
    }
    
    // Load current CV data
    $cvData = loadCvVariantData($cvVariantId);
    
    // Apply improvement based on type
    switch ($improvementType) {
        case 'professional_summary':
            if (empty($cvData['professional_summary'])) {
                $cvData['professional_summary'] = ['description' => ''];
            }
            $cvData['professional_summary']['description'] = $improvementContent;
            break;
            
        case 'work_experience':
            // For work experience, we'd need more context (which job, etc.)
            // For now, this is a placeholder
            throw new Exception('Work experience improvements require additional context');
            
        default:
            // For guidance_only or unknown types, just update professional summary as fallback
            if (empty($cvData['professional_summary'])) {
                $cvData['professional_summary'] = ['description' => ''];
            }
            $cvData['professional_summary']['description'] = $improvementContent;
            break;
    }
    
    // Save updated CV data
    saveCvVariantData($cvVariantId, $cvData);
    
    // Log activity
    logActivity($user['id'], 'cv_improvement_applied', [
        'cv_variant_id' => $cvVariantId,
        'improvement_type' => $improvementType
    ]);
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Improvement applied successfully'
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log("Apply CV Improvement Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

