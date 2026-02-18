<?php
/**
 * Super Admin - Competitive Analysis
 * SEO visibility, competitor learnings, and improvement roadmap
 * Based on search analysis: "simple cv builder", "free CV builder UK", etc.
 */

require_once __DIR__ . '/../php/helpers.php';

requireSuperAdmin();

$user = getCurrentUser();
$siteUrl = 'https://simple-cv-builder.com';

// Competitors that rank for "simple cv builder" and related terms
$competitorsSimpleCvBuilder = [
    ['name' => 'SimpleCVBuilder', 'url' => 'https://www.simplecvbuilder.com/', 'note' => 'No hyphens – ranks in top results', 'priority' => 'Critical'],
    ['name' => 'SimpleCV', 'url' => 'https://www.simplecv.me/', 'note' => '"#1 Resume Maker", 100+ templates', 'priority' => 'High'],
    ['name' => 'SimpleCV', 'url' => 'https://simplecv.co/', 'note' => 'AI-powered, cloud sync', 'priority' => 'High'],
    ['name' => 'FreeCV', 'url' => 'https://www.freecv.org/', 'note' => 'Completely free, no signup', 'priority' => 'Medium'],
    ['name' => 'Simple CV Maker', 'url' => 'https://www.simple-cv-maker.com/', 'note' => 'Free templates, cover letters', 'priority' => 'Medium'],
];

// Competitors that rank for "free CV builder UK"
$competitorsFreeCvUk = [
    ['name' => 'Reed.co.uk', 'url' => 'https://www.reed.co.uk/cvbuilder', 'note' => 'Major UK job board – huge authority', 'priority' => 'High'],
    ['name' => 'CV.run', 'url' => 'https://cv.run/', 'note' => 'UK-focused AI CV maker – direct competitor', 'priority' => 'High'],
    ['name' => 'CVMaker', 'url' => 'https://cvmaker.com/', 'note' => '36 layouts, 15 min creation', 'priority' => 'High'],
    ['name' => 'Resume.io', 'url' => 'https://resume.io/uk', 'note' => 'DR 76, 8K referring domains, ~1.8M visits/mo', 'priority' => 'High'],
    ['name' => 'My World of Work', 'url' => 'https://www.myworldofwork.co.uk/', 'note' => 'Scottish careers service – government backing', 'priority' => 'Medium'],
];

// Key learnings from top rankers
$learnings = [
    [
        'title' => 'Domain confusion',
        'detail' => 'simplecvbuilder.com (no hyphens) ranks; simple-cv-builder.com (hyphens) does not. Users typing "simplecvbuilder" land on competitor.',
        'action' => 'Reinforce "Simple CV Builder" brand everywhere – titles, schema, content. Consider redirect from non-hyphen variant if owned.',
        'priority' => 'Critical',
    ],
    [
        'title' => 'Content depth – resume/CV guides',
        'detail' => 'simplecvbuilder.com has 7+ guide pages: "What Makes a Great Resume in 2025?", "How Do I Beat ATS?", "How Should I Tailor My Resume by Industry?". cv.run has blog + CV examples by industry.',
        'action' => 'Add "How to write a CV UK 2025", "Best CV format UK", "How to beat ATS", "CV examples by industry" – capture long-tail searches.',
        'priority' => 'High',
    ],
    [
        'title' => 'UK keyword targeting',
        'detail' => 'cv.run heavily targets "British CV", "UK CV templates", "CV examples UK", "free CV builder UK".',
        'action' => 'Weave "Free CV builder UK", "British CV", "UK CV templates", "CV examples UK" into homepage, pricing, templates, resources.',
        'priority' => 'High',
    ],
    [
        'title' => 'Templates showcase page',
        'detail' => 'Top rankers have dedicated /templates with thumbnails and descriptions.',
        'action' => 'Add or strengthen /templates (or cv-templates-feature) as a standalone showcase page.',
        'priority' => 'High',
    ],
    [
        'title' => 'Social proof & trust',
        'detail' => 'Competitors use "50K+ CVs created", "95% interview success rate", testimonials, "No watermarks", pricing comparisons.',
        'action' => 'Add real usage stats, testimonials, clear USPs above the fold.',
        'priority' => 'Medium',
    ],
    [
        'title' => 'Authority & backlinks',
        'detail' => 'Resume.io: ~8K referring domains, DR 76. Reed/My World of Work: institutional backing.',
        'action' => 'Build backlinks: Product Hunt, Capterra, G2, career/education sites, guest posts on job blogs.',
        'priority' => 'Medium',
    ],
];

// Improvement roadmap – actionable items
$improvements = [
    ['item' => 'Create "Free CV builder UK – guide 2025" article', 'status' => 'pending', 'notes' => 'Targets primary keyword + long-tail'],
    ['item' => 'Create "How to beat ATS" / "ATS CV guide" page', 'status' => 'pending', 'notes' => 'High search volume, links to ATS feature'],
    ['item' => 'Create "CV examples UK by industry" page', 'status' => 'pending', 'notes' => 'Technology, Business, Creative, Finance – like cv.run'],
    ['item' => 'Add "British CV" / "UK CV templates" to homepage H1 or hero', 'status' => 'pending', 'notes' => 'Strengthen UK targeting'],
    ['item' => 'Add dedicated /templates showcase if missing', 'status' => 'pending', 'notes' => 'Thumbnails, descriptions, industry tags'],
    ['item' => 'Add usage stats to homepage (e.g. "X CVs created")', 'status' => 'pending', 'notes' => 'Social proof'],
    ['item' => 'Submit to Product Hunt, Capterra, G2, career directories', 'status' => 'pending', 'notes' => 'Backlink building'],
    ['item' => 'Request indexing for key pages in GSC', 'status' => 'in_progress', 'notes' => 'See SEO Plan for URL list'],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Competitive Analysis | Super Admin',
        'metaDescription' => 'SEO visibility, competitor learnings, improvement roadmap',
        'canonicalUrl' => APP_URL . '/admin/competitive-analysis.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>
    <?php partial('admin/quick-actions'); ?>

    <main id="main-content" class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Competitive Analysis</h1>
                <p class="mt-1 text-sm text-gray-500">SEO visibility, competitor learnings, and improvement roadmap. Last updated: 2026-02-10.</p>
                <p class="mt-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                    <strong>Context:</strong> Site does not appear in Google for "simple-cv-builder" even after 34 pages. Competitors with similar names and products rank instead.
                </p>
            </div>

            <!-- Competitors: "simple cv builder" -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Competitors: "simple cv builder"</h2>
                <p class="text-sm text-gray-600 mb-4">Sites that rank when users search for our brand / product name.</p>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Study</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($competitorsSimpleCvBuilder as $row): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="<?php echo e($row['url']); ?>" target="_blank" rel="noopener" class="font-medium text-blue-600 hover:underline"><?php echo e($row['name']); ?></a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['note']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo $row['priority'] === 'Critical' ? 'bg-red-100 text-red-800' : ($row['priority'] === 'High' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e($row['priority']); ?></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?php echo e($row['url']); ?>" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline">Open</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Competitors: "free CV builder UK" -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Competitors: "free CV builder UK"</h2>
                <p class="text-sm text-gray-600 mb-4">Sites that rank for our target market keyword.</p>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Study</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($competitorsFreeCvUk as $row): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="<?php echo e($row['url']); ?>" target="_blank" rel="noopener" class="font-medium text-blue-600 hover:underline"><?php echo e($row['name']); ?></a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['note']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo $row['priority'] === 'High' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800'; ?>"><?php echo e($row['priority']); ?></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?php echo e($row['url']); ?>" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline">Open</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Key Learnings -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Key Learnings from Top Rankers</h2>
                <p class="text-sm text-gray-600 mb-4">What competitors do that we can adopt or improve on.</p>
                <div class="space-y-4">
                    <?php foreach ($learnings as $l): ?>
                    <div class="rounded-lg border border-gray-200 bg-white p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-medium text-gray-900"><?php echo e($l['title']); ?></h3>
                                <p class="mt-1 text-sm text-gray-600"><?php echo e($l['detail']); ?></p>
                                <p class="mt-2 text-sm text-blue-700 bg-blue-50 rounded px-2 py-1"><strong>Action:</strong> <?php echo e($l['action']); ?></p>
                            </div>
                            <span class="inline-flex flex-shrink-0 rounded-full px-2 py-0.5 text-xs font-medium <?php echo $l['priority'] === 'Critical' ? 'bg-red-100 text-red-800' : ($l['priority'] === 'High' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e($l['priority']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Improvement Roadmap -->
            <section class="mb-10 rounded-xl border-2 border-cyan-200 bg-cyan-50/50 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Improvement Roadmap</h2>
                <p class="text-sm text-gray-600 mb-4">Actionable items with an eye on SEO visibility. Mark items complete as you ship.</p>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($improvements as $i): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($i['item']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo $i['status'] === 'done' ? 'bg-green-100 text-green-800' : ($i['status'] === 'in_progress' ? 'bg-cyan-100 text-cyan-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $i['status']))); ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($i['notes']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-gray-500">Update status in this file as items are completed. Consider moving to a tracked checklist later.</p>
            </section>

            <!-- Links to Study -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Sites to Study in Depth</h2>
                <p class="text-sm text-gray-600 mb-4">Direct competitors worth analysing for content, structure, and messaging.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="https://cv.run/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        cv.run – UK AI CV maker
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <a href="https://www.simplecvbuilder.com/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        simplecvbuilder.com – content strategy
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <a href="https://resume.io/uk" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        resume.io – structure & authority
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </section>

            <!-- Related -->
            <section>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Related</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="/admin/seo-plan.php" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 hover:border-cyan-300 hover:bg-cyan-50 transition-colors">
                        SEO Plan (keywords, GSC quick wins)
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>
