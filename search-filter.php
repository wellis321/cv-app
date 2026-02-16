<?php
/**
 * Quick Search & Filter – feature page
 * Describes searching and filtering job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Quick Search & Filter';
$canonicalUrl = APP_URL . '/search-filter.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Quickly find applications by company name, job title, or status. Filter your applications to focus on active opportunities or review your history.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1460925895917-9ada21bccfda?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 via-indigo-600/90 to-purple-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">Job applications</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Quick Search & Filter
                </h1>
                <p class="mt-6 text-xl text-blue-50 max-w-2xl mx-auto leading-relaxed">
                    Quickly find applications by company name, job title, or status. <strong class="text-white">Filter your applications</strong> to focus on active opportunities or review your history.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Try search & filter
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Search Features -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Find applications instantly
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Search by text</h3>
                        <p class="text-sm text-gray-600">Type in the search box to find applications by company name, job title, or any text in the description or notes.</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Filter by status</h3>
                        <p class="text-sm text-gray-600">Filter to show only applications at a specific stage: Interested, Applied, Interviewing, Offered, etc.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Sort & organise</h3>
                        <p class="text-sm text-gray-600">Sort by date, company name, or status. Organise your applications to see what needs attention first.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How search & filter works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Use the search box</h3>
                            <p class="mt-3 text-gray-600">
                                Type any text in the search box at the top of your job list. The search looks through company names, job titles, descriptions, and notes to find matching applications instantly.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-template-lightbox="/static/images/search-filter/search.png" aria-label="View search box image larger">
                                <img src="/static/images/search-filter/search.png" alt="Search box - type to find applications by company, job title or keyword" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Filter by status</h3>
                            <p class="mt-3 text-gray-600">
                                Use the status filter dropdown to show only applications at a specific stage. Perfect for focusing on active opportunities (Interviewing, Applied) or reviewing completed ones (Accepted, Rejected).
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-template-lightbox="/static/images/search-filter/filter-status.png" aria-label="View filter by status image larger">
                                <img src="/static/images/search-filter/filter-status.png" alt="Filter by status - Total, Applied, Interviewing, Offered tabs with job application cards" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Combine search and filter</h3>
                            <p class="mt-3 text-gray-600">
                                Use search and filter together for powerful results. For example, search for "developer" and filter by "Interviewing" to see only developer roles you're interviewing for.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-template-lightbox="/static/images/search-filter/combine.png" aria-label="View combine search and filter image larger">
                                <img src="/static/images/search-filter/combine.png" alt="Combine search and filter - status filter and search box together for refined results" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
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
                    Why search & filter?
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Save time</h3>
                        <p class="text-sm text-gray-600">Find the application you're looking for instantly instead of scrolling through a long list.</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay focused</h3>
                        <p class="text-sm text-gray-600">Filter to see only active opportunities or applications that need your attention right now.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Review history</h3>
                        <p class="text-sm text-gray-600">Filter by status to review your application history and learn from past applications.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start searching and filtering
                </h2>
                <p class="mt-4 text-blue-50 max-w-xl mx-auto">
                    Search and filter are included with every account. Find any application instantly and focus on what matters most.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Try search & filter
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
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
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>

    <!-- Image lightbox -->
    <div id="template-lightbox" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-label="Image preview">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/70 transition-opacity" data-close-template-lightbox aria-hidden="true"></div>
            <div class="relative max-w-4xl w-full flex items-center justify-center">
                <button type="button" class="absolute right-2 top-2 z-10 rounded-full bg-white/90 p-2 text-gray-600 hover:bg-white hover:text-gray-900 transition-colors" data-close-template-lightbox aria-label="Close">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <img id="template-lightbox-image" src="" alt="" class="max-h-[90vh] w-auto rounded-lg shadow-2xl object-contain">
            </div>
        </div>
    </div>
    <script>
(function() {
    const lightbox = document.getElementById('template-lightbox');
    const lightboxImage = document.getElementById('template-lightbox-image');
    if (!lightbox || !lightboxImage) return;

    function openLightbox(src, alt) {
        lightboxImage.src = src;
        lightboxImage.alt = alt || 'Image preview';
        lightbox.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        var closeBtn = lightbox.querySelector('button[data-close-template-lightbox]');
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 50);
    }
    function closeLightbox() {
        lightbox.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-template-lightbox]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openLightbox(this.dataset.templateLightbox, this.getAttribute('aria-label') || 'Image preview');
        });
    });
    document.querySelectorAll('[data-close-template-lightbox]').forEach(function(btn) {
        btn.addEventListener('click', closeLightbox);
    });
    lightbox.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
})();
    </script>
</body>
</html>
