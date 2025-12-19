<?php
require_once __DIR__ . '/../php/helpers.php';

$pageTitle = 'Resources';
$canonicalUrl = APP_URL . '/resources/';

// Get all articles with content
$allArticles = getAllArticles();

// Add the healthcare careers article (not in getAllArticles yet)
$allArticles[] = [
    'title' => 'Healthcare Career Paths: Start Your Journey Without a Degree',
    'url' => '/resources/jobs/entry-level-healthcare-careers.php',
    'excerpt' => 'Discover rewarding healthcare support roles you can start without a university degree. Learn about entry-level positions, training requirements, salaries, and how to begin your healthcare career today.',
    'category' => 'jobs',
    'section' => 'Popular Job Paths',
];

// Organize articles by category
$articlesByCategory = [
    'jobs' => [
        'title' => 'Job Market Insights',
        'description' => 'Stay ahead of hiring trends with curated job search guides and practical advice.',
        'color' => 'blue',
        'articles' => [],
    ],
    'career' => [
        'title' => 'Career Advice Hub',
        'description' => 'Career planning tools, CV tips, and professional development guidance.',
        'color' => 'indigo',
        'articles' => [],
    ],
    'extra-income' => [
        'title' => 'Extra Income Ideas',
        'description' => 'Practical, flexible extra-income ideas you can start this week.',
        'color' => 'orange',
        'articles' => [],
    ],
];

// Sort articles into categories
foreach ($allArticles as $article) {
    if (isset($articlesByCategory[$article['category']])) {
        $articlesByCategory[$article['category']]['articles'][] = $article;
    }
}

// Remove empty categories
$articlesByCategory = array_filter($articlesByCategory, function($category) {
    return !empty($category['articles']);
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Explore our collection of job search guides, career advice, CV tips, and extra income ideas to help you advance your career.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
<?php partial('header'); ?>

<main class="bg-gray-50">
    <section class="bg-gradient-to-br from-blue-600 via-indigo-500 to-purple-600 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-3xl">
                <span class="inline-flex items-center rounded-full bg-white/20 px-4 py-1 text-sm font-medium">Resources</span>
                <h1 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="mt-4 text-lg text-blue-100">
                    Explore our collection of guides, templates, and practical advice to help you advance your career, find opportunities, and build your professional skills.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-blue-600 shadow hover:bg-blue-50">
                        Get unlimited CV sections
                    </a>
                    <a href="#articles" class="inline-flex items-center justify-center rounded-lg border border-white/60 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10">
                        Browse articles
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="articles" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <?php
        $colorMap = [
            'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-200', 'hover' => 'hover:bg-blue-50', 'gradient' => 'from-blue-50'],
            'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'hover' => 'hover:bg-indigo-50', 'gradient' => 'from-indigo-50'],
            'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'border' => 'border-orange-200', 'hover' => 'hover:bg-orange-50', 'gradient' => 'from-orange-50'],
        ];

        foreach ($articlesByCategory as $categoryKey => $category):
            $colors = $colorMap[$category['color']] ?? $colorMap['blue'];
        ?>
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo e($category['title']); ?></h2>
                    <p class="mt-2 text-gray-500 max-w-3xl"><?php echo e($category['description']); ?></p>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($category['articles'] as $article): ?>
                        <article class="group relative overflow-hidden rounded-2xl border <?php echo e($colors['border']); ?> bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="absolute inset-0 bg-gradient-to-br <?php echo e($colors['gradient']); ?> via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></div>
                            <div class="relative">
                                <div class="inline-flex items-center rounded-full <?php echo e($colors['bg']); ?> px-3 py-1 text-xs font-semibold uppercase tracking-wide <?php echo e($colors['text']); ?>">
                                    <?php
                                    if ($categoryKey === 'jobs') {
                                        echo 'Guide';
                                    } elseif ($categoryKey === 'career') {
                                        echo 'Toolkit';
                                    } else {
                                        echo 'Extra cash';
                                    }
                                    ?>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-gray-900">
                                    <?php echo e($article['title']); ?>
                                </h3>
                                <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                                    <?php echo e($article['excerpt']); ?>
                                </p>
                                <a href="<?php echo e($article['url']); ?>" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold <?php echo e($colors['text']); ?> hover:opacity-80">
                                    Read full guide
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
