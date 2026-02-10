<?php
/**
 * Privacy Policy page
 */

require_once __DIR__ . '/php/helpers.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Simple CV Builder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow rounded-lg p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">Privacy Policy</h1>

            <p class="text-gray-600 mb-8">Last updated: <?php echo date('j F Y'); ?></p>

            <div class="prose max-w-none">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Introduction</h2>
                    <p class="text-gray-700 mb-4">
                        Simple CV Builder ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our CV building service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Information We Collect</h2>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Personal Information</h3>
                    <p class="text-gray-700 mb-4">
                        When you create an account with us, we collect information that you provide directly, including:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Name, email address, and phone number</li>
                        <li>Professional information (work experience, education, skills)</li>
                        <li>Profile photo (if you choose to upload one)</li>
                        <li>Biography and additional CV details</li>
                        <li>Job application information (job titles, companies, application dates, status, notes, uploaded documents)</li>
                        <li>Organisation membership information (if you are part of an organisation account)</li>
                        <li>AI service preferences and configuration (if you use AI features)</li>
                        <li>User-provided API keys for third-party AI services (encrypted and stored securely)</li>
                        <li>Custom CV templates and designs (if you use AI template generation)</li>
                        <li>CV variants and versions (multiple versions of your CV for different job applications)</li>
                        <li>Feedback submissions (when you submit feedback through our feedback widget, we collect your feedback message, the page URL where you submitted it, and technical information about your device and browser to help us understand and address your feedback)</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Automatically Collected Information</h3>
                    <p class="text-gray-700 mb-4">
                        When you visit our website, we automatically collect certain information about your device, including:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Device information (browser type, device type, operating system)</li>
                        <li>Usage data (pages visited, time spent on site)</li>
                        <li>IP address and general location information</li>
                        <li>Referrer information</li>
                        <li>System capabilities (CPU cores, memory, GPU information) - only when you explicitly request a system check</li>
                        <li>Technical information when submitting feedback (screen resolution, viewport size, device pixel ratio, timezone, language, browser user agent, and referrer URL) - collected automatically to help us understand and reproduce issues you report</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">How We Use Your Information</h2>
                    <p class="text-gray-700 mb-4">
                        We use the information we collect to:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Create and maintain your CV profile</li>
                        <li>Provide, operate, and improve our service</li>
                        <li>Generate PDF downloads of your CV</li>
                        <li>Share your CV via online links</li>
                        <li>Track and manage your job applications</li>
                        <li>Process CV data through AI services for rewriting, quality assessment, and template generation (when you choose to use these features)</li>
                        <li>Store and manage multiple CV variants for different job applications</li>
                        <li>Store uploaded documents (CVs, job descriptions) associated with job applications</li>
                        <li>Manage organisation accounts and member access (for organisation administrators)</li>
                        <li>Analyse usage patterns to improve our service</li>
                        <li>Send important service notifications</li>
                        <li>Respond to your inquiries and provide customer support</li>
                        <li>Provide system capability recommendations (only when you explicitly request this feature)</li>
                        <li>Process and respond to feedback submissions (we use the technical information collected with your feedback to understand and reproduce issues, improve our service, and respond to your concerns)</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Cookies and Tracking Technologies</h2>
                    <p class="text-gray-700 mb-4">
                        We use cookies and similar tracking technologies to track activity on our website and hold certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier.
                    </p>
                    <p class="text-gray-700 mb-4">
                        We use cookies for:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li><strong>Essential Cookies:</strong> Required for authentication and security</li>
                        <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website</li>
                        <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">AI Services and Third-Party Processing</h2>
                    <p class="text-gray-700 mb-4">
                        Our service offers AI-powered features for CV rewriting, quality assessment, and custom template generation. When you choose to use these features, your CV data may be processed by third-party AI services:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li><strong>Local AI (Ollama):</strong> If you configure local AI on your device, your CV data is processed entirely on your computer and never sent to external servers. We do not have access to this data during processing.</li>
                        <li><strong>Browser-Based AI:</strong> If you use browser-based AI, models run entirely in your browser. Your CV data is processed locally and never sent to external servers. Models are cached in your browser's storage.</li>
                        <li><strong>Cloud AI Services (User API Keys):</strong> If you provide your own API keys for cloud-based AI services (OpenAI, Anthropic, Google Gemini, xAI Grok, Hugging Face), your CV data will be sent to these third-party providers for processing. Your API keys are encrypted and stored securely. We do not have access to your API keys in plain text. These services have their own privacy policies and data handling practices.</li>
                        <li><strong>Site Default AI:</strong> If you use the site's default AI service, your CV data may be processed by our configured AI provider. This is subject to the provider's privacy policy.</li>
                        <li><strong>AI-Generated Content:</strong> Content generated by AI services (including CV variants, templates, and assessments) is stored in your account and treated the same as manually created content under this Privacy Policy.</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>API Key Security:</strong> When you provide API keys for third-party AI services, we encrypt them using industry-standard encryption before storing them in our database. We cannot access your API keys in plain text. You are responsible for managing your API key usage and costs with the respective providers.
                    </p>
                    <p class="text-gray-700 mb-4">
                        We recommend reviewing the privacy policies of any third-party AI services you choose to use. You can opt to use local AI or browser-based AI exclusively to keep all processing on your device.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Organisation Accounts and Data Sharing</h2>
                    <p class="text-gray-700 mb-4">
                        If you are part of an organisation account:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Organisation administrators may have access to view member CVs and job application data within the organisation</li>
                        <li>Your account information (name, email, role) is visible to organisation administrators</li>
                        <li>Organisation administrators can manage member access and permissions</li>
                        <li>You may be removed from an organisation by an administrator, which will revoke your access to organisation-specific features</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        If you are an organisation administrator, you are responsible for ensuring that member data is handled in accordance with applicable privacy laws and this Privacy Policy.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Storage and Security</h2>
                    <p class="text-gray-700 mb-4">
                        Your data is stored securely in MySQL database. We implement appropriate technical and organisational measures to protect your personal information against unauthorised access, alteration, disclosure, or destruction.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>Encryption:</strong> Sensitive data, including API keys you provide, is encrypted using industry-standard encryption (AES-256-GCM) before storage. We cannot access your encrypted API keys in plain text.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>File Storage:</strong> Uploaded documents (such as job application files) are stored securely and are only accessible to you (and organisation administrators if you are part of an organisation account).
                    </p>
                    <p class="text-gray-700 mb-4">
                        While we strive to use commercially acceptable means to protect your personal information, no method of transmission over the Internet or method of electronic storage is 100% secure.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your Rights</h2>
                    <p class="text-gray-700 mb-4">
                        Under applicable data protection laws, you have the right to:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li><strong>Access:</strong> Request copies of your personal data</li>
                        <li><strong>Rectification:</strong> Request correction of inaccurate or incomplete data</li>
                        <li><strong>Erasure:</strong> Request deletion of your personal data</li>
                        <li><strong>Restriction:</strong> Request restriction of processing of your personal data</li>
                        <li><strong>Data Portability:</strong> Request transfer of your data to another service</li>
                        <li><strong>Objection:</strong> Object to processing of your personal data</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        To exercise these rights, please contact us using the information provided in the Contact section below.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Retention</h2>
                    <p class="text-gray-700 mb-4">
                        We retain your personal information for as long as your account is active or as needed to provide you services. If you delete your account, we will delete or anonymise your personal information within 30 days, except where we are required to retain it for legal or regulatory purposes.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Children's Privacy</h2>
                    <p class="text-gray-700 mb-4">
                        Our service is not intended for individuals under the age of 16. We do not knowingly collect personal information from children under 16. If we become aware that we have collected personal information from a child under 16, we will take steps to delete such information.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">International Data Transfers</h2>
                    <p class="text-gray-700 mb-4">
                        Your information may be transferred to and maintained on computers located outside of your state, province, country, or other governmental jurisdiction where data protection laws may differ from those in your jurisdiction.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Changes to This Privacy Policy</h2>
                    <p class="text-gray-700 mb-4">
                        We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. You are advised to review this Privacy Policy periodically for any changes.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Us</h2>
                    <p class="text-gray-700 mb-4">
                        If you have any questions about this Privacy Policy, please contact us:
                    </p>
                    <p class="text-gray-700 mb-4">
                        Email: <a href="mailto:noreply@simple-job-tracker.com" class="text-blue-600 hover:text-blue-800">Contact Us</a>
                    </p>
                    <p class="text-gray-700 mb-4">
                        By using this service, you acknowledge that you have read and understood this Privacy Policy.
                    </p>
                </section>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <a href="/" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <?php partial('footer'); ?>
</body>
</html>
