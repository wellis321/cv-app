<?php
/**
 * Track Your Progress – feature page
 * Describes statistics and progress tracking for job applications.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Track Your Progress';
$canonicalUrl = APP_URL . '/track-progress.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Visualise your job search progress with statistics and insights. See total applications, counts by status, and upcoming follow-up dates at a glance.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d128efb9b3fb?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-indigo-900/80 to-blue-900/80" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-blue-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">Job applications</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Track Your Progress
                </h1>
                <p class="mt-6 text-xl text-blue-100 max-w-2xl mx-auto leading-relaxed">
                    Visualise your job search progress with <strong class="text-white">statistics and insights</strong> into your applications. See totals, status breakdowns, and upcoming dates at a glance.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                            Open job list
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#statistics" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        View statistics
                    </a>
                </div>
            </div>
        </section>

        <!-- Why Track Progress -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why track your progress?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Understanding your job search patterns helps you make better decisions, stay motivated, and improve your approach over time.
                    </p>
                </div>

                <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                    <div class="flex flex-col">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">See the big picture</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            When you're applying to multiple jobs, it's easy to lose sight of how you're doing overall. Are you getting interviews? Are applications progressing? Statistics give you a clear view of your entire job search at a glance.
                        </p>
                        <p class="text-gray-600 leading-relaxed">
                            See your total number of applications, how many are at each stage, and which ones are moving forward. This helps you understand whether you need to apply to more roles, improve your applications, or simply be patient while employers review your submissions.
                        </p>
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/all-in-one/seamless-workflow.png" aria-label="View seamless workflow dashboard image larger">
                            <img src="/static/images/all-in-one/seamless-workflow.png" alt="See the big picture - dashboard with Build My CV, Manage Jobs, CV Quality Assess and more" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover aspect-video" width="800" height="450" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Overview -->
        <section id="statistics" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        What statistics show you
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Your job application tracker provides clear insights into your job search progress, helping you understand what's working and what might need adjustment.
                    </p>
                </div>

                <div class="space-y-20">
                    <!-- Total applications -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="lg:order-2 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Total applications</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                See at a glance how many applications you've submitted. This number helps you understand the scale of your job search and whether you're applying to enough roles. Most job seekers need to apply to many positions before landing interviews, so tracking your total helps set realistic expectations.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                As your total grows, you'll also see patterns emerge. Perhaps you're applying to too many roles without tailoring your applications, or maybe you need to cast a wider net. The total gives you context for all your other statistics.
                            </p>
                        </div>
                        <div class="lg:order-1 flex items-center">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/search-filter/search.png" aria-label="View job list image larger">
                                <img src="/static/images/search-filter/search.png" alt="Job list with search, filters and application cards" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover" width="800" height="450" />
                            </button>
                        </div>
                    </div>

                    <!-- Status breakdown -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Counts by status</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                See exactly how many applications are at each stage: Interested, In Progress, Applied, Interviewing, Offered, Accepted, Rejected, or Withdrawn. This breakdown shows you where your applications are in the pipeline and helps you identify bottlenecks.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                If you have many applications stuck at "Applied" with no movement to "Interviewing", it might be time to review your CV or application approach. If you're getting interviews but not offers, you might need to work on your interview skills. The status breakdown gives you actionable insights.
                            </p>
                        </div>
                        <div class="flex items-center">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/search-filter/filter-status.png" aria-label="View status bar image larger">
                                <img src="/static/images/search-filter/filter-status.png" alt="Status bar with counts for Applied, Interviewing, Offered and more" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover" width="800" height="450" />
                            </button>
                        </div>
                    </div>

                    <!-- Upcoming dates -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="lg:order-2 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Upcoming follow-up dates</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                See which applications have follow-up dates or closing dates approaching. This helps you prioritise your time and ensures you never miss an important deadline or follow-up opportunity. You can see what needs attention today, this week, or next week.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Upcoming dates are displayed prominently in your job list, so you can plan your week effectively. Instead of scrambling to remember when you should follow up, everything is organised and visible, helping you stay proactive in your job search.
                            </p>
                        </div>
                        <div class="lg:order-1 flex items-center">
                            <button type="button" class="w-full text-left cursor-zoom-in hover:opacity-95 transition-opacity rounded-xl overflow-hidden" data-image-lightbox="/static/images/follow-up/view-dates.png" aria-label="View upcoming dates image larger">
                                <img src="/static/images/follow-up/view-dates.png" alt="Job cards with follow-up dates and deadline indicators" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover" width="800" height="450" />
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
                    How progress tracking works
                </h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-lg">1</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Statistics update automatically</h3>
                            <p class="text-gray-600 leading-relaxed">As you add applications and update their statuses, your statistics update automatically. There's no manual calculation needed—just use the tracker normally, and your progress is always visible.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-lg">2</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">View statistics in your job list</h3>
                            <p class="text-gray-600 leading-relaxed">Your job list displays key statistics at the top: total applications, counts by status, and upcoming follow-up dates. Everything you need to understand your progress is right there, updated in real time as you manage your applications.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-lg">3</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Use insights to improve</h3>
                            <p class="text-gray-600 leading-relaxed">Review your statistics regularly to identify patterns and areas for improvement. Are applications getting stuck at a certain stage? Are you getting interviews but not offers? Use these insights to adjust your approach and improve your success rate over time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-blue-600 to-indigo-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Start tracking your progress today
                </h2>
                <p class="mt-4 text-blue-100 max-w-xl mx-auto">
                    Progress tracking is included with every account. Add your first application and watch your statistics grow as you build your job search.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Open job list
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
    <?php partial('image-lightbox'); ?>
</body>
</html>
