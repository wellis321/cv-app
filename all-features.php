<?php
/**
 * All Features - Comprehensive Feature Listing
 * A complete overview of all features offered by Simple CV Builder
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'All Features';
$canonicalUrl = APP_URL . '/all-features.php';

// Define all features organized by category
$features = [
    'Platform & Value' => [
        [
            'name' => 'All in One Place',
            'description' => 'Manage your CV and job applications together in a single integrated platform. Build, track, and tailor—all seamlessly connected',
            'free' => true,
            'pro' => true,
            'link' => '/all-in-one-place.php'
        ],
        [
            'name' => 'Free with Account',
            'description' => 'Job application tracking is included with every account at no extra cost. Track applications, generate CV variants, and manage your job search—all included',
            'free' => true,
            'pro' => true,
            'link' => '/free-with-account.php'
        ],
    ],
    'CV Building' => [
        [
            'name' => 'CV Building',
            'description' => 'Build your professional CV with all sections: personal profile, work experience, education, projects, skills, certifications, memberships, and more',
            'free' => true,
            'pro' => true,
            'link' => '/cv-building-feature.php'
        ],
        [
            'name' => 'CV Templates',
            'description' => 'Choose from professional templates (Minimal template free, premium templates for Pro)',
            'free' => true,
            'pro' => true,
            'link' => '/cv-templates-feature.php',
            'note' => 'Free: Minimal template | Pro: All templates'
        ],
        [
            'name' => 'Template Customisation',
            'description' => 'Customise colours, fonts, and layout to match your brand',
            'free' => false,
            'pro' => true,
            'link' => '/template-customisation-feature.php'
        ],
        [
            'name' => 'CV Variants',
            'description' => 'Create multiple versions of your CV for different job types',
            'free' => false,
            'pro' => true,
            'link' => '/cv-variants-feature.php'
        ],
    ],
    'Online CV & Sharing' => [
        [
            'name' => 'Unique Online CV Link',
            'description' => 'Share your CV at /cv/@username - always up to date',
            'free' => true,
            'pro' => true,
            'link' => '/online-cv-username.php'
        ],
        [
            'name' => 'PDF Export',
            'description' => 'Export your CV as a professional PDF document with selective sections and optional QR codes',
            'free' => false,
            'pro' => true,
            'link' => '/pdf-export-feature.php'
        ],
    ],
    'Job Application Tracking' => [
        [
            'name' => 'Job Applications Overview',
            'description' => 'Comprehensive overview of job application tracking: save jobs, track status, follow up, and manage your applications in one place',
            'free' => true,
            'pro' => true,
            'link' => '/job-applications-features.php'
        ],
        [
            'name' => 'Save Jobs from Anywhere',
            'description' => 'Save job listings from any website using browser extension or quick-add link',
            'free' => true,
            'pro' => true,
            'link' => '/job-applications-features.php'
        ],
        [
            'name' => 'Browser Extension',
            'description' => 'One-click job saving from any job board (Chrome extension)',
            'free' => true,
            'pro' => true,
            'link' => '/job-applications-features.php'
        ],
        [
            'name' => 'Track All Applications',
            'description' => 'Centralized dashboard to manage all your job applications',
            'free' => true,
            'pro' => true,
            'link' => '/track-all-applications.php'
        ],
        [
            'name' => 'Status Tracking',
            'description' => 'Track application status: Interested, Applied, Interviewing, Offered, etc.',
            'free' => true,
            'pro' => true,
            'link' => '/status-tracking.php'
        ],
        [
            'name' => 'Follow-Up Dates',
            'description' => 'Set follow-up and closing dates for each application',
            'free' => true,
            'pro' => true,
            'link' => '/follow-up-dates.php'
        ],
        [
            'name' => 'Browser Notifications',
            'description' => 'Get automatic browser notifications for follow-up dates (customisable)',
            'free' => true,
            'pro' => true,
            'link' => '/never-miss-follow-up.php'
        ],
        [
            'name' => 'Interview Tracking',
            'description' => 'Track interview stages and record feedback',
            'free' => true,
            'pro' => true,
            'link' => '/interview-tracking.php'
        ],
        [
            'name' => 'Application Notes',
            'description' => 'Add notes, research, and reminders for each application',
            'free' => true,
            'pro' => true,
            'link' => '/application-notes.php'
        ],
        [
            'name' => 'Search & Filter',
            'description' => 'Search and filter applications by status, company, date, and more',
            'free' => true,
            'pro' => true,
            'link' => '/search-filter.php'
        ],
        [
            'name' => 'Progress Tracking',
            'description' => 'Visual dashboard showing your application progress and statistics',
            'free' => true,
            'pro' => true,
            'link' => '/track-progress.php'
        ],
    ],
    'Support & Feedback' => [
        [
            'name' => 'Feedback & Support',
            'description' => 'Submit feedback, report bugs, suggest features, or get help',
            'free' => true,
            'pro' => true,
            'link' => '/feedback-feature.php'
        ],
    ],
    'AI Features' => [
        [
            'name' => 'Browser AI (Free)',
            'description' => 'All AI features run in your browser - no API keys, completely free and private',
            'free' => true,
            'pro' => true,
            'link' => '/browser-ai-free.php'
        ],
        [
            'name' => 'AI CV Generation',
            'description' => 'Generate job-specific CV variants tailored to each application',
            'free' => false,
            'pro' => true,
            'link' => '/ai-cv-generation-feature.php'
        ],
        [
            'name' => 'AI Cover Letter Generation',
            'description' => 'Generate tailored cover letters for each job application',
            'free' => false,
            'pro' => true,
            'link' => '/cover-letters-feature.php'
        ],
        [
            'name' => 'AI Application Questions',
            'description' => 'Get AI-powered help answering application form questions',
            'free' => false,
            'pro' => true,
            'link' => '/application-questions-feature.php'
        ],
        [
            'name' => 'Keyword Extraction',
            'description' => 'Extract keywords from job descriptions using AI',
            'free' => false,
            'pro' => true,
            'link' => '/keyword-extraction.php'
        ],
        [
            'name' => 'AI Keyword Integration',
            'description' => 'Automatically integrate keywords into CV variants for better ATS compatibility',
            'free' => false,
            'pro' => true,
            'link' => '/keyword-ai-integration.php'
        ],
        [
            'name' => 'CV Variant Linking',
            'description' => 'Generate job-specific CV variants linked to job applications',
            'free' => false,
            'pro' => true,
            'link' => '/cv-variant-linking.php'
        ],
        [
            'name' => 'CV Quality Assessment',
            'description' => 'Get AI-powered feedback on your CV with scores and recommendations',
            'free' => false,
            'pro' => true,
            'link' => '/cv-quality-assessment-feature.php'
        ],
        [
            'name' => 'Tailor CV Content',
            'description' => 'AI-powered CV tailoring for specific job applications',
            'free' => false,
            'pro' => true,
            'link' => '/tailor-cv-content.php'
        ],
    ],
    'File Management' => [
        [
            'name' => 'File Uploads',
            'description' => 'Upload PDFs, Word docs, Excel files, images, and text files',
            'free' => true,
            'pro' => true,
            'link' => '/file-uploads-ai.php'
        ],
        [
            'name' => 'Smart Text Extraction',
            'description' => 'AI automatically extracts text from uploaded files',
            'free' => true,
            'pro' => true,
            'link' => '/smart-text-extraction.php'
        ],
    ],
    'Organisation & Collaboration' => [
        [
            'name' => 'Candidate Management',
            'description' => 'Invite, manage, and track multiple candidates. Assign recruiters, view CVs, and monitor candidate progress',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Team Management',
            'description' => 'Invite and manage team members with role-based access (Owner, Admin, Recruiter, Viewer). Control permissions and access levels',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Agency Dashboard',
            'description' => 'Centralised dashboard with statistics, usage metrics, recent activity, and quick actions for managing your organisation',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Custom Homepage',
            'description' => 'Create a fully customised public homepage for your organisation with custom HTML, CSS, and JavaScript',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Role-Based Access Control',
            'description' => 'Four-tier permission system: Owner (full access), Admin (manage team & candidates), Recruiter (assigned candidates), Viewer (read-only)',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Activity Log',
            'description' => 'Complete audit trail of all organisation activities including candidate actions, team changes, and settings updates',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Security Logs',
            'description' => 'Monitor security events, login attempts, and suspicious activity across your organisation for enhanced security',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Usage & Statistics',
            'description' => 'Track candidate and team member usage against plan limits. View usage percentages and request limit increases when needed',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Seat Management',
            'description' => 'Request increases to candidate and team member limits. Tailor your plan to match your organisation\'s needs',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Organisation AI Settings',
            'description' => 'Configure AI services at organisation level. All candidates automatically get browser-based AI (free), with optional cloud AI configuration',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
        [
            'name' => 'Agency Branding',
            'description' => 'Customise organisation colours, branding, and public-facing appearance. White-label your organisation\'s presence',
            'free' => false,
            'pro' => false,
            'agency' => true,
            'link' => '/organisations.php'
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'All features of Simple CV Builder: free CV builder UK, job application tracker, AI cover letters, CV templates, PDF export. Complete feature overview.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
    <style>
        .feature-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .feature-table thead th {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #1f2937;
            color: white;
            border-bottom: 3px solid #111827;
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: left;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .feature-table thead th:first-child {
            border-top-left-radius: 0.75rem;
        }
        .feature-table thead th:last-child {
            border-top-right-radius: 0.75rem;
        }
        .feature-table thead th.text-center {
            text-align: center;
        }
        .feature-table tbody tr {
            transition: background-color 0.15s ease;
        }
        .feature-table tbody tr:nth-child(even) {
            background-color: #f3f4f6;
        }
        .feature-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        .feature-table tbody tr.row-link {
            cursor: pointer;
            position: relative;
        }
        .feature-table tbody tr.row-link:hover {
            background-color: #dbeafe !important;
            box-shadow: inset 4px 0 0 #2563eb;
        }
        .feature-table tbody tr.row-link:hover td:first-child a {
            color: #2563eb;
            text-decoration: underline;
        }
        .feature-table tbody tr:not(:last-child) {
            border-bottom: 1px solid #e5e7eb;
        }
        .feature-table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: top;
        }
        .feature-table tbody td:first-child {
            font-weight: 600;
            color: #111827;
        }
        .feature-table tbody td:nth-child(2) {
            font-size: 0.9375rem;
            line-height: 1.6;
            color: #4b5563;
            font-weight: 400;
        }
        .check-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #10b981;
        }
        .category-header {
            background: #e5e7eb;
            font-weight: 700;
            font-size: 1.125rem;
            color: #111827;
            border-top: 3px solid #9ca3af;
            border-bottom: 3px solid #9ca3af;
        }
        .category-header td {
            padding: 0.875rem 1.5rem !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 text-white">
            <div class="absolute inset-0 bg-gray-900/20"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        All Features
                    </h1>
                    <p class="mt-6 text-xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                        A comprehensive overview of everything Simple CV Builder offers. From CV creation to job application tracking, AI-powered tools, and organisation features.
                    </p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="/subscription.php" class="inline-flex items-center rounded-lg bg-white px-6 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            View Pricing
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center rounded-lg border-2 border-white/80 bg-white/10 px-6 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- Features Table -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Notice about clickable rows -->
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Tip:</strong> Click on any feature row to learn more about that feature and see detailed information.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto shadow-lg rounded-xl border border-gray-200" style="max-height: 80vh; overflow-y: auto;">
                    <table class="feature-table">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th>Description</th>
                                <th class="text-center w-24">Free</th>
                                <th class="text-center w-24">Pro</th>
                                <th class="text-center w-24">Agency</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($features as $category => $categoryFeatures): ?>
                                <tr class="category-header">
                                    <td colspan="5">
                                        <?php echo e($category); ?>
                                    </td>
                                </tr>
                                <?php foreach ($categoryFeatures as $feature): ?>
                                    <tr class="<?php echo !empty($feature['link']) ? 'row-link' : ''; ?>" <?php echo !empty($feature['link']) ? 'data-href="' . e($feature['link']) . '"' : ''; ?>>
                                        <td>
                                            <div class="flex items-center group">
                                                <?php if (!empty($feature['link'])): ?>
                                                    <a href="<?php echo e($feature['link']); ?>" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                                                        <?php echo e($feature['name']); ?>
                                                        <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="font-semibold"><?php echo e($feature['name']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($feature['note'])): ?>
                                                <div class="mt-1.5 text-xs text-gray-500 font-normal">
                                                    <?php echo e($feature['note']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e($feature['description']); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($feature['free'])): ?>
                                                <svg class="check-icon mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            <?php else: ?>
                                                <span class="text-gray-300">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($feature['pro'])): ?>
                                                <svg class="check-icon mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            <?php else: ?>
                                                <span class="text-gray-300">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($feature['agency'])): ?>
                                                <svg class="check-icon mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            <?php else: ?>
                                                <span class="text-gray-300">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Additional Info -->
                <div class="mt-12 grid md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Free Plan</h3>
                        <p class="text-sm text-blue-800">
                            Perfect for getting started. Build your CV, track a few applications, and use basic features. Upgrade anytime to unlock more.
                        </p>
                    </div>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-indigo-900 mb-2">Pro Plan</h3>
                        <p class="text-sm text-indigo-800">
                            Full access to all features including AI-powered CV generation, unlimited applications, PDF exports, and premium templates.
                        </p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Agency Plan</h3>
                        <p class="text-sm text-purple-800">
                            For recruitment agencies and teams. Manage multiple candidates, collaborate with team members, and customise branding.
                        </p>
                    </div>
                </div>

                <!-- CTA -->
                <div class="mt-12 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to get started?</h2>
                    <p class="text-gray-600 mb-6">
                        Choose the plan that's right for you and start building your professional CV today.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <?php if (isLoggedIn()): ?>
                            <a href="/dashboard.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                Go to Dashboard
                            </a>
                        <?php else: ?>
                            <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                Create Free Account
                            </button>
                        <?php endif; ?>
                        <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg border-2 border-blue-600 bg-white px-8 py-3 text-base font-semibold text-blue-600 hover:bg-blue-50 transition-colors">
                            View Pricing Plans
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>
    <script>
        // Make entire row clickable
        document.querySelectorAll('.row-link').forEach(function(row) {
            row.addEventListener('click', function(e) {
                // Don't navigate if clicking on a link directly
                if (e.target.tagName === 'A' || e.target.closest('a')) {
                    return;
                }
                var href = this.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            });
        });
    </script>
</body>
</html>
