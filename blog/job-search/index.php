<?php
require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'Job Search Advice UK | CV Tips & Career Guides';
$metaDescription = 'Free job search guides, CV tips, and career advice for UK job seekers. Learn how to update your CV, use AI in applications, pass ATS, and land your next role.';
$canonicalUrl = APP_URL . '/blog/job-search/';
$sections = [
    [
        'title' => '',
        'description' => '',
        'articles' => [
            [
                'title' => '11 Remote Jobs Perfect for Beginners in 2026',
                'excerpt' => 'Curated list of flexible remote roles ranging from customer success to data analysis.',
                'href' => '/blog/job-search/remote-jobs-begginers.php',
                'cta' => 'Read full guide',
                'badge' => 'Guide',
            ],
            [
                'title' => 'Using AI in Your Job Applications: A Practical Guide',
                'excerpt' => 'Harness tools like ChatGPT responsibly for CVs, cover letters, and interviews without losing authenticity.',
                'href' => '/blog/job-search/using-ai-in-job-applications.php',
                'cta' => 'Read full guide',
                'badge' => 'Guide',
            ],
            [
                'title' => 'Six Steps to Refreshing Your CV in 30 Minutes',
                'excerpt' => 'Quick wins to modernise your CV layout, keywords, and story.',
                'href' => '/blog/job-search/how-to-refresh-your-cv-in-30-minutes.php',
                'cta' => 'Read full guide',
                'badge' => 'Guide',
            ],
            [
                'title' => 'AI Prompt Cheat Sheet',
                'excerpt' => 'Ready-to-use AI prompts for job applications, CVs, and cover letters.',
                'href' => '/blog/job-search/ai-prompt-cheat-sheet.php',
                'cta' => 'Read full guide',
                'badge' => 'Guide',
            ],
            [
                'title' => 'Healthcare Career Paths: Start Your Journey Without a Degree',
                'excerpt' => 'Discover rewarding healthcare support roles you can start without a university degree.',
                'href' => '/blog/job-search/entry-level-healthcare-careers.php',
                'cta' => 'Read full guide',
                'badge' => 'Guide',
            ],
            [
                'title' => 'How to Update Your CV: A Complete Guide',
                'excerpt' => 'Step-by-step advice for refreshing every section of your CV whenever opportunity knocks.',
                'href' => '/blog/cv-tips/how-to-update-your-cv.php',
                'cta' => 'Read full guide',
                'badge' => 'CV Tips',
            ],
            [
                'title' => 'CV Update Checklist',
                'excerpt' => 'A comprehensive printable checklist to systematically update your CV and ensure nothing is missed.',
                'href' => '/blog/cv-tips/cv-update-checklist.php',
                'cta' => 'Read full guide',
                'badge' => 'CV Tips',
            ],
            [
                'title' => 'CV Keywords and ATS: A Complete Guide',
                'excerpt' => 'Learn how Applicant Tracking Systems work and how to optimise your CV to pass ATS screening.',
                'href' => '/blog/cv-tips/keywords-and-ats-guide.php',
                'cta' => 'Read full guide',
                'badge' => 'Guide',
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
<body class="bg-gray-50">
<?php partial('header'); ?>

<main class="bg-gray-50">
    <section class="bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-3xl">
                <a href="/blog/" class="inline-flex items-center gap-1 text-sm text-blue-100 hover:text-white mb-4">← Back to Blog</a>
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="mt-4 text-lg text-blue-100">
                    Stay ahead of hiring trends with curated guides, templates, and practical advice.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/#pricing" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-blue-600 shadow hover:bg-blue-50">
                        Get unlimited CV sections
                    </a>
                    <a href="#sections" class="inline-flex items-center justify-center rounded-lg border border-white/60 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10" data-smooth-scroll>
                        Browse topics
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="sections" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16 scroll-mt-32">
        <?php foreach ($sections as $section): ?>
            <div class="space-y-6">
                <?php if (!empty($section['title']) || !empty($section['description'])): ?>
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <?php if (!empty($section['title'])): ?>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo e($section['title']); ?></h2>
                        <?php endif; ?>
                        <?php if (!empty($section['description'])): ?>
                        <p class="mt-2 text-gray-500 max-w-3xl"><?php echo e($section['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <a href="/blog/" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                        View all guides
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($section['articles'] as $article): ?>
                        <article class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></div>
                            <div class="relative">
                                <?php if (!empty($article['badge'])): ?>
                                <div class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    <?php echo e($article['badge']); ?>
                                </div>
                                <?php endif; ?>
                                <h3 class="<?php echo !empty($article['badge']) ? 'mt-4' : ''; ?> text-lg font-semibold text-gray-900">
                                    <?php echo e($article['title']); ?>
                                </h3>
                                <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                                    <?php echo e($article['excerpt']); ?>
                                </p>
                                <a href="<?php echo e($article['href']); ?>" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
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

<script>
    document.querySelectorAll('a[data-smooth-scroll], a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const headerOffset = 100;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                }
            }
        });
    });
</script>
</body>
</html>
