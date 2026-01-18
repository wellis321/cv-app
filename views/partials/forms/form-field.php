<?php
/**
 * Form Field Component
 * Reusable form field with consistent styling and validation
 * 
 * Usage:
 * partial('forms/form-field', [
 *     'type' => 'text',
 *     'name' => 'field_name',
 *     'label' => 'Field Label',
 *     'required' => true,
 *     'value' => $value,
 *     'placeholder' => 'Placeholder text',
 *     'help' => 'Help text',
 *     'error' => $errorMessage
 * ]);
 */

$type = $type ?? 'text';
$name = $name ?? '';
$id = $id ?? $name;
$label = $label ?? '';
$required = $required ?? false;
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$help = $help ?? null;
$error = $error ?? null;
$options = $options ?? []; // For select fields
$rows = $rows ?? 3; // For textarea
$min = $min ?? null;
$max = $max ?? null;
$pattern = $pattern ?? null;
$classes = $classes ?? '';
?>

<div class="mb-6">
    <label for="<?php echo e($id); ?>" class="block text-base font-semibold text-gray-900 mb-3">
        <?php echo e($label); ?>
        <?php if ($required): ?>
            <span class="text-red-600 font-bold ml-1">*</span>
        <?php endif; ?>
    </label>
    
    <?php if ($type === 'select'): ?>
        <select name="<?php echo e($name); ?>" 
                id="<?php echo e($id); ?>"
                <?php echo $required ? 'required' : ''; ?>
                class="block w-full rounded-lg border-2 <?php echo $error ? 'border-red-400 bg-red-50' : 'border-gray-400 bg-white'; ?> px-4 py-3 text-base font-medium text-gray-900 shadow-sm transition-colors focus:border-blue-600 focus:ring-4 focus:ring-blue-200 focus:outline-none <?php echo e($classes); ?>">
            <?php if (!empty($placeholder)): ?>
                <option value=""><?php echo e($placeholder); ?></option>
            <?php endif; ?>
            <?php foreach ($options as $optValue => $optLabel): ?>
                <option value="<?php echo e($optValue); ?>" <?php echo ($value == $optValue) ? 'selected' : ''; ?>>
                    <?php echo e($optLabel); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($type === 'textarea'): ?>
        <textarea name="<?php echo e($name); ?>" 
                  id="<?php echo e($id); ?>"
                  rows="<?php echo $rows; ?>"
                  <?php echo $required ? 'required' : ''; ?>
                  placeholder="<?php echo e($placeholder); ?>"
                  class="block w-full rounded-lg border-2 <?php echo $error ? 'border-red-400 bg-red-50' : 'border-gray-400 bg-white'; ?> px-4 py-3 text-base font-medium text-gray-900 shadow-sm transition-colors focus:border-blue-600 focus:ring-4 focus:ring-blue-200 focus:outline-none resize-y <?php echo e($classes); ?>"><?php echo e($value); ?></textarea>
    <?php elseif ($type === 'checkbox'): ?>
        <div class="flex items-center">
            <input type="checkbox" 
                   name="<?php echo e($name); ?>" 
                   id="<?php echo e($id); ?>"
                   value="1"
                   <?php echo $value ? 'checked' : ''; ?>
                   class="h-5 w-5 rounded border-2 border-gray-400 text-blue-600 focus:ring-4 focus:ring-blue-200 focus:ring-offset-0">
            <?php if ($help): ?>
                <label for="<?php echo e($id); ?>" class="ml-3 text-base text-gray-700 font-medium">
                    <?php echo e($help); ?>
                </label>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <input type="<?php echo e($type); ?>" 
               name="<?php echo e($name); ?>" 
               id="<?php echo e($id); ?>"
               value="<?php echo e($value); ?>"
               <?php echo $required ? 'required' : ''; ?>
               <?php echo $placeholder ? 'placeholder="' . e($placeholder) . '"' : ''; ?>
               <?php echo $min !== null ? 'min="' . $min . '"' : ''; ?>
               <?php echo $max !== null ? 'max="' . $max . '"' : ''; ?>
               <?php echo $pattern ? 'pattern="' . e($pattern) . '"' : ''; ?>
               class="block w-full rounded-lg border-2 <?php echo $error ? 'border-red-400 bg-red-50' : 'border-gray-400 bg-white'; ?> px-4 py-3 text-base font-medium text-gray-900 shadow-sm transition-colors focus:border-blue-600 focus:ring-4 focus:ring-blue-200 focus:outline-none <?php echo e($classes); ?>">
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p class="mt-2 text-sm font-medium text-red-700 flex items-center">
            <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <?php echo e($error); ?>
        </p>
    <?php elseif ($help && $type !== 'checkbox'): ?>
        <p class="mt-2 text-sm text-gray-600 font-medium"><?php echo e($help); ?></p>
    <?php endif; ?>
</div>

