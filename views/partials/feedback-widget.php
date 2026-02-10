<?php
/**
 * Feedback Widget - Floating button and modal
 * Provides easy access to feedback form from any page
 * Only shown to logged-in users
 */

// Only show feedback widget to logged-in users
if (!isLoggedIn()) {
    return;
}
?>

<!-- Floating Feedback Button -->
<button 
    type="button"
    id="feedback-button"
    aria-label="Submit feedback"
    class="fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all hover:scale-110 md:bottom-8 md:right-8"
    title="Submit feedback">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
</button>

<!-- Feedback Modal -->
<div id="feedback-modal" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="feedback-modal-title">
    <div class="flex min-h-full items-center justify-center px-4 py-10 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" id="feedback-modal-backdrop"></div>

        <!-- Modal Content -->
        <div class="relative inline-block w-full max-w-lg transform rounded-2xl bg-white px-6 py-6 text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle sm:p-8">
            <!-- Close Button -->
            <button 
                type="button" 
                id="feedback-modal-close"
                class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-1" 
                aria-label="Close feedback form">
                <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="mb-6">
                <h3 class="text-2xl font-semibold text-gray-900" id="feedback-modal-title">Submit Feedback</h3>
                <p class="mt-1 text-sm text-gray-500">Help us improve by sharing your thoughts, reporting issues, or suggesting features.</p>
            </div>

            <!-- Message Display Area -->
            <div id="feedback-message" class="mb-4 hidden rounded-md border px-4 py-3 text-sm font-medium"></div>

            <!-- Feedback Form -->
            <form id="feedback-form" method="POST" action="/api/submit-feedback.php">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                <input type="hidden" name="page_url" id="feedback-page-url" value="">
                <input type="hidden" name="user_agent" id="feedback-user-agent" value="">
                <?php if (isLoggedIn()): ?>
                    <input type="hidden" name="user_id" value="<?php echo e(getUserId()); ?>">
                <?php endif; ?>

                <!-- Feedback Type -->
                <div class="mb-4">
                    <label for="feedback-type" class="block text-sm font-medium text-gray-700 mb-2">
                        Feedback Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="feedback-type"
                        name="feedback_type"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500">
                        <option value="">Select a type...</option>
                        <option value="bug">Bug Report</option>
                        <option value="spelling">Spelling/Grammar</option>
                        <option value="feature_request">Feature Request</option>
                        <option value="personal_issue">Personal Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>


                <!-- Message -->
                <div class="mb-6">
                    <label for="feedback-message" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Feedback <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="feedback-message"
                        name="message"
                        rows="5"
                        required
                        minlength="10"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                        placeholder="Please describe your feedback in detail..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">Minimum 10 characters</p>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button 
                        type="button"
                        id="feedback-modal-cancel"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        id="feedback-submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="feedback-submit-text">Submit Feedback</span>
                        <span id="feedback-submit-loading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
