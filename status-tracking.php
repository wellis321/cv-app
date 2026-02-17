<?php
/**
 * Status Tracking – feature page
 * Describes tracking job application statuses from interest through to offer.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Status Tracking';
$canonicalUrl = APP_URL . '/status-tracking.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Track job application status from Applied to Offer. Monitor progress: Interviewing, Offered, Accepted, Rejected.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d128efb9b3fb?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-purple-900/80 to-indigo-900/80" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-purple-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">Job applications</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Status Tracking
                </h1>
                <p class="mt-6 text-xl text-purple-100 max-w-2xl mx-auto leading-relaxed">
                    Never lose track of where each application stands. Monitor your progress from <strong class="text-white">initial interest</strong> through to <strong class="text-white">offer</strong> with clear, visual status indicators.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-purple-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-purple-700 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-purple-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-purple-700 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#statuses" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        View all statuses
                    </a>
                </div>
            </div>
        </section>

        <!-- Status Overview -->
        <section id="statuses" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Application Statuses
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Track your job search journey with clear status indicators. Each status helps you understand where you are in the process and what comes next.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Interested -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border-2 border-blue-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500 text-white font-bold text-lg">1</div>
                            <h3 class="text-lg font-bold text-gray-900">Interested</h3>
                        </div>
                        <p class="text-sm text-gray-700">You've found a role you're interested in. Save it and start preparing your application.</p>
                    </div>

                    <!-- In Progress -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border-2 border-yellow-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-500 text-white font-bold text-lg">2</div>
                            <h3 class="text-lg font-bold text-gray-900">In Progress</h3>
                        </div>
                        <p class="text-sm text-gray-700">You're actively working on the application—tailoring your CV, writing a cover letter, or gathering documents.</p>
                    </div>

                    <!-- Applied -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl border-2 border-green-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-500 text-white font-bold text-lg">3</div>
                            <h3 class="text-lg font-bold text-gray-900">Applied</h3>
                        </div>
                        <p class="text-sm text-gray-700">Application submitted. Set a follow-up date to check in or wait for a response.</p>
                    </div>

                    <!-- Interviewing -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border-2 border-purple-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-500 text-white font-bold text-lg">4</div>
                            <h3 class="text-lg font-bold text-gray-900">Interviewing</h3>
                        </div>
                        <p class="text-sm text-gray-700">You're in the interview process. Track multiple rounds and add notes after each interview.</p>
                    </div>

                    <!-- Offered -->
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border-2 border-emerald-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500 text-white font-bold text-lg">5</div>
                            <h3 class="text-lg font-bold text-gray-900">Offered</h3>
                        </div>
                        <p class="text-sm text-gray-700">You've received an offer! Review the details and decide whether to accept or negotiate.</p>
                    </div>

                    <!-- Accepted -->
                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl border-2 border-teal-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-500 text-white font-bold text-lg">✓</div>
                            <h3 class="text-lg font-bold text-gray-900">Accepted</h3>
                        </div>
                        <p class="text-sm text-gray-700">Congratulations! You've accepted the offer. Keep this record for your career history.</p>
                    </div>

                    <!-- Rejected -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-500 text-white font-bold text-lg">✗</div>
                            <h3 class="text-lg font-bold text-gray-900">Rejected</h3>
                        </div>
                        <p class="text-sm text-gray-700">Application wasn't successful this time. Keep the record for reference and learn from the experience.</p>
                    </div>

                    <!-- Withdrawn -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border-2 border-orange-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-500 text-white font-bold text-lg">↩</div>
                            <h3 class="text-lg font-bold text-gray-900">Withdrawn</h3>
                        </div>
                        <p class="text-sm text-gray-700">You've withdrawn your application. Useful to track if you changed your mind or found another role.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why track status?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Status tracking transforms your job search from a scattered collection of applications into a clear, manageable process. Here's how it helps you stay on top of everything.
                    </p>
                </div>

                <div class="space-y-20">
                    <!-- See progress at a glance -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">See progress at a glance</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                When you're managing multiple applications, it's easy to lose track of where each one stands. With status tracking, you can instantly see how many applications are at each stage—how many you've just applied to, how many are in the interview process, and which ones have resulted in offers.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                This visual overview helps you prioritise your time. You'll know immediately which applications need follow-up, which ones are moving forward, and which ones might need a gentle nudge. No more scrolling through endless lists or trying to remember what happened with each application.
                            </p>
                        </div>
                        <div class="flex items-center">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/interview-tracking/update-status.png" aria-label="View update status image larger">
                                <img src="/static/images/interview-tracking/update-status.png" alt="Update job status, work arrangement and follow-up date at a glance" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover" width="800" height="450" />
                            </button>
                        </div>
                    </div>

                    <!-- Stay organised -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="lg:order-2 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Stay organised</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Job searching can feel chaotic, especially when you're applying to multiple roles across different companies and platforms. Without a system, it's all too easy to forget where you've applied, lose track of important deadlines, or miss follow-up opportunities.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Status tracking gives you a centralised place to manage everything. Filter by status to focus on active opportunities that need your attention, or review your history to see patterns in your job search. Whether you're looking at applications that are "In Progress" or reflecting on those that were "Accepted" or "Rejected", everything is organised and accessible.
                            </p>
                        </div>
                        <div class="lg:order-1 flex items-center">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/follow-up/view-dates.png" aria-label="View view dates image larger">
                                <img src="/static/images/follow-up/view-dates.png" alt="Job cards with status dates and follow-up indicators" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover" width="800" height="450" />
                            </button>
                        </div>
                    </div>

                    <!-- Make informed decisions -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Make informed decisions</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Understanding your job search patterns is crucial for improving your approach. When you track statuses over time, you start to see which types of applications progress furthest, which companies respond fastest, and where you might need to adjust your strategy.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Perhaps you notice that applications marked "In Progress" for too long rarely result in interviews—that might be a sign to streamline your application process. Or maybe you see that certain industries or roles consistently move to "Interviewing" faster, helping you prioritise where to focus your energy. Status tracking turns your job search into data you can learn from.
                            </p>
                        </div>
                        <div class="flex items-center">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/all-in-one/seamless-workflow.png" aria-label="View seamless workflow image larger">
                                <img src="/static/images/all-in-one/seamless-workflow.png" alt="Dashboard with CV building, job tracking and AI tools for informed decisions" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover" width="800" height="450" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How status tracking works
                </h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-600 text-white font-bold text-lg">1</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Start with the right status</h3>
                            <p class="text-gray-600 leading-relaxed">When you first save a job posting or add an application manually, you'll choose where it fits in your journey. Perhaps you've just spotted an interesting role and want to save it for later—that's "Interested". Or maybe you're already crafting your cover letter and tailoring your CV—in that case, mark it as "In Progress". The choice is yours, and you can always change it later.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-600 text-white font-bold text-lg">2</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Keep it updated as things happen</h3>
                            <p class="text-gray-600 leading-relaxed">As your application moves through the process, updating the status takes just a moment. Hit "Submit" on that application form? Change it to "Applied". Receive an email inviting you for a chat? Switch to "Interviewing". Get that exciting call with an offer? Mark it as "Offered". It's designed to be quick and intuitive, so you can focus on what matters—your job search—rather than wrestling with complicated tools.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-600 text-white font-bold text-lg">3</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Focus on what you need to see</h3>
                            <p class="text-gray-600 leading-relaxed">When you've got multiple applications at different stages, the status filter becomes your best friend. Want to see only the roles you're actively interviewing for? Filter by "Interviewing". Need to review which applications are still waiting for a response? Filter by "Applied". Or perhaps you'd like to reflect on your journey and see everything that's been "Accepted" or "Rejected". The filter helps you cut through the noise and focus on exactly what matters right now.</p>
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
        <section class="py-16 bg-gradient-to-br from-purple-600 to-indigo-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start tracking your applications
                </h2>
                <p class="mt-4 text-purple-100 max-w-xl mx-auto">
                    Status tracking is included with every account. Add your first application and see how easy it is to stay organised.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-purple-600 shadow-lg hover:bg-purple-50 transition-colors">
                            Open job list
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-purple-600 shadow-lg hover:bg-purple-50 transition-colors">
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
    <?php partial('image-lightbox'); ?>
</body>
</html>
