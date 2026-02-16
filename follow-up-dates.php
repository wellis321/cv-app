<?php
/**
 * Follow-Up & Closing Dates – feature page
 * Describes setting follow-up dates and closing date reminders for job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Follow-Up & Closing Dates';
$canonicalUrl = APP_URL . '/follow-up-dates.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Set follow-up and closing dates for job applications. Get reminders before deadlines so you never miss an application window or forget to follow up.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-r from-amber-600 via-orange-600 to-red-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-amber-600/90 via-orange-600/90 to-red-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">Never miss a deadline</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Follow-Up & Closing Dates
                </h1>
                <p class="mt-6 text-xl text-orange-50 max-w-2xl mx-auto leading-relaxed">
                    Set a follow-up date to check in with employers, or a closing date for application deadlines. <strong class="text-white">Get reminders before important dates</strong> so you never miss an opportunity.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-orange-600 shadow-lg hover:bg-orange-50 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-orange-600 shadow-lg hover:bg-orange-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        How it works
                    </a>
                </div>
            </div>
        </section>

        <!-- Two Types -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Two types of dates
                </h2>
                <div class="grid gap-8 md:grid-cols-2">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border-2 border-blue-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Follow-Up Date</h3>
                        </div>
                        <p class="text-gray-700 mb-4">Set a date to check in with the employer or recruiter. Perfect for:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Following up after submitting an application</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Checking in after an interview</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Reminding yourself to send thank-you notes</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl border-2 border-red-200 p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-red-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Closing Date</h3>
                        </div>
                        <p class="text-gray-700 mb-4">Set the application deadline. You'll get a reminder before it closes:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Never miss an application deadline</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Plan your time to complete applications</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>See upcoming deadlines at a glance</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How it works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-orange-100 text-orange-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Set a date when adding or editing</h3>
                            <p class="mt-3 text-gray-600">
                                When you add a new job application or edit an existing one, you can set either a follow-up date or closing date (or both). Use the date picker to choose the date and time.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/follow-up/set-a-date.png" aria-label="View set date image larger">
                                <img src="/static/images/follow-up/set-a-date.png" alt="Set follow-up or closing date - Application Date and Follow-up date fields with date picker" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-orange-100 text-orange-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Get browser notifications</h3>
                            <p class="mt-3 text-gray-600">
                                When you visit your dashboard or job list, you'll automatically receive browser notifications for upcoming closing dates. These reminders appear 7 days, 3 days, and 1 day before important deadlines—so you never miss an application window. Notifications only show once per day per job, keeping you informed without being overwhelming.
                            </p>
                            <p class="mt-3 text-sm text-gray-500">
                                You'll also see upcoming follow-up and closing dates highlighted in your job list, so you can see what needs attention at a glance.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/follow-up/browser-notification.png" aria-label="View browser notification image larger">
                                <img src="/static/images/follow-up/browser-notification.png" alt="Job application closing date reminders - enable browser notifications and set reminder days" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-orange-100 text-orange-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">View all upcoming dates</h3>
                            <p class="mt-3 text-gray-600">
                                Your dashboard and job list show upcoming follow-up and closing dates at a glance. Filter by date range or status to focus on what needs attention now.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/follow-up/view-dates.png" aria-label="View upcoming dates image larger">
                                <img src="/static/images/follow-up/view-dates.png" alt="Job list with upcoming dates - cards showing job titles, organisations and dates" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover" width="600" height="340" />
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
                    Never miss an opportunity
                </h2>
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="text-center p-6">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay on top of deadlines</h3>
                        <p class="text-sm text-gray-600">Set closing dates and get reminders so you never miss an application window.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Plan your follow-ups</h3>
                        <p class="text-sm text-gray-600">Schedule when to check in with employers and never forget to follow up after interviews.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">See what's urgent</h3>
                        <p class="text-sm text-gray-600">Your dashboard highlights upcoming dates so you know what needs attention first.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customise Reminders Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Customise Your Reminders
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                        You have full control over when you receive closing date reminders. Customise the timing to match your workflow.
                    </p>
                </div>
                <div class="bg-white rounded-xl border-2 border-gray-200 shadow-lg p-8 md:p-10">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Set Your Preferences</h3>
                            <p class="text-gray-600 mb-6">
                                Go to your <?php if (isLoggedIn()): ?><a href="/profile.php" class="text-blue-600 hover:underline font-medium">Profile settings</a><?php else: ?><button type="button" data-open-login data-redirect="/profile.php" class="text-blue-600 hover:underline font-medium bg-transparent border-0 p-0 cursor-pointer">Profile settings</button><?php endif; ?> and open the <strong>Reminders</strong> tab to customise your notification preferences.
                            </p>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Enable or disable reminders</h4>
                                        <p class="text-sm text-gray-600">Toggle browser notifications on or off with a single checkbox.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Choose preset days</h4>
                                        <p class="text-sm text-gray-600">Select from preset options: 14 days, 7 days, 3 days, or 1 day before closing dates.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Add custom days</h4>
                                        <p class="text-sm text-gray-600">Enter your own custom reminder days (comma-separated) for complete flexibility.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <?php if (isLoggedIn()): ?>
                                    <a href="/profile.php" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                        Go to Profile Settings →
                                    </a>
                                <?php else: ?>
                                    <button type="button" data-open-login data-redirect="/profile.php" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                                        Log in to Profile Settings →
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                            <p class="text-sm text-gray-600 mb-4 font-medium">Default settings:</p>
                            <div class="space-y-2">
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Reminder days</p>
                                    <p class="text-sm font-semibold text-gray-900">7, 3, and 1 day before</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Notifications</p>
                                    <p class="text-sm font-semibold text-gray-900">Enabled by default</p>
                                </div>
                            </div>
                            <p class="mt-4 text-xs text-gray-500">You can change these anytime in your Profile settings</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Browser Notifications Detail -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-200 shadow-xl p-10 md:p-12">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-500 text-white">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                <h2 class="text-3xl font-bold text-gray-900">Automatic Browser Notifications</h2>
                            </div>
                            <p class="text-lg text-gray-700 mb-6">
                                Never miss a deadline with automatic browser notifications. When you visit your dashboard or job list, you'll receive notifications for upcoming closing dates. By default, you'll get reminders 7 days, 3 days, and 1 day before each deadline, but you can customise these in your <?php if (isLoggedIn()): ?><a href="/profile.php" class="text-blue-600 hover:underline font-medium">Profile settings</a><?php else: ?><button type="button" data-open-login data-redirect="/profile.php" class="text-blue-600 hover:underline font-medium bg-transparent border-0 p-0 cursor-pointer">Profile settings</button><?php endif; ?>.
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
                                <p class="text-sm text-green-800 font-medium mb-2">Customise your reminders</p>
                                <p class="text-xs text-green-700">You can customise when you receive reminders in your <?php if (isLoggedIn()): ?><a href="/profile.php" class="underline font-medium">Profile settings</a><?php else: ?><button type="button" data-open-login data-redirect="/profile.php" class="underline font-medium bg-transparent border-0 p-0 text-green-700 cursor-pointer">Profile settings</button><?php endif; ?>. Choose from preset options (14, 7, 3, or 1 day before) or add your own custom days. Default is 7, 3, and 1 day before closing dates.</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-8 border-2 border-blue-200 shadow-lg">
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
        <section class="py-16 bg-gradient-to-r from-orange-600 via-red-600 to-orange-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start using dates to stay organised
                </h2>
                <p class="mt-4 text-orange-50 max-w-xl mx-auto">
                    Follow-up and closing dates with automatic browser notifications are included with every account. Set your first date and never miss an opportunity again.
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
    <?php partial('image-lightbox'); ?>
</body>
</html>
