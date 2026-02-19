<?php
/**
 * File storage functions
 */

require_once __DIR__ . '/config.php';

/**
 * Ensure storage directory exists
 */
function ensureStorageDir($path) {
    $fullPath = STORAGE_PATH . '/' . $path;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
    }
    return $fullPath;
}

/**
 * Resize an image to specified dimensions
 */
function resizeImage($sourcePath, $destinationPath, $maxWidth, $maxHeight, $quality = 85) {
    if (!extension_loaded('gd')) {
        return false;
    }

    // Get image info
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }

    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    // Calculate new dimensions maintaining aspect ratio
    $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
    $newWidth = (int)($sourceWidth * $ratio);
    $newHeight = (int)($sourceHeight * $ratio);

    // Create image resource based on type
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $sourceImage = imagecreatefromwebp($sourcePath);
            } else {
                return false;
            }
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and GIF
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resize image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

    // Save resized image
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($newImage, $destinationPath, $quality);
            break;
        case 'image/png':
            // PNG quality is 0-9 (inverted)
            $pngQuality = 9 - round($quality / 10);
            $result = imagepng($newImage, $destinationPath, $pngQuality);
            break;
        case 'image/gif':
            $result = imagegif($newImage, $destinationPath);
            break;
        case 'image/webp':
            if (function_exists('imagewebp')) {
                $result = imagewebp($newImage, $destinationPath, $quality);
            }
            break;
    }

    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($newImage);

    return $result;
}

/**
 * Upload a file and generate responsive versions
 */
function uploadFile($file, $userId, $bucket = 'uploads', $generateResponsive = true) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    // Validate file size
    if ($file['size'] > STORAGE_MAX_SIZE) {
        return ['success' => false, 'error' => 'File too large'];
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Get file extension
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ALLOWED_IMAGE_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file extension'];
    }

    // Create storage path
    $storageDir = ensureStorageDir($bucket . '/' . $userId);
    $baseName = time() . '_' . bin2hex(random_bytes(8));
    $fileName = $baseName . '.' . $ext;
    $filePath = $storageDir . '/' . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => false, 'error' => 'Failed to save file'];
    }

    // Generate responsive versions if requested and GD is available
    $responsiveVersions = [];
    if ($generateResponsive && extension_loaded('gd') && in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
        // Define responsive sizes
        $sizes = [
            'thumb' => ['width' => 150, 'height' => 150],
            'small' => ['width' => 400, 'height' => 400],
            'medium' => ['width' => 800, 'height' => 800],
            'large' => ['width' => 1200, 'height' => 1200]
        ];

        foreach ($sizes as $sizeName => $dimensions) {
            $resizedFileName = $baseName . '_' . $sizeName . '.' . $ext;
            $resizedPath = $storageDir . '/' . $resizedFileName;
            
            if (resizeImage($filePath, $resizedPath, $dimensions['width'], $dimensions['height'])) {
                // Store relative path only - URL will be generated dynamically based on current APP_URL
                $relativePath = $bucket . '/' . $userId . '/' . $resizedFileName;
                $responsiveVersions[$sizeName] = [
                    'path' => $relativePath,
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height']
                ];
            }
        }
    }

    // Generate URL
    $relativePath = $bucket . '/' . $userId . '/' . $fileName;
    $url = STORAGE_URL . '/' . $relativePath;

    return [
        'success' => true,
        'url' => $url,
        'path' => $relativePath,
        'filename' => $fileName,
        'responsive' => $responsiveVersions
    ];
}

/**
 * Upload a document file (non-image)
 */
function uploadDocumentFile($file, $userId, $bucket = 'job-applications', $applicationId = null) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    // Validate file size
    if ($file['size'] > DOCUMENT_MAX_SIZE) {
        return ['success' => false, 'error' => 'File too large. Maximum size: ' . (DOCUMENT_MAX_SIZE / 1024 / 1024) . 'MB'];
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_DOCUMENT_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type: ' . $mimeType];
    }

    // Get file extension
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ALLOWED_DOCUMENT_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file extension: ' . $ext];
    }

    // Create storage path
    $storagePath = $bucket . '/' . $userId;
    if ($applicationId) {
        $storagePath .= '/' . $applicationId;
    }
    $storageDir = ensureStorageDir($storagePath);
    
    $baseName = time() . '_' . bin2hex(random_bytes(8));
    $fileName = $baseName . '.' . $ext;
    $filePath = $storageDir . '/' . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => false, 'error' => 'Failed to save file'];
    }

    // Generate relative path and URL
    $relativePath = $storagePath . '/' . $fileName;
    $url = STORAGE_URL . '/' . $relativePath;

    return [
        'success' => true,
        'url' => $url,
        'path' => $relativePath,
        'filename' => $fileName,
        'mime_type' => $mimeType,
        'size' => $file['size'],
        'original_name' => $file['name']
    ];
}

/**
 * Delete a file
 */
function deleteFile($path) {
    $fullPath = STORAGE_PATH . '/' . $path;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Get file URL
 */
function getFileUrl($path) {
    return STORAGE_URL . '/' . $path;
}

/**
 * Serve file (for storage-proxy functionality)
 */
function serveFile($path) {
    $fullPath = STORAGE_PATH . '/' . $path;

    if (!file_exists($fullPath) || !is_file($fullPath)) {
        http_response_code(404);
        die('File not found');
    }

    // Get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fullPath);
    finfo_close($finfo);

    // Set headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($fullPath));
    header('Cache-Control: public, max-age=3600');
    header('Content-Disposition: inline');

    // Output file
    readfile($fullPath);
    exit;
}
