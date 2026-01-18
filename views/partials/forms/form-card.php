<?php
/**
 * Form Card Component
 * Standard container for all forms with consistent styling
 * 
 * Usage:
 * partial('forms/form-card', [
 *     'title' => 'Form Title',
 *     'description' => 'Optional description',
 *     'backUrl' => '/back/link',
 *     'backText' => 'Back to list'
 * ]);
 */

$title = $title ?? 'Form';
$description = $description ?? null;
$backUrl = $backUrl ?? null;
$backText = $backText ?? 'Back';
$maxWidth = $maxWidth ?? 'max-w-2xl';
?>

<div class="<?php echo e($maxWidth); ?> mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <?php if ($backUrl): ?>
            <div class="mb-4">
                <a href="<?php echo e($backUrl); ?>" 
                   class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <?php echo e($backText); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <h1 class="text-2xl font-bold text-gray-900"><?php echo e($title); ?></h1>
        <?php if ($description): ?>
            <p class="mt-1 text-sm text-gray-500"><?php echo e($description); ?></p>
        <?php endif; ?>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow-lg rounded-xl border-2 border-gray-200">
        <div class="p-6 sm:p-8">
            <?php // Form content will be inserted here ?>
            <?php echo $content ?? ''; ?>
        </div>
    </div>
</div>

