<?php
/**
 * Cover Letters – feature page
 * Describes creating and managing cover letters linked to job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'AI Cover Letter Generator | Free Cover Letter Builder UK';
$canonicalUrl = APP_URL . '/cover-letters-feature.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'AI cover letter generator UK. Create and manage tailored cover letters for each job application. Generate professional cover letters with AI and keep them linked to the right job.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-rose-600 via-pink-600 to-fuchsia-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1450101491212-3f7e0d4dff11?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-rose-600/90 via-pink-600/90 to-fuchsia-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">AI-powered</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    AI Cover Letter Generator - Create Tailored Cover Letters
                </h1>
                <p class="mt-6 text-xl text-pink-50 max-w-2xl mx-auto leading-relaxed">
                    AI cover letter generator UK. Create tailored cover letters for each job application. <strong class="text-white">Generate with AI</strong> or write your own, and keep them linked to the right job so you always know which letter goes with which application.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/cover-letters.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-rose-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Open cover letters
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-rose-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Everything you need for cover letters
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-xl border-2 border-rose-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-rose-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">AI Generation</h3>
                        <p class="text-sm text-gray-600">Generate a tailored cover letter with AI based on the job description and your CV. Edit and refine it to match your voice.</p>
                    </div>

                    <div class="bg-gradient-to-br from-pink-50 to-fuchsia-50 rounded-xl border-2 border-pink-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-pink-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Linked to Jobs</h3>
                        <p class="text-sm text-gray-600">Each cover letter is linked to a specific job application. Never lose track of which letter goes with which role.</p>
                    </div>

                    <div class="bg-gradient-to-br from-fuchsia-50 to-rose-50 rounded-xl border-2 border-fuchsia-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-fuchsia-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Management</h3>
                        <p class="text-sm text-gray-600">View, edit, and manage all your cover letters in one place. Export to PDF or copy text for email applications.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How cover letters work
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-rose-100 text-rose-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Create from a job application</h3>
                            <p class="mt-3 text-gray-600">
                                Open any job application and click <strong>Create Cover Letter</strong>. The system uses the job description and your CV to generate a tailored letter.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cover-letters-features/use-and-manage.png" aria-label="View Create from a job application image larger">
                                <img src="/static/images/cover-letters-features/use-and-manage.png" alt="AI cover letter generator – create from job application, Generate Cover Letter with AI" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-rose-100 text-rose-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Review and edit</h3>
                            <p class="mt-3 text-gray-600">
                                Review the AI-generated cover letter. Edit it to match your voice, add specific examples, or rewrite sections. The letter stays linked to the job application.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cover-letters-features/review-and-edit.png" aria-label="View Review and edit cover letter image larger">
                                <img src="/static/images/cover-letters-features/review-and-edit.png" alt="Review and edit - Cover Letter with Edit, Regenerate, Export PDF and Delete actions" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-rose-100 text-rose-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Use and manage</h3>
                            <p class="mt-3 text-gray-600">
                                Once your cover letter is ready, use it however you need: <strong>copy the text</strong> to paste into email applications or web forms, <strong>export to PDF</strong> to attach to applications, or <strong>view it in the job view</strong> whenever you open that application. Every cover letter stays linked to its job so you always know which letter goes with which role.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cover-letters-features/create-from-a-job.png" aria-label="View Use and manage cover letters image larger">
                                <img src="/static/images/cover-letters-features/create-from-a-job.png" alt="Use and manage - tailored cover letter for GreenTech Solutions" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Why use cover letters?
                </h2>
                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-white rounded-xl border-2 border-rose-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-rose-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Save time</h3>
                        </div>
                        <p class="text-gray-600">Let AI generate a tailored cover letter based on the job description. Edit and refine instead of starting from scratch.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-pink-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-pink-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Stay organised</h3>
                        </div>
                        <p class="text-gray-600">Each cover letter is linked to its job application. Never lose track of which letter goes with which role or forget to send one.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-fuchsia-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-fuchsia-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Better applications</h3>
                        </div>
                        <p class="text-gray-600">Tailored cover letters that reference the job description show employers you've read the posting and understand the role.</p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-rose-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-rose-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Professional presentation</h3>
                        </div>
                        <p class="text-gray-600">Export to PDF or copy formatted text. Present yourself professionally with well-written, tailored cover letters.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Explore All Features -->
        <section class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Explore All Features
                </h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    This is just one of many features we offer. Discover everything Simple CV Builder can do for your job search and career development.
                </p>
                <div class="mt-8">
                    <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        View All Features
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-rose-600 via-pink-600 to-fuchsia-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start creating cover letters
                </h2>
                <p class="mt-4 text-pink-50 max-w-xl mx-auto">
                    Cover letters are included with every account. Generate with AI or write your own, and keep them linked to your job applications.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/cover-letters.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-rose-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Open cover letters
                        </a>
                        <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-rose-600 shadow-lg hover:bg-pink-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Explore features
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>
    <?php partial('image-lightbox'); ?>
</body>
</html>
