<?php
require_once __DIR__ . '/php/sitemap-urls.php';

header('Content-Type: application/xml; charset=UTF-8');

$urls = getSitemapUrls();

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
