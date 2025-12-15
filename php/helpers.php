<?php
/**
 * Helper functions and includes
 */

// Load all core files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/cv-data.php';
require_once __DIR__ . '/subscriptions.php';
require_once __DIR__ . '/stripe.php';

/**
 * Enforce canonical domain (prevents www/non-www duplicates)
 */
function enforceCanonicalDomain() {
    if (PHP_SAPI === 'cli') {
        return;
    }

    $currentHost = $_SERVER['HTTP_HOST'] ?? null;
    if (!$currentHost) {
        return;
    }

    $primaryHost = parse_url(APP_URL, PHP_URL_HOST);
    if (!$primaryHost) {
        return;
    }

    $normalizedCurrent = strtolower($currentHost);
    $normalizedPrimary = strtolower($primaryHost);

    if ($normalizedCurrent === $normalizedPrimary) {
        return;
    }

    $scheme = parse_url(APP_URL, PHP_URL_SCHEME) ?: 'https';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $redirectUrl = $scheme . '://' . $primaryHost . $requestUri;

    header('Location: ' . $redirectUrl, true, 301);
    exit;
}

if (APP_ENV === 'production') {
    enforceCanonicalDomain();
}

// Set security headers early (before any output)
if (!headers_sent()) {
    setSecurityHeaders();
}

/**
 * Render a view/template
 */
function render($template, $data = []) {
    extract($data);
    $templatePath = __DIR__ . '/../views/' . $template . '.php';

    if (!file_exists($templatePath)) {
        die("Template not found: {$template}");
    }

    ob_start();
    include $templatePath;
    return ob_get_clean();
}

/**
 * Include partial/template
 */
function partial($template, $data = []) {
    extract($data);
    $templatePath = __DIR__ . '/../views/partials/' . $template . '.php';

    if (!file_exists($templatePath)) {
        die("Partial not found: {$template}");
    }

    include $templatePath;
}

/**
 * Get flash message
 */
function getFlash($key) {
    $message = $_SESSION['flash_' . $key] ?? null;
    unset($_SESSION['flash_' . $key]);
    return $message;
}

/**
 * Set flash message
 */
function setFlash($key, $message) {
    $_SESSION['flash_' . $key] = $message;
}

/**
 * Output view
 */
function view($template, $data = []) {
    echo render($template, $data);
}

/**
 * Include layout with content
 */
function layout($layout, $content, $data = []) {
    $data['content'] = $content;
    echo render('layouts/' . $layout, $data);
}

/**
 * Get CV sections in order
 */
function getCvSections() {
    return [
        ['id' => 'professional-summary', 'name' => 'Professional Summary', 'path' => '/professional-summary.php'],
        ['id' => 'work-experience', 'name' => 'Work Experience', 'path' => '/work-experience.php'],
        ['id' => 'education', 'name' => 'Education', 'path' => '/education.php'],
        ['id' => 'projects', 'name' => 'Projects', 'path' => '/projects.php'],
        ['id' => 'skills', 'name' => 'Skills', 'path' => '/skills.php'],
        ['id' => 'certifications', 'name' => 'Certifications', 'path' => '/certifications.php'],
        ['id' => 'qualification-equivalence', 'name' => 'Professional Qualification Equivalence', 'path' => '/qualification-equivalence.php'],
        ['id' => 'memberships', 'name' => 'Professional Memberships', 'path' => '/memberships.php'],
        ['id' => 'interests', 'name' => 'Interests & Activities', 'path' => '/interests.php'],
    ];
}

/**
 * Get section navigation (previous and next sections)
 */
function getSectionNavigation($currentSectionId) {
    $sections = getCvSections();
    $currentIndex = -1;

    foreach ($sections as $index => $section) {
        if ($section['id'] === $currentSectionId) {
            $currentIndex = $index;
            break;
        }
    }

    if ($currentIndex === -1) {
        return ['previous' => null, 'current' => null, 'next' => null];
    }

    $previous = ($currentIndex > 0) ? $sections[$currentIndex - 1] : null;
    $current = $sections[$currentIndex];
    $next = ($currentIndex < count($sections) - 1) ? $sections[$currentIndex + 1] : null;

    return ['previous' => $previous, 'current' => $current, 'next' => $next];
}

/**
 * Generate JSON-LD structured data
 */
function generateStructuredData($type = 'default', $data = []) {
    $schemas = [];

    // Organization schema (always included)
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Simple CV Builder',
        'url' => APP_URL,
        'logo' => APP_URL . '/static/favicon.png',
        'description' => 'Build a standout CV online, share it instantly, and unlock premium templates with Simple CV Builder.',
        'sameAs' => [
            // Add social media URLs if available
        ]
    ];

    // WebSite schema (always included)
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Simple CV Builder',
        'url' => APP_URL,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => APP_URL . '/?s={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];

    // SoftwareApplication schema (for homepage)
    if ($type === 'homepage' || $type === 'default') {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'Simple CV Builder',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web Browser',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'GBP',
                'description' => 'Free CV builder with premium templates available'
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.5',
                'ratingCount' => '100'
            ]
        ];
    }

    // Article schema (for resource/blog pages)
    if ($type === 'article' && !empty($data)) {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? APP_URL . '/static/images/default-profile.svg',
            'datePublished' => $data['datePublished'] ?? date('Y-m-d'),
            'dateModified' => $data['dateModified'] ?? date('Y-m-d'),
            'author' => [
                '@type' => 'Organization',
                'name' => 'Simple CV Builder'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Simple CV Builder',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => APP_URL . '/static/favicon.png'
                ]
            ]
        ];
    }

    // BreadcrumbList schema (if breadcrumbs provided)
    if ($type === 'breadcrumb' && !empty($data['breadcrumbs'])) {
        $itemListElement = [];
        $position = 1;
        foreach ($data['breadcrumbs'] as $crumb) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $crumb['name'],
                'item' => $crumb['url']
            ];
        }

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];
    }

    // FAQPage schema (if FAQs provided)
    if ($type === 'faq' && !empty($data['faqs'])) {
        $mainEntity = [];
        foreach ($data['faqs'] as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];
    }

    return $schemas;
}

/**
 * Output JSON-LD structured data script tags
 */
function outputStructuredData($schemas) {
    foreach ($schemas as $schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n" . '</script>' . "\n";
    }
}
