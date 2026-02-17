<?php
/**
 * IndexNow API - Submit URLs to Bing, Yandex and other search engines
 * POST to notify when content is added/updated. No daily quota like Google.
 */

ob_start();

try {
    require_once __DIR__ . '/../php/helpers.php';
    require_once __DIR__ . '/../php/authorisation.php';
} catch (Throwable $e) {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'error' => 'Setup error: ' . $e->getMessage()]);
    exit;
}

if (!isLoggedIn() || !isSuperAdmin()) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Super admin access required']);
    exit;
}

if (!isPost()) {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$csrfName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : 'csrf_token';
if (!verifyCsrfToken($_POST[$csrfName] ?? '')) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$key = defined('INDEXNOW_KEY') ? INDEXNOW_KEY : '3c9dc093a70141689f13ee385002d0d1';
$keyFile = dirname(__DIR__) . '/' . $key . '.txt';

// Ensure key file exists
if (!file_exists($keyFile)) {
    file_put_contents($keyFile, $key);
}

// Use production URL for IndexNow (key file must be publicly accessible to Bing)
$indexNowSiteUrl = defined('INDEXNOW_SITE_URL') ? INDEXNOW_SITE_URL : APP_URL;
$siteHost = parse_url($indexNowSiteUrl, PHP_URL_HOST) ?: 'simple-cv-builder.com';
$keyLocation = $indexNowSiteUrl . '/' . $key . '.txt';

// Get URLs from sitemap (use production URL for IndexNow)
require_once __DIR__ . '/../php/sitemap-urls.php';
$sitemapUrls = getSitemapUrls();
$urlList = array_map(function ($u) use ($indexNowSiteUrl) {
    $loc = $u['loc'];
    if (defined('APP_URL') && APP_URL !== $indexNowSiteUrl) {
        $loc = str_replace(APP_URL, $indexNowSiteUrl, $loc);
    }
    return $loc;
}, $sitemapUrls);

// IndexNow endpoint (Bing, Yandex, etc. use this)
$indexNowUrl = 'https://api.indexnow.org/indexnow';

$payload = [
    'host' => $siteHost,
    'key' => $key,
    'keyLocation' => $keyLocation,
    'urlList' => $urlList,
];

$ch = curl_init($indexNowUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json; charset=utf-8',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Request failed: ' . $curlError,
        'urlCount' => count($urlList),
    ]);
    exit;
}

if ($httpCode >= 200 && $httpCode < 300) {
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'URLs submitted to IndexNow (Bing, Yandex, etc.)',
        'urlCount' => count($urlList),
        'httpCode' => $httpCode,
    ]);
} else {
    ob_end_clean();
    http_response_code($httpCode >= 400 ? $httpCode : 500);
    echo json_encode([
        'success' => false,
        'error' => 'IndexNow returned HTTP ' . $httpCode,
        'response' => $response ?: 'No response body',
        'urlCount' => count($urlList),
    ]);
}
