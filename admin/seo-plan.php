<?php
/**
 * Super Admin - SEO Plan
 * Internal SEO strategy, target keywords, and implementation status
 */

require_once __DIR__ . '/../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();

// Site URL for SEO tool links – always use production (local dev would test localhost, not useful)
$siteUrl = 'https://simple-cv-builder.com';
$siteUrlEnc = urlencode($siteUrl);

// Recommended SEO tools (from awesome-seo: https://github.com/teles/awesome-seo)
$recommendedTools = [
    [
        'name' => 'Google Search Console',
        'description' => 'Monitor rankings, impressions, clicks, and indexing status',
        'url' => 'https://search.google.com/search-console',
        'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
    ],
    [
        'name' => 'PageSpeed Insights',
        'description' => 'Test site performance and get optimization tips',
        'url' => 'https://pagespeed.web.dev/analysis?url=' . $siteUrlEnc,
        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
    ],
    [
        'name' => 'Mobile Friendly Test',
        'description' => 'Check mobile compatibility according to Google',
        'url' => 'https://search.google.com/test/mobile-friendly?url=' . $siteUrlEnc,
        'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
    ],
    [
        'name' => 'Rich Results Test',
        'description' => 'Validate FAQ schema and structured data markup',
        'url' => 'https://search.google.com/test/rich-results?url=' . $siteUrlEnc,
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ],
    [
        'name' => 'Ubersuggest',
        'description' => 'Free keyword research and competitor analysis',
        'url' => 'https://neilpatel.com/ubersuggest/',
        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    ],
];

// SEO plan data (synced with docs/SEO_PLAN.md – do not push)
$primaryKeywords = [
    ['keyword' => 'CV builder', 'priority' => 'High', 'status' => 'Optimising', 'pages' => 'Homepage, cv-building-feature'],
    ['keyword' => 'Free CV builder UK', 'priority' => 'High', 'status' => 'Optimising', 'pages' => 'Homepage, pricing, individual-users'],
    ['keyword' => 'Job application tracker', 'priority' => 'High', 'status' => 'Optimising', 'pages' => 'job-applications-features'],
    ['keyword' => 'Online CV builder', 'priority' => 'High', 'status' => 'Optimising', 'pages' => 'Homepage, cv-building-feature'],
    ['keyword' => 'CV templates', 'priority' => 'Medium', 'status' => 'Optimising', 'pages' => 'cv-templates-feature'],
    ['keyword' => 'AI CV generator', 'priority' => 'Medium', 'status' => 'Optimising', 'pages' => 'ai-cv-generation-feature'],
    ['keyword' => 'AI cover letter generator', 'priority' => 'Medium', 'status' => 'Optimising', 'pages' => 'cover-letters-feature'],
];

$secondaryKeywords = [
    'CV builder UK', 'Professional CV maker', 'Job application management',
    'CV quality assessment', 'CV variants', 'PDF CV export', 'Shareable CV link',
];

$longTailKeywords = [
    'Free online CV builder UK', 'Professional CV maker for job seekers',
    'Best free CV builder UK', 'Job application tracker free',
    'AI-powered CV builder', 'Create CV online free', 'CV builder with job tracking',
];

$phases = [
    ['name' => 'Phase 1: Title & H1 Optimization', 'done' => true, 'items' => ['Feature page titles include primary keywords', 'H1 tags include primary keywords', 'Resource article titles optimised']],
    ['name' => 'Phase 2: Internal Linking', 'done' => true, 'items' => ['Homepage links to Free CV & Job Guides', 'FAQ links to key resource articles', 'Footer links to Job Market Insights, Career Advice Hub', 'CTAs at end of resource articles']],
    ['name' => 'Phase 3: Meta Descriptions', 'done' => true, 'items' => ['Resource articles have keyword-rich meta descriptions'], 'pending' => ['Audit feature page meta descriptions']],
    ['name' => 'Phase 4: Content & On-Page', 'done' => false, 'items' => ['Ensure keywords in first paragraph of key pages', 'Add keywords to image alt text', 'Optimise internal link anchor text']],
    ['name' => 'Phase 5: Technical & Monitoring', 'done' => false, 'items' => ['Submit sitemap to Google Search Console', 'Request indexing for key pages', 'Monitor rankings in GSC', 'Track organic traffic growth']],
];

$keyPages = [
    ['page' => 'Homepage', 'primary' => 'Free CV builder UK, CV builder', 'secondary' => 'Job tracker, AI cover letters'],
    ['page' => '/pricing.php', 'primary' => 'Free CV builder UK', 'secondary' => 'CV builder, pricing'],
    ['page' => '/individual-users.php', 'primary' => 'Free CV builder UK', 'secondary' => 'Professional CV maker'],
    ['page' => '/job-applications-features.php', 'primary' => 'Job application tracker', 'secondary' => 'Job tracker'],
    ['page' => '/cv-building-feature.php', 'primary' => 'CV builder, Online CV builder', 'secondary' => 'Professional CV maker'],
    ['page' => '/cv-templates-feature.php', 'primary' => 'CV templates, Free CV templates', 'secondary' => 'CV template UK'],
    ['page' => '/ai-cv-generation-feature.php', 'primary' => 'AI CV generator', 'secondary' => 'AI CV builder'],
    ['page' => '/cover-letters-feature.php', 'primary' => 'AI cover letter generator', 'secondary' => 'Cover letter builder'],
    ['page' => '/resources/jobs/', 'primary' => 'Job search advice UK', 'secondary' => 'CV tips, job application'],
    ['page' => '/faq.php', 'primary' => 'Free CV builder UK FAQ', 'secondary' => 'CV builder questions'],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'SEO Plan | Super Admin',
        'metaDescription' => 'Internal SEO strategy and target keywords',
        'canonicalUrl' => APP_URL . '/admin/seo-plan.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>
    <?php partial('admin/quick-actions'); ?>

    <main id="main-content" class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="/admin/dashboard.php" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Dashboard
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">SEO Plan</h1>
                <p class="mt-1 text-sm text-gray-500">Target keywords, implementation phases, and key pages. Last updated: 2025-02-10.</p>
            </div>

            <!-- Recommended Tools -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recommended Tools</h2>
                <p class="text-sm text-gray-600 mb-4">Free SEO tools from the <a href="https://github.com/teles/awesome-seo" target="_blank" rel="noopener" class="text-blue-600 hover:underline">awesome-seo</a> list. Use these regularly as part of your improvement plan.</p>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($recommendedTools as $tool): ?>
                    <a href="<?php echo e($tool['url']); ?>" target="_blank" rel="noopener" class="flex items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-blue-300 hover:shadow-md transition-all group">
                        <div class="flex-shrink-0 rounded-lg bg-blue-50 p-2 group-hover:bg-blue-100 transition-colors">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($tool['icon']); ?>"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600"><?php echo e($tool['name']); ?></p>
                            <p class="mt-0.5 text-xs text-gray-500"><?php echo e($tool['description']); ?></p>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- GSC Quick Wins -->
            <section class="mb-10 rounded-lg border-2 border-dashed border-cyan-200 bg-cyan-50/50 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">GSC Quick Wins</h2>
                <p class="text-sm text-gray-600 mb-4">Detect pages ranking in positions 4–10 with low CTR—easy wins to improve titles and meta descriptions. Use Cursor's GSC MCP (<code class="text-xs bg-cyan-100 px-1 rounded">mcp_gsc_detect_quick_wins</code>) when configured, or run audits directly in Search Console.</p>
                <a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700 transition-colors">
                    Open Search Console
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </section>

            <!-- Primary Keywords -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Primary Keywords</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keyword</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target Pages</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($primaryKeywords as $row): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($row['keyword']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['priority']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['status']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['pages']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Secondary & Long-Tail -->
            <section class="mb-10 grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Secondary Keywords</h2>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <?php foreach ($secondaryKeywords as $kw): ?>
                        <li>• <?php echo e($kw); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Long-Tail Keywords</h2>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <?php foreach ($longTailKeywords as $kw): ?>
                        <li>• <?php echo e($kw); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <!-- Implementation Phases -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Implementation Phases</h2>
                <div class="space-y-4">
                    <?php foreach ($phases as $phase): ?>
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <h3 class="text-base font-medium text-gray-900"><?php echo e($phase['name']); ?></h3>
                            <?php if ($phase['done']): ?>
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Done</span>
                            <?php else: ?>
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">In progress</span>
                            <?php endif; ?>
                        </div>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <?php foreach ($phase['items'] as $item): ?>
                            <li class="flex items-center gap-2">
                                <?php if ($phase['done']): ?>
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <?php else: ?>
                                <span class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0"></span>
                                <?php endif; ?>
                                <?php echo e($item); ?>
                            </li>
                            <?php endforeach; ?>
                            <?php if (!empty($phase['pending'])): ?>
                            <?php foreach ($phase['pending'] as $item): ?>
                            <li class="flex items-center gap-2 text-amber-700">
                                <span class="w-4 h-4 rounded-full border-2 border-amber-400 flex-shrink-0"></span>
                                <?php echo e($item); ?>
                            </li>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Key Pages -->
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Key Pages & Target Keywords</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Primary Keyword</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secondary</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($keyPages as $row): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($row['page']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['primary']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['secondary']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Success Metrics -->
            <section class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Success Metrics</h2>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• Title tags: 100% of pages include primary keyword</li>
                    <li>• H1 tags: 100% of pages include primary keyword</li>
                    <li>• Meta descriptions: 2–3 relevant keywords per page</li>
                    <li>• Internal links: Key resource articles linked from homepage, FAQ, footer</li>
                    <li>• CTAs: All resource articles have conversion CTA</li>
                </ul>
                <p class="mt-4 text-sm text-gray-500">Track via Google Search Console: keyword rankings, impressions, clicks. Monitor organic traffic growth.</p>
            </section>
        </div>
    </main>
</body>
</html>
