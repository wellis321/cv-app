<?php
/**
 * CV Variants – feature page
 * Describes creating and managing multiple CV versions for different job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'CV Variants';
$canonicalUrl = APP_URL . '/cv-variants-feature.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'CV variants for different jobs. Create multiple CV versions with AI. Job-specific tailoring, UK CV builder. Keep all variants organised in one place.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-teal-600 via-cyan-600 to-blue-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1460925895917-9ada21bccfda?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-teal-600/90 via-cyan-600/90 to-blue-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">Pro Feature</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    CV Variants
                </h1>
                <p class="mt-6 text-xl text-teal-50 max-w-2xl mx-auto leading-relaxed">
                    Create multiple versions of your CV for different job types. <strong class="text-white">Generate job-specific variants with AI</strong> and keep them all organised—each variant stays linked to its application.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/cv-variants.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            View Variants
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Create Free Account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Key Features -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Multiple CV versions made easy
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Create tailored CVs for different roles, industries, or job types while maintaining one master CV online.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl border-2 border-teal-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Master CV</h3>
                        <p class="text-sm text-gray-600">Your main CV serves as the base for all variants. Update it once, and create variants from it for specific applications.</p>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border-2 border-cyan-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Linked to Jobs</h3>
                        <p class="text-sm text-gray-600">Each variant can be linked to a specific job application. Variants show a "Linked" badge and the job title for easy identification.</p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-teal-50 rounded-xl border-2 border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">AI-Generated Variants</h3>
                        <p class="text-sm text-gray-600">Generate variants automatically using AI. AI tailors your CV to match each job description, saving you time.</p>
                    </div>

                    <div class="bg-gradient-to-br from-teal-50 to-green-50 rounded-xl border-2 border-teal-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Edit Any Variant</h3>
                        <p class="text-sm text-gray-600">Edit any variant like a normal CV. Make changes specific to that variant without affecting your master CV or other variants.</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Organised Management</h3>
                        <p class="text-sm text-gray-600">View all variants in one place. Rename, delete, or create new variants easily. Never lose track of which CV goes with which application.</p>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Export & Share</h3>
                        <p class="text-sm text-gray-600">Export any variant to PDF or share its unique online link. Each variant can have its own URL and PDF export.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-20 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How CV variants work
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Build your master CV</h3>
                            <p class="mt-3 text-gray-600">
                                Start by building your main CV with all your sections: work experience, education, skills, projects, and more. This becomes your master CV that serves as the base for all variants.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/build-your-master.pdf.png" aria-label="View building master CV image larger">
                                <img src="/static/images/cv-variants/build-your-master.pdf.png" alt="Build your master CV" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Create variants for jobs</h3>
                            <p class="mt-3 text-gray-600">
                                When you save a job application, create a CV variant for it. Use "Generate AI CV for this job" for a one-click tailored variant, or "Tailor CV for this job…" to choose which sections to customise. The variant is automatically linked to the job.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/create-variants.png" aria-label="View creating CV variant for job image larger">
                                <img src="/static/images/cv-variants/create-variants.png" alt="Create variants for jobs" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Manage and use variants</h3>
                            <p class="mt-3 text-gray-600">
                                View all your variants in one place. Each variant shows if it's linked to a job application. Edit variants, rename them, export to PDF, or share their unique online links. Your master CV remains unchanged.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/manage-and-use.png" aria-label="View managing CV variants image larger">
                                <img src="/static/images/cv-variants/manage-and-use.png" alt="Manage and use variants" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why use CV variants?
                    </h2>
                </div>

                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-teal-50 border-2 border-teal-200 rounded-xl p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-teal-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Tailor for each role</h3>
                        </div>
                        <p class="text-gray-700">
                            Create CVs tailored to specific job requirements. Highlight relevant experience for tech roles, emphasise different skills for management positions, and adjust content for different industries—all while maintaining one master CV.
                        </p>
                    </div>

                    <div class="bg-cyan-50 border-2 border-cyan-200 rounded-xl p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Stay organised</h3>
                        </div>
                        <p class="text-gray-700">
                            Never lose track of which CV goes with which application. Variants linked to jobs show the job title and a "Linked" badge. Click through to the job application or view the variant—everything is connected.
                        </p>
                    </div>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">AI-powered efficiency</h3>
                        </div>
                        <p class="text-gray-700">
                            Generate variants automatically with AI. AI analyses job descriptions and tailors your CV content to match each role, saving hours of manual work. Review and refine the AI-generated content to match your voice.
                        </p>
                    </div>

                    <div class="bg-teal-50 border-2 border-teal-200 rounded-xl p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-teal-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Multiple versions</h3>
                        </div>
                        <p class="text-gray-700">
                            Create as many variants as you need. One for software development roles, one for project management, one for consulting—each tailored to its specific job type while maintaining your master CV online.
                        </p>
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
        <section class="py-16 bg-gradient-to-br from-teal-600 to-cyan-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start creating CV variants
                </h2>
                <p class="mt-4 text-teal-100 max-w-xl mx-auto">
                    CV variants are available on Pro plans. Upgrade to unlock unlimited variants and AI-powered CV generation.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Upgrade to Pro
                        </a>
                        <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All Features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Create Free Account
                        </button>
                        <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            View Pricing
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
