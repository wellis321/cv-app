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

            <p class="text-gray-600 mb-8">Last updated: 5 November 2025</p>

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
                        <li>Analyze usage patterns to improve our service</li>
                        <li>Send important service notifications</li>
                        <li>Respond to your inquiries and provide customer support</li>
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
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Storage and Security</h2>
                    <p class="text-gray-700 mb-4">
                        Your data is stored securely in MySQL database. We implement appropriate technical and organisational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
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
                        We retain your personal information for as long as your account is active or as needed to provide you services. If you delete your account, we will delete or anonymize your personal information within 30 days, except where we are required to retain it for legal or regulatory purposes.
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
