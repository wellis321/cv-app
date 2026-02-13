<?php
/**
 * Save Job From Anywhere – feature page
 * Describes the browser extension and one-click save from any job listing.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Save Job From Anywhere';
$canonicalUrl = APP_URL . '/save-job-from-anywhere.php';
$placeholderBase = '/static/images/features/save-job-from-anywhere';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Save job listings to your Simple CV Builder job list in one click from any website. Use the browser extension or bookmarklet—no copy-paste, no leaving the page.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gray-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1481627834810-7b0dc0a24339?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gray-900/70" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <span class="inline-flex items-center rounded-full bg-green-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">One-click save</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Save Job From Anywhere
                </h1>
                <p class="mt-6 text-xl text-gray-200 max-w-2xl leading-relaxed">
                    See a job you like? Save it to your job list <strong class="text-white">without leaving the page</strong>. Use our browser extension or bookmarklet—one click and the link (and title) are in your Simple CV Builder. Fill in the details later.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/save-job-token.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                            Get save token
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Why use it -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center">
                    Why save from anywhere?
                </h2>
                <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto text-center">
                    Stop copying links, switching tabs, or forgetting where you saw that role. One click from any job page and it’s in your list.
                </p>
                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <img src="<?php echo e($img('1504384308090-c894fdcc538d', 600)); ?>" alt="One click save from your browser" class="w-full aspect-video object-cover rounded-lg border border-gray-200 mb-4" width="400" height="225" />
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">One click</h3>
                        <p class="mt-2 text-sm text-gray-600">Click the extension or right‑click → “Save job to Simple CV Builder”. No forms, no new tab.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <img src="<?php echo e($img('1517694712202-14dd9538aa97', 600)); ?>" alt="Works on any job site" class="w-full aspect-video object-cover rounded-lg border border-gray-200 mb-4" width="400" height="225" />
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Works on any site</h3>
                        <p class="mt-2 text-sm text-gray-600">Indeed, LinkedIn, company career pages, job boards—if it has a URL, you can save it.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm sm:col-span-2 lg:col-span-1">
                        <img src="<?php echo e($img('1531403009284-440f080d1e12', 600)); ?>" alt="Add details when you’re ready" class="w-full aspect-video object-cover rounded-lg border border-gray-200 mb-4" width="400" height="225" />
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Details later</h3>
                        <p class="mt-2 text-sm text-gray-600">The job is saved with link and page title. Add company, dates, and notes in your job list when you’re ready.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center">
                    How it works
                </h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto text-center">
                    Get a personal save token, add it to the extension (or bookmarklet), then save from any job page.
                </p>

                <div class="mt-16 space-y-20">
                    <!-- Step 1 -->
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Get your save token</h3>
                            <p class="mt-3 text-gray-600">
                                Log in to Simple CV Builder, open <strong>My CV → Get save token</strong>. Copy your token (or regenerate a new one). You’ll paste this into the extension so it can save jobs to your account.
                            </p>
                            <?php if (isLoggedIn()): ?>
                                <a href="/save-job-token.php" class="mt-4 inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Get save token →</a>
                            <?php endif; ?>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <figure>
                                <img src="<?php echo e($img('1586281380349-632531db7ed4', 600)); ?>" alt="Get your save token from My CV" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                                <figcaption class="mt-2 text-sm text-gray-500">Get your save token from My CV → Get save token</figcaption>
                            </figure>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Download and install the extension</h3>
                            <p class="mt-3 text-gray-600">
                                <a href="/download-extension.php" class="text-blue-600 hover:underline font-medium">Download the extension for Chrome/Edge/Brave</a> or <a href="/download-extension-firefox.php" class="text-orange-600 hover:underline font-medium">for Firefox</a> as a ZIP file, extract it, then install it. Firefox users must use the Firefox download. Open the extension’s <strong>Options</strong>: enter this site’s URL (copy it from your browser’s address bar) and paste your save token. Click <strong>Save settings</strong>.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <figure>
                                <img src="<?php echo e($img('1550751827-4bd374c3f58b', 600)); ?>" alt="Configure the extension with Site URL and token" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                                <figcaption class="mt-2 text-sm text-gray-500">Extension options: this site’s URL and save token</figcaption>
                            </figure>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Save from any job page</h3>
                            <p class="mt-3 text-gray-600">
                                On any job listing (Indeed, LinkedIn, company careers, etc.), click the extension icon and hit <strong>Save job</strong>, or right‑click the page and choose <strong>Save job to Simple CV Builder</strong>. The job is added to your list immediately.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <figure>
                                <img src="<?php echo e($img('1586281380349-632531db7ed4', 600)); ?>" alt="Save from any job listing page" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                                <figcaption class="mt-2 text-sm text-gray-500">Click Save job from the extension or context menu on any job page</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Extension / bookmarklet note -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Browser extension &amp; bookmarklet
                </h2>
                <p class="mt-4 text-gray-600">
                    We provide a <strong>Chrome extension</strong> (load unpacked from the <code class="text-sm bg-gray-200 px-1 rounded">extension</code> folder in the app) and the same “quick add” flow is available from the job list in the app. Your save token works for both.
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
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-12 md:px-12 text-center text-white shadow-xl">
                    <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('<?php echo e($img('1557804506-669a67965ba0', 1200)); ?>');" aria-hidden="true"></div>
                    <div class="relative">
                    <h2 class="text-2xl font-bold sm:text-3xl">
                        Start saving jobs in one click
                    </h2>
                    <p class="mt-4 text-blue-100 max-w-xl mx-auto">
                        Get your save token, add it to the extension, and save any job from any site to your Simple CV Builder job list.
                    </p>
                    <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <?php if (isLoggedIn()): ?>
                            <a href="/save-job-token.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                                Get save token
                            </a>
                            <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                                Open job list
                            </a>
                        <?php else: ?>
                            <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-white/90 transition-colors">
                                Create free account
                            </button>
                            <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                                Job tracker features
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
