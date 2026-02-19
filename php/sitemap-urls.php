<?php
/**
 * Sitemap URL list - shared between sitemap.xml output and IndexNow submission
 */
require_once __DIR__ . '/helpers.php';

function getSitemapUrls() {
    $urls = [
        ['loc' => APP_URL . '/', 'lastmod' => date('Y-m-d'), 'changefreq' => 'weekly', 'priority' => '1.0'],
        ['loc' => APP_URL . '/pricing', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.9'],
        ['loc' => APP_URL . '/help/faq.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => APP_URL . '/blog/', 'lastmod' => date('Y-m-d'), 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => APP_URL . '/help/', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => APP_URL . '/about.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => APP_URL . '/terms.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => APP_URL . '/privacy.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => APP_URL . '/individual-users.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/organisations.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/job-applications-features.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/all-features.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => APP_URL . '/cv-building-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/pdf-export-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/ai-cv-generation-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/template-customisation-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/cv-variants-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/smart-text-extraction.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/status-tracking.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/follow-up-dates.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/keyword-extraction.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/cv-variant-linking.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/cover-letters-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/interview-tracking.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/application-notes.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/search-filter.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/file-uploads-ai.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/track-all-applications.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/browser-ai-free.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/cv-templates-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/online-cv-username.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => APP_URL . '/keyword-ai-integration.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/qr-codes-pdf.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/tailor-cv-content.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/cv-quality-assessment-feature.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/never-miss-follow-up.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/track-progress.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/all-in-one-place.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => APP_URL . '/free-with-account.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'],
    ];

    // Blog and Help sections (replaces old resources/)
    $blogPath = dirname(__DIR__) . '/blog';
    $helpPath = dirname(__DIR__) . '/help';
    foreach ([$blogPath => 'blog', $helpPath => 'help'] as $basePath => $prefix) {
        if (!is_dir($basePath)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace(dirname(__DIR__), '', $fileInfo->getPathname());
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            $relative = ltrim($relative, '/');

            if (substr($relative, -9) === 'index.php') {
                $urlPath = substr($relative, 0, -9);
                $loc = rtrim(APP_URL . '/' . $urlPath, '/') . '/';
            } else {
                $loc = APP_URL . '/' . $relative;
            }

            $urls[] = [
                'loc' => $loc,
                'lastmod' => gmdate('Y-m-d', $fileInfo->getMTime()),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }
    }

    return $urls;
}
