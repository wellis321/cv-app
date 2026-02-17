<?php
$title = $pageTitle ?? 'Simple CV Builder';
$useHomeCss = $useHomeCss ?? false;
$description = $metaDescription ?? 'Build a standout CV online, share it instantly, and unlock premium templates with Simple CV Builder.';
$canonicalUrl = $canonicalUrl ?? (APP_URL . $_SERVER['REQUEST_URI']);
$metaImage = $metaImage ?? (APP_URL . '/static/images/default-profile.svg');
$metaNoindex = $metaNoindex ?? false;
$structuredDataType = $structuredDataType ?? 'default';
$structuredData = $structuredData ?? [];
$breadcrumbs = $breadcrumbs ?? null;
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($title); ?></title>
<meta name="description" content="<?php echo e($description); ?>">
<link rel="canonical" href="<?php echo e($canonicalUrl); ?>">
<?php if ($metaNoindex): ?>
    <meta name="robots" content="noindex, nofollow">
<?php endif; ?>

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo e($title); ?>">
<meta property="og:description" content="<?php echo e($description); ?>">
<meta property="og:url" content="<?php echo e($canonicalUrl); ?>">
<meta property="og:image" content="<?php echo e($metaImage); ?>">
<meta property="og:site_name" content="Simple CV Builder">
<meta property="og:locale" content="en_GB">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo e($title); ?>">
<meta name="twitter:description" content="<?php echo e($description); ?>">
<meta name="twitter:image" content="<?php echo e($metaImage); ?>">

<!-- Structured Data (JSON-LD) -->
<?php
$schemas = generateStructuredData($structuredDataType, $structuredData);
if ($breadcrumbs) {
    $schemas = array_merge($schemas, generateStructuredData('breadcrumb', ['breadcrumbs' => $breadcrumbs]));
}
outputStructuredData($schemas);
?>

<!-- Favicons (absolute URLs help Bing/Google discover them in search snippets) -->
<link rel="icon" href="<?php echo e(APP_URL); ?>/favicon.ico" sizes="any">
<link rel="icon" type="image/png" sizes="192x192" href="<?php echo e(APP_URL); ?>/static/images/favicon_io/android-chrome-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(APP_URL); ?>/static/images/favicon_io/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(APP_URL); ?>/static/images/favicon_io/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(APP_URL); ?>/static/images/favicon_io/apple-touch-icon.png">
<link rel="manifest" href="<?php echo e(APP_URL); ?>/static/images/favicon_io/site.webmanifest">
<style>
/* Accessibility: Skip to main content link */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
.sr-only:focus {
    position: fixed;
    width: auto;
    height: auto;
    padding: 0.5rem 1rem;
    margin: 0;
    overflow: visible;
    clip: auto;
    white-space: normal;
    z-index: 9999;
}
/* Mobile touch improvements */
@media (max-width: 768px) {
    /* Ensure minimum touch target size */
    button, a[role="button"], input[type="submit"], input[type="button"], 
    select, .touch-target {
        min-height: 44px;
        min-width: 44px;
    }
    /* Improve touch response */
    button, a, input, select, textarea {
        touch-action: manipulation;
        -webkit-tap-highlight-color: rgba(59, 130, 246, 0.3);
    }
    /* Prevent text size adjustment on iOS */
    input, select, textarea, button {
        font-size: 16px;
    }
    /* Better spacing for touch */
    .form-field input, .form-field select, .form-field textarea {
        padding: 0.75rem 1rem;
    }
}
</style>
<?php $cssFile = ($useHomeCss && file_exists(__DIR__ . '/../../static/css/tailwind-home.css')) ? 'tailwind-home.css' : 'tailwind.css'; ?>
<link rel="preload" href="/static/css/<?php echo $cssFile; ?>" as="style">
<link rel="stylesheet" href="/static/css/<?php echo $cssFile; ?>">
<!-- marked.js loaded by footer only when .markdown-content exists -->