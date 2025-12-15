<?php
/**
 * Section navigation partial - shows previous/next section links
 */

if (!isset($currentSectionId)) {
    return;
}

// Check if function exists (in case helpers.php wasn't loaded properly)
if (!function_exists('getSectionNavigation')) {
    // Silently fail - don't show navigation if function isn't available
    return;
}

try {
    $nav = getSectionNavigation($currentSectionId);

    // If navigation returned null for current, something went wrong
    if (!$nav || !isset($nav['current'])) {
        return;
    }
} catch (Exception $e) {
    // Silently fail on error
    error_log("Section navigation error: " . $e->getMessage());
    return;
}
?>
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-3">
            <div class="flex-1">
                <?php if ($nav['previous']): ?>
                    <a href="<?php echo e($nav['previous']['path']); ?>" class="text-purple-600 hover:text-purple-800 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <?php echo e($nav['previous']['name']); ?>
                    </a>
                <?php else: ?>
                    <span class="text-gray-400">First Section</span>
                <?php endif; ?>
            </div>
            <div class="flex-1 text-center">
                <span class="text-gray-900 font-medium"><?php echo e($nav['current']['name']); ?></span>
            </div>
            <div class="flex-1 text-right">
                <?php if ($nav['next']): ?>
                    <a href="<?php echo e($nav['next']['path']); ?>" class="text-purple-600 hover:text-purple-800 font-medium flex items-center justify-end">
                        <?php echo e($nav['next']['name']); ?>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                <?php else: ?>
                    <span class="text-gray-400">Last Section</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
