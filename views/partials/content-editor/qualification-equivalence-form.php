<?php
/**
 * Qualification Equivalence Form Partial
 * When variant_id is in GET, loads from cv_variant_qualification_equivalence and cv_variant_supporting_evidence.
 */

$editingId = $_GET['edit'] ?? null;
$variantId = $_GET['variant_id'] ?? null;
$editingQual = null;
$qualifications = [];
$isVariantContext = false;

if ($variantId) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if ($variant) {
        $isVariantContext = true;
        if ($editingId) {
            $editingQual = db()->fetchOne("SELECT * FROM cv_variant_qualification_equivalence WHERE cv_variant_id = ? AND (id = ? OR original_qualification_id = ?)", [$variantId, $editingId, $editingId]);
            if ($editingQual) {
                $editingQual['evidence'] = db()->fetchAll(
                    "SELECT * FROM cv_variant_supporting_evidence WHERE qualification_equivalence_id = ? ORDER BY sort_order ASC",
                    [$editingQual['id']]
                );
            }
        }
        $qualifications = db()->fetchAll("SELECT * FROM cv_variant_qualification_equivalence WHERE cv_variant_id = ? ORDER BY level ASC", [$variantId]);
    }
}

if (!$isVariantContext) {
    if ($editingId) {
        $editingQual = db()->fetchOne("SELECT * FROM professional_qualification_equivalence WHERE id = ? AND profile_id = ?", [$editingId, $userId]);
        if ($editingQual) {
            $editingQual['evidence'] = db()->fetchAll(
                "SELECT * FROM supporting_evidence WHERE qualification_equivalence_id = ? ORDER BY sort_order ASC",
                [$editingQual['id']]
            );
        }
    }
    $qualifications = db()->fetchAll("SELECT * FROM professional_qualification_equivalence WHERE profile_id = ? ORDER BY created_at ASC", [$userId]);
}

$canAddQual = planCanAddEntry($subscriptionContext, 'qualification_equivalence', $userId, count($qualifications));
?>
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Professional Qualification Equivalence</h1>
        <button type="button" onclick="assessSection('qualification-equivalence')" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-md border border-gray-300 hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Assess This Section
        </button>
    </div>
    
    <!-- Add/Edit Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingQual ? 'Edit Qualification' : 'Add New Qualification'; ?>
        </h2>
        
        <?php if (!$editingQual && !$canAddQual): ?>
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
                <?php echo getPlanLimitMessage($subscriptionContext, 'qualification_equivalence'); ?>
            </div>
        <?php else: ?>
        <form method="POST" data-section-form data-form-type="<?php echo $editingQual ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingQual ? 'update' : 'create'; ?>">
            <input type="hidden" name="section_id" value="qualification-equivalence">
            <?php if ($editingQual): ?>
                <input type="hidden" name="id" value="<?php echo e($editingQual['id']); ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Qualification Level *</label>
                    <input type="text" id="level" name="level" value="<?php echo $editingQual ? e($editingQual['level']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo $editingQual ? e($editingQual['description'] ?? '') : ''; ?></textarea>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingQual ? 'Update Qualification' : 'Add Qualification'; ?>
                </button>
                <?php if ($editingQual): ?>
                    <button type="button" data-action="cancel" class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Existing Qualifications List -->
    <div id="qualification-equivalence-entries-list">
        <?php if (empty($qualifications)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No qualifications added yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($qualifications as $qual): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900"><?php echo e($qual['level']); ?></h3>
                                <?php if ($qual['description']): ?>
                                    <p class="text-gray-600 mt-2"><?php echo e($qual['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" data-action="edit" data-entry-id="<?php echo e($qual['id']); ?>" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                <button type="button" data-action="delete" data-entry-id="<?php echo e($qual['id']); ?>" data-entry-type="qualification-equivalence" class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
