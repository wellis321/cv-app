<?php
/**
 * Generate CV from Job Modal
 * Modal for selecting a job application and generating a CV variant
 */

if (!function_exists('getUserJobApplications')) {
    require_once __DIR__ . '/../../../php/job-applications.php';
}
if (!function_exists('getUserCvVariants')) {
    require_once __DIR__ . '/../../../php/cv-variants.php';
}

$userId = getUserId();
$jobApplications = getUserJobApplications($userId);
$cvVariants = getUserCvVariants($userId);
?>
<div id="generate-cv-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Generate CV from Job</h3>
                    <p class="mt-1 text-sm text-gray-500">Create a job-specific CV variant using AI</p>
                </div>
                <button id="close-generate-cv-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="generate-cv-form" class="space-y-6">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo generateCsrfToken(); ?>">

                <!-- Source CV Selection -->
                <div>
                    <label for="generate-cv-source-variant" class="block text-sm font-medium text-gray-700 mb-2">
                        Source CV
                    </label>
                    <select id="generate-cv-source-variant" name="cv_variant_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Master CV</option>
                        <?php foreach ($cvVariants as $variant): ?>
                            <?php if (!$variant['is_master']): ?>
                                <option value="<?php echo e($variant['id']); ?>">
                                    <?php echo e($variant['variant_name']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Select which CV to use as the base for rewriting</p>
                </div>

                <!-- Job Application Selection -->
                <div>
                    <label for="generate-cv-job-application" class="block text-sm font-medium text-gray-700 mb-2">
                        Job Application
                    </label>
                    <select id="generate-cv-job-application" name="job_application_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a job application...</option>
                        <?php foreach ($jobApplications as $jobApp): ?>
                            <option value="<?php echo e($jobApp['id']); ?>">
                                <?php echo e($jobApp['company_name']); ?> - <?php echo e($jobApp['job_title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Select the job application to tailor your CV for</p>
                </div>

                <!-- Variant Name -->
                <div>
                    <label for="generate-cv-variant-name" class="block text-sm font-medium text-gray-700 mb-2">
                        CV Name (Optional)
                    </label>
                    <input type="text" 
                           id="generate-cv-variant-name" 
                           name="variant_name" 
                           placeholder="AI-Generated CV"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate a name</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="cancel-generate-cv" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        Generate CV
                    </button>
                </div>
            </form>

            <!-- Loading State -->
            <div id="generate-cv-loading" class="hidden mt-6 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Generating your CV... This may take a minute.</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('generate-cv-modal');
    const form = document.getElementById('generate-cv-form');
    const closeBtn = document.getElementById('close-generate-cv-modal');
    const cancelBtn = document.getElementById('cancel-generate-cv');
    const loadingDiv = document.getElementById('generate-cv-loading');
    
    // Open modal
    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Close modal
    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        form.reset();
        loadingDiv.classList.add('hidden');
        form.classList.remove('hidden');
    }
    
    // Event listeners
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Form submission
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const jobApplicationId = formData.get('job_application_id');
            
            if (!jobApplicationId) {
                if (typeof window.showNotificationModal === 'function') {
                    window.showNotificationModal({ type: 'info', title: 'Select job', message: 'Please select a job application' });
                } else {
                    alert('Please select a job application');
                }
                return;
            }
            
            // Show loading state
            form.classList.add('hidden');
            loadingDiv.classList.remove('hidden');
            
            try {
                const response = await fetch('/api/ai-rewrite-cv.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });
                
                const result = await response.json();
                
                if (result.success && result.variant_id) {
                    // Redirect to view the new variant
                    window.location.href = '/cv.php?variant_id=' + result.variant_id;
                } else {
                    throw new Error(result.error || 'Failed to generate CV');
                }
            } catch (error) {
                if (typeof window.showNotificationModal === 'function') {
                    window.showNotificationModal({ type: 'error', title: 'Error', message: 'Error generating CV: ' + error.message });
                } else {
                    alert('Error generating CV: ' + error.message);
                }
                loadingDiv.classList.add('hidden');
                form.classList.remove('hidden');
            }
        });
    }
    
    // Make openModal available globally
    window.openGenerateCvModal = openModal;
    
    // Attach to button if it exists
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('generate-cv-from-job-btn');
        if (btn) {
            btn.addEventListener('click', openModal);
        }
    });
})();
</script>
