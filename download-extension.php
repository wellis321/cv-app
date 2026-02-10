<?php
/**
 * Download Extension - Serves the browser extension as a ZIP file
 * Users can download this ZIP, extract it, and load it as an unpacked extension
 */

require_once __DIR__ . '/php/helpers.php';

// Set headers for ZIP download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="simple-cv-builder-extension.zip"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

$extensionDir = __DIR__ . '/extension';
$zipFile = sys_get_temp_dir() . '/simple-cv-builder-extension-' . time() . '.zip';

// Create ZIP file
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    die('Failed to create extension ZIP file');
}

// Add all extension files to ZIP
$files = [
    'manifest.json',
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
    unlink($zipFile); // Clean up temp file
    exit;
} else {
    http_response_code(500);
    die('Failed to create extension ZIP file');
}
