/**
 * Feedback Widget JavaScript
 * Handles feedback modal interactions and form submission
 */

(function() {
    'use strict';

    // Get elements
    const feedbackButton = document.getElementById('feedback-button');
    const feedbackModal = document.getElementById('feedback-modal');
    const feedbackForm = document.getElementById('feedback-form');
    const feedbackModalClose = document.getElementById('feedback-modal-close');
    const feedbackModalCancel = document.getElementById('feedback-modal-cancel');
    const feedbackModalBackdrop = document.getElementById('feedback-modal-backdrop');
    const feedbackMessage = document.getElementById('feedback-message');
    const feedbackSubmit = document.getElementById('feedback-submit');
    const feedbackSubmitText = document.getElementById('feedback-submit-text');
    const feedbackSubmitLoading = document.getElementById('feedback-submit-loading');
    const feedbackPageUrl = document.getElementById('feedback-page-url');
    const feedbackUserAgent = document.getElementById('feedback-user-agent');

    if (!feedbackButton || !feedbackModal || !feedbackForm) {
        return; // Widget not present on this page
    }

    // Initialize: Auto-populate hidden fields
    function initializeFeedbackForm() {
        // Set current page URL
        if (feedbackPageUrl) {
            feedbackPageUrl.value = window.location.href;
        }

        // Set user agent
        if (feedbackUserAgent) {
            feedbackUserAgent.value = navigator.userAgent;
        }
        
        // Capture additional browser/environment info
        const additionalInfo = {
            screenWidth: window.screen.width,
            screenHeight: window.screen.height,
            viewportWidth: window.innerWidth,
            viewportHeight: window.innerHeight,
            devicePixelRatio: window.devicePixelRatio || 1,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            language: navigator.language,
            platform: navigator.platform,
            referrer: document.referrer || '',
            cookieEnabled: navigator.cookieEnabled,
            onLine: navigator.onLine
        };
        
        // Add hidden field for additional info (we'll send this as JSON in a hidden field)
        let additionalInfoField = document.getElementById('feedback-additional-info');
        if (!additionalInfoField) {
            additionalInfoField = document.createElement('input');
            additionalInfoField.type = 'hidden';
            additionalInfoField.id = 'feedback-additional-info';
            additionalInfoField.name = 'additional_info';
            if (feedbackForm) {
                feedbackForm.appendChild(additionalInfoField);
            }
        }
        additionalInfoField.value = JSON.stringify(additionalInfo);
    }

    // Open modal
    function openFeedbackModal() {
        // Update page URL to current page (in case user navigated)
        if (feedbackPageUrl) {
            feedbackPageUrl.value = window.location.href;
        }
        
        // Refresh additional info when modal opens
        const additionalInfo = {
            screenWidth: window.screen.width,
            screenHeight: window.screen.height,
            viewportWidth: window.innerWidth,
            viewportHeight: window.innerHeight,
            devicePixelRatio: window.devicePixelRatio || 1,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            language: navigator.language,
            platform: navigator.platform,
            referrer: document.referrer || '',
            cookieEnabled: navigator.cookieEnabled,
            onLine: navigator.onLine
        };
        
        let additionalInfoField = document.getElementById('feedback-additional-info');
        if (!additionalInfoField) {
            additionalInfoField = document.createElement('input');
            additionalInfoField.type = 'hidden';
            additionalInfoField.id = 'feedback-additional-info';
            additionalInfoField.name = 'additional_info';
            if (feedbackForm) {
                feedbackForm.appendChild(additionalInfoField);
            }
        }
        additionalInfoField.value = JSON.stringify(additionalInfo);
        
        feedbackModal.classList.remove('hidden');
        feedbackModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        
        // Focus first input
        const firstInput = feedbackForm.querySelector('select, input, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }

    // Close modal
    function closeFeedbackModal() {
        feedbackModal.classList.add('hidden');
        feedbackModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        
        // Reset form and messages
        feedbackForm.reset();
        hideMessage();
        resetSubmitButton();
    }

    // Show message
    function showMessage(message, type) {
        if (!feedbackMessage) return;
        
        feedbackMessage.classList.remove('hidden');
        feedbackMessage.textContent = message;
        
        // Set message styling based on type
        feedbackMessage.className = 'mb-4 rounded-md border px-4 py-3 text-sm font-medium';
        if (type === 'success') {
            feedbackMessage.classList.add('border-green-200', 'bg-green-50', 'text-green-800');
        } else {
            feedbackMessage.classList.add('border-red-200', 'bg-red-50', 'text-red-800');
        }
        
        // Scroll to message
        feedbackMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Hide message
    function hideMessage() {
        if (feedbackMessage) {
            feedbackMessage.classList.add('hidden');
            feedbackMessage.textContent = '';
        }
    }

    // Set submit button loading state
    function setSubmitButtonLoading(loading) {
        if (loading) {
            feedbackSubmit.disabled = true;
            feedbackSubmitText.classList.add('hidden');
            feedbackSubmitLoading.classList.remove('hidden');
        } else {
            feedbackSubmit.disabled = false;
            feedbackSubmitText.classList.remove('hidden');
            feedbackSubmitLoading.classList.add('hidden');
        }
    }

    // Reset submit button
    function resetSubmitButton() {
        setSubmitButtonLoading(false);
    }

    // Handle form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        // Hide previous messages
        hideMessage();
        
        // Validate form
        const feedbackType = feedbackForm.querySelector('[name="feedback_type"]').value;
        const message = feedbackForm.querySelector('[name="message"]').value;
        
        if (!feedbackType) {
            showMessage('Please select a feedback type.', 'error');
            feedbackForm.querySelector('[name="feedback_type"]').focus();
            return;
        }
        
        if (!message || message.trim().length < 10) {
            showMessage('Please provide feedback with at least 10 characters.', 'error');
            feedbackForm.querySelector('[name="message"]').focus();
            return;
        }
        
        // Set loading state
        setSubmitButtonLoading(true);
        
        // Prepare form data
        const formData = new FormData(feedbackForm);
        
        // Submit via AJAX
        fetch('/api/submit-feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Thank you for your feedback!', 'success');
                
                // Reset form after delay
                setTimeout(() => {
                    feedbackForm.reset();
                    resetSubmitButton();
                    // Close modal after showing success message
                    setTimeout(() => {
                        closeFeedbackModal();
                    }, 2000);
                }, 1000);
            } else {
                showMessage(data.error || 'Failed to submit feedback. Please try again.', 'error');
                resetSubmitButton();
            }
        })
        .catch(error => {
            console.error('Feedback submission error:', error);
            showMessage('An error occurred. Please try again later.', 'error');
            resetSubmitButton();
        });
    }

    // Event listeners
    feedbackButton.addEventListener('click', openFeedbackModal);
    
    if (feedbackModalClose) {
        feedbackModalClose.addEventListener('click', closeFeedbackModal);
    }
    
    if (feedbackModalCancel) {
        feedbackModalCancel.addEventListener('click', closeFeedbackModal);
    }
    
    if (feedbackModalBackdrop) {
        feedbackModalBackdrop.addEventListener('click', closeFeedbackModal);
    }
    
    feedbackForm.addEventListener('submit', handleFormSubmit);
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !feedbackModal.classList.contains('hidden')) {
            closeFeedbackModal();
        }
    });
    
    // Prevent modal from closing when clicking inside modal content
    const modalContent = feedbackModal.querySelector('.relative');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Initialize on page load
    initializeFeedbackForm();
})();
