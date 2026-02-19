<?php
/**
 * Update Example CV Project Image
 * 
 * Updates the example CV project to reference the uploaded image files.
 * Run from command line: php scripts/update-example-cv-image.php
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/database.php';

// Only allow running from command line
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

echo "Update Example CV Project Image\n";
echo "================================\n\n";

$username = 'simple-cv-example';
$userId = '80b7c3eb-fdc5-4cd7-95cb-dc975e0095dd'; // Example CV user ID
$imageFilename = '1768483673_c3900a11895999f1.jpeg';

// Get the example profile
$profile = db()->fetchOne("SELECT id FROM profiles WHERE username = ?", [$username]);

if (!$profile) {
    die("Error: Example CV profile not found.\n");
}

if ($profile['id'] !== $userId) {
    echo "Warning: Profile ID mismatch. Expected: $userId, Found: {$profile['id']}\n";
    $userId = $profile['id'];
}

// Get the project
$project = db()->fetchOne(
    "SELECT id, title FROM projects WHERE profile_id = ? AND title = 'AI Marketing Toolkit' LIMIT 1",
    [$userId]
);

if (!$project) {
    die("Error: Project 'AI Marketing Toolkit' not found for example CV.\n");
}

echo "Found project: {$project['title']} (ID: {$project['id']})\n";

// Construct the image path and URL
$imagePath = "projects/{$userId}/{$imageFilename}";
$imageUrl = STORAGE_URL . '/' . $imagePath;

echo "Image path: {$imagePath}\n";
echo "Image URL: {$imageUrl}\n\n";

// Check if responsive image data exists
$responsiveData = null;
$responsivePath = STORAGE_PATH . '/' . $imagePath;
if (file_exists($responsivePath)) {
    // Build responsive versions data
    $baseName = pathinfo($imageFilename, PATHINFO_FILENAME);
    $ext = pathinfo($imageFilename, PATHINFO_EXTENSION);
    
    $responsiveVersions = [];
    $sizes = [
        'thumb' => ['width' => 150, 'height' => 150],
        'small' => ['width' => 400, 'height' => 300],
        'medium' => ['width' => 800, 'height' => 600],
        'large' => ['width' => 1200, 'height' => 900]
    ];
    
    foreach ($sizes as $sizeName => $dimensions) {
        $variantFilename = "{$baseName}_{$sizeName}.{$ext}";
        $variantPath = STORAGE_PATH . "/projects/{$userId}/{$variantFilename}";
        
        if (file_exists($variantPath)) {
            $responsiveVersions[$sizeName] = [
                'path' => "projects/{$userId}/{$variantFilename}",
                'width' => $dimensions['width'],
                'height' => $dimensions['height']
            ];
        }
    }
    
    if (!empty($responsiveVersions)) {
        $responsiveData = json_encode($responsiveVersions);
        echo "Found responsive image variants: " . implode(', ', array_keys($responsiveVersions)) . "\n";
    }
}

// Update the project
$updateData = [
    'image_url' => $imageUrl,
    'image_path' => $imagePath,
    'updated_at' => date('Y-m-d H:i:s')
];

if ($responsiveData) {
    $updateData['image_responsive'] = $responsiveData;
}

try {
    db()->update('projects', $updateData, 'id = ?', [$project['id']]);
    echo "\n✓ Project updated successfully!\n";
    echo "Image should now display on: " . APP_URL . "/cv/@{$username}\n";
} catch (Exception $e) {
    die("Error updating project: " . $e->getMessage() . "\n");
}
