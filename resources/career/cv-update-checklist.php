<?php
require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'CV Update Checklist | Free Printable';
$metaDescription = 'Free CV update checklist for UK job seekers. Systematic guide to refreshing your CV. Printable, nothing missed.';
$canonicalUrl = APP_URL . '/resources/career/cv-update-checklist.php';

$checklistItems = [
    [
        'category' => 'Contact Information',
        'items' => [
            'Phone number is current and professional',
            'Email address is up to date and professional',
            'Location is accurate',
            'LinkedIn profile URL is included and current',
            'Portfolio or professional website links are included (if applicable)',
        ],
    ],
    [
        'category' => 'Personal Statement',
        'items' => [
            'Reflects current experience level accurately',
            'Includes years of experience (updated)',
            'Highlights most relevant skills for target roles',
            'Includes notable achievements',
            'Is concise (3-4 sentences)',
            'Is tailored for each application',
        ],
    ],
    [
        'category' => 'Work Experience',
        'items' => [
            'All recent positions are included',
            'Current role is marked as "present"',
            'Job titles are accurate and up to date',
            'Company names are correct',
            'Dates are accurate (month and year)',
            'Achievements are quantified with numbers where possible',
            'Bullet points focus on impact, not just duties',
            'Older positions (10+ years) are condensed or removed',
            'Relevant projects and initiatives are highlighted',
        ],
    ],
    [
        'category' => 'Skills & Certifications',
        'items' => [
            'New skills from recent roles are added',
            'Outdated skills are removed',
            'Skills align with target job descriptions',
            'Technical skills are current and relevant',
            'New certifications are included',
            'Certification expiry dates are checked',
            'Industry-specific credentials are featured prominently',
        ],
    ],
    [
        'category' => 'Education',
        'items' => [
            'Qualifications are listed in reverse chronological order',
            'Most recent education is first',
            'Degree names and institutions are accurate',
            'Graduation dates are correct',
            'Relevant coursework or achievements are included (if applicable)',
        ],
    ],
    [
        'category' => 'Formatting & Presentation',
        'items' => [
            'Font is clean and professional',
            'Styling is consistent throughout',
            'CV length is appropriate (1-2 pages)',
            'Adequate white space for readability',
            'Headings are clear and consistent',
            'Bullet points are formatted consistently',
            'Margins are appropriate',
        ],
    ],
    [
        'category' => 'ATS Optimisation (Applicant Tracking System)',
        'items' => [
            'Relevant keywords are included naturally',
            'Standard section headings are used',
            'Formatting is ATS-friendly (no complex tables/graphics)',
            'File can be saved as .docx if requested',
        ],
    ],
    [
        'category' => 'Content Quality',
        'items' => [
            'Content is tailored for each application',
            'Vague statements are replaced with specific evidence',
            'Achievements are quantified with metrics',
            'Relevant experience is emphasised',
            'Irrelevant information is removed',
        ],
    ],
    [
        'category' => 'Final Checks',
        'items' => [
            'All dates are current and accurate',
            'Contact information is verified',
            'LinkedIn profile matches CV information',
            'Spell-check has been run',
            'Grammar has been checked',
            'File is saved with professional name (e.g., "Jane_Smith_CV.pdf")',
            'PDF version is ready for sending',
            '.docx version is available if needed',
            'Someone else has reviewed your CV',
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
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
            .print-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
<?php partial('header'); ?>

<main>
    <article class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-500 to-pink-500 text-white">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[80%] rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="absolute -bottom-32 right-0 h-64 w-64 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <header class="space-y-8">
                <div class="inline-flex items-center rounded-full border border-white/20 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white/80">
                    CV Essentials
                </div>
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="text-lg text-indigo-100 max-w-3xl leading-relaxed">
                    Use this comprehensive checklist to systematically update your CV. Print it out or keep it open while you work through each section to ensure nothing is missed.
                </p>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center no-print">
                    <a href="/resources/career/how-to-update-your-cv.php" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-indigo-600 shadow hover:bg-indigo-50">
                        ← Back to Complete Guide
                    </a>
                    <button onclick="window.print()" class="inline-flex items-center justify-center rounded-lg border border-white/40 bg-white/10 px-5 py-2 text-sm font-semibold text-white hover:bg-white/20">
                        Print Checklist
                    </button>
                </div>
            </header>
        </div>
    </article>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-lg shadow-slate-900/5 p-8">
            <div class="mb-8 pb-6 border-b border-slate-200">
                <h2 class="text-2xl font-semibold text-slate-900 mb-2">Your CV Update Checklist</h2>
                <p class="text-slate-600">
                    Work through each section and check off items as you complete them. This ensures your CV is comprehensive, current, and ready for new opportunities.
                </p>
            </div>

            <div class="space-y-8">
                <?php foreach ($checklistItems as $index => $category): ?>
                    <div class="<?php echo $index > 0 && $index % 3 === 0 ? 'print-break' : ''; ?>">
                        <h3 class="text-xl font-semibold text-slate-900 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <?php echo e($category['category']); ?>
                        </h3>
                        <ul class="space-y-3">
                            <?php foreach ($category['items'] as $itemIndex => $item): ?>
                                <li class="flex items-start gap-3 text-base text-slate-700">
                                    <input type="checkbox" class="mt-1 h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 print:hidden" id="check-<?php echo e($index . '-' . $itemIndex); ?>">
                                    <label for="check-<?php echo e($index . '-' . $itemIndex); ?>" class="flex-1 cursor-pointer print:cursor-default">
                                        <?php echo e($item); ?>
                                    </label>
                                    <span class="hidden print:inline text-slate-400 mr-2">□</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 no-print">
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-6 py-5">
                    <h3 class="text-lg font-semibold text-indigo-900 mb-2">Ready to update your CV?</h3>
                    <p class="text-indigo-800 mb-4">
                        Use Simple CV Builder to create a polished, ATS-friendly CV. Our Pro plans unlock premium templates, QR codes, and PDF exports.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <?php if (isLoggedIn()): ?>
                            <a href="/dashboard.php" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 transition-colors">
                                Build Your CV
                            </a>
                            <a href="/subscription.php" class="inline-flex items-center justify-center rounded-lg border-2 border-indigo-600 px-5 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors">
                                Upgrade to Pro
                            </a>
                        <?php else: ?>
                            <a href="#" data-open-register class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 transition-colors">
                                Create Free Account
                            </a>
                            <a href="/#pricing" class="inline-flex items-center justify-center rounded-lg border-2 border-indigo-600 px-5 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors">
                                Compare Plans
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
