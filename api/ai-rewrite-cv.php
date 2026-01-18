<?php
/**
 * AI CV Rewriting API Endpoint
 * Generates job-specific CV variants using AI
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering to prevent any output before JSON
ob_start();

// Increase timeout for AI processing (Ollama can take 30-60 seconds)
set_time_limit(180); // 3 minutes
ini_set('max_execution_time', 180);

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
    $jobApplicationId = $_POST['job_application_id'] ?? null;
    $sourceVariantId = $_POST['cv_variant_id'] ?? null;
    $variantName = $_POST['variant_name'] ?? 'AI-Generated CV';
    
    // Load job application if provided
    $jobDescription = null;
    $fileContents = [];
    
    if ($jobApplicationId) {
        require_once __DIR__ . '/../php/job-applications.php';
        
        $jobApp = db()->fetchOne(
            "SELECT * FROM job_applications WHERE id = ? AND user_id = ?",
            [$jobApplicationId, $user['id']]
        );
        
        if (!$jobApp) {
            throw new Exception('Job application not found');
        }
        
        // Get job description from text field
        $jobDescription = $jobApp['job_description'] ?? $jobApp['notes'] ?? '';
        
        // Get files and extract their content
        $filesWithText = getJobApplicationFilesForAI($jobApplicationId, $user['id']);
        foreach ($filesWithText as $fileData) {
            if (!empty($fileData['text'])) {
                $fileContents[] = $fileData['text'];
            }
        }
        
        // Check if variant already exists for this job
        $existingVariant = db()->fetchOne(
            "SELECT id FROM cv_variants WHERE job_application_id = ? AND user_id = ?",
            [$jobApplicationId, $user['id']]
        );
        
        if ($existingVariant) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'error' => 'CV variant already exists for this job application',
                'variant_id' => $existingVariant['id']
            ]);
            exit;
        }
    } else {
        // Job description provided directly
        $jobDescription = $_POST['job_description'] ?? '';
    }
    
    // Combine job description with file contents
    $combinedDescription = $jobDescription;
    if (!empty($fileContents)) {
        if (!empty($combinedDescription)) {
            $combinedDescription .= "\n\n--- Additional Information from Uploaded Files ---\n\n";
        }
        $combinedDescription .= implode("\n\n--- File Content ---\n\n", $fileContents);
    }
    
    if (empty($combinedDescription)) {
        throw new Exception('Job description or file content is required');
    }
    
    // Load source CV data
    $cvData = null;
    if ($sourceVariantId) {
        // Load from variant
        $variant = getCvVariant($sourceVariantId, $user['id']);
        if (!$variant) {
            throw new Exception('Source CV variant not found');
        }
        $cvData = loadCvVariantData($sourceVariantId);
    } else {
        // Load master CV
        $cvData = loadCvData($user['id']);
    }
    
    if (!$cvData || empty($cvData)) {
        throw new Exception('No CV data found');
    }
    
    // Get sections to rewrite from POST (default to standard sections)
    $sectionsToRewrite = $_POST['sections_to_rewrite'] ?? ['professional_summary', 'work_experience', 'skills'];
    if (is_string($sectionsToRewrite)) {
        // If it's a JSON string, decode it
        $decoded = json_decode($sectionsToRewrite, true);
        if (is_array($decoded)) {
            $sectionsToRewrite = $decoded;
        } else {
            // If it's a comma-separated string, convert to array
            $sectionsToRewrite = array_filter(array_map('trim', explode(',', $sectionsToRewrite)));
        }
    }
    // Ensure professional_summary is always included
    if (!in_array('professional_summary', $sectionsToRewrite)) {
        $sectionsToRewrite[] = 'professional_summary';
    }
    
    // Get prompt instructions mode and custom instructions
    $promptMode = $_POST['prompt_instructions_mode'] ?? 'default';
    $customInstructions = null;
    
    if ($promptMode === 'saved') {
        // Use saved custom instructions from user's profile
        $userProfile = db()->fetchOne(
            "SELECT cv_rewrite_prompt_instructions FROM profiles WHERE id = ?",
            [$user['id']]
        );
        $customInstructions = $userProfile['cv_rewrite_prompt_instructions'] ?? null;
    } elseif ($promptMode === 'custom') {
        // Use custom instructions provided for this generation only
        $customInstructions = trim($_POST['prompt_custom_text'] ?? '');
        if (empty($customInstructions)) {
            throw new Exception('Custom instructions are required when "custom" mode is selected');
        }
        // Validate length
        if (strlen($customInstructions) > 2000) {
            throw new Exception('Custom instructions must be 2000 characters or less');
        }
    }
    // If mode is 'default', $customInstructions remains null and defaults will be used
    
    // Check if this is a browser AI result being saved (from client-side execution)
    $browserAiResult = $_POST['browser_ai_result'] ?? null;
    
    if ($browserAiResult) {
        // Browser AI already executed client-side - parse and use the result
        $rewrittenData = json_decode($browserAiResult, true);
        if (!$rewrittenData || !is_array($rewrittenData)) {
            throw new Exception('Invalid browser AI result format');
        }
    } else {
        // Server-side AI execution
        // Get AI service with user ID for user-specific settings
        $aiService = getAIService($user['id']);
        
        // Call AI to rewrite CV with combined description (text + file contents)
        // Pass sections to rewrite and custom instructions
        $result = $aiService->rewriteCvForJob($cvData, $combinedDescription, [
            'sections_to_rewrite' => $sectionsToRewrite,
            'custom_instructions' => $customInstructions
        ]);
        
        // Check if this is browser execution mode
        if (isset($result['browser_execution']) && $result['browser_execution']) {
            // Browser AI - return prompt and instructions for frontend execution
            ob_end_clean();
            echo json_encode([
                'success' => true,
                'browser_execution' => true,
                'prompt' => $result['prompt'] ?? '',
                'model' => $result['model'] ?? 'llama3.2',
                'model_type' => $result['model_type'] ?? 'webllm',
                'cv_data' => $cvData,
                'job_description' => $combinedDescription,
                'message' => 'Browser AI execution required. Frontend will handle this request.'
            ]);
            exit;
        }
        
        if (!$result['success']) {
            throw new Exception($result['error'] ?? 'AI rewriting failed');
        }
        
        $rewrittenData = $result['cv_data'];
    }
    
    // Check for duplicates before creating (check by name as well as job_application_id)
    if ($jobApplicationId) {
        $existingVariant = db()->fetchOne(
            "SELECT id FROM cv_variants WHERE job_application_id = ? AND user_id = ?",
            [$jobApplicationId, $user['id']]
        );
        
        if ($existingVariant) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'error' => 'CV variant already exists for this job application',
                'variant_id' => $existingVariant['id']
            ]);
            exit;
        }
    }
    
    // Create new CV variant
    $newVariant = createCvVariant(
        $user['id'],
        $sourceVariantId,
        $variantName,
        $jobApplicationId
    );
    
    if (!$newVariant['success']) {
        throw new Exception($newVariant['error'] ?? 'Failed to create CV variant');
    }
    
    $newVariantId = $newVariant['variant_id'];
    
    // Merge AI-rewritten data with original data
    // IMPORTANT: The AI only returns rewritten sections based on user selection
    // We must preserve all other sections from the original CV data
    
    // Update professional summary if rewritten
    if (isset($rewrittenData['professional_summary'])) {
        $cvData['professional_summary'] = array_merge(
            $cvData['professional_summary'] ?? ['description' => ''],
            $rewrittenData['professional_summary']
        );
    }
    
    // Update work experience if rewritten
    if (isset($rewrittenData['work_experience']) && is_array($rewrittenData['work_experience'])) {
        foreach ($rewrittenData['work_experience'] as $rewrittenWork) {
            // Find matching work experience by ID
            foreach ($cvData['work_experience'] as &$work) {
                if (isset($rewrittenWork['id']) && $work['id'] === $rewrittenWork['id']) {
                    // Update description
                    if (isset($rewrittenWork['description'])) {
                        $work['description'] = $rewrittenWork['description'];
                    }
                    
                    // Update responsibility categories
                    if (isset($rewrittenWork['responsibility_categories'])) {
                        $work['responsibility_categories'] = $rewrittenWork['responsibility_categories'];
                    }
                    break;
                }
            }
        }
    }
    
    // Update skills if rewritten
    if (isset($rewrittenData['skills']) && is_array($rewrittenData['skills'])) {
        // Merge with existing skills, prioritising rewritten ones
        $existingSkillNames = array_map(function($s) { return strtolower($s['name']); }, $cvData['skills'] ?? []);
        $newSkills = [];
        
        foreach ($rewrittenData['skills'] as $skill) {
            $skillName = is_array($skill) ? $skill['name'] : $skill;
            if (!in_array(strtolower($skillName), $existingSkillNames)) {
                $newSkills[] = is_array($skill) ? $skill : ['name' => $skill, 'category' => null];
            }
        }
        
        // Add new skills to existing
        $cvData['skills'] = array_merge($cvData['skills'] ?? [], $newSkills);
    }
    
    // Update education if rewritten
    if (isset($rewrittenData['education']) && is_array($rewrittenData['education'])) {
        foreach ($rewrittenData['education'] as $rewrittenEdu) {
            foreach ($cvData['education'] as &$edu) {
                if (isset($rewrittenEdu['id']) && $edu['id'] === $rewrittenEdu['id']) {
                    if (isset($rewrittenEdu['description'])) {
                        $edu['description'] = $rewrittenEdu['description'];
                    }
                    break;
                }
            }
        }
    }
    
    // Update projects if rewritten
    if (isset($rewrittenData['projects']) && is_array($rewrittenData['projects'])) {
        foreach ($rewrittenData['projects'] as $rewrittenProj) {
            foreach ($cvData['projects'] as &$proj) {
                if (isset($rewrittenProj['id']) && $proj['id'] === $rewrittenProj['id']) {
                    if (isset($rewrittenProj['description'])) {
                        $proj['description'] = $rewrittenProj['description'];
                    }
                    break;
                }
            }
        }
    }
    
    // Update certifications if rewritten
    if (isset($rewrittenData['certifications']) && is_array($rewrittenData['certifications'])) {
        foreach ($rewrittenData['certifications'] as $rewrittenCert) {
            foreach ($cvData['certifications'] as &$cert) {
                if (isset($rewrittenCert['id']) && $cert['id'] === $rewrittenCert['id']) {
                    if (isset($rewrittenCert['description'])) {
                        $cert['description'] = $rewrittenCert['description'];
                    }
                    break;
                }
            }
        }
    }
    
    // Update professional memberships if rewritten
    if (isset($rewrittenData['professional_memberships']) && is_array($rewrittenData['professional_memberships'])) {
        foreach ($rewrittenData['professional_memberships'] as $rewrittenMembership) {
            foreach ($cvData['professional_memberships'] as &$membership) {
                if (isset($rewrittenMembership['id']) && $membership['id'] === $rewrittenMembership['id']) {
                    if (isset($rewrittenMembership['description'])) {
                        $membership['description'] = $rewrittenMembership['description'];
                    }
                    break;
                }
            }
        }
    }
    
    // Update interests if rewritten
    if (isset($rewrittenData['interests']) && is_array($rewrittenData['interests'])) {
        foreach ($rewrittenData['interests'] as $rewrittenInterest) {
            foreach ($cvData['interests'] as &$interest) {
                if (isset($rewrittenInterest['id']) && $interest['id'] === $rewrittenInterest['id']) {
                    if (isset($rewrittenInterest['description'])) {
                        $interest['description'] = $rewrittenInterest['description'];
                    }
                    break;
                }
            }
        }
    }
    
    // Preserve all other sections from original CV that weren't rewritten
    // These sections should already be in $cvData from loadCvData() or loadCvVariantData()
    // But ensure they're not accidentally removed if AI response doesn't include them
    
    // Mark as AI-generated
    db()->update('cv_variants',
        ['ai_generated' => true],
        'id = ?',
        [$newVariantId]
    );
    
    // Save the merged CV data to the new variant
    $saveResult = saveCvVariantData($newVariantId, $cvData);
    
    if (!$saveResult['success']) {
        throw new Exception($saveResult['error'] ?? 'Failed to save rewritten CV');
    }
    
    // Clean output buffer and return success
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'variant_id' => $newVariantId,
        'message' => 'CV successfully rewritten for this job'
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log("AI Rewrite CV Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

