<?php
/**
 * Interests Form Partial
 * When variant_id is in GET, loads from cv_variant_interests.
 */

$editingId = $_GET['edit'] ?? null;
$variantId = $_GET['variant_id'] ?? null;
$editingInterest = null;
$interests = [];
$isVariantContext = false;

if ($variantId) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if ($variant) {
        $isVariantContext = true;
        if ($editingId) {
            $editingInterest = db()->fetchOne("SELECT * FROM cv_variant_interests WHERE cv_variant_id = ? AND (id = ? OR original_interest_id = ?)", [$variantId, $editingId, $editingId]);
        }
        $interests = db()->fetchAll("SELECT * FROM cv_variant_interests WHERE cv_variant_id = ? ORDER BY name ASC", [$variantId]);
    }
}

if (!$isVariantContext) {
    if ($editingId) {
        $editingInterest = db()->fetchOne("SELECT * FROM interests WHERE id = ? AND profile_id = ?", [$editingId, $userId]);
    }
    $interests = db()->fetchAll("SELECT * FROM interests WHERE profile_id = ? ORDER BY name ASC", [$userId]);
}

$canAddInterest = planCanAddEntry($subscriptionContext, 'interests', $userId, count($interests));
?>
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Interests & Activities</h1>
        <button type="button" onclick="assessSection('interests')" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-md border border-gray-300 hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <?php echo $editingInterest ? 'Assess this entry' : 'Assess this section'; ?>
        </button>
    </div>
    
    <!-- Add/Edit Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingInterest ? 'Edit Interest' : 'Add New Interest'; ?>
        </h2>
        
        <?php if (!$editingInterest && !$canAddInterest): ?>
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
                <?php echo getPlanLimitMessage($subscriptionContext, 'interests'); ?>
            </div>
        <?php else: ?>
        <form method="POST" data-section-form data-form-type="<?php echo $editingInterest ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingInterest ? 'update' : 'create'; ?>">
            <input type="hidden" name="section_id" value="interests">
            <?php if ($editingInterest): ?>
                <input type="hidden" name="id" value="<?php echo e($editingInterest['id']); ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Interest Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $editingInterest ? e($editingInterest['name']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo $editingInterest ? e($editingInterest['description'] ?? '') : ''; ?></textarea>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingInterest ? 'Update Interest' : 'Add Interest'; ?>
                </button>
                <?php if ($editingInterest): ?>
                    <button type="button" data-action="cancel" class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Existing Interests List -->
    <div id="interests-entries-list">
        <?php if (empty($interests)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No interests added yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($interests as $interest): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900"><?php echo e($interest['name']); ?></h3>
                                <?php if ($interest['description']): ?>
                                    <p class="text-gray-600 mt-1"><?php echo e($interest['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" data-action="edit" data-entry-id="<?php echo e($interest['id']); ?>" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                <button type="button" data-action="delete" data-entry-id="<?php echo e($interest['id']); ?>" data-entry-type="interests" class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
