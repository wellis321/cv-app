<?php
/**
 * Shared section nav item used by section-sidebar.php.
 * Expects $section (with id, name, count, isComplete) and $currentSectionId in scope.
 */
?>
<div class="section-nav-wrapper relative" data-section-id="<?php echo e($section['id']); ?>" draggable="false">
    <!-- Drag handle (hidden until reorder mode) -->
    <div class="drag-handle-sidebar hidden absolute left-0 top-0 bottom-0 flex items-center pl-1 cursor-move text-gray-400" style="z-index:1">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
        </svg>
    </div>
    <a href="#<?php echo e($section['id']); ?>"
       class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-l-4 shadow-sm text-sm font-medium transition-all <?php echo $currentSectionId === $section['id'] ? 'border-gray-300 border-l-blue-500 bg-blue-100 text-blue-700' : 'border-gray-300 border-l-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 hover:shadow'; ?>"
       data-section-id="<?php echo e($section['id']); ?>">
        <div class="flex items-center min-w-0">
            <span class="truncate"><?php echo e($section['name']); ?></span>
        </div>
        <div class="flex items-center space-x-2 flex-shrink-0">
            <?php if ($section['count'] > 0): ?>
                <span class="text-xs text-gray-500"><?php echo $section['count']; ?></span>
            <?php endif; ?>
            <?php if ($section['isComplete']): ?>
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            <?php else: ?>
                <div class="w-2 h-2 rounded-full bg-gray-300"></div>
            <?php endif; ?>
        </div>
    </a>
</div>
