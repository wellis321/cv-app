<?php
require_once __DIR__ . '/../php/helpers.php';

$pageTitle = 'Free CV & Job Guides | Career Advice & Resources';
$metaDescription = 'Free job search guides, CV tips, career advice, and extra income ideas for UK job seekers. Learn how to update your CV, use AI in applications, and land your next role.';
$canonicalUrl = APP_URL . '/resources/';

$sections = [
    [
        'title' => 'Job Search & CV Tips',
        'description' => 'Guides to help you stand out in applications and land your next role.',
        'href' => '/resources/jobs/',
        'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    ],
    [
        'title' => 'Career Advice Hub',
        'description' => 'CV updates, ATS keywords, and planning tools for every stage of your career.',
        'href' => '/resources/career/',
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ],
    [
        'title' => 'Extra Income Ideas',
        'description' => 'Legitimate ways to earn money online and from home. No scams.',
        'href' => '/resources/extra-income/',
        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    [
        'title' => 'Passive Income Ideas',
        'description' => 'Strategies to build income streams alongside your main career.',
        'href' => '/resources/passive-income/',
        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
    ],
    [
        'title' => 'AI Setup Guide',
        'description' => 'Set up Ollama and use AI for CVs and cover letters.',
        'href' => '/resources/ai/setup-ollama.php',
        'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => $metaDescription,
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-slate-50 text-slate-900">
<?php partial('header'); ?>

<main>
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-3xl">
                <span class="inline-flex items-center rounded-full bg-white/20 px-4 py-1 text-sm font-medium">Resources</span>
                <h1 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="mt-4 text-lg text-slate-200">
                    Free guides, CV tips, career advice, and job search insights. Everything you need to build a standout CV and land your next role.
                </p>
                <a href="#sections" class="mt-8 inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow hover:bg-slate-100">
                    Browse all resources
                </a>
            </div>
        </div>
    </section>

    <section id="sections" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 scroll-mt-24">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($sections as $section): ?>
            <a href="<?php echo e($section['href']); ?>" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg">
                <div class="flex-shrink-0 rounded-xl bg-indigo-50 p-3 group-hover:bg-indigo-100 transition-colors">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($section['icon']); ?>"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-semibold text-slate-900 group-hover:text-indigo-600"><?php echo e($section['title']); ?></h2>
                    <p class="mt-2 text-sm text-slate-600"><?php echo e($section['description']); ?></p>
                    <span class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-indigo-600">
                        Explore
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <?php partial('resources-footer-cta'); ?>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
