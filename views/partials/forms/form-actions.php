<?php
/**
 * Form Actions Component
 * Standard action buttons for forms
 * 
 * Usage:
 * partial('forms/form-actions', [
 *     'submitText' => 'Save',
 *     'cancelUrl' => '/back/link',
 *     'cancelText' => 'Cancel',
 *     'deleteUrl' => '/delete/link', // Optional
 *     'deleteText' => 'Delete' // Optional
 * ]);
 */

$submitText = $submitText ?? 'Save';
$cancelUrl = $cancelUrl ?? null;
$cancelText = $cancelText ?? 'Cancel';
$deleteUrl = $deleteUrl ?? null;
$deleteText = $deleteText ?? 'Delete';
$submitClass = $submitClass ?? 'bg-blue-600 hover:bg-blue-700';
$showDelete = $showDelete ?? false;
?>

<div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-4 pt-8 border-t-2 border-gray-300 mt-8">
    <div class="flex flex-col sm:flex-row gap-3">
        <?php if ($cancelUrl): ?>
            <a href="<?php echo e($cancelUrl); ?>"
               class="inline-flex justify-center items-center rounded-lg bg-white px-6 py-3 text-base font-semibold text-gray-900 shadow-md ring-2 ring-inset ring-gray-400 hover:bg-gray-50 hover:ring-gray-500 transition-all">
                <?php echo e($cancelText); ?>
            </a>
        <?php endif; ?>
        
        <?php if ($showDelete && $deleteUrl): ?>
            <a href="<?php echo e($deleteUrl); ?>"
               onclick="return confirm('Are you sure you want to delete this? This action cannot be undone.');"
               class="inline-flex justify-center items-center rounded-lg bg-red-600 px-6 py-3 text-base font-semibold text-white shadow-md hover:bg-red-700 transition-all">
                <?php echo e($deleteText); ?>
            </a>
        <?php endif; ?>
    </div>
    
    <button type="submit"
            class="inline-flex justify-center items-center rounded-lg <?php echo e($submitClass); ?> px-8 py-3 text-base font-bold text-white shadow-lg hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-4 focus-visible:ring-blue-200 transition-all">
        <?php echo e($submitText); ?>
    </button>
</div>

