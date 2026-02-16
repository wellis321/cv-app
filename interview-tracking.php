<?php
/**
 * Interview Tracking – feature page
 * Describes tracking interviews and moving applications through stages.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Interview Tracking';
$canonicalUrl = APP_URL . '/interview-tracking.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Track interviews for each job application. Move status through stages from Applied to Interviewing to Offered, and use notes to record feedback and next steps.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d128efb9b3fb?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-violet-600/90 via-purple-600/90 to-fuchsia-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">Job applications</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Interview Tracking
                </h1>
                <p class="mt-6 text-xl text-purple-50 max-w-2xl mx-auto leading-relaxed">
                    Track whether you've had an interview for each application and move status through stages. <strong class="text-white">Record interview feedback and next steps</strong> so you never lose important details.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-violet-600 shadow-lg hover:bg-purple-50 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-violet-600 shadow-lg hover:bg-purple-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Interview Stages -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Track your interview journey
                </h2>
                <div class="grid gap-6 md:grid-cols-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border-2 border-blue-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mx-auto mb-4 font-bold text-lg">1</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Applied</h3>
                        <p class="text-sm text-gray-700">You've submitted your application and are waiting to hear back.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border-2 border-purple-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mx-auto mb-4 font-bold text-lg">2</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Interviewing</h3>
                        <p class="text-sm text-gray-700">You've been invited to interview. Track multiple rounds and add notes after each one.</p>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border-2 border-emerald-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500 text-white mx-auto mb-4 font-bold text-lg">3</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Offered</h3>
                        <p class="text-sm text-gray-700">You've received an offer! Review the details and decide next steps.</p>
                    </div>

                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl border-2 border-teal-200 p-6 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mx-auto mb-4 font-bold text-lg">✓</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Accepted</h3>
                        <p class="text-sm text-gray-700">Congratulations! You've accepted the offer and secured the role.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How interview tracking works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-violet-100 text-violet-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Update status when invited</h3>
                            <p class="mt-3 text-gray-600">
                                When you receive an interview invitation, update the application status to <strong>Interviewing</strong>. This helps you see at a glance which applications are in the interview stage.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/interview-tracking/update-status.png" aria-label="View update status image larger">
                                <img src="/static/images/interview-tracking/update-status.png" alt="Update status - Status dropdown with Interested, Interviewing, Applied and other stages" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-violet-100 text-violet-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Add interview notes</h3>
                            <p class="mt-3 text-gray-600">
                                After each interview, add notes to the application. Record questions asked, your answers, interviewer feedback, and any next steps discussed. This helps you prepare for follow-up interviews and remember important details.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/interview-tracking/notes.png" aria-label="View interview notes image larger">
                                <img src="/static/images/interview-tracking/notes.png" alt="Add interview notes - rich text editor for recording recruiter calls, interview feedback and next steps" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-violet-100 text-violet-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Move through stages</h3>
                            <p class="mt-3 text-gray-600">
                                As you progress, update the status: <strong>Interviewing</strong> → <strong>Offered</strong> → <strong>Accepted</strong> (or <strong>Rejected</strong> if not successful). Each status change helps you track your progress and see which opportunities are moving forward.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/interview-tracking/update-status.png" aria-label="View move through stages image larger">
                                <img src="/static/images/interview-tracking/update-status.png" alt="Move through stages - Status dropdown with Interviewing, Offered, Accepted, Rejected" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
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
                    Why track interviews?
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl border-2 border-violet-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-violet-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay prepared</h3>
                        <p class="text-sm text-gray-600">Review your notes before follow-up interviews. Remember what was discussed and prepare better answers.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-xl border-2 border-purple-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Never forget details</h3>
                        <p class="text-sm text-gray-600">Record interviewer names, questions asked, and feedback. Keep all important information in one place.</p>
                    </div>

                    <div class="bg-gradient-to-br from-fuchsia-50 to-violet-50 rounded-xl border-2 border-fuchsia-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-fuchsia-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">See your progress</h3>
                        <p class="text-sm text-gray-600">Filter by status to see which applications are in the interview stage and which have progressed further.</p>
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
        <section class="py-16 bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start tracking your interviews
                </h2>
                <p class="mt-4 text-purple-50 max-w-xl mx-auto">
                    Interview tracking is included with every account. Update statuses and add notes to stay organized throughout the interview process.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-violet-600 shadow-lg hover:bg-purple-50 transition-colors">
                            Open job list
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-violet-600 shadow-lg hover:bg-purple-50 transition-colors">
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
