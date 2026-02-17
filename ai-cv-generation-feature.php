<?php
/**
 * AI CV Generation – feature page
 * Describes AI-powered CV variant generation for job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'AI CV Builder UK | AI-Powered CV Generator';
$canonicalUrl = APP_URL . '/ai-cv-generation-feature.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'AI CV builder UK. Generate job-specific CV variants automatically. Tailor your CV for each application with AI analysis.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 via-indigo-600/90 to-purple-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">AI-powered</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    AI-Powered CV Builder - Generate Job-Specific CVs
                </h1>
                <p class="mt-6 text-xl text-indigo-50 max-w-2xl mx-auto leading-relaxed">
                    AI CV generator UK. Generate job-specific CV variants automatically. <strong class="text-white">AI analyzes job descriptions</strong> and tailors your CV content to match each role—saving hours of manual work.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-indigo-600 shadow-lg hover:bg-indigo-50 transition-colors">
                            Generate CV Variant
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-indigo-600 shadow-lg hover:bg-indigo-50 transition-colors">
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
                        Why use AI CV generation?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Create tailored CV variants for each job application in seconds, not hours.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Save time</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Instead of manually rewriting your CV for each application, AI does the heavy lifting. Generate a tailored variant in 30-60 seconds, then refine it to match your voice.
                        </p>
                        <p class="text-gray-700">
                            Focus on applying to more jobs rather than spending hours customising each CV.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Better keyword matching</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            AI analyzes job descriptions to identify key requirements and skills. It then rewrites your CV sections to include relevant keywords and phrases that match what employers are looking for.
                        </p>
                        <p class="text-gray-700">
                            Improve your ATS (Applicant Tracking System) compatibility and increase your chances of getting noticed.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-purple-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Maintains accuracy</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            AI doesn't invent experiences, dates, or qualifications. It rewrites and rephrases your existing content to better match job requirements while keeping all facts accurate.
                        </p>
                        <p class="text-gray-700">
                            You always review and edit AI-generated content before using it, ensuring everything reflects your actual experience.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl border-2 border-pink-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-pink-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Multiple variants</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Create as many CV variants as you need—one for each job application. Each variant is automatically linked to its job application, so you always know which CV goes with which role.
                        </p>
                        <p class="text-gray-700">
                            Your master CV stays unchanged, and you can edit any variant independently.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-rose-50 to-orange-50 rounded-xl border-2 border-rose-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-rose-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Smart section selection</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Choose which sections to tailor: professional summary, work experience, skills, or all of them. AI focuses on the sections that matter most for each role.
                        </p>
                        <p class="text-gray-700">
                            Keep your education and certifications consistent while tailoring the content that demonstrates fit for each specific job.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border-2 border-orange-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-orange-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">100% private</h3>
                        </div>
                        <p class="text-gray-700 mb-4">
                            All AI processing happens in your browser using free Browser AI technology. Your CV data and job descriptions never leave your device—complete privacy and security.
                        </p>
                        <p class="text-gray-700">
                            No cloud services, no API keys, no data sharing. Everything runs locally in your browser, so your information stays private.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-20 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How it works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-indigo-100 text-indigo-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Add a job application</h3>
                            <p class="mt-3 text-gray-600">
                                Save a job listing from any website using the browser extension or quick-add link. The job description is automatically captured and stored with your application.
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                You can also upload job description files (PDF, Word, Excel) or paste the job description manually.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/ai-cv-generation/add-a-job.png" aria-label="View Add a job application image larger">
                                <img src="/static/images/ai-cv-generation/add-a-job.png" alt="Add a job application - job view with Overview, Files and Application questions" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-indigo-100 text-indigo-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Generate AI CV variant</h3>
                            <p class="mt-3 text-gray-600">
                                From the job view, click "Generate AI CV for this job" for a one-click tailored CV, or "Tailor CV for this job…" to choose which sections to tailor. AI analyzes the job description and your master CV, then creates a new variant tailored to that specific role.
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                Processing typically takes 30-60 seconds. The AI rewrites professional summary, work experience, and skills to better match the job requirements.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/manage-and-use.png" aria-label="View Generate AI CV variant image larger">
                                <img src="/static/images/cv-variants/manage-and-use.png" alt="AI CV generator – CV variant preview with Edit variant, Generate PDF and Print" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-indigo-100 text-indigo-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Review and refine</h3>
                            <p class="mt-3 text-gray-600">
                                Review the AI-generated content and make any necessary edits to match your personal writing style. The variant is automatically linked to the job application, so you can easily see which CV goes with which role.
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                All variants are stored separately from your master CV, so you can create as many tailored versions as you need.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/create-variants.png" aria-label="View Review and refine CV variant image larger">
                                <img src="/static/images/cv-variants/create-variants.png" alt="Review and refine - CV Variants table with linked job applications" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What Gets Tailored -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        What gets tailored?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        AI focuses on rewriting the sections that matter most for demonstrating fit with each role.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Professional Summary</h3>
                        <p class="text-sm text-gray-600">Rewritten to highlight relevant experience and skills that match the job requirements. Emphasizes your fit for the specific role.</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Work Experience</h3>
                        <p class="text-sm text-gray-600">Job descriptions and responsibilities are rephrased to emphasize relevant skills and achievements that align with the role you're applying for.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Skills</h3>
                        <p class="text-sm text-gray-600">Skills are reordered and rephrased to match the terminology used in the job description, improving keyword matching for ATS systems.</p>
                    </div>
                </div>

                <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> Education, certifications, and other factual sections remain unchanged. AI only rewrites content sections to improve relevance and keyword matching.
                    </p>
                </div>
            </div>
        </section>

        <!-- Browser AI Section -->
        <section class="py-20 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl border-2 border-indigo-200 shadow-xl p-10 md:p-12">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-indigo-500 text-white">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <h2 class="text-3xl font-bold text-gray-900">Browser-Based AI</h2>
                            </div>
                            <p class="text-lg text-gray-700 mb-6">
                                All AI CV generation runs directly in your browser using free Browser AI technology. No API keys, no cloud signup, no setup required—it works immediately for all users.
                            </p>
                            <div class="space-y-4 mb-8">
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">100% free</h4>
                                        <p class="text-sm text-gray-600">No API costs, no usage limits, no subscription required for browser-based AI.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Completely private</h4>
                                        <p class="text-sm text-gray-600">Your CV data and job descriptions never leave your device. Everything processes locally in your browser.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Works immediately</h4>
                                        <p class="text-sm text-gray-600">No configuration needed. Just create an account and start generating CV variants.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Cloud AI available</h4>
                                        <p class="text-sm text-gray-600">Organisations or users who prefer cloud-based AI (OpenAI, Anthropic, Gemini) can configure it in account settings.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <p class="text-sm text-indigo-800"><strong>Note:</strong> Browser AI requires a modern browser with WebAssembly support. Check if your browser supports it at <a href="/browser-ai-check.php" class="underline font-medium">browser-ai-check.php</a>.</p>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl p-8 border-2 border-indigo-200">
                            <p class="text-sm text-gray-600 mb-4 font-medium">How Browser AI works:</p>
                            <div class="space-y-3">
                                <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                    <p class="text-xs text-gray-500 mb-1">Processing location</p>
                                    <p class="text-sm font-semibold text-gray-900">Your browser (local)</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                    <p class="text-xs text-gray-500 mb-1">Data privacy</p>
                                    <p class="text-sm font-semibold text-gray-900">100% private - no data leaves your device</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                    <p class="text-xs text-gray-500 mb-1">Cost</p>
                                    <p class="text-sm font-semibold text-gray-900">Completely free - no API costs</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                    <p class="text-xs text-gray-500 mb-1">Setup required</p>
                                    <p class="text-sm font-semibold text-gray-900">None - works immediately</p>
                                </div>
                            </div>
                        </div>
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
        <section class="py-16 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start generating tailored CV variants
                </h2>
                <p class="mt-4 text-indigo-100 max-w-xl mx-auto">
                    AI CV generation is available on Pro plans. Create your account, build your master CV, and start generating job-specific variants in seconds.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-indigo-600 shadow-lg hover:bg-indigo-50 transition-colors">
                            Generate CV Variant
                        </a>
                        <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Upgrade to Pro
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-indigo-600 shadow-lg hover:bg-indigo-50 transition-colors">
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
