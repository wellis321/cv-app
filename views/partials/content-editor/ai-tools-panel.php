<?php
/**
 * AI Tools Panel for Content Editor
 * Includes CV Quality Assessment functionality
 */

if (!function_exists('getUserCvVariants')) {
    require_once __DIR__ . '/../../../php/cv-variants.php';
}

$userId = getUserId();
$variantId = $_GET['variant_id'] ?? null;
$jobApplicationId = $_GET['job_application_id'] ?? null;

// Get CV variant
$cvVariant = null;
if ($variantId) {
    $cvVariant = getCvVariant($variantId, $userId);
    if (!$cvVariant) {
        $variantId = null;
    }
} else {
    // Use master CV
    $masterVariantId = getOrCreateMasterVariant($userId);
    if ($masterVariantId) {
        $cvVariant = getCvVariant($masterVariantId);
        $variantId = $masterVariantId;
    }
}

// Get latest assessment
$assessment = null;
if ($variantId) {
    $assessment = db()->fetchOne(
        "SELECT * FROM cv_quality_assessments 
         WHERE cv_variant_id = ? AND user_id = ?
         ORDER BY created_at DESC 
         LIMIT 1",
        [$variantId, $userId]
    );
    
    if ($assessment) {
        $assessment['recommendations'] = json_decode($assessment['recommendations'] ?? '[]', true);
        $assessment['strengths'] = json_decode($assessment['strengths'] ?? '[]', true);
        $assessment['weaknesses'] = json_decode($assessment['weaknesses'] ?? '[]', true);
        $assessment['enhanced_recommendations'] = json_decode($assessment['enhanced_recommendations'] ?? null, true);
        if (!is_array($assessment['enhanced_recommendations'])) {
            $assessment['enhanced_recommendations'] = [];
        }
    }
}

$cvVariants = getUserCvVariants($userId);
$csrf = csrfToken();
?>
<div class="p-6 max-w-6xl mx-auto" data-ai-tools-panel>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">AI Assessments</h1>
        <p class="mt-1 text-sm text-gray-500">Get AI-powered feedback on your CV quality</p>
    </div>

    <!-- Cost Warning -->
    <div id="cost-warning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Cost Notice:</strong> You're using a paid AI service. This assessment will incur API costs. 
                    <a href="/ai-settings.php" class="underline font-semibold">Switch to free options (Local Ollama or Browser-Based AI)</a> to avoid charges.
                </p>
            </div>
        </div>
    </div>

    <!-- CV Selection -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-medium text-gray-700">Select CV to Assess</label>
            <a href="/resources/ai/setup-ollama.php" class="text-xs text-purple-600 hover:text-purple-700 underline">
                Setup Local AI
            </a>
        </div>
        <select id="cv-quality-variant-select" 
                class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Master CV</option>
            <?php 
            foreach ($cvVariants as $v): 
                if ($v['is_master']) continue;
                $selected = ($variantId && $variantId === $v['id']) ? 'selected' : '';
            ?>
                <option value="<?php echo e($v['id']); ?>" <?php echo $selected; ?>>
                    <?php echo e($v['variant_name']); ?>
                    <?php if ($v['ai_generated']): ?> [AI]<?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="mt-2 text-xs text-gray-500">
            <?php if ($cvVariant): ?>
                Currently assessing: <strong><?php echo e($cvVariant['variant_name']); ?></strong>
                <?php if ($cvVariant['is_master']): ?>
                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Master CV</span>
                <?php endif; ?>
            <?php else: ?>
                Assessing your <strong>Master CV</strong> (your main CV with all sections)
            <?php endif; ?>
        </p>
    </div>

    <!-- Run Assessment Button -->
    <div class="mb-6 flex justify-end">
        <button id="run-cv-assessment-btn" 
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
            <span id="assess-text">Run Assessment</span>
            <span id="assess-loading" class="hidden">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Assessing...
            </span>
        </button>
    </div>

    <!-- Assessment Results Container -->
    <div id="cv-assessment-results" class="space-y-6">
        <?php if ($assessment): ?>
            <?php include __DIR__ . '/cv-quality-assessment-results.php'; ?>
        <?php else: ?>
            <!-- No Assessment Yet -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No assessment yet</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Click "Run Assessment" to get AI-powered feedback on your CV.
                </p>
                <p class="mt-2 text-xs text-gray-400">Note: Assessment may take 30-60 seconds depending on your AI service configuration.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    position: relative;
}
.score-excellent { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.score-good { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
.score-fair { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.score-poor { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.score-label {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.5rem;
}
</style>

<script>
(function() {
    // Define runAssessment first so it's available when we attach the event listener
    async function runAssessment() {
        const assessBtn = document.getElementById('run-cv-assessment-btn');
        const assessText = document.getElementById('assess-text');
        const assessLoading = document.getElementById('assess-loading');
        const variantSelect = document.getElementById('cv-quality-variant-select');
        
        if (!assessBtn) return;
        
        assessBtn.disabled = true;
        if (assessText) assessText.classList.add('hidden');
        if (assessLoading) assessLoading.classList.remove('hidden');
        
        // Show loading overlay
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'assessment-loading-overlay';
        loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        loadingOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Assessing Your CV</h3>
                <p class="text-sm text-gray-600 mb-4">This may take 30-60 seconds. Please wait...</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 60%"></div>
                </div>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
        
        const formData = new FormData();
        formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo $csrf; ?>');
        formData.append('action', 'assess');
        const variantId = variantSelect ? variantSelect.value : '';
        if (variantId) {
            formData.append('cv_variant_id', variantId);
        }
        <?php if ($jobApplicationId): ?>
            formData.append('job_application_id', '<?php echo e($jobApplicationId); ?>');
        <?php endif; ?>
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 180000);
            
            const response = await fetch('/api/ai-assess-cv.php', {
                method: 'POST',
                body: formData,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                const errorText = await response.text();
                let errorData;
                try {
                    errorData = JSON.parse(errorText);
                } catch (e) {
                    errorData = { error: errorText || 'Server error' };
                }
                throw new Error(errorData.error || 'Assessment failed');
            }
            
            const result = await response.json();
            
            // Check if browser AI execution is required
            if (result.success && result.browser_execution) {
                await executeBrowserAI(result, loadingOverlay, variantId);
                return;
            }
            
            loadingOverlay.remove();
            
            if (result.success) {
                // Reload the ai-tools section to show new assessment
                if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                    const hash = variantId ? '#ai-tools&variant_id=' + encodeURIComponent(variantId) : '#ai-tools';
                    window.location.hash = hash;
                    setTimeout(() => {
                        window.contentEditor.loadSection('ai-tools');
                    }, 100);
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(result.error || 'Failed to assess CV');
            }
        } catch (error) {
            if (loadingOverlay && loadingOverlay.parentNode) {
                loadingOverlay.remove();
            }
            
            console.error('Error:', error);
            
            let errorMessage = 'An error occurred. Please try again.';
            if (error.name === 'AbortError') {
                errorMessage = 'Request timed out. The assessment is taking longer than expected. Please try again or check if Ollama is running properly.';
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            alert('Error: ' + errorMessage);
            
            if (assessBtn) assessBtn.disabled = false;
            if (assessText) assessText.classList.remove('hidden');
            if (assessLoading) assessLoading.classList.add('hidden');
        }
    }

    async function executeBrowserAI(result, loadingOverlay, variantId) {
        try {
            // BrowserAIService should already be loaded globally, but check anyway
            if (typeof BrowserAIService === 'undefined') {
                throw new Error('Browser AI service not available. Please refresh the page.');
            }

            const support = BrowserAIService.checkBrowserSupport();
            if (!support.required) {
                throw new Error('Browser does not support WebGPU or WebGL. Browser AI requires a modern browser with GPU support.');
            }

            if (loadingOverlay) {
                const p = loadingOverlay.querySelector('p');
                if (p) p.textContent = 'Loading AI model. This may take a few minutes on first use...';
            }

            const modelType = result.model_type === 'webllm' ? 'webllm' : 'tensorflow';
            await BrowserAIService.initBrowserAI(modelType, result.model, (progress) => {
                if (loadingOverlay && progress && progress.message) {
                    const p = loadingOverlay.querySelector('p');
                    if (p) p.textContent = progress.message;
                }
            });

            let prompt = result.prompt || '';
            if (!prompt) {
                const cvData = result.cv_data || {};
                const jobDescription = result.job_description || '';
                prompt = `Assess this CV for quality and provide scores and recommendations. CV data: ${JSON.stringify(cvData)}. Job description: ${jobDescription}`;
            }

            if (loadingOverlay) {
                const p = loadingOverlay.querySelector('p');
                if (p) p.textContent = 'Assessing CV... This may take 30-60 seconds.';
            }

            const assessmentText = await BrowserAIService.generateText(prompt, {
                temperature: 0.3,
                maxTokens: 2000
            });

            let assessment;
            try {
                assessment = JSON.parse(assessmentText);
            } catch (e) {
                const jsonMatch = assessmentText.match(/\{[\s\S]*\}/);
                if (jsonMatch) {
                    assessment = JSON.parse(jsonMatch[0]);
                } else {
                    throw new Error('Failed to parse AI response as JSON');
                }
            }

            const saveFormData = new FormData();
            saveFormData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo $csrf; ?>');
            saveFormData.append('action', 'assess');
            saveFormData.append('browser_ai_result', JSON.stringify(assessment));
            if (variantId) {
                saveFormData.append('cv_variant_id', variantId);
            }

            const saveResponse = await fetch('/api/ai-assess-cv.php', {
                method: 'POST',
                body: saveFormData
            });

            const saveResult = await saveResponse.json();

            await BrowserAIService.cleanup();
            if (loadingOverlay && loadingOverlay.parentNode) {
                loadingOverlay.remove();
            }

            if (saveResult.success) {
                // Reload the ai-tools section to show new assessment
                if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                    const hash = variantId ? '#ai-tools&variant_id=' + encodeURIComponent(variantId) : '#ai-tools';
                    window.location.hash = hash;
                    setTimeout(() => {
                        window.contentEditor.loadSection('ai-tools');
                    }, 100);
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(saveResult.error || 'Failed to save assessment');
            }
        } catch (error) {
            console.error('Browser AI execution error:', error);
            if (loadingOverlay && loadingOverlay.parentNode) {
                loadingOverlay.remove();
            }
            
            const assessBtn = document.getElementById('run-cv-assessment-btn');
            const assessText = document.getElementById('assess-text');
            const assessLoading = document.getElementById('assess-loading');
            
            if (assessBtn) assessBtn.disabled = false;
            if (assessText) assessText.classList.remove('hidden');
            if (assessLoading) assessLoading.classList.add('hidden');
            
            alert('Browser AI Error: ' + error.message);
        }
    }

    // Initialize function that can be called after DOM is ready
    function initAiToolsPanel() {
        // Check AI service and show cost warning if using paid service
        (async function() {
            try {
                const response = await fetch('/api/get-ai-service.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.is_paid) {
                        const warning = document.getElementById('cost-warning');
                        if (warning) warning.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Failed to check AI service:', error);
            }
        })();

        // CV variant selector - update hash when changed
        const variantSelect = document.getElementById('cv-quality-variant-select');
        if (variantSelect && !variantSelect.dataset.listenerAttached) {
            variantSelect.dataset.listenerAttached = '1';
            variantSelect.addEventListener('change', function() {
                const variantId = this.value;
                if (variantId) {
                    window.location.hash = '#ai-tools&variant_id=' + encodeURIComponent(variantId);
                } else {
                    window.location.hash = '#ai-tools';
                }
            });
        }

        // Run assessment button
        const assessBtn = document.getElementById('run-cv-assessment-btn');
        if (assessBtn && !assessBtn.dataset.listenerAttached) {
            assessBtn.dataset.listenerAttached = '1';
            assessBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                runAssessment();
            });
        }
    }

    // Run initialization after a short delay to ensure DOM is ready
    setTimeout(() => {
        initAiToolsPanel();
    }, 50);

    // Also listen for the custom event in case initialization needs to be retriggered
    const panel = document.querySelector('[data-ai-tools-panel]');
    if (panel) {
        panel.addEventListener('ai-tools-loaded', initAiToolsPanel);
    }

    // Toggle explanation sections
    window.toggleExplanation = function(id) {
        const element = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
            if (icon) {
                icon.classList.add('rotate-180');
            }
        } else {
            element.classList.add('hidden');
            if (icon) {
                icon.classList.remove('rotate-180');
            }
        }
    };
})();
</script>
