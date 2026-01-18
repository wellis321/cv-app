<?php
/**
 * Job Application Tracker Features
 * Documentation and features page for the job application tracking system
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Job Application Tracker';
$canonicalUrl = APP_URL . '/job-applications-features.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Track and manage all your job applications in one place. Built-in job application tracker with status tracking, follow-up reminders, and progress monitoring.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero Section -->
        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                        Job Application Tracker
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 leading-relaxed">
                        Track and manage all your job applications in one place. Never lose track of where you've applied, stay on top of follow-ups, and land your next role. Built right into Simple CV Builder with powerful AI integration and file upload capabilities.
                    </p>
                    <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <?php if (isLoggedIn()): ?>
                            <a href="/job-applications.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                Open Job Applications
                            </a>
                        <?php else: ?>
                            <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                Create Free Account
                            </button>
                        <?php endif; ?>
                        <a href="#features" class="inline-flex items-center justify-center rounded-lg border-2 border-gray-300 bg-white px-8 py-3 text-base font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Features Section -->
        <section id="features" class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Core Features
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Everything you need to manage your job search effectively, all in one place.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Track All Applications</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Keep track of every job application in one centralised dashboard. Never lose track of where you've applied or what stage each application is at.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Status Tracking</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Monitor your progress from initial interest through to offer. Track statuses like Applied, Interviewing, Offered, Accepted, or Rejected.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Follow-Up Reminders</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Set follow-up reminders so you never miss an opportunity to check in with employers or schedule your next interview.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Interview Tracking</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Track interview stages and outcomes. Mark whether you've had interviews, note interview dates, and record your interview performance.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Application Notes</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Add detailed notes to each application. Record important details about the role, company culture, interview feedback, and next steps.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Quick Search & Filter</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Quickly find applications by company name, job title, or status. Filter your applications to focus on active opportunities or review your history.
                        </p>
                    </div>

                    <!-- Feature 7: File Uploads -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">File Uploads & AI Integration</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Upload job description files (PDF, Word, Excel) directly to each application. The AI automatically reads these files when generating CV variants, eliminating the need to copy and paste job descriptions.
                        </p>
                    </div>

                    <!-- Feature 8: Text Extraction -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Smart Text Extraction</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Extract text from uploaded files with one click. Automatically populate the job description field from PDFs, Word documents, and other file formats to save time.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why Use Job Application Tracker?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Stay organised and never miss an opportunity with our comprehensive job application management system.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Never Miss a Follow-Up</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Set reminders for important follow-ups and stay on top of your job search.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Track Your Progress</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Visualise your job search progress with statistics and insights into your applications.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">All in One Place</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Manage your CV and job applications together in a single platform for maximum efficiency.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Free with Every Account</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Job application tracking is included with every Simple CV Builder account at no extra cost.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Getting Started Section -->
        <section class="bg-gradient-to-br from-blue-50 to-indigo-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-2xl bg-white border border-blue-200 p-8 md:p-12 shadow-xl">
                    <div class="max-w-3xl mx-auto text-center">
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            Ready to Track Your Job Applications?
                        </h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Start tracking your job applications today. It's included with every Simple CV Builder account - no additional cost required.
                        </p>
                        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                            <?php if (isLoggedIn()): ?>
                                <a href="/job-applications.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                    Open Job Applications
                                </a>
                            <?php else: ?>
                                <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                    Create Free Account
                                </button>
                                <a href="/#pricing" class="inline-flex items-center justify-center rounded-lg border-2 border-blue-600 px-8 py-3 text-base font-semibold text-blue-600 hover:bg-blue-50 transition-colors">
                                    View Pricing
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>
</body>
</html>

