<?php
/**
 * Free Browser AI Integration – feature page
 * Describes the amazing free AI features powered by Browser AI - no API keys, no costs, runs in your browser.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Free Browser AI';
$canonicalUrl = APP_URL . '/browser-ai-free.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Free Browser AI for CVs—no API keys. AI runs in your browser for rewriting, quality assessment, keyword extraction.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-purple-700 via-indigo-700 to-blue-700 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d128efb9b3fb?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-purple-700/90 via-indigo-700/90 to-blue-700/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-green-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">100% FREE</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Free Browser AI Integration
                </h1>
                <p class="mt-6 text-xl text-purple-50 max-w-2xl mx-auto leading-relaxed">
                    Powerful AI features that run <strong class="text-white">directly in your browser</strong>—no cloud services, no API keys, no costs. Generate unlimited CV variants, assess quality, extract keywords, and create cover letters—<strong class="text-white">all completely free</strong>.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-purple-700 shadow-lg hover:bg-purple-50 transition-colors">
                            Try Browser AI now
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-purple-700 shadow-lg hover:bg-purple-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                    <a href="/browser-ai-check.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        Check browser support
                    </a>
                </div>
            </div>
        </section>

        <!-- Browser Requirements -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Browser Requirements
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                        Browser AI requires WebGPU or WebGL support. Check if your browser is compatible.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 p-8 mb-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white flex-shrink-0">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Check Your Browser Compatibility</h3>
                            <p class="text-gray-700 mb-4">
                                Not sure if your browser supports Browser AI? Use our browser compatibility checker to verify WebGPU/WebGL support, storage availability, and get detailed information about your browser's capabilities.
                            </p>
                            <a href="/browser-ai-check.php" class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-6 py-3 text-base font-semibold text-white shadow-lg hover:bg-purple-700 transition-colors">
                                Check browser support now →
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border-2 border-gray-200 p-8 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Supported Browsers</h3>
                    <div class="space-y-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Chrome</h4>
                            <p class="text-sm text-gray-600">Version 113+ (WebGPU) or Version 56+ (WebGL 2.0)</p>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Microsoft Edge</h4>
                            <p class="text-sm text-gray-600">Version 113+ (WebGPU) or Version 79+ (WebGL 2.0)</p>
                        </div>
                        <div class="border-l-4 border-orange-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Firefox</h4>
                            <p class="text-sm text-gray-600">Version 141+ on Windows only (WebGPU), Version 51+ (WebGL 2.0) on all platforms</p>
                            <p class="text-xs text-gray-500 mt-1 italic">Note: Firefox WebGPU currently works only on Windows. Support for macOS and Linux is coming soon.</p>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Safari</h4>
                            <p class="text-sm text-gray-600">Version 16.4+ on macOS 13.3+ (WebGPU), Version 15.2+ (WebGL 2.0)</p>
                        </div>
                        <div class="border-l-4 border-red-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Opera</h4>
                            <p class="text-sm text-gray-600">Version 99+ (WebGPU) or Version 43+ (WebGL 2.0)</p>
                        </div>
                    </div>
                    
                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Technical Requirements</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><strong>WebGPU or WebGL:</strong> Required for AI model execution. WebGPU provides better performance but requires newer browser versions.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><strong>IndexedDB:</strong> Required for caching AI models in your browser for faster subsequent use.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><strong>Storage Space:</strong> Models require significant browser storage space (several hundred MB). First use may take several minutes to download the model.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="/browser-ai-check.php" class="inline-flex items-center gap-2 rounded-lg border-2 border-purple-600 bg-white px-6 py-3 text-base font-semibold text-purple-600 hover:bg-purple-50 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Check if your browser is compatible
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- What's Free -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        What you get for free
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        All these AI features run in your browser—no API costs, no limits, no setup required.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">AI CV Rewriting</h3>
                        <p class="text-sm text-gray-600">Generate unlimited job-specific CV variants. Tailor your CV for each application automatically.</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border-2 border-indigo-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">CV Quality Assessment</h3>
                        <p class="text-sm text-gray-600">Get AI-powered scores and recommendations to improve your CV's ATS compatibility and quality.</p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border-2 border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Keyword Extraction</h3>
                        <p class="text-sm text-gray-600">AI identifies important keywords from job descriptions so you can emphasise them in your CV.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Cover Letter Generation</h3>
                        <p class="text-sm text-gray-600">Generate tailored cover letters with AI based on job descriptions and your CV.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How Browser AI works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-purple-100 text-purple-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">AI runs in your browser</h3>
                            <p class="mt-3 text-gray-600">
                                When you use AI features, the AI model loads and runs directly in your browser using WebGPU or WebGL. No data is sent to external servers—everything happens locally on your device.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/browser-ai/ai-runs-in-browser.png" aria-label="View AI runs in browser image larger">
                                <img src="/static/images/browser-ai/ai-runs-in-browser.png" alt="AI runs in your browser - processing locally" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-purple-100 text-purple-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">No API keys or setup</h3>
                            <p class="mt-3 text-gray-600">
                                Browser AI works immediately—no configuration, no API keys, no accounts with third-party services. Just open the feature and start using it. The AI model downloads once and caches in your browser.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/browser-ai/no-api.png" aria-label="View no API keys or setup image larger">
                                <img src="/static/images/browser-ai/no-api.png" alt="No API keys or setup required" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-purple-100 text-purple-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Unlimited use, zero cost</h3>
                            <p class="mt-3 text-gray-600">
                                Generate as many CV variants, run as many quality assessments, and extract keywords as often as you want—all completely free. No usage limits, no per-request costs, no hidden fees.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/browser-ai/unlimited-use.png" aria-label="View unlimited use image larger">
                                <img src="/static/images/browser-ai/unlimited-use.png" alt="Unlimited use, zero cost" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Browser AI -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Why Browser AI is amazing
                </h2>
                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-green-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">100% Free Forever</h3>
                        </div>
                        <p class="text-gray-700 mb-4">No API costs, no per-request fees, no usage limits. Browser AI is completely free for all users, including free plan members.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Unlimited CV variants</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Unlimited quality assessments</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Unlimited keyword extraction</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Privacy & Security</h3>
                        </div>
                        <p class="text-gray-700 mb-4">Your data never leaves your device. All AI processing happens locally in your browser—no cloud services, no data transmission, complete privacy.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>No data sent to external servers</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Works offline after first load</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>No API keys or accounts needed</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-purple-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Instant Setup</h3>
                        </div>
                        <p class="text-gray-700 mb-4">No configuration required. Browser AI works immediately—just open any AI feature and start using it. The model downloads automatically on first use.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Works in Chromium browsers and Safari</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Model caches in browser for faster subsequent use</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>No installation or setup required</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border-2 border-indigo-200 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">No Rate Limits</h3>
                        </div>
                        <p class="text-gray-700 mb-4">Unlike cloud AI services with rate limits and quotas, Browser AI has no restrictions. Use it as much as you want, whenever you want.</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Generate CVs back-to-back without waiting</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Run multiple assessments in a row</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>No monthly quotas or usage caps</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparison -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Browser AI vs Cloud AI
                </h2>
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Feature</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-green-700">Browser AI (Free)</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Cloud AI (Paid)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">Cost</td>
                                    <td class="px-6 py-4 text-sm text-center text-green-600 font-semibold">100% Free</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-600">API costs apply</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">Setup Required</td>
                                    <td class="px-6 py-4 text-sm text-center text-green-600 font-semibold">None</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-600">API keys needed</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">Usage Limits</td>
                                    <td class="px-6 py-4 text-sm text-center text-green-600 font-semibold">Unlimited</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-600">Rate limits apply</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">Privacy</td>
                                    <td class="px-6 py-4 text-sm text-center text-green-600 font-semibold">100% Local</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-600">Data sent to cloud</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">Works Offline</td>
                                    <td class="px-6 py-4 text-sm text-center text-green-600 font-semibold">Yes (after first load)</td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-600">No</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="mt-6 text-center text-sm text-gray-600">
                    <strong>Note:</strong> Cloud AI options (OpenAI, Anthropic, Gemini) are available for organisations or users who prefer them, but Browser AI is the default and completely free for everyone.
                </p>
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
        <section class="py-16 bg-gradient-to-br from-purple-700 via-indigo-700 to-blue-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start using free Browser AI today
                </h2>
                <p class="mt-4 text-purple-50 max-w-xl mx-auto">
                    Browser AI is included with every account—free plan included. No setup, no costs, no limits. Generate your first AI CV variant now.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-purple-700 shadow-lg hover:bg-purple-50 transition-colors">
                            Try Browser AI now
                        </a>
                        <a href="/cv-variants.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Generate CV variant
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-purple-700 shadow-lg hover:bg-purple-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/individual-users.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Learn more
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
