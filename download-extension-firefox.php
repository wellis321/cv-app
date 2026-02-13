<?php
/**
 * Download Extension for Firefox - Serves the extension as a ZIP with manifest.json
 * that uses background.scripts (Firefox requires this; it ignores the selected file
 * and always loads manifest.json from the extension directory)
 */

require_once __DIR__ . '/php/helpers.php';

// Set headers for ZIP download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="simple-cv-builder-extension-firefox.zip"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

$extensionDir = __DIR__ . '/extension';
$zipFile = sys_get_temp_dir() . '/simple-cv-builder-extension-firefox-' . time() . '.zip';

// Create ZIP file
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    die('Failed to create extension ZIP file');
}

// Firefox manifest (background.scripts) - use this AS manifest.json in the zip
$manifestFirefoxPath = $extensionDir . '/manifest.firefox.json';
if (!file_exists($manifestFirefoxPath)) {
    http_response_code(500);
    die('Firefox manifest not found');
}
$zip->addFile($manifestFirefoxPath, 'manifest.json');

// Add all other extension files (exclude manifest.json - we used Firefox version)
$files = [
    'background.js',
    'popup.html',
    'popup.js',
    'options.html',
    'options.js',
    'linkedin-job-title.js',
    'extract-closing-date.js'
];

foreach ($files as $file) {
    $filePath = $extensionDir . '/' . $file;
    if (file_exists($filePath)) {
        $zip->addFile($filePath, $file);
    }
}

$zip->close();

// Stream the ZIP file
if (file_exists($zipFile)) {
    readfile($zipFile);
    unlink($zipFile);
    exit;
} else {
    http_response_code(500);
    die('Failed to create extension ZIP file');
}
