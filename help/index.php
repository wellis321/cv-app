<?php
require_once __DIR__ . '/../php/helpers.php';

$pageTitle = 'Help & Support';
$metaDescription = 'FAQ, setup guides, and support for Simple CV Builder. Get answers and learn how to use AI features, the browser extension, and more.';
$canonicalUrl = APP_URL . '/help/';

$sections = [
    [
        'title' => 'FAQ',
        'description' => 'Frequently asked questions about Simple CV Builder.',
        'href' => '/help/faq.php',
        'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    [
        'title' => 'Ollama Setup',
        'description' => 'Set up local AI with Ollama for CV rewriting and assessment.',
        'href' => '/help/setup/ollama.php',
        'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    ],
    [
        'title' => 'Browser Extension Setup',
        'description' => 'Install and use the Save Job browser extension.',
        'href' => '/help/setup/extension.php',
        'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
    ],
    [
        'title' => 'AI Prompt Best Practices',
        'description' => 'Learn how to write effective prompts for AI CV rewriting.',
        'href' => '/help/guides/ai-prompts.php',
        'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
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
                <span class="inline-flex items-center rounded-full bg-white/20 px-4 py-1 text-sm font-medium">Help</span>
                <h1 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="mt-4 text-lg text-slate-200">
                    Find answers, setup guides, and support for Simple CV Builder.
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2">
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
                        Open
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
