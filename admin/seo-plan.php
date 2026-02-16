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

// Essential SEO tools (Lighthouse = PageSpeed Insights; GSC for rankings; Bing for ChatGPT)
$recommendedTools = [
    [
        'name' => 'Google Search Console',
        'description' => 'Monitor rankings, impressions, clicks, indexing. Submit sitemap, request indexing.',
        'url' => 'https://search.google.com/search-console',
        'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
    ],
    [
        'name' => 'Bing Webmaster Tools',
        'description' => 'Submit sitemap for Bing (powers ChatGPT web search). Import from GSC or add manually.',
        'url' => 'https://www.bing.com/webmasters',
        'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
    ],
    [
        'name' => 'Lighthouse (PageSpeed Insights)',
        'description' => 'Performance, accessibility, SEO, best practices. Run on mobile or desktop.',
        'url' => 'https://pagespeed.web.dev/analysis?url=' . $siteUrlEnc,
        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
    ],
];

// Phase 5: Sitemap and indexing URLs
$sitemapUrl = $siteUrl . '/sitemap.xml';

// Pages to submit for indexing (URL Inspection in Search Console)
$pagesToSubmitForIndexing = [
    ['label' => 'Homepage', 'url' => $siteUrl . '/'],
    ['label' => 'Pricing', 'url' => $siteUrl . '/pricing'],
    ['label' => 'Individual Users', 'url' => $siteUrl . '/individual-users.php'],
    ['label' => 'Job Application Tracker', 'url' => $siteUrl . '/job-applications-features.php'],
    ['label' => 'CV Building', 'url' => $siteUrl . '/cv-building-feature.php'],
    ['label' => 'CV Templates', 'url' => $siteUrl . '/cv-templates-feature.php'],
    ['label' => 'AI CV Generator', 'url' => $siteUrl . '/ai-cv-generation-feature.php'],
    ['label' => 'AI Cover Letters', 'url' => $siteUrl . '/cover-letters-feature.php'],
    ['label' => 'All Features', 'url' => $siteUrl . '/all-features.php'],
    ['label' => 'FAQ', 'url' => $siteUrl . '/faq.php'],
    ['label' => 'Resources Hub', 'url' => $siteUrl . '/resources/'],
    ['label' => 'Job Market Insights', 'url' => $siteUrl . '/resources/jobs/'],
    ['label' => 'For Organisations', 'url' => $siteUrl . '/organisations.php'],
    ['label' => 'Remote Jobs for Beginners', 'url' => $siteUrl . '/resources/jobs/remote-jobs-begginers.php'],
    ['label' => 'Legitimate Ways to Earn Online', 'url' => $siteUrl . '/resources/extra-income/legitimate-ways-to-earn-money-online.php'],
    ['label' => 'Using AI in Job Applications', 'url' => $siteUrl . '/resources/jobs/using-ai-in-job-applications.php'],
];

// GSC Quick Wins: pages ranking position 4–10 with low CTR (from export 2026-02-16)
$gscQuickWins = [
    ['page' => '/resources/jobs/remote-jobs-begginers.php', 'position' => 8.7, 'impressions' => 205, 'ctr' => '0%', 'priority' => 'High', 'action' => 'Improve title & meta'],
    ['page' => '/resources/extra-income/legitimate-ways-to-earn-money-online.php', 'position' => 8.9, 'impressions' => 48, 'ctr' => '0%', 'priority' => 'High', 'action' => 'Done – title, meta, TOC, links'],
    ['page' => '/terms.php', 'position' => 4.5, 'impressions' => 4, 'ctr' => '0%', 'priority' => 'Medium', 'action' => 'Done – meta description added'],
    ['page' => '/resources/', 'position' => 6.0, 'impressions' => 2, 'ctr' => '0%', 'priority' => 'Low', 'action' => 'Done – index page added'],
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
    ['name' => 'Phase 3: Meta Descriptions', 'done' => true, 'items' => ['Resource articles have keyword-rich meta descriptions', 'Feature pages audited and meta descriptions improved']],
    ['name' => 'Phase 4: Content & On-Page', 'done' => true, 'items' => ['Ensure keywords in first paragraph of key pages', 'Add keywords to image alt text', 'Optimise internal link anchor text']],
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
                <p class="text-sm text-gray-600 mb-4">Essential tools for monitoring performance and search performance.</p>
                <div class="grid gap-4 sm:grid-cols-3">
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
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">GSC Quick Wins</h2>
                <p class="text-sm text-gray-600 mb-4">Pages ranking position 4–10 with low CTR. Improve titles and meta descriptions to boost clicks. Export fresh data from Search Console → Performance → Export to Google Sheets, then filter Position 4–10 and sort by CTR ascending.</p>
                <div class="mb-4">
                    <a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700 transition-colors">
                        Open Search Console
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Impressions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CTR</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($gscQuickWins as $row): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <a href="<?php echo e($siteUrl . $row['page']); ?>" target="_blank" rel="noopener" class="text-blue-600 hover:underline"><?php echo e($row['page']); ?></a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['position']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['impressions']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['ctr']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo $row['priority'] === 'High' ? 'bg-amber-100 text-amber-800' : ($row['priority'] === 'Medium' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e($row['priority']); ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($row['action']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-gray-500">Data from GSC export 2026-02-16. Re-export periodically to refresh.</p>
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

            <!-- Phase 5: Technical & Monitoring – Action Steps -->
            <section class="mb-10 rounded-xl border-2 border-cyan-200 bg-cyan-50/50 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Phase 5: Technical & Monitoring – Do These Now</h2>
                <p class="text-sm text-gray-600 mb-4">Complete these steps in Google Search Console and Bing Webmaster Tools. Each takes 1–2 minutes.</p>
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 rounded-lg bg-white p-4 border border-cyan-100">
                        <div class="flex-shrink-0 font-medium text-gray-900">1. Submit sitemap to Google</div>
                        <div class="flex-1 text-sm text-gray-600">
                            <a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="text-cyan-600 hover:underline font-medium">Open Search Console</a> → Sitemaps → Add sitemap: <code class="bg-gray-100 px-2 py-0.5 rounded text-sm">sitemap.xml</code>
                        </div>
                        <a href="<?php echo e($sitemapUrl); ?>" target="_blank" rel="noopener" class="text-xs text-gray-500 hover:text-cyan-600">Verify sitemap</a>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 rounded-lg bg-white p-4 border border-cyan-100">
                        <div class="flex-shrink-0 font-medium text-gray-900">2. Submit sitemap to Bing</div>
                        <div class="flex-1 text-sm text-gray-600">
                            <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener" class="text-cyan-600 hover:underline font-medium">Open Bing Webmaster Tools</a> → Sitemaps → Submit: <code class="bg-gray-100 px-2 py-0.5 rounded text-sm"><?php echo e($sitemapUrl); ?></code>
                        </div>
                    </div>
                    <div class="rounded-lg bg-white p-4 border border-cyan-100">
                        <div class="font-medium text-gray-900 mb-3">3. Request indexing for key pages</div>
                        <p class="text-sm text-gray-600 mb-4">
                            <a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="text-cyan-600 hover:underline font-medium">Open URL Inspection</a> → paste each URL below → Request indexing.
                        </p>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">URL (click to copy)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <?php foreach ($pagesToSubmitForIndexing as $row): ?>
                                    <tr>
                                        <td class="px-3 py-2 font-medium text-gray-900"><?php echo e($row['label']); ?></td>
                                        <td class="px-3 py-2">
                                            <button type="button" data-copy-url="<?php echo e($row['url']); ?>" class="text-left text-cyan-600 hover:underline font-mono text-xs break-all group flex items-center gap-1">
                                                <?php echo e($row['url']); ?>
                                                <span class="text-gray-400 group-hover:text-cyan-600" title="Copy">⎘</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 rounded-lg bg-white p-4 border border-cyan-100">
                        <div class="flex-shrink-0 font-medium text-gray-900">4. Monitor rankings</div>
                        <div class="flex-1 text-sm text-gray-600">
                            <a href="https://search.google.com/search-console" target="_blank" rel="noopener" class="text-cyan-600 hover:underline font-medium">Search Console</a> → Performance. Check weekly for impressions, clicks, average position. Export to find quick wins.
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500">After completing steps 1–3, mark Phase 5 as done in the plan below.</p>
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
    <script>
    (function() {
        document.querySelectorAll('[data-copy-url]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-copy-url');
                navigator.clipboard.writeText(url).then(function() {
                    var span = btn.querySelector('span');
                    if (span) { span.textContent = '✓'; setTimeout(function() { span.textContent = '⎘'; }, 1500); }
                });
            });
        });
    })();
    </script>
</body>
</html>
