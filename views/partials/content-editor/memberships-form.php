<?php
/**
 * Memberships Form Partial
 * When variant_id is in GET, loads from cv_variant_memberships.
 */

$editingId = $_GET['edit'] ?? null;
$variantId = $_GET['variant_id'] ?? null;
$editingMembership = null;
$memberships = [];
$isVariantContext = false;

if ($variantId) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if ($variant) {
        $isVariantContext = true;
        if ($editingId) {
            $editingMembership = db()->fetchOne("SELECT * FROM cv_variant_memberships WHERE cv_variant_id = ? AND (id = ? OR original_membership_id = ?)", [$variantId, $editingId, $editingId]);
        }
        $memberships = db()->fetchAll("SELECT * FROM cv_variant_memberships WHERE cv_variant_id = ? ORDER BY start_date DESC", [$variantId]);
    }
}

if (!$isVariantContext) {
    if ($editingId) {
        $editingMembership = db()->fetchOne("SELECT * FROM professional_memberships WHERE id = ? AND profile_id = ?", [$editingId, $userId]);
    }
    $memberships = db()->fetchAll("SELECT * FROM professional_memberships WHERE profile_id = ? ORDER BY start_date DESC", [$userId]);
}

$canAddMembership = planCanAddEntry($subscriptionContext, 'memberships', $userId, count($memberships));
?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Professional Memberships</h1>
    
    <!-- Add/Edit Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingMembership ? 'Edit Membership' : 'Add New Membership'; ?>
        </h2>
        
        <?php if (!$editingMembership && !$canAddMembership): ?>
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
                <?php echo getPlanLimitMessage($subscriptionContext, 'memberships'); ?>
            </div>
        <?php else: ?>
        <form method="POST" data-section-form data-form-type="<?php echo $editingMembership ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingMembership ? 'update' : 'create'; ?>">
            <input type="hidden" name="section_id" value="memberships">
            <?php if ($editingMembership): ?>
                <input type="hidden" name="id" value="<?php echo e($editingMembership['id']); ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="organisation" class="block text-sm font-medium text-gray-700 mb-1">Organisation *</label>
                    <input type="text" id="organisation" name="organisation" value="<?php echo $editingMembership ? e($editingMembership['organisation']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <input type="text" id="role" name="role" value="<?php echo $editingMembership ? e($editingMembership['role'] ?? '') : ''; ?>" maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo $editingMembership && $editingMembership['start_date'] ? date('Y-m-d', strtotime($editingMembership['start_date'])) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo $editingMembership && $editingMembership['end_date'] ? date('Y-m-d', strtotime($editingMembership['end_date'])) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo $editingMembership ? e($editingMembership['description'] ?? '') : ''; ?></textarea>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingMembership ? 'Update Membership' : 'Add Membership'; ?>
                </button>
                <?php if ($editingMembership): ?>
                    <button type="button" data-action="cancel" class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Existing Memberships List -->
    <div id="memberships-entries-list">
        <?php if (empty($memberships)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No memberships added yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($memberships as $membership): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900"><?php echo e($membership['organisation']); ?></h3>
                                <?php if ($membership['role']): ?>
                                    <p class="text-lg text-gray-700"><?php echo e($membership['role']); ?></p>
                                <?php endif; ?>
                                <?php if ($membership['start_date'] || $membership['end_date']): ?>
                                    <p class="text-sm text-gray-500">
                                        <?php echo $membership['start_date'] ? date('M Y', strtotime($membership['start_date'])) : ''; ?> - 
                                        <?php echo $membership['end_date'] ? date('M Y', strtotime($membership['end_date'])) : 'Present'; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" data-action="edit" data-entry-id="<?php echo e($membership['id']); ?>" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                <button type="button" data-action="delete" data-entry-id="<?php echo e($membership['id']); ?>" data-entry-type="memberships" class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
