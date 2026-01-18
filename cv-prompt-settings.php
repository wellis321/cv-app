<?php
/**
 * CV Prompt Settings Page
 * Allows users to customise their CV rewrite prompt instructions
 */

require_once __DIR__ . '/php/helpers.php';

requireAuth();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get current custom instructions from database
$userProfile = db()->fetchOne(
    "SELECT cv_rewrite_prompt_instructions FROM profiles WHERE id = ?",
    [$user['id']]
);
$currentInstructions = $userProfile['cv_rewrite_prompt_instructions'] ?? '';

// Default instructions for reference
$defaultInstructions = "1. Maintain factual accuracy - do not invent experiences, dates, or qualifications\n";
$defaultInstructions .= "2. ENHANCE and EXPAND content with relevant details, achievements, and metrics. Do NOT simplify or reduce content. Preserve all original information while adding job-relevant details.\n";
$defaultInstructions .= "3. Emphasize relevant skills and experiences that match the job description\n";
$defaultInstructions .= "4. Use keywords from the job description naturally throughout\n";
$defaultInstructions .= "5. Keep the same structure and format\n";
$defaultInstructions .= "6. Maintain professional tone\n";
$defaultInstructions .= "7. For work experience, rewrite descriptions and responsibility items to highlight relevant achievements with specific examples and quantifiable results\n";
$defaultInstructions .= "8. For professional summary, tailor it to emphasize alignment with the job while maintaining or increasing detail level\n";
$defaultInstructions .= "9. Ensure skills section includes relevant keywords from the job description\n";
$defaultInstructions .= "10. When rewriting, add context, metrics, and achievements where appropriate - make content more compelling, not less";

// If no custom instructions, use defaults
$instructionsToEdit = !empty($currentInstructions) ? $currentInstructions : $defaultInstructions;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'CV Prompt Settings | Simple CV Builder',
        'metaDescription' => 'Customise your CV rewrite prompt instructions for better AI results.',
        'canonicalUrl' => APP_URL . '/cv-prompt-settings.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">CV Prompt Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Customise how the AI rewrites your CV by editing the prompt instructions</p>
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

            <!-- Info Box -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">About Prompt Customisation</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>You can customise the instructions that guide the AI when rewriting your CV. These instructions are merged with the default system instructions to ensure proper formatting and structure.</p>
                            <p class="mt-2">
                                <a href="/resources/ai/prompt-best-practices.php" class="text-blue-600 hover:text-blue-800 underline">Learn best practices for writing effective prompts</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form id="prompt-settings-form" class="space-y-6">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo generateCsrfToken(); ?>">

                    <!-- Instructions Editor -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="instructions" class="block text-sm font-medium text-gray-700">
                                Custom Instructions
                            </label>
                            <span id="char-count" class="text-sm text-gray-500">0 / 2000 characters</span>
                        </div>
                        <textarea id="instructions" 
                                  name="instructions" 
                                  rows="15" 
                                  maxlength="2000"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                  placeholder="Enter your custom instructions here..."><?php echo e($instructionsToEdit); ?></textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            These instructions will be added to the default system instructions. Be specific about what you want the AI to emphasize or how you want content rewritten.
                        </p>
                    </div>

                    <!-- Preview Section -->
                    <div>
                        <button type="button" 
                                id="preview-btn"
                                class="text-sm text-blue-600 hover:text-blue-800">
                            Preview Full Prompt →
                        </button>
                        <div id="preview-section" class="hidden mt-4 p-4 bg-gray-50 border border-gray-300 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Prompt Preview</h4>
                            <pre id="preview-content" class="text-xs text-gray-600 whitespace-pre-wrap font-mono overflow-x-auto"></pre>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <button type="button" 
                                id="reset-btn"
                                class="text-sm text-gray-600 hover:text-gray-800">
                            Reset to Defaults
                        </button>
                        <div class="flex space-x-3">
                            <a href="/cv-variants/rewrite.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    id="save-btn"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Instructions
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tips for Writing Effective Instructions</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>Be specific about what you want emphasized (e.g., "Focus on quantifiable achievements and metrics")</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>Specify tone preferences (e.g., "Use action verbs and active voice")</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>Mention industry-specific keywords or terminology to include</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>Request specific formatting or structure preferences</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-600 mr-2">•</span>
                        <span>Always emphasize maintaining factual accuracy</span>
                    </li>
                </ul>
                <p class="mt-4 text-sm text-gray-600">
                    <a href="/resources/ai/prompt-best-practices.php" class="text-blue-600 hover:text-blue-800 underline">Read the full guide for more detailed tips and examples</a>
                </p>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('prompt-settings-form');
            const instructionsTextarea = document.getElementById('instructions');
            const charCount = document.getElementById('char-count');
            const previewBtn = document.getElementById('preview-btn');
            const previewSection = document.getElementById('preview-section');
            const previewContent = document.getElementById('preview-content');
            const resetBtn = document.getElementById('reset-btn');
            const saveBtn = document.getElementById('save-btn');
            const defaultInstructions = `<?php echo addslashes($defaultInstructions); ?>`;

            // Character counter
            function updateCharCount() {
                const length = instructionsTextarea.value.length;
                charCount.textContent = `${length} / 2000 characters`;
                if (length > 2000) {
                    charCount.classList.add('text-red-600');
                } else {
                    charCount.classList.remove('text-red-600');
                }
            }
            instructionsTextarea.addEventListener('input', updateCharCount);
            updateCharCount();

            // Preview functionality
            previewBtn.addEventListener('click', () => {
                const customInstructions = instructionsTextarea.value.trim();
                const preview = `System Instructions (Fixed):
${defaultInstructions}

${customInstructions ? 'Additional User Instructions:\n' + customInstructions : ''}

Note: The full prompt also includes your CV data and the job description.`;
                
                previewContent.textContent = preview;
                previewSection.classList.toggle('hidden');
                previewBtn.textContent = previewSection.classList.contains('hidden') ? 'Preview Full Prompt →' : 'Hide Preview ←';
            });

            // Reset to defaults
            resetBtn.addEventListener('click', () => {
                if (confirm('Reset your custom instructions to the default values? This cannot be undone.')) {
                    instructionsTextarea.value = defaultInstructions;
                    updateCharCount();
                }
            });

            // Form submission
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';

                const formData = new FormData(form);

                try {
                    const response = await fetch('/api/save-prompt-instructions.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.href = '/cv-prompt-settings.php?success=Instructions saved successfully';
                    } else {
                        alert('Error: ' + (result.error || 'Failed to save instructions'));
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save Instructions';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Instructions';
                }
            });
        });
    </script>
</body>
</html>

