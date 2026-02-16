<?php
/**
 * CV Quality Assessment – feature page
 * Describes AI-powered CV quality assessment with scores and recommendations.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'CV Quality Assessment';
$canonicalUrl = APP_URL . '/cv-quality-assessment-feature.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Get comprehensive AI-powered feedback on your CV with scores and actionable recommendations. Assess overall quality, ATS compatibility, content quality, and more.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/90 via-teal-600/90 to-cyan-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">AI-powered</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    CV Quality Assessment
                </h1>
                <p class="mt-6 text-xl text-teal-50 max-w-2xl mx-auto leading-relaxed">
                    Get comprehensive AI-powered feedback on your CV with scores and actionable recommendations. <strong class="text-white">Identify strengths and weaknesses</strong> to improve your CV's effectiveness.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#cv-variants" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Assess Your CV
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Create Free Account
                        </button>
                    <?php endif; ?>
                    <a href="#what-it-assesses" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        What it assesses
                    </a>
                </div>
            </div>
        </section>

        <!-- What It Assesses -->
        <section id="what-it-assesses" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Comprehensive CV analysis
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Get detailed scores and feedback across five key areas that determine CV effectiveness.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Overall Quality</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Assesses general CV completeness, professionalism, and clarity. Looks at how well your CV presents your professional profile overall.
                        </p>
                        <p class="text-sm text-gray-600">
                            Score: 0-100 based on completeness, structure, and professional presentation.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl border-2 border-teal-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-teal-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">ATS Compatibility</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Evaluates how well your CV will pass Applicant Tracking Systems. Focuses on keyword usage, content structure, and how well content can be parsed.
                        </p>
                        <p class="text-sm text-gray-600">
                            Score: 0-100 based on keyword optimization and parseable content structure.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border-2 border-cyan-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Content Quality</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Analyzes relevance, impact, and specificity of your content. Looks for quantifiable achievements and strong descriptions.
                        </p>
                        <p class="text-sm text-gray-600">
                            Score: 0-100 based on relevance, impact, and use of quantifiable achievements.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Content Consistency</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Checks date formatting consistency, description completeness, and identifies missing information. Focuses on user-controllable aspects.
                        </p>
                        <p class="text-sm text-gray-600">
                            Score: 0-100 based on formatting consistency and completeness.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Keyword Matching</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            When a job description is provided, evaluates how well your CV aligns with job requirements. Analyzes keyword usage and relevance.
                        </p>
                        <p class="text-sm text-gray-600">
                            Score: 0-100 based on alignment with job requirements (only shown when job description provided).
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feedback Types -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Detailed feedback you can act on
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Get more than just scores—receive specific, actionable feedback to improve your CV.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-white rounded-xl border-2 border-emerald-200 p-8 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Strengths</h3>
                        <p class="text-gray-600">
                            Identify what you're doing well. The AI highlights strong sections, effective descriptions, and areas where your CV excels.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-orange-200 p-8 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Weaknesses</h3>
                        <p class="text-gray-600">
                            Discover areas that need improvement. Get specific feedback on missing information, weak descriptions, or formatting issues.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-blue-200 p-8 shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Recommendations</h3>
                        <p class="text-gray-600">
                            Receive specific, actionable steps to improve your CV. Each recommendation tells you exactly what to change and why.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-20 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How to assess your CV
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Select a CV variant</h3>
                            <p class="mt-3 text-gray-600">
                                Choose which CV variant you want to assess—your master CV or any job-specific variant. You can assess any CV variant at any time.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/create-variants.png" aria-label="View select CV variant image larger">
                                <img src="/static/images/cv-variants/create-variants.png" alt="Select CV variant" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Run assessment</h3>
                            <p class="mt-3 text-gray-600">
                                Click "Assess" or "Run Assessment" on your chosen CV variant. Optionally provide a job description for keyword matching analysis. Processing typically takes 20-40 seconds.
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                All processing happens in your browser using Browser AI—no cloud services needed.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-quality/run-assessment.png" aria-label="View run CV assessment image larger">
                                <img src="/static/images/cv-quality/run-assessment.png" alt="Run CV assessment" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Review results</h3>
                            <p class="mt-3 text-gray-600">
                                Check scores for each category, read strengths and weaknesses, and follow recommendations to improve. Each assessment provides detailed feedback you can act on.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-quality/review-results.png" aria-label="View review assessment results image larger">
                                <img src="/static/images/cv-quality/review-results.png" alt="Review assessment results" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-20 bg-gradient-to-br from-emerald-50 to-teal-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why assess your CV quality?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Get objective feedback to improve your CV's effectiveness and increase your chances of getting noticed.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-white rounded-xl border-2 border-emerald-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Improve ATS compatibility</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Many employers use Applicant Tracking Systems to filter CVs before human review. Get feedback on keyword usage and content structure to improve your chances of passing these systems.
                        </p>
                        <p class="text-gray-700">
                            The assessment focuses on user-controllable aspects like keyword optimization and content structure, not template formatting.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-teal-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-teal-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Track your progress</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Run assessments before and after making improvements to see how your scores change. Track your CV's quality over time and identify trends.
                        </p>
                        <p class="text-gray-700">
                            Each assessment is saved, so you can compare results and see which changes had the biggest impact.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-cyan-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Actionable recommendations</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Get specific, actionable feedback—not just scores. Each recommendation tells you exactly what to change, why it matters, and how to improve.
                        </p>
                        <p class="text-gray-700">
                            Recommendations cover everything from adding quantifiable achievements to improving keyword usage and fixing formatting inconsistencies.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl border-2 border-blue-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">100% private</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            All AI processing happens in your browser using free Browser AI technology. Your CV data never leaves your device—complete privacy and security.
                        </p>
                        <p class="text-gray-700">
                            No cloud services, no API keys, no data sharing. Everything runs locally in your browser, so your information stays private.
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
        <section class="py-16 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start assessing your CV quality
                </h2>
                <p class="mt-4 text-teal-100 max-w-xl mx-auto">
                    CV quality assessment is available on Pro plans. Get comprehensive feedback on your CV and improve your chances of getting noticed.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#cv-variants" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Assess Your CV
                        </a>
                        <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Upgrade to Pro
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
