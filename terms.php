<?php
/**
 * Terms of Service page
 */

require_once __DIR__ . '/php/helpers.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Simple CV Builder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow rounded-lg p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">Terms of Service</h1>

            <p class="text-gray-600 mb-8">Last updated: <?php echo date('j F Y'); ?></p>

            <div class="prose max-w-none">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Acceptance of Terms</h2>
                    <p class="text-gray-700 mb-4">
                        By accessing and using Simple CV Builder, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Use License</h2>
                    <p class="text-gray-700 mb-4">
                        Permission is granted to temporarily use Simple CV Builder for personal CV creation. This is the grant of a license, not a transfer of title, and under this license you may not:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>modify or copy the materials</li>
                        <li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial)</li>
                        <li>attempt to decompile or reverse engineer any software contained on Simple CV Builder</li>
                        <li>remove any copyright or other proprietary notations from the materials</li>
                        <li>transfer the materials to another person or "mirror" the materials on any other server</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">User Account</h2>
                    <p class="text-gray-700 mb-4">
                        When you create an account with us, you must provide information that is accurate, complete, and current at all times. You are responsible for safeguarding the password and for all activities that occur under your account.
                    </p>
                    <p class="text-gray-700 mb-4">
                        You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorised use of your account.
                    </p>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-4">Organisation Accounts</h3>
                    <p class="text-gray-700 mb-4">
                        If you create or join an organisation account:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>Organisation administrators have the authority to manage member access, view member CVs, and manage organisation settings</li>
                        <li>You are responsible for ensuring that you have proper authorisation to add members to an organisation</li>
                        <li>Organisation administrators may remove members from the organisation at any time</li>
                        <li>You agree to comply with your organisation's policies and any applicable data protection requirements</li>
                        <li>Organisation subscriptions and billing are the responsibility of the organisation administrator</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Content</h2>
                    <p class="text-gray-700 mb-4">
                        You retain all ownership rights to the content you create using Simple CV Builder. You grant us a license to use, store, and display your content solely for the purpose of providing and improving our service.
                    </p>
                    <p class="text-gray-700 mb-4">
                        You are solely responsible for the content you create and share. You agree not to upload, post, or transmit any content that:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>is illegal, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, or otherwise objectionable</li>
                        <li>infringes upon any patent, trademark, trade secret, copyright, or other proprietary rights of any party</li>
                        <li>contains software viruses or any other computer code designed to interrupt, destroy, or limit the functionality of any computer software or hardware</li>
                        <li>impersonates any person or entity, or falsely states or misrepresents your affiliation with a person or entity</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">AI Features and Generated Content</h2>
                    <p class="text-gray-700 mb-4">
                        Our service provides AI-powered features for CV rewriting, quality assessment, and custom template generation. By using these features, you acknowledge and agree that:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>AI-generated content is provided "as is" and may contain errors, inaccuracies, or inappropriate suggestions</li>
                        <li>You are solely responsible for reviewing, editing, and verifying all AI-generated content before using it</li>
                        <li>We do not guarantee the accuracy, quality, or suitability of AI-generated content for any particular purpose</li>
                        <li>When using cloud-based AI services with your own API keys (OpenAI, Anthropic, Google Gemini, xAI Grok, Hugging Face), your CV data will be processed by third-party providers subject to their terms of service. You are responsible for all costs and usage associated with your API keys.</li>
                        <li>When using local AI (Ollama) or browser-based AI, processing occurs on your device/browser and we have no access to your data during processing</li>
                        <li>You retain ownership of all content, including AI-generated content, but you are responsible for ensuring it does not infringe on third-party rights</li>
                        <li>Custom CV templates generated by AI may include HTML, CSS, and JavaScript. You are responsible for ensuring these templates do not contain malicious code or violate any laws</li>
                        <li>System capability checks are performed locally in your browser with your explicit permission. This information is not stored or transmitted to our servers</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>API Key Responsibility:</strong> If you provide API keys for third-party AI services, you are solely responsible for:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                        <li>Managing your API key usage and costs</li>
                        <li>Setting appropriate usage limits and budgets with the API provider</li>
                        <li>Keeping your API keys secure and confidential</li>
                        <li>Complying with the terms of service of the API provider</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        We are not liable for any consequences arising from the use of AI-generated content, including but not limited to job application outcomes, professional reputation, legal issues, or API costs incurred through your use of third-party services.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Job Application Tracker</h2>
                    <p class="text-gray-700 mb-4">
                        Our job application tracking feature allows you to record and manage information about job applications. By using this feature, you agree that:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li>You are responsible for the accuracy of all information you enter about job applications</li>
                        <li>We are not responsible for the accuracy of job listings or information from external sources</li>
                        <li>Job application data is stored securely but we cannot guarantee absolute security</li>
                        <li>You should not store sensitive information (such as social security numbers or bank details) in job application notes</li>
                        <li>Uploaded documents (CVs, job descriptions, etc.) are stored securely but you are responsible for ensuring you have the right to upload and store such documents</li>
                        <li>You may create multiple CV variants for different job applications. Each variant is stored separately and you are responsible for managing these variants</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Subscriptions and Payments</h2>
                    <p class="text-gray-700 mb-4">
                        Simple CV Builder offers subscription plans including free, trial, and paid options. By subscribing to a paid plan, you agree to the following:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                        <li><strong>Payment processing:</strong> Payments are processed securely by Stripe. You agree to provide accurate payment information and authorise us to charge your chosen payment method for subscription fees.</li>
                        <li><strong>Subscription plans:</strong> We offer free, weekly, monthly, and 3-month plans. All paid plans include a 7-day free trial. Plan features, limits, and pricing are described on our pricing page and may change with notice.</li>
                        <li><strong>Billing and renewal:</strong> Paid plans (weekly, monthly, 3-month) include a 7-day free trial. You will not be charged until the trial ends. After the trial, you will be billed at the start of each billing period until you cancel. Cancel before the trial ends to avoid being charged.</li>
                        <li><strong>Cancellation:</strong> You may cancel your subscription at any time through your account settings or the Stripe billing portal. Cancellation takes effect at the end of your current billing period; you retain access until that date.</li>
                        <li><strong>Refunds:</strong> We do not offer refunds for partial billing periods or for one-time payments (e.g. lifetime) except where required by law or at our sole discretion.</li>
                        <li><strong>Downgrade:</strong> When your subscription ends (by cancellation or non-payment), your account will be downgraded to the free plan. Features and limits of the free plan will apply.</li>
                        <li><strong>Price changes:</strong> We may change subscription prices with reasonable notice. Price changes will apply at your next renewal. Continued use after a price change constitutes acceptance.</li>
                        <li><strong>Organisation subscriptions:</strong> Organisation administrators are responsible for their organisation's subscription and billing. Organisation plans may have different terms, limits, and pricing.</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Disclaimers</h2>
                    <p class="text-gray-700 mb-4">
                        The materials on Simple CV Builder are provided on an 'as is' basis. Simple CV Builder makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
                    </p>
                    <p class="text-gray-700 mb-4">
                        Further, Simple CV Builder does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Limitations</h2>
                    <p class="text-gray-700 mb-4">
                        In no event shall Simple CV Builder or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Simple CV Builder, even if Simple CV Builder or a Simple CV Builder authorised representative has been notified orally or in writing of the possibility of such damage.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Accuracy of Materials</h2>
                    <p class="text-gray-700 mb-4">
                        The materials appearing on Simple CV Builder could include technical, typographical, or photographic errors. Simple CV Builder does not warrant that any of the materials on its website are accurate, complete, or current. Simple CV Builder may make changes to the materials contained on its website at any time without notice.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Links</h2>
                    <p class="text-gray-700 mb-4">
                        Simple CV Builder has not reviewed all of the sites linked to our website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by Simple CV Builder of the site. Use of any such linked website is at the user's own risk.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Modifications</h2>
                    <p class="text-gray-700 mb-4">
                        Simple CV Builder may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Termination</h2>
                    <p class="text-gray-700 mb-4">
                        We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever and without limitation, including but not limited to a breach of the Terms.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Governing Law</h2>
                    <p class="text-gray-700 mb-4">
                        These terms and conditions are governed by and construed in accordance with the laws and you irrevocably submit to the exclusive jurisdiction of the courts in that location.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Information</h2>
                    <p class="text-gray-700 mb-4">
                        If you have any questions about these Terms of Service, please contact us:
                    </p>
                    <p class="text-gray-700 mb-4">
                        Email: <a href="mailto:noreply@simple-job-tracker.com" class="text-blue-600 hover:text-blue-800">Contact Us</a>
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
