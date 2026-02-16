<?php
/**
 * Keyword Extraction – feature page
 * Describes AI-powered keyword extraction from job descriptions for CV tailoring.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Keyword Extraction';
$canonicalUrl = APP_URL . '/keyword-extraction.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Extract keywords from job descriptions using AI. Identify skills, qualifications, and requirements to tailor your CV and improve ATS compatibility.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-teal-600 via-cyan-600 to-blue-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1454167574059-b80302a6923a?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-teal-600/90 via-cyan-600/90 to-blue-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">AI-powered</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Keyword Extraction
                </h1>
                <p class="mt-6 text-xl text-cyan-50 max-w-2xl mx-auto leading-relaxed">
                    Let AI analyse job descriptions and extract the most important keywords, skills, and requirements. <strong class="text-white">Choose which keywords to emphasise</strong> when generating your tailored CV.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Try it now
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Why Keywords Matter -->
        <section class="py-16 bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl border-2 border-teal-200 p-8 md:p-12 shadow-lg">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            Why keywords matter
                        </h2>
                        <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                            Most job applications never reach a human recruiter. Understanding why keywords are crucial can make the difference between your CV being seen or rejected.
                        </p>
                    </div>

                    <div class="grid gap-8 md:grid-cols-2 mb-8">
                        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-500 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Without Keywords</h3>
                            </div>
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span><strong>ATS systems reject your CV</strong> before a human sees it</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span><strong>Low matching score</strong> — your CV doesn't align with job requirements</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span><strong>Missed opportunities</strong> — qualified candidates get filtered out</span>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">With Keywords</h3>
                            </div>
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>ATS systems approve your CV</strong> and forward it to recruiters</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>High matching score</strong> — your CV aligns with job requirements</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>Better chances</strong> — your CV reaches human reviewers</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">The Reality of Modern Job Applications</h3>
                        <div class="space-y-4 text-gray-700">
                            <p>
                                <strong>75% of large companies use Applicant Tracking Systems (ATS)</strong> to filter CVs before they reach recruiters. These systems scan your CV for specific keywords that match the job description.
                            </p>
                            <p>
                                If your CV doesn't include the right keywords, it gets automatically rejected—even if you're perfectly qualified. <strong>Keywords are your ticket past the first gate</strong> in the hiring process.
                            </p>
                            <div class="mt-4 p-4 bg-white rounded-lg border border-blue-200">
                                <p class="text-sm text-gray-700">
                                    <strong>Example:</strong> If a job requires "Python" and "Machine Learning," but your CV only mentions "programming" and "data analysis," the ATS may reject it even though you have those skills. Using the exact keywords from the job description ensures you pass the initial screening.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What it does -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        What keywords are extracted?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Our AI identifies the most important terms that Applicant Tracking Systems (ATS) and recruiters look for in job descriptions.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl border-2 border-teal-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mx-auto mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Technical Skills</h3>
                        <p class="text-sm text-gray-700">Programming languages, software, tools, and technologies</p>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl border-2 border-cyan-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-500 text-white mx-auto mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Qualifications</h3>
                        <p class="text-sm text-gray-700">Degrees, certifications, licenses, and professional qualifications</p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border-2 border-blue-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mx-auto mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Soft Skills</h3>
                        <p class="text-sm text-gray-700">Communication, leadership, teamwork, and other interpersonal skills</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border-2 border-indigo-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-500 text-white mx-auto mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Requirements</h3>
                        <p class="text-sm text-gray-700">Years of experience, industry knowledge, and specific job requirements</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How keyword extraction works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Add a job description</h3>
                            <p class="mt-3 text-gray-600">
                                Add or edit a job application and include the job description. You can paste it manually, upload a file (PDF, Word, Excel), or extract text from an uploaded file.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/keyword-extraction/add-a-job-description.png" aria-label="View Add a job description image larger">
                                <img src="/static/images/keyword-extraction/add-a-job-description.png" alt="Add a job description - Description, Application Link and Keywords & Skills" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Click Extract Keywords</h3>
                            <p class="mt-3 text-gray-600">
                                Open the job application and click <strong>Extract Keywords</strong>. Our AI analyses the job description and identifies the most important keywords, skills, and requirements.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/keyword-integration/extract-keyword.png" aria-label="View Extract keywords image larger">
                                <img src="/static/images/keyword-integration/extract-keyword.png" alt="Click Extract Keywords - Key Keywords & Skills with Re-extract button" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Select keywords to emphasise</h3>
                            <p class="mt-3 text-gray-600">
                                Review the extracted keywords and select which ones you want to emphasise in your CV. When you generate an AI CV variant for this job, those keywords will be prioritised.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/build-your-master.pdf.png" aria-label="View Select keywords to emphasise image larger">
                                <img src="/static/images/cv-variants/build-your-master.pdf.png" alt="Select keywords to emphasise - CV with keywords integrated in professional summary" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-teal-100 text-teal-800 px-3 py-1 text-sm font-semibold">Step 4</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Generate your tailored CV</h3>
                            <p class="mt-3 text-gray-600">
                                Use <strong>Generate AI CV for this job</strong> to create a CV variant that emphasises your selected keywords. The AI will naturally incorporate these terms throughout your CV to improve ATS compatibility.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/cv-variants/manage-and-use.png" aria-label="View Generate your tailored CV image larger">
                                <img src="/static/images/cv-variants/manage-and-use.png" alt="Generate your tailored CV - manage and use CV variants" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
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
                    Why use keyword extraction?
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl border border-teal-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Improve ATS compatibility</h3>
                        <p class="text-sm text-gray-600">ATS systems scan for specific keywords. By emphasising the right terms, your CV is more likely to pass initial screening.</p>
                    </div>
                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border border-cyan-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Save time</h3>
                        <p class="text-sm text-gray-600">Let AI identify important keywords instead of manually scanning job descriptions. Focus on tailoring your CV, not finding keywords.</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Better match</h3>
                        <p class="text-sm text-gray-600">By emphasising the keywords employers are looking for, your CV better matches the job description and stands out to recruiters.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-teal-600 via-cyan-600 to-blue-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start extracting keywords today
                </h2>
                <p class="mt-4 text-cyan-50 max-w-xl mx-auto">
                    Keyword extraction is included with every account. Add a job description and let AI identify the most important terms.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Try it now
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-teal-600 shadow-lg hover:bg-teal-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Explore features
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php partial('image-lightbox'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>
</body>
</html>
