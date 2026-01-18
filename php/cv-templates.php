<?php
/**
 * CV Templates Management Functions
 * Handle creating, loading, saving, and managing CV templates
 */

require_once __DIR__ . '/utils.php';

/**
 * Get all templates for a user
 */
function getUserCvTemplates($userId) {
    return db()->fetchAll(
        "SELECT * FROM cv_templates 
         WHERE user_id = ? 
         ORDER BY is_active DESC, created_at DESC",
        [$userId]
    );
}

/**
 * Get a template by ID
 */
function getCvTemplate($templateId, $userId = null) {
    if ($userId) {
        return db()->fetchOne(
            "SELECT * FROM cv_templates WHERE id = ? AND user_id = ?",
            [$templateId, $userId]
        );
    }
    return db()->fetchOne(
        "SELECT * FROM cv_templates WHERE id = ?",
        [$templateId]
    );
}

/**
 * Get the active template for a user
 */
function getActiveCvTemplate($userId) {
    return db()->fetchOne(
        "SELECT * FROM cv_templates WHERE user_id = ? AND is_active = TRUE",
        [$userId]
    );
}

/**
 * Create a new template
 */
function createCvTemplate($userId, $templateName, $templateHtml, $templateCss = '', $templateDescription = '') {
    // Check template limit
    $currentCount = db()->fetchOne(
        "SELECT COUNT(*) as count FROM cv_templates WHERE user_id = ?",
        [$userId]
    )['count'];
    
    if ($currentCount >= MAX_CV_TEMPLATES_PER_USER) {
        return [
            'success' => false,
            'error' => "You have reached the maximum limit of " . MAX_CV_TEMPLATES_PER_USER . " templates. Please delete an existing template before creating a new one."
        ];
    }
    
    // Check template size
    $totalSize = strlen($templateHtml) + strlen($templateCss);
    $maxSizeBytes = MAX_TEMPLATE_SIZE_KB * 1024;
    
    if ($totalSize > $maxSizeBytes) {
        return [
            'success' => false,
            'error' => "Template is too large (" . round($totalSize / 1024, 2) . " KB). Maximum size is " . MAX_TEMPLATE_SIZE_KB . " KB."
        ];
    }
    
    try {
        $templateId = generateUuid();
        
        db()->insert('cv_templates', [
            'id' => $templateId,
            'user_id' => $userId,
            'template_name' => sanitizeInput($templateName),
            'template_html' => $templateHtml,
            'template_css' => $templateCss,
            'template_description' => sanitizeInput($templateDescription),
            'is_active' => 0, // Don't activate automatically
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        return ['success' => true, 'template_id' => $templateId];
    } catch (Exception $e) {
        error_log("Error creating CV template: " . $e->getMessage());
        return ['success' => false, 'error' => 'Failed to create template'];
    }
}

/**
 * Update a template
 */
function updateCvTemplate($templateId, $userId, $templateName = null, $templateHtml = null, $templateCss = null, $templateDescription = null) {
    $updates = [];
    
    if ($templateName !== null) {
        $updates['template_name'] = sanitizeInput($templateName);
    }
    if ($templateHtml !== null) {
        $updates['template_html'] = $templateHtml;
    }
    if ($templateCss !== null) {
        $updates['template_css'] = $templateCss;
    }
    if ($templateDescription !== null) {
        $updates['template_description'] = sanitizeInput($templateDescription);
    }
    
    $updates['updated_at'] = date('Y-m-d H:i:s');
    
    if (empty($updates)) {
        return ['success' => false, 'error' => 'No updates provided'];
    }
    
    try {
        db()->update('cv_templates', $updates, 'id = ? AND user_id = ?', [$templateId, $userId]);
        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error updating CV template: " . $e->getMessage());
        return ['success' => false, 'error' => 'Failed to update template'];
    }
}

/**
 * Delete a template
 */
function deleteCvTemplate($templateId, $userId) {
    try {
        db()->delete('cv_templates', 'id = ? AND user_id = ?', [$templateId, $userId]);
        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error deleting CV template: " . $e->getMessage());
        return ['success' => false, 'error' => 'Failed to delete template'];
    }
}

/**
 * Activate a template (deactivates all others for the user)
 */
function activateCvTemplate($templateId, $userId) {
    try {
        // Deactivate all templates for this user
        db()->update('cv_templates', ['is_active' => 0], 'user_id = ?', [$userId]);
        
        // Activate the specified template
        db()->update('cv_templates', ['is_active' => 1], 'id = ? AND user_id = ?', [$templateId, $userId]);
        
        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error activating CV template: " . $e->getMessage());
        return ['success' => false, 'error' => 'Failed to activate template'];
    }
}

/**
 * Deactivate all templates for a user
 */
function deactivateAllCvTemplates($userId) {
    try {
        db()->update('cv_templates', ['is_active' => 0], 'user_id = ?', [$userId]);
        return ['success' => true];
    } catch (Exception $e) {
        error_log("Error deactivating CV templates: " . $e->getMessage());
        return ['success' => false, 'error' => 'Failed to deactivate templates'];
    }
}

/**
 * Get template count and storage size for a user
 */
function getCvTemplateStats($userId) {
    $templates = getUserCvTemplates($userId);
    $totalSize = 0;
    $activeCount = 0;
    
    foreach ($templates as $template) {
        $totalSize += strlen($template['template_html'] ?? '');
        $totalSize += strlen($template['template_css'] ?? '');
        if ($template['is_active']) {
            $activeCount++;
        }
    }
    
    return [
        'count' => count($templates),
        'active_count' => $activeCount,
        'total_size_kb' => round($totalSize / 1024, 2),
        'max_templates' => MAX_CV_TEMPLATES_PER_USER,
        'max_size_kb' => MAX_TEMPLATE_SIZE_KB
    ];
}

