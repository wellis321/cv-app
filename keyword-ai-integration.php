<?php
/**
 * Keyword AI Integration – feature page
 * Describes how keywords are automatically integrated into CV variants using AI at no cost.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'AI Keyword Integration';
$canonicalUrl = APP_URL . '/keyword-ai-integration.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Keywords from job descriptions auto-integrate into CV variants with free Browser AI. Improve ATS compatibility—no API keys.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1456308015183-dcb539243142?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/90 via-teal-600/90 to-cyan-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">100% FREE</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    AI Keyword Integration
                </h1>
                <p class="mt-6 text-xl text-emerald-50 max-w-2xl mx-auto leading-relaxed">
                    Keywords from job descriptions are <strong class="text-white">automatically integrated into your CV variants</strong> using free Browser AI. Improve ATS compatibility at <strong class="text-white">no cost</strong>—no API keys, no limits.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-emerald-600 shadow-lg hover:bg-emerald-50 transition-colors">
                            Try it now
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-emerald-600 shadow-lg hover:bg-emerald-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- The Magic -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        How keywords improve your CV
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        When you generate a CV variant for a job application, our free Browser AI automatically analyzes the job description and naturally incorporates important keywords throughout your CV.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-xl border-2 border-red-200 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Without keyword integration</h3>
                        <div class="space-y-3 text-gray-700">
                            <p class="text-sm">❌ Generic CV that doesn't match job requirements</p>
                            <p class="text-sm">❌ ATS systems may reject your application</p>
                            <p class="text-sm">❌ Missing important skills and terms from job description</p>
                            <p class="text-sm">❌ Lower chance of passing initial screening</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-200 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">With AI keyword integration</h3>
                        <div class="space-y-3 text-gray-700">
                            <p class="text-sm">✅ CV naturally incorporates job-relevant keywords</p>
                            <p class="text-sm">✅ Better ATS compatibility and matching scores</p>
                            <p class="text-sm">✅ Highlights skills and experience that match the role</p>
                            <p class="text-sm">✅ Higher chance of passing initial screening</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How keyword integration works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Extract keywords (optional)</h3>
                            <p class="mt-3 text-gray-600">
                                You can manually extract keywords from the job description using our free Browser AI keyword extraction tool. Select which keywords to emphasise, or let the AI handle it automatically.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/job-appplications/upload-files.png" aria-label="View extract keywords image larger">
                                <img src="/static/images/job-appplications/upload-files.png" alt="Extract keywords - Key Keywords & Skills selection" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Generate AI CV variant</h3>
                            <p class="mt-3 text-gray-600">
                                Click <strong>Generate AI CV for this job</strong>. Our free Browser AI analyzes the complete job description (including uploaded files) and your master CV. It automatically identifies important keywords and naturally incorporates them throughout your CV.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/create-variants.png" aria-label="View generate AI CV variant image larger">
                                <img src="/static/images/cv-variants/create-variants.png" alt="Generate AI CV variant - CV variants list with Master CV and AI-generated variants" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Keywords integrated naturally</h3>
                            <p class="mt-3 text-gray-600">
                                The AI doesn't just add keywords randomly—it rewrites your professional summary, work experience descriptions, and skills sections to naturally incorporate relevant terms. Your CV reads naturally while matching what ATS systems are looking for.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/all-in-one/update-once.png" aria-label="View keywords integrated naturally image larger">
                                <img src="/static/images/all-in-one/update-once.png" alt="Keywords integrated naturally - CV preview with Select Sections" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why it matters -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Why keyword integration matters
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">ATS Compatibility</h3>
                        <p class="text-sm text-gray-600">Applicant Tracking Systems scan for specific keywords. By naturally incorporating job-relevant terms, your CV is more likely to pass initial screening.</p>
                    </div>

                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl border-2 border-teal-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Automatic & Free</h3>
                        <p class="text-sm text-gray-600">No manual keyword stuffing needed. Our free Browser AI handles keyword integration automatically when generating CV variants—no extra steps, no costs.</p>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-emerald-50 rounded-xl border-2 border-cyan-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Natural Integration</h3>
                        <p class="text-sm text-gray-600">Keywords are woven into your CV naturally—not just added as a list. Your CV reads professionally while matching what employers are looking for.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Free Forever -->
        <section class="py-16 bg-gradient-to-br from-emerald-50 to-teal-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="bg-white rounded-xl border-2 border-emerald-200 p-8 shadow-lg">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-white mx-auto mb-6">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">100% Free Forever</h2>
                    <p class="text-lg text-gray-700 mb-6">
                        Keyword integration is powered by free Browser AI—no API costs, no usage limits, no hidden fees. Generate as many keyword-optimized CV variants as you want, completely free.
                    </p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-medium text-emerald-800">No API keys needed</span>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-medium text-emerald-800">Unlimited use</span>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-medium text-emerald-800">Runs in your browser</span>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-medium text-emerald-800">100% private</span>
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
        <section class="py-16 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start using AI keyword integration
                </h2>
                <p class="mt-4 text-emerald-50 max-w-xl mx-auto">
                    Keyword integration is included with every account—free plan included. Generate your first keyword-optimized CV variant now.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-emerald-600 shadow-lg hover:bg-emerald-50 transition-colors">
                            Try it now
                        </a>
                        <a href="/browser-ai-free.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Learn about Browser AI
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-emerald-600 shadow-lg hover:bg-emerald-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/browser-ai-free.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Learn about Browser AI
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
