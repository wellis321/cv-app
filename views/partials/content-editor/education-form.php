<?php
/**
 * Education Form Partial
 * When variant_id is in GET, loads from cv_variant_education.
 */

$editingId = $_GET['edit'] ?? null;
$variantId = $_GET['variant_id'] ?? null;
$editingEducation = null;
$educationEntries = [];
$isVariantContext = false;

if ($variantId) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if ($variant) {
        $isVariantContext = true;
        if ($editingId) {
            $editingEducation = db()->fetchOne(
                "SELECT * FROM cv_variant_education WHERE cv_variant_id = ? AND (id = ? OR original_education_id = ?)",
                [$variantId, $editingId, $editingId]
            );
        }
        $educationEntries = db()->fetchAll("SELECT * FROM cv_variant_education WHERE cv_variant_id = ? ORDER BY start_date DESC", [$variantId]);
    }
}

if (!$isVariantContext) {
    if ($editingId) {
        $editingEducation = db()->fetchOne("SELECT * FROM education WHERE id = ? AND profile_id = ?", [$editingId, $userId]);
    }
    $educationEntries = db()->fetchAll("SELECT * FROM education WHERE profile_id = ? ORDER BY start_date DESC", [$userId]);
}

$canAddEducation = planCanAddEntry($subscriptionContext, 'education', $userId, count($educationEntries));
?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Education</h1>
    
    <!-- Add/Edit Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingEducation ? 'Edit Education' : 'Add New Education'; ?>
        </h2>
        
        <?php if (!$editingEducation && !$canAddEducation): ?>
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
                <?php echo getPlanLimitMessage($subscriptionContext, 'education'); ?>
            </div>
        <?php else: ?>
        <form method="POST" data-section-form data-form-type="<?php echo $editingEducation ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingEducation ? 'update' : 'create'; ?>">
            <input type="hidden" name="section_id" value="education">
            <?php if ($editingEducation): ?>
                <input type="hidden" name="id" value="<?php echo e($editingEducation['id']); ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="institution" class="block text-sm font-medium text-gray-700 mb-1">Institution *</label>
                    <input type="text" id="institution" name="institution" value="<?php echo $editingEducation ? e($editingEducation['institution']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="degree" class="block text-sm font-medium text-gray-700 mb-1">Degree *</label>
                    <input type="text" id="degree" name="degree" value="<?php echo $editingEducation ? e($editingEducation['degree']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="field_of_study" class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                    <input type="text" id="field_of_study" name="field_of_study" value="<?php echo $editingEducation ? e($editingEducation['field_of_study'] ?? '') : ''; ?>" maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $editingEducation ? date('Y-m-d', strtotime($editingEducation['start_date'])) : ''; ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $editingEducation && $editingEducation['end_date'] ? date('Y-m-d', strtotime($editingEducation['end_date'])) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank if still studying</p>
                </div>
                
                <div class="sm:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="hide_date" value="1" <?php echo $editingEducation && !empty($editingEducation['hide_date']) ? 'checked' : ''; ?> class="mr-2">
                        <span class="text-sm text-gray-700">Hide date on CV</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingEducation ? 'Update Education' : 'Add Education'; ?>
                </button>
                <?php if ($editingEducation): ?>
                    <button type="button" data-action="cancel" class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Existing Entries List -->
    <div id="education-entries-list">
        <?php if (empty($educationEntries)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No education entries yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($educationEntries as $edu): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xl font-semibold text-gray-900"><span class="text-gray-500 font-normal">Qual:</span> <?php echo e($edu['degree']); ?></p>
                                <p class="text-lg text-gray-700"><span class="text-gray-500 font-normal">Institution:</span> <?php echo e($edu['institution']); ?></p>
                                <?php if ($edu['field_of_study']): ?>
                                    <p class="text-gray-600"><span class="text-gray-500 font-normal">Subject:</span> <?php echo e($edu['field_of_study']); ?></p>
                                <?php endif; ?>
                                <?php if (empty($edu['hide_date'])): ?>
                                    <p class="text-sm text-gray-500"><?php echo date('M Y', strtotime($edu['start_date'])); ?> - <?php echo $edu['end_date'] ? date('M Y', strtotime($edu['end_date'])) : 'Present'; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" data-action="edit" data-entry-id="<?php echo e($edu['id']); ?>" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                <button type="button" data-action="delete" data-entry-id="<?php echo e($edu['id']); ?>" data-entry-type="education" class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
