<?php
/**
 * AI CV Rewriting API Endpoint
 * Generates job-specific CV variants using AI
 */

// Prevent canonical redirect
define('SKIP_CANONICAL_REDIRECT', true);

// Start output buffering to prevent any output before JSON
ob_start();

// Increase timeout for AI processing (Ollama CV rewrite can take 2-5+ minutes on Mac)
set_time_limit(300); // 5 minutes
ini_set('max_execution_time', 300);

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
    $updateVariantId = $_POST['update_variant_id'] ?? null;
    $jobApplicationId = $_POST['job_application_id'] ?? null;
    $sourceVariantId = $_POST['cv_variant_id'] ?? null;
    $variantName = $_POST['variant_name'] ?? 'AI-Generated CV';
    
    // When updating an existing variant, load it and use its job application for description
    if ($updateVariantId) {
        $existingVariant = getCvVariant($updateVariantId, $user['id']);
        if (!$existingVariant) {
            throw new Exception('Variant not found or access denied');
        }
        $jobApplicationId = $jobApplicationId ?: $existingVariant['job_application_id'];
    }
    
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
        
        // Get selected keywords if available
        $selectedKeywords = [];
        if (!empty($jobApp['selected_keywords'])) {
            $decoded = json_decode($jobApp['selected_keywords'], true);
            if (is_array($decoded)) {
                $selectedKeywords = $decoded;
            }
        }
        
        // Get files and extract their content
        $filesWithText = getJobApplicationFilesForAI($jobApplicationId, $user['id']);
        foreach ($filesWithText as $fileData) {
            if (!empty($fileData['text'])) {
                $fileContents[] = $fileData['text'];
            }
        }
        
        // Check if variant already exists for this job (only when creating new variant, not updating)
        if (!$updateVariantId) {
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
    
    // Load CV data: from variant we're updating, or from source/master for new variant
    $cvData = null;
    if ($updateVariantId) {
        $cvData = loadCvVariantData($updateVariantId);
    } elseif ($sourceVariantId) {
        $variant = getCvVariant($sourceVariantId, $user['id']);
        if (!$variant) {
            throw new Exception('Source CV variant not found');
        }
        $cvData = loadCvVariantData($sourceVariantId);
    } else {
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
    // Ensure at least one section is selected
    if (empty($sectionsToRewrite)) {
        $sectionsToRewrite = ['professional_summary'];
    }
    
    // Single-item scope for work experience or projects (used when AI is local/browser)
    $singleWorkExperienceId = trim($_POST['single_work_experience_id'] ?? '');
    $singleProjectId = trim($_POST['single_project_id'] ?? '');
    
    // Get prompt instructions mode and custom instructions
    $promptMode = $_POST['prompt_instructions_mode'] ?? 'default';
    $customInstructions = null;
    
    // Load prompt security functions
    require_once __DIR__ . '/../php/prompt-security.php';
    
    if ($promptMode === 'saved') {
        // Use saved custom instructions from user's profile
        $userProfile = db()->fetchOne(
            "SELECT cv_rewrite_prompt_instructions FROM profiles WHERE id = ?",
            [$user['id']]
        );
        $customInstructions = $userProfile['cv_rewrite_prompt_instructions'] ?? null;
        
        // Sanitize saved instructions (they may have been saved before security was added)
        if (!empty($customInstructions)) {
            $sanitizationResult = sanitizePromptInstructions($customInstructions);
            if ($sanitizationResult['blocked']) {
                // If blocked, fall back to defaults
                $customInstructions = null;
            } else {
                $customInstructions = $sanitizationResult['sanitized'];
            }
            // Log if there were warnings
            if (!empty($sanitizationResult['warnings'])) {
                logPromptSecurityEvent($user['id'], $customInstructions, $sanitizationResult);
            }
        }
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
        
        // Sanitize and validate custom instructions
        $sanitizationResult = sanitizePromptInstructions($customInstructions);
        $validationResult = validatePromptInstructions($sanitizationResult['sanitized']);
        
        // Log security events
        logPromptSecurityEvent($user['id'], $customInstructions, $sanitizationResult);
        
        // If blocked, reject
        if ($sanitizationResult['blocked']) {
            throw new Exception('Custom instructions contain prohibited content. Please ensure your instructions relate only to CV rewriting.');
        }
        
        // If validation failed, return errors
        if (!$validationResult['valid']) {
            throw new Exception(implode('. ', $validationResult['errors']));
        }
        
        // Use sanitized version
        $customInstructions = $sanitizationResult['sanitized'];
    }
    // If mode is 'default', $customInstructions remains null and defaults will be used
    
    // Check if this is a browser AI result being saved (from client-side execution)
    $browserAiResult = $_POST['browser_ai_result'] ?? null;
    $forceServerAI = isset($_POST['force_server_ai']) && $_POST['force_server_ai'] === '1';
    
    if ($browserAiResult && !$forceServerAI) {
        // Browser AI already executed client-side - parse and use the result
        $rewrittenData = json_decode($browserAiResult, true);
        if (!$rewrittenData || !is_array($rewrittenData)) {
            throw new Exception('Invalid browser AI result format');
        }
    } else {
        // Server-side AI execution (either normal flow or forced fallback)
        // Get AI service with user ID for user-specific settings
        if (!function_exists('getAIService')) {
            throw new Exception('getAIService function not found. Check if ai-service.php is loaded.');
        }
        
        // If forcing server AI, temporarily override the user's preference
        $originalService = null;
        if ($forceServerAI) {
            // Get the AI service but we'll need to override browser preference
            // For now, get the service and check if we need to switch
            $aiService = getAIService($user['id']);
            if ($aiService->service === 'browser') {
                // Need to use a different service - try to get first available cloud/local service
                // This is a simplified approach - in production you might want to check available services
                throw new Exception('Browser AI is selected but failed to load. Please configure Ollama or a cloud AI service (OpenAI, Anthropic, etc.) in Settings > AI Settings.');
            }
        } else {
            $aiService = getAIService($user['id']);
        }
        
        // Enforce limited scope for local/browser AI: work experience and projects must be one-at-a-time
        $aiScopeLimited = in_array($aiService->getService(), ['ollama', 'browser']);
        if ($aiScopeLimited) {
            if (in_array('work_experience', $sectionsToRewrite) && $singleWorkExperienceId === '') {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => 'With local or browser AI, tailor one role at a time. Use "Tailor section" and select a specific role, or switch to a cloud AI (OpenAI, Anthropic, etc.) in Settings > AI Settings to tailor all work experience at once.'
                ]);
                exit;
            }
            if (in_array('projects', $sectionsToRewrite) && $singleProjectId === '') {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => 'With local or browser AI, tailor one project at a time. Use "Tailor section" and select a specific project, or switch to a cloud AI in Settings > AI Settings to tailor all projects at once.'
                ]);
                exit;
            }
        }
        
        // Call AI to rewrite CV with combined description (text + file contents)
        // Pass sections to rewrite and custom instructions
        // Build rewrite options
        $rewriteOptions = [
            'sections_to_rewrite' => $sectionsToRewrite,
            'custom_instructions' => $customInstructions
        ];
        if ($singleWorkExperienceId !== '') {
            $rewriteOptions['single_work_experience_id'] = $singleWorkExperienceId;
        }
        if ($singleProjectId !== '') {
            $rewriteOptions['single_project_id'] = $singleProjectId;
        }
        
        // Add selected keywords if available
        if (!empty($selectedKeywords) && is_array($selectedKeywords)) {
            $rewriteOptions['selected_keywords'] = $selectedKeywords;
        }
        
        $result = $aiService->rewriteCvForJob($cvData, $combinedDescription, $rewriteOptions);
        
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
            ob_end_clean();
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'error' => $result['error'] ?? 'AI rewriting failed',
            ]);
            exit;
        }
        
        $rewrittenData = $result['cv_data'];
    }
    
    // When updating existing variant, use its id; otherwise create new variant
    if ($updateVariantId) {
        $newVariantId = $updateVariantId;
    } else {
        // Check for duplicates before creating (check by job_application_id)
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
    }
    
    // Merge AI-rewritten data with original data
    // IMPORTANT: The AI only returns rewritten sections based on user selection
    // We must preserve all other sections from the original CV data
    
    // Update professional summary if rewritten (AI may return string or array with description key)
    if (isset($rewrittenData['professional_summary'])) {
        $summary = $rewrittenData['professional_summary'];
        $base = $cvData['professional_summary'] ?? ['description' => ''];
        if (is_array($summary)) {
            $cvData['professional_summary'] = array_merge($base, $summary);
        } else {
            $cvData['professional_summary'] = array_merge($base, ['description' => (string) $summary]);
        }
    }
    
    $successMessage = 'CV successfully rewritten for this job';
    $singleRoleOutputUnchanged = false;
    // Update work experience if rewritten
    if (isset($rewrittenData['work_experience']) && is_array($rewrittenData['work_experience'])) {
        $rewrittenWorkCount = count($rewrittenData['work_experience']);
        $originalWorkCount = count($cvData['work_experience'] ?? []);
        if ($originalWorkCount > 0 && $rewrittenWorkCount < $originalWorkCount) {
            $successMessage = $rewrittenWorkCount . ' of ' . $originalWorkCount . ' work experience entries were tailored; the rest were left unchanged. Try tailoring again for more entries, or check that your AI model can handle long output.';
        }
        foreach ($rewrittenData['work_experience'] as $idx => $rewrittenWork) {
            $found = false;
            $matchType = null;
            
            foreach ($cvData['work_experience'] as &$work) {
                // Match by variant ID (use string comparison: DB may return int/string, JSON returns string)
                $idMatch = isset($rewrittenWork['id']) && isset($work['id']) && (string) $work['id'] === (string) $rewrittenWork['id'];
                
                // When tailoring a single role, match by the id we sent (AI may omit or alter id in response)
                $singleIdMatch = ($singleWorkExperienceId !== '' && isset($work['id']) && (string) $work['id'] === (string) $singleWorkExperienceId);
                
                // Match by original_work_experience_id (if AI returns master CV ID)
                $originalIdMatch = false;
                if (isset($rewrittenWork['id']) && isset($work['original_work_experience_id'])) {
                    $originalIdMatch = (string) $work['original_work_experience_id'] === (string) $rewrittenWork['id'];
                }
                
                // Fallback: match by position and company name (case-insensitive)
                $positionMatch = !empty($rewrittenWork['position']) && !empty($work['position']) && 
                                strtolower(trim($work['position'])) === strtolower(trim($rewrittenWork['position']));
                $companyMatch = !empty($rewrittenWork['company_name']) && !empty($work['company_name']) && 
                               strtolower(trim($work['company_name'])) === strtolower(trim($rewrittenWork['company_name']));
                
                if ($idMatch || $singleIdMatch || $originalIdMatch || ($positionMatch && $companyMatch)) {
                    // Determine match type for logging
                    $matchType = 'unknown';
                    if ($idMatch) $matchType = 'variant_id';
                    elseif ($singleIdMatch) $matchType = 'single_work_experience_id';
                    elseif ($originalIdMatch) $matchType = 'original_work_experience_id';
                    elseif ($positionMatch && $companyMatch) $matchType = 'position+company';
                    
                    
                    // Update description if provided (work experience is description-only; we do not send or merge responsibility_categories)
                    if (isset($rewrittenWork['description'])) {
                        $oldDesc = $work['description'] ?? '';
                        $newDesc = $rewrittenWork['description'];
                        if (is_array($newDesc)) {
                            $newDesc = implode("\n", array_map(function ($line) {
                                return is_array($line) ? ($line['content'] ?? $line['text'] ?? json_encode($line)) : (string) $line;
                            }, $newDesc));
                        } else {
                            $newDesc = (string) $newDesc;
                        }
                        // When tailoring a single role, reject only if description is unchanged (we only send/expect description, like summary)
                        if ($singleWorkExperienceId !== '') {
                            $normOldDesc = preg_replace('/\s+/', ' ', trim($oldDesc));
                            $normNewDesc = preg_replace('/\s+/', ' ', trim($newDesc));
                            if ($normOldDesc !== '' && $normOldDesc === $normNewDesc) {
                                $singleRoleOutputUnchanged = true;
                                $found = true;
                                break;
                            }
                        }
                        $work['description'] = $newDesc;
                        
                                            }
                    
                    // Work experience is description-only: do not overwrite responsibility_categories from AI (leave existing bullets as-is)
                    
                    $found = true;
                    $matchType = $idMatch ? 'id' : 'position+company';
                    break;
                }
            }
            
            // Index-based fallback: AI often returns "1"/"2" or changed titles; match by array position
            if (!$found && isset($cvData['work_experience'][$idx])) {
                $work = &$cvData['work_experience'][$idx];
                $matchType = 'index';
                if (isset($rewrittenWork['description'])) {
                    $newDesc = $rewrittenWork['description'];
                    if (is_array($newDesc)) {
                        $newDesc = implode("\n", array_map(function ($line) {
                            return is_array($line) ? ($line['content'] ?? $line['text'] ?? json_encode($line)) : (string) $line;
                        }, $newDesc));
                    } else {
                        $newDesc = (string) $newDesc;
                    }
                    $work['description'] = $newDesc;
                }
                // Work experience is description-only: do not overwrite responsibility_categories
                $found = true;
            }
            
            if (!$found) {
                            }
        }
        
        // When tailoring one role, reject unchanged output so user gets clear feedback
        if ($singleRoleOutputUnchanged) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'error' => 'The AI returned unchanged text for this role. Local models (Ollama) often copy instead of rephrasing. Try again, or use a cloud AI (OpenAI, Anthropic, Gemini) in Settings > AI Settings for better tailoring.'
            ]);
            exit;
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
    
    // Update education if rewritten (only update description by id; do not add or replace list; deduplicate AI response by id)
    if (isset($rewrittenData['education']) && is_array($rewrittenData['education'])) {
        $seenEduIds = [];
        foreach ($rewrittenData['education'] as $rewrittenEdu) {
            $rid = $rewrittenEdu['id'] ?? null;
            if ($rid === null || isset($seenEduIds[$rid])) {
                continue;
            }
            $seenEduIds[$rid] = true;
            foreach ($cvData['education'] as &$edu) {
                $eduId = $edu['id'] ?? $edu['original_education_id'] ?? null;
                if ($eduId !== null && (string)$eduId === (string)$rid) {
                    if (isset($rewrittenEdu['description'])) {
                        $edu['description'] = $rewrittenEdu['description'];
                    }
                    break;
                }
            }
        }
        unset($edu);
    }
    
    // Update projects only when we asked the AI to rewrite projects (otherwise do not reorder or overwrite)
    if (in_array('projects', $sectionsToRewrite) && isset($rewrittenData['projects']) && is_array($rewrittenData['projects'])) {
        $projectMapping = []; // rewrittenIndex => cvDataIndex (so we keep description and order in sync)
        foreach ($rewrittenData['projects'] as $projIdx => $rewrittenProj) {
            $found = false;
            foreach ($cvData['projects'] as $k => &$proj) {
                $idMatch = isset($rewrittenProj['id']) && isset($proj['id']) && (string)$proj['id'] === (string)$rewrittenProj['id'];
                $titleMatch = !empty($rewrittenProj['title']) && !empty($proj['title']) && 
                             strtolower(trim($proj['title'])) === strtolower(trim($rewrittenProj['title']));
                
                if ($idMatch || $titleMatch) {
                    if (isset($rewrittenProj['description'])) {
                        $proj['description'] = $rewrittenProj['description'];
                    }
                    $projectMapping[$projIdx] = $k;
                    $found = true;
                    break;
                }
            }
            // Index-based fallback: assume AI returns projects in same order as original
            if (!$found && isset($cvData['projects'][$projIdx])) {
                if (isset($rewrittenProj['description'])) {
                    $cvData['projects'][$projIdx]['description'] = $rewrittenProj['description'];
                }
                $projectMapping[$projIdx] = $projIdx;
            }
        }
        unset($proj); // break reference
        // Build final order from mapping (rewrittenData order = AI order; each slot gets the project we updated for that slot)
        $ordered = [];
        $used = [];
        foreach ($rewrittenData['projects'] as $projIdx => $rewrittenProj) {
            if (isset($projectMapping[$projIdx])) {
                $k = $projectMapping[$projIdx];
                if (!isset($used[$k])) {
                    $used[$k] = true;
                    $ordered[] = $cvData['projects'][$k];
                }
            }
        }
        // Append any original projects not in AI response (keep at end)
        foreach ($cvData['projects'] as $k => $proj) {
            if (!isset($used[$k])) {
                $ordered[] = $proj;
            }
        }
        if (!empty($ordered)) {
            $cvData['projects'] = $ordered;
        }
    }
    
    // Update certifications if rewritten (only update description by id; do not add or replace list; deduplicate AI response by id)
    if (isset($rewrittenData['certifications']) && is_array($rewrittenData['certifications'])) {
        $seenCertIds = [];
        foreach ($rewrittenData['certifications'] as $rewrittenCert) {
            $rid = $rewrittenCert['id'] ?? null;
            if ($rid === null || isset($seenCertIds[$rid])) {
                continue;
            }
            $seenCertIds[$rid] = true;
            foreach ($cvData['certifications'] as &$cert) {
                $certId = $cert['id'] ?? $cert['original_certification_id'] ?? null;
                if ($certId !== null && (string)$certId === (string)$rid) {
                    if (isset($rewrittenCert['description'])) {
                        $cert['description'] = $rewrittenCert['description'];
                    }
                    break;
                }
            }
        }
        unset($cert);
    }
    
    // Update memberships if rewritten (cvData uses 'memberships'; AI may return 'professional_memberships')
    $rewrittenMemberships = $rewrittenData['professional_memberships'] ?? $rewrittenData['memberships'] ?? null;
    if (isset($rewrittenMemberships) && is_array($rewrittenMemberships) && !empty($cvData['memberships'])) {
        foreach ($rewrittenMemberships as $idx => $rewrittenMembership) {
            if (isset($cvData['memberships'][$idx])) {
                if (isset($rewrittenMembership['description'])) {
                    $cvData['memberships'][$idx]['description'] = $rewrittenMembership['description'];
                }
            } else {
                foreach ($cvData['memberships'] as &$membership) {
                    if (isset($rewrittenMembership['id']) && isset($membership['id']) && $membership['id'] === $rewrittenMembership['id']) {
                        if (isset($rewrittenMembership['description'])) {
                            $membership['description'] = $rewrittenMembership['description'];
                        }
                        break;
                    }
                }
            }
        }
        unset($membership);
    }
    
    // Update interests if rewritten
    if (isset($rewrittenData['interests']) && is_array($rewrittenData['interests']) && !empty($cvData['interests'])) {
        foreach ($rewrittenData['interests'] as $idx => $rewrittenInterest) {
            if (isset($cvData['interests'][$idx])) {
                if (isset($rewrittenInterest['description'])) {
                    $cvData['interests'][$idx]['description'] = $rewrittenInterest['description'];
                }
            } else {
                foreach ($cvData['interests'] as &$interest) {
                    if (isset($rewrittenInterest['id']) && isset($interest['id']) && $interest['id'] === $rewrittenInterest['id']) {
                        if (isset($rewrittenInterest['description'])) {
                            $interest['description'] = $rewrittenInterest['description'];
                        }
                        break;
                    }
                }
            }
        }
        unset($interest);
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
        'message' => $successMessage
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    $errorMessage = $e->getMessage();
    $errorTrace = $e->getTraceAsString();
    error_log("AI Rewrite CV Error: " . $errorMessage);
    error_log("AI Rewrite CV Trace: " . $errorTrace);
    
    
    echo json_encode([
        'success' => false,
        'error' => $errorMessage,
        'debug' => APP_ENV === 'development' ? [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => substr($errorTrace, 0, 200)
        ] : null
    ]);
}

