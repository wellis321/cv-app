<?php
/**
 * Never Miss a Follow-Up – feature page
 * Describes follow-up date tracking and reminders for job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Never Miss a Follow-Up';
$canonicalUrl = APP_URL . '/never-miss-follow-up.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Set follow-up and closing dates for each job application. See upcoming dates at a glance and never miss an important deadline or follow-up opportunity.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-orange-900 via-red-900 to-orange-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-orange-900/80 via-red-900/80 to-orange-900/80" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-orange-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">Job applications</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Never Miss a Follow-Up
                </h1>
                <p class="mt-6 text-xl text-orange-100 max-w-2xl mx-auto leading-relaxed">
                    Set follow-up or closing dates for each application and get <strong class="text-white">automatic browser notifications</strong> before deadlines. Stay on top of deadlines and never let an opportunity slip away.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-orange-700 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-orange-700 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#browser-notifications" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        Learn about notifications
                    </a>
                </div>
            </div>
        </section>

        <!-- Browser Notifications Section - MOVED TO TOP -->
        <section id="browser-notifications" class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl border-2 border-blue-200 shadow-xl p-10 md:p-12">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-500 text-white">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                <h2 class="text-3xl font-bold text-gray-900">Browser Notifications</h2>
                            </div>
                            <p class="text-lg text-gray-700 mb-6">
                                Never miss a deadline with automatic browser notifications. When you visit your dashboard or job list, you'll receive notifications for upcoming closing dates. By default, you'll get reminders 7 days, 3 days, and 1 day before each deadline, but you can customize these in your <?php if (isLoggedIn()): ?><a href="/profile.php" class="text-blue-600 hover:underline font-medium">Profile settings</a><?php else: ?><button type="button" data-open-login data-redirect="/profile.php" class="text-blue-600 hover:underline font-medium bg-transparent border-0 p-0 cursor-pointer">Profile settings</button><?php endif; ?>.
                            </p>
                            <div class="space-y-4 mb-8">
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Automatic reminders</h4>
                                        <p class="text-sm text-gray-600">Notifications appear automatically when you visit your dashboard or job list. No need to check manually—we'll remind you.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Smart timing</h4>
                                        <p class="text-sm text-gray-600">Get reminders at 7 days, 3 days, and 1 day before closing dates—giving you plenty of time to prepare and submit applications.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">One notification per day</h4>
                                        <p class="text-sm text-gray-600">Each job gets one notification per day, so you're informed without being overwhelmed by repeated reminders.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Click to view</h4>
                                        <p class="text-sm text-gray-600">Click any notification to jump directly to your job list and see the application that needs attention.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                                <p class="text-sm text-blue-800"><strong>Note:</strong> Browser notifications require permission from your browser. You'll be prompted to allow notifications the first time you visit your dashboard. You can disable notifications anytime in your browser settings.</p>
                            </div>
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800 font-medium mb-2">Customize your reminders</p>
                                <p class="text-xs text-green-700">You can customize when you receive reminders in your <?php if (isLoggedIn()): ?><a href="/profile.php" class="underline font-medium">Profile settings</a><?php else: ?><button type="button" data-open-login data-redirect="/profile.php" class="underline font-medium bg-transparent border-0 p-0 text-green-700 cursor-pointer">Profile settings</button><?php endif; ?>. Choose from preset options (14, 7, 3, or 1 day before) or add your own custom days. Default is 7, 3, and 1 day before closing dates.</p>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-8 border-2 border-blue-200">
                            <div class="bg-white rounded-lg p-6 shadow-lg">
                                <p class="text-sm text-gray-600 mb-4 font-medium">Example notification:</p>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900 mb-1">Closing date soon</p>
                                            <p class="text-xs text-gray-600">In 3 days: Senior Developer at Tech Company</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-4 text-xs text-gray-500">Notifications appear in your browser's notification center</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Overview -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        How it works
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Set follow-up or closing dates for each application and get automatic reminders. Simple, effective, and always there when you need it.
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Set dates</h3>
                        <p class="text-gray-600">When you add an application, set a follow-up date or closing date (or both). The follow-up date is when to check in with the employer; the closing date is the application deadline.</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">See at a glance</h3>
                        <p class="text-gray-600">Your job list displays upcoming dates prominently. Dates are highlighted so you can immediately see what needs attention today, this week, or next week.</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Get notified</h3>
                        <p class="text-gray-600">Browser notifications remind you automatically—7 days, 3 days, and 1 day before closing dates. Customize the timing in your Profile settings to match your workflow.</p>
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
        <section class="py-16 bg-gradient-to-br from-orange-600 to-red-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start tracking follow-up dates today
                </h2>
                <p class="mt-4 text-orange-100 max-w-xl mx-auto">
                    Follow-up dates are included with every account. Add your first application and set a follow-up date to see how easy it is to stay organised.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-orange-600 shadow-lg hover:bg-orange-50 transition-colors">
                            Open job list
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-orange-600 shadow-lg hover:bg-orange-50 transition-colors">
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
</body>
</html>
