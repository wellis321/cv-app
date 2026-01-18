<?php
/**
 * AI CV Rewriting Interface
 * Generate job-specific CV variants using AI
 */

require_once __DIR__ . '/../php/helpers.php';

requireAuth();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get user's job applications
$jobApplications = db()->fetchAll(
    "SELECT id, company_name, job_title, job_description, notes 
     FROM job_applications 
     WHERE user_id = ? 
     ORDER BY created_at DESC",
    [$user['id']]
);

// Get user's CV variants
$variants = getUserCvVariants($user['id']);

// Suggest a unique variant name
$suggestedVariantName = suggestUniqueVariantName($user['id'], 'AI-Generated CV');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Generate AI CV | Simple CV Builder',
        'metaDescription' => 'Generate a job-specific CV using AI.',
        'canonicalUrl' => APP_URL . '/cv-variants/rewrite.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Generate AI CV</h1>
                <p class="mt-1 text-sm text-gray-500">Create a job-specific version of your CV using AI</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <?php echo e($success); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form id="rewrite-form" class="space-y-6">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo generateCsrfToken(); ?>">

                    <!-- Source CV Selection -->
                    <div>
                        <label for="cv_variant_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Source CV
                        </label>
                        <select id="cv_variant_id" name="cv_variant_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Master CV (Default)</option>
                            <?php foreach ($variants as $variant): ?>
                                <option value="<?php echo e($variant['id']); ?>">
                                    <?php echo e($variant['variant_name']); ?>
                                    <?php if ($variant['is_master']): ?> (Master)<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select which CV to use as the base for rewriting</p>
                    </div>

                    <!-- Job Application Selection -->
                    <div>
                        <label for="job_application_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Job Application (Optional)
                        </label>
                        <select id="job_application_id" name="job_application_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a job application...</option>
                            <?php foreach ($jobApplications as $jobApp): ?>
                                <option value="<?php echo e($jobApp['id']); ?>" 
                                        data-description="<?php echo e(htmlspecialchars($jobApp['job_description'] ?? $jobApp['notes'] ?? '')); ?>">
                                    <?php echo e($jobApp['company_name']); ?> - <?php echo e($jobApp['job_title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Or paste a job description below</p>
                    </div>

                    <!-- Job Description -->
                    <div>
                        <label for="job_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Job Description <span class="text-red-600">*</span>
                        </label>
                        <textarea id="job_description" 
                                  name="job_description" 
                                  rows="8" 
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Paste the job description here..."></textarea>
                        <p class="mt-1 text-sm text-gray-500">The AI will rewrite your CV to match this job description</p>
                    </div>

                    <!-- Prompt Instructions -->
                    <?php
                    // Check if user has saved custom instructions
                    $userProfile = db()->fetchOne(
                        "SELECT cv_rewrite_prompt_instructions FROM profiles WHERE id = ?",
                        [$user['id']]
                    );
                    $hasSavedInstructions = !empty($userProfile['cv_rewrite_prompt_instructions'] ?? '');
                    ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Prompt Instructions
                        </label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="prompt_default" 
                                       name="prompt_instructions_mode" 
                                       value="default"
                                       <?php echo !$hasSavedInstructions ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="prompt_default" class="ml-2 text-sm text-gray-700">
                                    Use default instructions
                                </label>
                            </div>
                            <?php if ($hasSavedInstructions): ?>
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="prompt_saved" 
                                       name="prompt_instructions_mode" 
                                       value="saved"
                                       checked
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="prompt_saved" class="ml-2 text-sm text-gray-700">
                                    Use saved custom instructions
                                </label>
                                <a href="/cv-prompt-settings.php" class="ml-2 text-xs text-blue-600 hover:text-blue-800 underline">(edit)</a>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-start">
                                <input type="radio" 
                                       id="prompt_custom" 
                                       name="prompt_instructions_mode" 
                                       value="custom"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 mt-1">
                                <div class="ml-2 flex-1">
                                    <label for="prompt_custom" class="text-sm text-gray-700">
                                        Enter custom instructions for this generation only
                                    </label>
                                    <textarea id="prompt_custom_text" 
                                              name="prompt_custom_text" 
                                              rows="6" 
                                              maxlength="2000"
                                              class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm hidden"
                                              placeholder="Enter custom instructions that will only be used for this CV generation..."></textarea>
                                    <p class="mt-1 text-xs text-gray-500 hidden" id="prompt_custom_help">
                                        These instructions will only be used for this CV generation and won't be saved.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            <a href="/cv-prompt-settings.php" class="text-blue-600 hover:text-blue-800">Manage saved instructions</a> or 
                            <a href="/resources/ai/prompt-best-practices.php" class="text-blue-600 hover:text-blue-800">learn best practices</a>
                        </p>
                    </div>

                    <!-- Sections to Rewrite -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sections to Rewrite
                        </label>
                        <div class="space-y-2 border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_professional_summary" 
                                       name="sections_to_rewrite[]" 
                                       value="professional_summary"
                                       checked
                                       disabled
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_professional_summary" class="ml-2 text-sm text-gray-700">
                                    Professional Summary <span class="text-gray-500">(always included)</span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_work_experience" 
                                       name="sections_to_rewrite[]" 
                                       value="work_experience"
                                       checked
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_work_experience" class="ml-2 text-sm text-gray-700">
                                    Work Experience
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_skills" 
                                       name="sections_to_rewrite[]" 
                                       value="skills"
                                       checked
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_skills" class="ml-2 text-sm text-gray-700">
                                    Skills
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_education" 
                                       name="sections_to_rewrite[]" 
                                       value="education"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_education" class="ml-2 text-sm text-gray-700">
                                    Education
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_projects" 
                                       name="sections_to_rewrite[]" 
                                       value="projects"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_projects" class="ml-2 text-sm text-gray-700">
                                    Projects
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_certifications" 
                                       name="sections_to_rewrite[]" 
                                       value="certifications"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_certifications" class="ml-2 text-sm text-gray-700">
                                    Certifications
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_professional_memberships" 
                                       name="sections_to_rewrite[]" 
                                       value="professional_memberships"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_professional_memberships" class="ml-2 text-sm text-gray-700">
                                    Professional Memberships
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="section_interests" 
                                       name="sections_to_rewrite[]" 
                                       value="interests"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="section_interests" class="ml-2 text-sm text-gray-700">
                                    Interests
                                </label>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Select which sections of your CV should be rewritten to match the job description. 
                            <a href="/cv-prompt-settings.php" class="text-blue-600 hover:text-blue-800">Customise prompt instructions</a> for better results.
                        </p>
                    </div>

                    <!-- Variant Name -->
                    <div>
                        <label for="variant_name" class="block text-sm font-medium text-gray-700 mb-2">
                            CV Variant Name
                        </label>
                        <input type="text" 
                               id="variant_name" 
                               name="variant_name" 
                               value="<?php echo e($suggestedVariantName); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Give this CV variant a name for easy identification</p>
                    </div>

                    <!-- Cost Warning -->
                    <div id="cost-warning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Cost Notice:</strong> You're using a paid AI service. This generation will incur API costs. 
                                    <a href="/ai-settings.php" class="underline font-semibold">Switch to free options (Local Ollama or Browser-Based AI)</a> to avoid charges.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <a href="/cv-variants.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                    <button type="submit" 
                            id="submit-btn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="submit-text">Generate CV</span>
                        <span id="submit-loading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating...
                        </span>
                    </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">How it works</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>The AI will analyse your CV and the job description, then rewrite relevant sections to better match the job requirements. You can review and edit the generated CV variant after it's created.</p>
                            <p class="mt-2 text-xs text-blue-600"><strong>Note:</strong> Generation may take 30-60 seconds. Please wait for the process to complete.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>

    <!-- Browser AI Service Scripts -->
    <script src="/js/model-cache-manager.js"></script>
    <script src="/js/browser-ai-service.js"></script>

    <script>
        // Handle prompt instructions mode selection
        document.addEventListener('DOMContentLoaded', () => {
            const promptModes = document.querySelectorAll('input[name="prompt_instructions_mode"]');
            const customTextarea = document.getElementById('prompt_custom_text');
            const customHelp = document.getElementById('prompt_custom_help');
            
            function toggleCustomTextarea() {
                const selectedMode = document.querySelector('input[name="prompt_instructions_mode"]:checked');
                if (selectedMode && selectedMode.value === 'custom') {
                    customTextarea.classList.remove('hidden');
                    customHelp.classList.remove('hidden');
                    customTextarea.required = true;
                } else {
                    customTextarea.classList.add('hidden');
                    customHelp.classList.add('hidden');
                    customTextarea.required = false;
                }
            }
            
            promptModes.forEach(mode => {
                mode.addEventListener('change', toggleCustomTextarea);
            });
            
            toggleCustomTextarea();
        });
        
        document.getElementById('job_application_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value && selectedOption.dataset.description) {
                document.getElementById('job_description').value = selectedOption.dataset.description;
            }
        });

        // Check AI service and show cost warning if using paid service
        (async function() {
            try {
                const response = await fetch('/api/get-ai-service.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    const paidServices = ['openai', 'anthropic', 'gemini', 'grok'];
                    if (data.service && paidServices.includes(data.service.toLowerCase())) {
                        document.getElementById('cost-warning').classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Failed to check AI service:', error);
            }
        })();

        document.getElementById('rewrite-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');
            
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            
            // Show loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'generation-loading-overlay';
            loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
            loadingOverlay.innerHTML = `
                <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                    <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Generating Your CV</h3>
                    <p class="text-sm text-gray-600 mb-4">This may take 30-60 seconds. Please wait...</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 60%"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingOverlay);
            
            const formData = new FormData(this);
            
            try {
                // Create AbortController for timeout
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 180000); // 3 minute timeout
                
                const response = await fetch('/api/ai-rewrite-cv.php', {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal
                });
                
                clearTimeout(timeoutId);
                
                // Remove loading overlay
                loadingOverlay.remove();
                
                if (!response.ok) {
                    const errorText = await response.text();
                    let errorData;
                    try {
                        errorData = JSON.parse(errorText);
                    } catch (e) {
                        errorData = { error: errorText || 'Server error' };
                    }
                    throw new Error(errorData.error || 'Generation failed');
                }
                
                const result = await response.json();
                
                // Check if this is browser AI execution
                if (result.success && result.browser_execution) {
                    // Browser AI mode - execute client-side
                    await executeBrowserAI(result, loadingOverlay);
                    return;
                }
                
                if (result.success) {
                    window.location.href = '/cv-variants.php?success=CV generated successfully';
                } else {
                    throw new Error(result.error || 'Failed to generate CV');
                }
            } catch (error) {
                // Remove loading overlay
                if (loadingOverlay && loadingOverlay.parentNode) {
                    loadingOverlay.remove();
                }
                
                console.error('Error:', error);
                
                let errorMessage = 'An error occurred. Please try again.';
                if (error.name === 'AbortError') {
                    errorMessage = 'Request timed out. The generation is taking longer than expected. Please try again or check if Ollama is running properly.';
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert('Error: ' + errorMessage);
                
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitLoading.classList.add('hidden');
            }
        });

        // Execute browser AI for CV rewriting
        async function executeBrowserAI(result, loadingOverlay) {
            try {
                // Check browser support
                const support = BrowserAIService.checkBrowserSupport();
                if (!support.required) {
                    throw new Error('Browser does not support WebGPU or WebGL. Browser AI requires a modern browser with GPU support.');
                }

                // Update loading overlay to show model loading
                if (loadingOverlay) {
                    loadingOverlay.querySelector('p').textContent = 'Loading AI model. This may take a few minutes on first use...';
                }

                // Initialize browser AI
                const modelType = result.model_type === 'webllm' ? 'webllm' : 'tensorflow';
                await BrowserAIService.initBrowserAI(modelType, result.model, (progress) => {
                    if (loadingOverlay && progress.message) {
                        loadingOverlay.querySelector('p').textContent = progress.message;
                    }
                });

                // Update loading overlay
                if (loadingOverlay) {
                    loadingOverlay.querySelector('p').textContent = 'Rewriting CV... This may take 30-60 seconds.';
                }

                // Generate rewritten CV using browser AI
                const rewrittenText = await BrowserAIService.generateText(result.prompt, {
                    temperature: 0.7,
                    maxTokens: 4000
                });

                // Parse rewritten CV JSON
                let rewrittenData;
                try {
                    rewrittenData = JSON.parse(rewrittenText);
                } catch (e) {
                    // If JSON parsing fails, try to extract JSON from markdown
                    const jsonMatch = rewrittenText.match(/\{[\s\S]*\}/);
                    if (jsonMatch) {
                        rewrittenData = JSON.parse(jsonMatch[0]);
                    } else {
                        throw new Error('Failed to parse AI response as JSON');
                    }
                }

                // Send rewritten CV to server to save as variant
                const formData = new FormData(document.getElementById('rewrite-form'));
                formData.append('browser_ai_result', JSON.stringify(rewrittenData));

                const saveResponse = await fetch('/api/ai-rewrite-cv.php', {
                    method: 'POST',
                    body: formData
                });

                const saveResult = await saveResponse.json();

                // Cleanup
                await BrowserAIService.cleanup();
                if (loadingOverlay) loadingOverlay.remove();

                if (saveResult.success) {
                    window.location.href = '/cv-variants.php?success=CV generated successfully';
                } else {
                    throw new Error(saveResult.error || 'Failed to save rewritten CV');
                }
            } catch (error) {
                console.error('Browser AI execution error:', error);
                if (loadingOverlay) loadingOverlay.remove();
                
                const submitBtn = document.getElementById('submit-btn');
                const submitText = document.getElementById('submit-text');
                const submitLoading = document.getElementById('submit-loading');
                
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.classList.remove('hidden');
                if (submitLoading) submitLoading.classList.add('hidden');
                
                alert('Browser AI Error: ' + error.message);
            }
        }
    </script>
</body>
</html>

