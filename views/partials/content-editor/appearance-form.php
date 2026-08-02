<?php
/**
 * Appearance Section Form Partial
 * Template selection + header gradient (moved from profile.php's Colours tab) and
 * accent colour (moved from preview-cv.php's Customise Colours panel, same plan gate).
 */

$variantId = $_GET['variant_id'] ?? null;
$isVariantContext = false;
$cvVariant = null;

$profile = db()->fetchOne("SELECT * FROM profiles WHERE id = ?", [$userId]);

if ($variantId) {
    $cvVariant = db()->fetchOne("SELECT * FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    $isVariantContext = !empty($cvVariant);
}

$subscriptionFrontendContext = buildSubscriptionFrontendContext($subscriptionContext);

// Current template: variant's own preferred_template_id wins if set, else the profile's.
$currentTemplateId = $profile['preferred_template_id'] ?? 'minimal';
if ($isVariantContext && !empty($cvVariant['pdf_preferences'])) {
    $decoded = json_decode($cvVariant['pdf_preferences'], true);
    if (is_array($decoded) && !empty($decoded['preferred_template_id'])) {
        $currentTemplateId = $decoded['preferred_template_id'];
    }
}

$currentFrom = $profile['cv_header_from_color'] ?? '#4338ca';
$currentTo = $profile['cv_header_to_color'] ?? '#7e22ce';

$colorSchemes = [
    ['name' => 'Indigo to Purple', 'from' => '#4338ca', 'to' => '#7e22ce'],
    ['name' => 'Blue to Cyan', 'from' => '#2563eb', 'to' => '#06b6d4'],
    ['name' => 'Green to Teal', 'from' => '#059669', 'to' => '#14b8a6'],
    ['name' => 'Red to Pink', 'from' => '#dc2626', 'to' => '#ec4899'],
    ['name' => 'Orange to Red', 'from' => '#ea580c', 'to' => '#dc2626'],
    ['name' => 'Purple to Pink', 'from' => '#7c3aed', 'to' => '#ec4899'],
    ['name' => 'Slate to Blue', 'from' => '#475569', 'to' => '#3b82f6'],
    ['name' => 'Emerald to Blue', 'from' => '#10b981', 'to' => '#3b82f6'],
];

$pdfPrefs = getPdfPreferencesForCv($profile, $cvVariant);
$currentColourPreset = $pdfPrefs['colour_preset'] ?? 'default';
$currentCustomAccentHex = $pdfPrefs['custom_accent_hex'] ?: '#2563eb';

$colourPresetOptions = [
    'default' => ['label' => 'Default (template colours)', 'swatch' => null],
    'conservative' => ['label' => 'Conservative Navy', 'swatch' => '#1e3a8a'],
    'professional' => ['label' => 'Professional Blue', 'swatch' => '#2563eb'],
    'teal' => ['label' => 'Teal', 'swatch' => '#0d9488'],
    'purple' => ['label' => 'Purple', 'swatch' => '#7c3aed'],
    'rose' => ['label' => 'Rose', 'swatch' => '#e11d48'],
    'custom' => ['label' => 'Custom accent', 'swatch' => null],
];
?>
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Appearance</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Template &amp; Header Colours</h2>
        <form method="POST" data-section-form data-form-type="update">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="section_id" value="appearance">
            <?php if ($isVariantContext): ?>
                <input type="hidden" name="variant_id" value="<?php echo e($variantId); ?>">
            <?php endif; ?>

            <div class="mb-6">
                <label for="appearance-template-select" class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                <select id="appearance-template-select" name="preferred_template_id" data-current-template="<?php echo e($currentTemplateId); ?>" data-allowed-template-ids="<?php echo e(json_encode($subscriptionFrontendContext['allowedTemplateIds'] ?? [])); ?>" class="w-full bg-white border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 cursor-pointer hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="<?php echo e($currentTemplateId); ?>">Loading templates…</option>
                </select>
            </div>

            <?php if ($isVariantContext): ?>
                <p class="text-sm text-gray-500 mb-2">Header colours apply to your whole profile, not individual CVs - edit them from <a href="/content-editor.php#appearance" class="text-indigo-600 hover:text-indigo-800">Appearance</a> on your master CV.</p>
            <?php else: ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Header Colour Scheme</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <?php foreach ($colorSchemes as $scheme):
                        $isSelected = ($scheme['from'] === $currentFrom && $scheme['to'] === $currentTo);
                    ?>
                        <button type="button" data-colour-scheme data-from="<?php echo e($scheme['from']); ?>" data-to="<?php echo e($scheme['to']); ?>"
                                class="relative p-3 border-2 rounded-lg hover:border-blue-500 transition-colors <?php echo $isSelected ? 'border-blue-600 ring-2 ring-blue-500' : 'border-gray-200'; ?>">
                            <div class="w-full h-16 rounded mb-2" style="background: linear-gradient(to right, <?php echo e($scheme['from']); ?>, <?php echo e($scheme['to']); ?>);"></div>
                            <p class="text-xs text-gray-600 text-center"><?php echo e($scheme['name']); ?></p>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="cv_header_from_color" class="block text-sm font-medium text-gray-700 mb-2">Start Colour</label>
                    <div class="flex gap-3">
                        <input type="color" id="cv_header_from_color" name="cv_header_from_color" value="<?php echo e($currentFrom); ?>" class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <input type="text" id="cv_header_from_color_text" value="<?php echo e($currentFrom); ?>" pattern="^#[0-9A-Fa-f]{6}$" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="#4338ca">
                    </div>
                </div>
                <div>
                    <label for="cv_header_to_color" class="block text-sm font-medium text-gray-700 mb-2">End Colour</label>
                    <div class="flex gap-3">
                        <input type="color" id="cv_header_to_color" name="cv_header_to_color" value="<?php echo e($currentTo); ?>" class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <input type="text" id="cv_header_to_color_text" value="<?php echo e($currentTo); ?>" pattern="^#[0-9A-Fa-f]{6}$" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="#7e22ce">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                <div id="appearance-color-preview" class="w-full h-24 rounded-lg shadow-sm" style="background: linear-gradient(to right, <?php echo e($currentFrom); ?>, <?php echo e($currentTo); ?>);"></div>
            </div>
            <?php endif; ?>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">Save</button>
            </div>
        </form>
    </div>

    <?php if (!empty($subscriptionFrontendContext['templateCustomizationEnabled'])): ?>
    <div class="bg-white shadow rounded-lg p-6 mb-6" id="appearance-accent-section" data-variant-id="<?php echo $isVariantContext ? e($variantId) : ''; ?>">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-xl font-semibold">Accent Colour</h2>
            <span id="appearance-accent-status" class="text-sm text-gray-500" aria-live="polite"></span>
        </div>
        <p class="text-sm text-gray-500 mb-4">Drives headings, links, and section dividers when generating a PDF. Saves automatically.</p>
        <div class="space-y-2 mb-3">
            <?php foreach ($colourPresetOptions as $value => $option): ?>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="colour-preset" value="<?php echo e($value); ?>" <?php echo $currentColourPreset === $value ? 'checked' : ''; ?> class="text-blue-600 focus:ring-blue-500">
                    <span class="text-sm"><?php echo e($option['label']); ?></span>
                    <?php if ($option['swatch']): ?>
                        <span class="w-4 h-4 rounded-full border border-gray-300" style="background:<?php echo e($option['swatch']); ?>"></span>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div id="appearance-custom-accent-row" class="<?php echo $currentColourPreset === 'custom' ? '' : 'hidden'; ?> mt-2">
            <div class="flex items-center gap-2">
                <input type="color" id="appearance-custom-accent-color" value="<?php echo e($currentCustomAccentHex); ?>" class="h-8 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" id="appearance-custom-accent-hex" value="<?php echo e($currentCustomAccentHex); ?>" class="flex-1 text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2" maxlength="7" placeholder="#2563eb">
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
