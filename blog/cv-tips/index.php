<?php
require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'CV Tips & Writing Guides';
$metaDescription = 'CV writing guides, ATS optimisation, and update checklists. Learn how to make your CV stand out and pass applicant tracking systems.';
$canonicalUrl = APP_URL . '/blog/cv-tips/';
$sections = [
    [
        'title' => 'CVs, Updates & ATS',
        'description' => 'Support to help you stand out through every stage of the hiring process.',
        'articles' => [
            [
                'title' => 'How to Update Your CV: A Complete Guide',
                'excerpt' => 'Step-by-step advice for refreshing every section of your CV whenever opportunity knocks.',
                'href' => '/blog/cv-tips/how-to-update-your-cv.php',
                'cta' => 'Read full guide',
            ],
            [
                'title' => 'CV Update Checklist',
                'excerpt' => 'A comprehensive printable checklist to systematically update your CV and ensure nothing is missed.',
                'href' => '/blog/cv-tips/cv-update-checklist.php',
                'cta' => 'View checklist',
            ],
            [
                'title' => 'CV Keywords and ATS: A Complete Guide',
                'excerpt' => 'Learn how Applicant Tracking Systems work, why keywords matter, and how to optimise your CV to pass ATS screening.',
                'href' => '/blog/cv-tips/keywords-and-ats-guide.php',
                'cta' => 'Read guide',
            ],
        ],
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
<body class="bg-slate-50 text-gray-800">
<?php partial('header'); ?>

<main>
    <section class="bg-gradient-to-br from-violet-600 via-indigo-500 to-blue-500 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-3xl">
                <a href="/blog/" class="inline-flex items-center gap-1 text-sm text-white/80 hover:text-white mb-4">← Back to Blog</a>
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="mt-4 text-lg text-indigo-100">
                    Build confidence at every stage of your career. These guides cover CV updates, ATS optimisation, and more.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/blog/job-search/" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-indigo-600 shadow hover:bg-indigo-50">
                        Explore job search guides
                    </a>
                    <a href="#guides" class="inline-flex items-center justify-center rounded-lg border border-white/60 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10">
                        Browse guides
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="guides" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <?php foreach ($sections as $section): ?>
            <div class="space-y-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo e($section['title']); ?></h2>
                        <p class="mt-2 text-gray-500 max-w-3xl"><?php echo e($section['description']); ?></p>
                    </div>
                    <a href="/blog/" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                        View all articles
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($section['articles'] as $article): ?>
                        <article class="group relative overflow-hidden rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></div>
                            <div class="relative">
                                <div class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                    CV Tips
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-gray-900">
                                    <?php echo e($article['title']); ?>
                                </h3>
                                <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                                    <?php echo e($article['excerpt']); ?>
                                </p>
                                <a href="<?php echo e($article['href']); ?>" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                                    <?php echo e($article['cta'] ?? 'Read guide'); ?>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <?php partial('resources-footer-cta'); ?>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
