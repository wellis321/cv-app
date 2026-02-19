<?php
require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'How to Refresh Your CV in 30 Minutes';
$metaDescription = 'Update your CV fast. Free 30-minute CV refresh checklist—improve your summary, skills, and experience. CV tips for UK job seekers.';
$canonicalUrl = APP_URL . '/blog/job-search/how-to-refresh-your-cv-in-30-minutes.php';
$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL . '/'],
    ['name' => 'Blog', 'url' => APP_URL . '/blog/'],
    ['name' => 'Job Search', 'url' => APP_URL . '/blog/job-search/'],
    ['name' => $pageTitle, 'url' => $canonicalUrl],
];

$sections = [
    [
        'id' => 'professional-summary',
        'title' => 'Refresh Your Professional Summary',
        'content' => [
            ['type' => 'paragraph', 'text' => 'Start with your professional summary at the top of your CV. This opening paragraph is prime real estate that recruiters read first when reviewing job applications, so it needs to accurately reflect where you are now in your career.'],
            ['type' => 'paragraph', 'text' => 'Update this section to include:'],
            ['type' => 'list', 'items' => [
                'Your current experience level and years in the industry',
                'Recently acquired skills that are in demand',
                'New certifications or qualifications',
                'Updated metrics about your scope of responsibility',
            ]],
            ['type' => 'paragraph', 'text' => 'This takes just a few minutes but significantly impacts how recruiters perceive your current capabilities and career progression.'],
        ],
    ],
    [
        'id' => 'key-skills',
        'title' => 'Revise Your Key Skills Section',
        'content' => [
            ['type' => 'paragraph', 'text' => 'If your CV includes a skills section (and it should), this is one of the fastest areas to update when improving your CV. This bulleted list provides recruiters with an at-a-glance view of your capabilities.'],
            ['type' => 'paragraph', 'text' => 'Make these quick adjustments:'],
            ['type' => 'list', 'items' => [
                'Add technical skills you\'ve developed in recent roles',
                'Include skills mentioned in job descriptions you\'re targeting',
                'Remove outdated skills that no longer apply to your career direction',
                'Prioritise skills that align with your next career move',
            ]],
            ['type' => 'paragraph', 'text' => 'If you have a specific role in mind, review the job posting and mirror relevant skills that you genuinely possess.'],
        ],
    ],
    [
        'id' => 'recent-position',
        'title' => 'Add Your Most Recent Position',
        'content' => [
            ['type' => 'paragraph', 'text' => 'Your current or latest role receives the most attention from recruiters, making this section critical to update. While you might normally spend considerable time perfecting this, focus on essential details when time is limited.'],
            ['type' => 'paragraph', 'text' => 'Include these elements:'],
            ['type' => 'list', 'items' => [
                'A brief overview of your role and the company',
                'Three to five key responsibilities that relate to your target positions',
                'Quantifiable achievements that demonstrate your impact',
                'Projects or initiatives relevant to jobs you\'re pursuing',
            ]],
            ['type' => 'paragraph', 'text' => 'Use specific numbers and metrics where possible, such as revenue increased, costs saved, or team members managed, to show tangible results.'],
        ],
    ],
    [
        'id' => 'remove-outdated',
        'title' => 'Remove Outdated Information',
        'content' => [
            ['type' => 'paragraph', 'text' => 'Creating space for new, relevant content often means trimming the old. This editing process is quick and makes a significant difference.'],
            ['type' => 'paragraph', 'text' => 'Consider reducing or removing:'],
            ['type' => 'list', 'items' => [
                'Early career positions from 10+ years ago (keep them brief or remove entirely)',
                'Outdated technical skills or software you no longer use',
                'Hobbies and interests that don\'t relate to your career goals',
                'The references line — these can be provided upon request',
            ]],
            ['type' => 'paragraph', 'text' => 'Aim to keep your CV to two pages maximum. This length is professional, respects recruiters\' time, and forces you to prioritise your most impressive achievements.'],
        ],
    ],
    [
        'id' => 'professional-development',
        'title' => 'Highlight Recent Professional Development',
        'content' => [
            ['type' => 'paragraph', 'text' => 'If you\'ve taken courses, earned certifications, or developed new skills recently, these additions demonstrate your commitment to continuous learning.'],
            ['type' => 'paragraph', 'text' => 'Be sure to add:'],
            ['type' => 'list', 'items' => [
                'Online courses or certifications completed',
                'Industry-relevant side projects or freelance work',
                'Volunteer work that showcases transferable skills',
                'Professional development activities related to your target industry',
            ]],
            ['type' => 'paragraph', 'text' => 'This is especially important if you experienced a career gap and want to show how you invested in your professional growth during that time.'],
        ],
    ],
    [
        'id' => 'final-checks',
        'title' => 'Final Quick Checks',
        'content' => [
            ['type' => 'paragraph', 'text' => 'Before sending your refreshed CV, take five minutes for these essential checks:'],
            ['type' => 'list', 'items' => [
                'Verify all dates are current and accurate',
                'Ensure contact information is up to date',
                'Confirm your LinkedIn profile URL is included',
                'Run a quick spell-check',
                'Give the file a professional name (e.g., "Priya_Patel_CV.pdf")',
            ]],
        ],
    ],
    [
        'id' => 'strategic-updates',
        'title' => 'The Power of Strategic Updates',
        'content' => [
            ['type' => 'paragraph', 'text' => 'A complete CV overhaul isn\'t always necessary—especially when opportunities arise quickly. By focusing on these high-impact areas you can create a compelling, current CV in just 30 minutes. These CV tips work whether you\'re applying for a new role or simply keeping your resume up to date.'],
            ['type' => 'paragraph', 'text' => 'These targeted updates ensure your CV accurately represents your current capabilities while highlighting the skills and experiences most relevant to your target roles. Sometimes, it\'s these strategic refinements that make the difference between landing an interview and being passed over.'],
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
        'metaImage' => APP_URL . '/static/images/resources-images/Proofreader.png',
        'metaKeywords' => 'CV refresh, update CV, CV tips, CV checklist, job applications, UK job seeker, professional summary, CV skills, recruiters',
        'breadcrumbs' => $breadcrumbs,
        'structuredDataType' => 'article',
        'structuredData' => [
            'title' => $pageTitle,
            'description' => $metaDescription,
            'url' => $canonicalUrl,
            'image' => APP_URL . '/static/images/resources-images/Proofreader.png',
            'datePublished' => '2025-01-01',
            'dateModified' => date('Y-m-d'),
        ],
    ]); ?>
</head>
<body class="bg-slate-50 text-slate-900">
<?php partial('header'); ?>

<main>
    <article class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[80%] rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="absolute -bottom-32 right-0 h-64 w-64 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <header class="space-y-8">
                <div class="inline-flex items-center rounded-full border border-white/20 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white/80">
                    CV refresh workflow
                </div>
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="text-lg text-slate-200 leading-relaxed max-w-3xl">
                    A recruiter calls with an exciting opportunity and needs your CV today. Instead of scrambling, use this 30-minute CV refresh checklist to sharpen the sections that matter most—professional summary, skills, experience—and respond with confidence.
                </p>
                <nav aria-label="Page sections" class="rounded-xl border border-white/20 bg-white/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-white/70 mb-3">Jump to a section</p>
                    <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                        <li><a href="#professional-summary" class="text-white/90 hover:text-white underline underline-offset-2">Professional summary</a></li>
                        <li><a href="#key-skills" class="text-white/90 hover:text-white underline underline-offset-2">Key skills</a></li>
                        <li><a href="#recent-position" class="text-white/90 hover:text-white underline underline-offset-2">Recent position</a></li>
                        <li><a href="#remove-outdated" class="text-white/90 hover:text-white underline underline-offset-2">Remove outdated</a></li>
                        <li><a href="#professional-development" class="text-white/90 hover:text-white underline underline-offset-2">Professional development</a></li>
                        <li><a href="#final-checks" class="text-white/90 hover:text-white underline underline-offset-2">Final checks</a></li>
                    </ul>
                </nav>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/blog/cv-tips/how-to-update-your-cv.php" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow hover:bg-slate-100">
                        Need the full deep-dive?
                    </a>
                    <a href="#professional-summary" class="inline-flex items-center justify-center rounded-lg border border-white/40 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10">
                        Jump to checklist
                    </a>
                </div>
            </header>
        </div>
    </article>

    <section id="summary" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-900/5 overflow-hidden">
            <img src="/static/images/resources-images/Proofreader.png" alt="Proofreader reviewing documents - updating your CV" class="w-full h-56 object-cover rounded-xl -mx-8 -mt-8 mb-8" width="800" height="320">
            <p class="text-base leading-relaxed text-slate-600">
                When time is tight, focus on CV updates that dial up clarity, relevance, and proof of impact. Whether you're preparing for job applications or a recruiter has just reached out, the sections below are ordered by the influence they have on hiring decisions—start at the top and work down until the clock runs out.
            </p>
        </div>

        <?php foreach ($sections as $i => $section): ?>
            <?php if ($i === 1): ?>
            <img src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&q=80" alt="Professional workspace" class="w-full rounded-xl object-cover h-64 shadow-lg" width="800" height="320">
            <?php endif; ?>
            <section id="<?php echo e($section['id'] ?? ''); ?>" class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
                <h2 class="text-2xl font-semibold text-slate-900"><?php echo e($section['title']); ?></h2>
                <div class="mt-4 space-y-4 text-base leading-relaxed text-slate-600">
                    <?php foreach ($section['content'] as $block): ?>
                        <?php if ($block['type'] === 'paragraph'): ?>
                            <p><?php echo e($block['text']); ?></p>
                        <?php elseif ($block['type'] === 'list'): ?>
                            <ul class="list-disc space-y-2 pl-5">
                                <?php foreach ($block['items'] as $item): ?>
                                    <li><?php echo e($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php if ($i === 2): ?>
            <?php partial('blog-cta-inline', ['heading' => 'Ready to apply these updates?', 'subtext' => 'Build or refresh your CV with our free tool.']); ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg">
            <h2 class="text-xl font-semibold text-slate-900">Other Articles You Might Like</h2>
            <p class="mt-2 text-slate-600 mb-6">More CV and job search advice.</p>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                <a href="/blog/cv-tips/how-to-update-your-cv.php" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400">How to Update Your CV →</a>
                <a href="/blog/job-search/remote-jobs-begginers.php" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400">11 Simple Work From Home Jobs →</a>
                <a href="/blog/career/legitimate-ways-to-earn-money-online.php" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400">Ways to Earn Money Online →</a>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-900 bg-slate-950 p-8 text-slate-100 shadow-xl">
            <h2 class="text-2xl font-semibold text-white">Ready to put this checklist to work?</h2>
            <p class="mt-4 text-base leading-relaxed text-slate-100/80">
                Build or refresh your CV in minutes with Simple CV Builder. Duplicate sections, drop in quantified wins, and export polished PDFs without wrestling with layout.
            </p>
            <div class="mt-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                <a href="/#pricing" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-400">Create your CV free →</a>
                <a href="/blog/cv-tips/how-to-update-your-cv.php" class="inline-flex items-center justify-center rounded-lg border border-white/40 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Full CV update guide →</a>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
            <p class="text-xs text-slate-500">Last updated: 10 February 2025.</p>
        </section>
    </section>

    <?php partial('resources-footer-cta'); ?>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
