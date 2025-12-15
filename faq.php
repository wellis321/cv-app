<?php
/**
 * FAQ Page with FAQPage Schema
 */

require_once __DIR__ . '/php/helpers.php';

$faqs = [
    [
        'question' => 'What is Simple CV Builder?',
        'answer' => 'Simple CV Builder is an online platform that helps you create a professional CV that updates in real-time and can be shared as a simple link. You can build a comprehensive CV with sections for work experience, education, projects, skills, certifications, and more.'
    ],
    [
        'question' => 'Is Simple CV Builder free to use?',
        'answer' => 'Yes! Simple CV Builder offers a free plan that allows you to create a basic CV with essential sections. You can share your CV with a unique link and download it as a PDF. Premium plans are available for unlimited entries and additional features.'
    ],
    [
        'question' => 'How do I create my CV?',
        'answer' => 'After registering for a free account, you can start building your CV by filling in your profile information and adding sections like work experience, education, skills, and more. Your CV updates in real-time as you make changes, and you can preview it at any time.'
    ],
    [
        'question' => 'Can I share my CV online?',
        'answer' => 'Yes! Each CV gets a unique URL that you can share with employers, recruiters, or anyone else. Your CV is accessible online and updates automatically whenever you make changes. You can also control privacy settings for your CV.'
    ],
    [
        'question' => 'Can I download my CV as a PDF?',
        'answer' => 'Absolutely! You can download your CV as a PDF at any time. The PDF includes a QR code that links directly back to your online CV, making it easy for employers to access the latest version of your CV.'
    ],
    [
        'question' => 'What sections can I include in my CV?',
        'answer' => 'You can include professional summary, work experience, education, projects, skills, certifications, professional qualification equivalence, professional memberships, and interests & activities. Each section can be customized to showcase your unique background.'
    ],
    [
        'question' => 'What\'s the difference between the free and premium plans?',
        'answer' => 'The free plan allows you to create a basic CV with limited entries per section. Premium plans offer unlimited entries, priority support, and access to premium CV templates. You can upgrade or downgrade your plan at any time.'
    ],
    [
        'question' => 'How do I update my CV?',
        'answer' => 'Simply log in to your account and navigate to the section you want to update. You can add, edit, or delete entries at any time. Changes are saved automatically and reflected immediately on your public CV link.'
    ],
    [
        'question' => 'Is my data secure and private?',
        'answer' => 'Yes, we take your privacy seriously. Your data is encrypted and stored securely. You control who can see your CV through privacy settings. We never share your personal information with third parties. Read our Privacy Policy for more details.'
    ],
    [
        'question' => 'Can I customize the appearance of my CV?',
        'answer' => 'Yes! You can customize your CV header colors, choose whether to display your photo, and control various display options. Premium plans offer additional customization options and template choices.'
    ],
    [
        'question' => 'Do I need to verify my email address?',
        'answer' => 'Yes, email verification is required to ensure account security and enable important features like password reset. You\'ll receive a verification email after registration. If you don\'t receive it, you can request a new verification email.'
    ],
    [
        'question' => 'What if I forget my password?',
        'answer' => 'You can reset your password using the "Forgot Password" link on the login page. Enter your email address and you\'ll receive instructions to reset your password securely.'
    ],
    [
        'question' => 'Can I delete my account?',
        'answer' => 'Yes, you can delete your account at any time from your profile settings. This will permanently remove all your data and CV information. Please note that this action cannot be undone.'
    ],
    [
        'question' => 'How do I contact support?',
        'answer' => 'You can contact our support team by emailing support@simple-cv-builder.com. Premium plan subscribers receive priority support. We typically respond within 24-48 hours.'
    ],
    [
        'question' => 'Can I use Simple CV Builder on mobile devices?',
        'answer' => 'Yes! Simple CV Builder is fully responsive and works on smartphones, tablets, and desktop computers. You can create, edit, and view your CV from any device with an internet connection.'
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Frequently Asked Questions | Simple CV Builder',
        'metaDescription' => 'Find answers to common questions about Simple CV Builder, including how to create your CV, pricing, features, and more.',
        'canonicalUrl' => APP_URL . '/faq.php',
        'structuredDataType' => 'faq',
        'structuredData' => ['faqs' => $faqs],
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" role="main">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
                <p class="text-lg text-gray-600">
                    Everything you need to know about Simple CV Builder
                </p>
            </div>

            <div class="space-y-6">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button
                            type="button"
                            class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
                            aria-expanded="false"
                            aria-controls="faq-answer-<?php echo $index; ?>"
                            data-faq-toggle
                            data-target="faq-answer-<?php echo $index; ?>"
                        >
                            <h2 class="text-lg font-semibold text-gray-900 pr-8">
                                <?php echo e($faq['question']); ?>
                            </h2>
                            <svg
                                class="flex-shrink-0 h-5 w-5 text-gray-500 transform transition-transform duration-300 ease-in-out"
                                data-faq-icon
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            id="faq-answer-<?php echo $index; ?>"
                            class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out"
                            data-faq-content
                        >
                            <div class="px-6 pt-0 pb-6">
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo e($faq['answer']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Still have questions?</h3>
                <p class="text-gray-700 mb-4">
                    Can't find the answer you're looking for? We're here to help!
                </p>
                <a
                    href="mailto:support@simple-cv-builder.com"
                    class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Contact Support
                </a>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>

    <style>
        .faq-content {
            max-height: 0;
            opacity: 0;
            transition: max-height 0.4s ease-in-out, opacity 0.3s ease-in-out, padding-top 0.3s ease-in-out, padding-bottom 0.3s ease-in-out;
            overflow: hidden;
        }
        .faq-content.open {
            max-height: 2000px; /* Large enough for longest FAQ answer */
            opacity: 1;
            padding-top: 0.5rem;
        }
        .faq-content > div {
            transition: opacity 0.2s ease-in-out 0.1s;
        }
        .faq-content:not(.open) > div {
            opacity: 0;
        }
        .faq-content.open > div {
            opacity: 1;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faqButtons = document.querySelectorAll('[data-faq-toggle]');

            faqButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const target = document.getElementById(targetId);
                    const icon = this.querySelector('[data-faq-icon]');
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';

                    // Close all other FAQs with smooth animation
                    faqButtons.forEach(function(otherButton) {
                        if (otherButton !== button) {
                            const otherTargetId = otherButton.getAttribute('data-target');
                            const otherTarget = document.getElementById(otherTargetId);
                            const otherIcon = otherButton.querySelector('[data-faq-icon]');

                            if (otherTarget && otherTarget.classList.contains('open')) {
                                otherTarget.classList.remove('open');
                                otherButton.setAttribute('aria-expanded', 'false');
                                if (otherIcon) {
                                    otherIcon.classList.remove('rotate-180');
                                }
                            }
                        }
                    });

                    // Toggle current FAQ with smooth animation
                    if (target) {
                        if (isExpanded) {
                            target.classList.remove('open');
                            this.setAttribute('aria-expanded', 'false');
                            if (icon) {
                                icon.classList.remove('rotate-180');
                            }
                        } else {
                            target.classList.add('open');
                            this.setAttribute('aria-expanded', 'true');
                            if (icon) {
                                icon.classList.add('rotate-180');
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
