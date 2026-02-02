<?php
/**
 * Skills Form Partial
 * When variant_id is in GET, loads from cv_variant_skills.
 */

$editingId = $_GET['edit'] ?? null;
$variantId = $_GET['variant_id'] ?? null;
$editingSkill = null;
$skills = [];
$isVariantContext = false;

if ($variantId) {
    $variant = db()->fetchOne("SELECT id FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    if ($variant) {
        $isVariantContext = true;
        if ($editingId) {
            $editingSkill = db()->fetchOne("SELECT * FROM cv_variant_skills WHERE cv_variant_id = ? AND (id = ? OR original_skill_id = ?)", [$variantId, $editingId, $editingId]);
        }
        $skills = db()->fetchAll("SELECT * FROM cv_variant_skills WHERE cv_variant_id = ? ORDER BY category ASC, name ASC", [$variantId]);
    }
}

if (!$isVariantContext) {
    if ($editingId) {
        $editingSkill = db()->fetchOne("SELECT * FROM skills WHERE id = ? AND profile_id = ?", [$editingId, $userId]);
    }
    $skills = db()->fetchAll("SELECT * FROM skills WHERE profile_id = ? ORDER BY category ASC, name ASC", [$userId]);
}

$canAddSkill = planCanAddEntry($subscriptionContext, 'skills', $userId, count($skills));
?>
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Skills</h1>
        <button type="button" onclick="assessSection('skills')" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-md border border-gray-300 hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Assess This Section
        </button>
    </div>
    
    <!-- Add/Edit Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingSkill ? 'Edit Skill' : 'Add New Skill'; ?>
        </h2>
        
        <?php if (!$editingSkill && !$canAddSkill): ?>
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
                <?php echo getPlanLimitMessage($subscriptionContext, 'skills'); ?>
            </div>
        <?php else: ?>
        <form method="POST" data-section-form data-form-type="<?php echo $editingSkill ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingSkill ? 'update' : 'create'; ?>">
            <input type="hidden" name="section_id" value="skills">
            <?php if ($editingSkill): ?>
                <input type="hidden" name="id" value="<?php echo e($editingSkill['id']); ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Skill Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo $editingSkill ? e($editingSkill['name']) : ''; ?>" required maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Proficiency Level</label>
                    <input type="text" id="level" name="level" value="<?php echo $editingSkill ? e($editingSkill['level'] ?? '') : ''; ?>" maxlength="50" placeholder="e.g., Beginner, Intermediate, Advanced" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="sm:col-span-2">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <input type="text" id="category" name="category" value="<?php echo $editingSkill ? e($editingSkill['category'] ?? '') : ''; ?>" maxlength="100" placeholder="e.g., Technical, Languages, Soft Skills" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingSkill ? 'Update Skill' : 'Add Skill'; ?>
                </button>
                <?php if ($editingSkill): ?>
                    <button type="button" data-action="cancel" class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Existing Skills List -->
    <div id="skills-entries-list">
        <?php if (empty($skills)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No skills added yet.</p>
            </div>
        <?php else: ?>
            <?php
            // Group skills by category
            $grouped = [];
            foreach ($skills as $skill) {
                $cat = $skill['category'] ?: 'Other';
                if (!isset($grouped[$cat])) {
                    $grouped[$cat] = [];
                }
                $grouped[$cat][] = $skill;
            }
            ksort($grouped);
            ?>
            <div class="space-y-6">
                <?php foreach ($grouped as $category => $categorySkills): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3"><?php echo e($category); ?></h3>
                        <div class="space-y-2">
                            <?php foreach ($categorySkills as $skill): ?>
                                <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                                    <span>
                                        <?php echo e($skill['name']); ?>
                                        <?php if ($skill['level']): ?>
                                            <span class="text-gray-500">(<?php echo e($skill['level']); ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                    <div class="flex gap-2">
                                        <button type="button" data-action="edit" data-entry-id="<?php echo e($skill['id']); ?>" class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                        <button type="button" data-action="delete" data-entry-id="<?php echo e($skill['id']); ?>" data-entry-type="skills" class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
