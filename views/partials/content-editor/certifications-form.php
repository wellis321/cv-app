<?php
/**
 * Certifications Form Partial
 * When variant_id is in GET, loads from cv_variant_certifications.
 */

$editingId = $_GET['edit'] ?? null;
$variantId = $_GET['variant_id'] ?? null;
$editingCertification = null;
$certifications = [];
$isVariantContext = false;

if ($variantId) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if ($variant) {
        $isVariantContext = true;
        if ($editingId) {
            $editingCertification = db()->fetchOne("SELECT * FROM cv_variant_certifications WHERE cv_variant_id = ? AND (id = ? OR original_certification_id = ?)", [$variantId, $editingId, $editingId]);
        }
        $certifications = db()->fetchAll("SELECT * FROM cv_variant_certifications WHERE cv_variant_id = ? ORDER BY date_obtained DESC", [$variantId]);
    }
}

if (!$isVariantContext) {
    if ($editingId) {
        $editingCertification = db()->fetchOne("SELECT * FROM certifications WHERE id = ? AND profile_id = ?", [$editingId, $userId]);
    }
    $certifications = db()->fetchAll("SELECT * FROM certifications WHERE profile_id = ? ORDER BY date_obtained DESC", [$userId]);
}

$canAddCertification = planCanAddEntry($subscriptionContext, 'certifications', $userId, count($certifications));
?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Certifications</h1>
    
    <!-- Add/Edit Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingCertification ? 'Edit Certification' : 'Add New Certification'; ?>
        </h2>
        
        <?php if (!$editingCertification && !$canAddCertification): ?>
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
                <?php echo getPlanLimitMessage($subscriptionContext, 'certifications'); ?>
            </div>
        <?php else: ?>
        <form method="POST" data-section-form data-form-type="<?php echo $editingCertification ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingCertification ? 'update' : 'create'; ?>">
            <input type="hidden" name="section_id" value="certifications">
            <?php if ($editingCertification): ?>
                <input type="hidden" name="id" value="<?php echo e($editingCertification['id']); ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Certification Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $editingCertification ? e($editingCertification['name']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="issuer" class="block text-sm font-medium text-gray-700 mb-1">Issuing Organization *</label>
                    <input type="text" id="issuer" name="issuer" value="<?php echo $editingCertification ? e($editingCertification['issuer']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="date_obtained" class="block text-sm font-medium text-gray-700 mb-1">Date Obtained</label>
                        <input type="date" id="date_obtained" name="date_obtained" value="<?php echo $editingCertification && $editingCertification['date_obtained'] ? date('Y-m-d', strtotime($editingCertification['date_obtained'])) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" id="expiry_date" name="expiry_date" value="<?php echo $editingCertification && $editingCertification['expiry_date'] ? date('Y-m-d', strtotime($editingCertification['expiry_date'])) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingCertification ? 'Update Certification' : 'Add Certification'; ?>
                </button>
                <?php if ($editingCertification): ?>
                    <button type="button" data-action="cancel" class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Existing Certifications List -->
    <div id="certifications-entries-list">
        <?php if (empty($certifications)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No certifications added yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($certifications as $cert): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900"><?php echo e($cert['name']); ?></h3>
                                <p class="text-lg text-gray-700"><?php echo e($cert['issuer']); ?></p>
                                <?php if ($cert['date_obtained'] || $cert['expiry_date']): ?>
                                    <p class="text-sm text-gray-500">
                                        <?php if ($cert['date_obtained']): ?>
                                            Issued: <?php echo date('M Y', strtotime($cert['date_obtained'])); ?>
                                        <?php endif; ?>
                                        <?php if ($cert['expiry_date']): ?>
                                            <?php echo $cert['date_obtained'] ? ' · ' : ''; ?>Expires: <?php echo date('M Y', strtotime($cert['expiry_date'])); ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" data-action="edit" data-entry-id="<?php echo e($cert['id']); ?>" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                <button type="button" data-action="delete" data-entry-id="<?php echo e($cert['id']); ?>" data-entry-type="certifications" class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
