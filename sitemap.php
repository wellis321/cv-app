<?php
require_once __DIR__ . '/php/helpers.php';

header('Content-Type: application/xml; charset=UTF-8');

$urls = [
    [
        'loc' => APP_URL . '/',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '1.0',
    ],
    [
        'loc' => APP_URL . '/faq.php',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'monthly',
        'priority' => '0.8',
    ],
    [
        'loc' => APP_URL . '/subscription.php',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'monthly',
        'priority' => '0.6',
    ],
];

$resourcesPath = __DIR__ . '/resources';
if (is_dir($resourcesPath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($resourcesPath, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
            continue;
        }

        $relative = str_replace(__DIR__, '', $fileInfo->getPathname());
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

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url): ?>
        <url>
            <loc><?php echo htmlspecialchars($url['loc'], ENT_XML1); ?></loc>
            <lastmod><?php echo $url['lastmod']; ?></lastmod>
            <changefreq><?php echo $url['changefreq']; ?></changefreq>
            <priority><?php echo $url['priority']; ?></priority>
        </url>
    <?php endforeach; ?>
</urlset>
<?php exit; ?>
