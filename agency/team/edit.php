<?php
/**
 * Edit Team Member
 * Standardized form page for editing team member roles
 */

require_once __DIR__ . '/../../php/helpers.php';

// Require authentication and admin access
$org = requireOrganisationAccess('admin');

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

$memberId = sanitizeInput(get('id') ?? '');

if (empty($memberId)) {
    setFlash('error', 'Team member not specified.');
    redirect('/agency/team.php');
}

// Get member details
$member = db()->fetchOne(
    "SELECT om.*, p.email, p.full_name 
     FROM organisation_members om
     JOIN profiles p ON om.user_id = p.id
     WHERE om.id = ? AND om.organisation_id = ?",
    [$memberId, $org['organisation_id']]
);

if (!$member) {
    setFlash('error', 'Team member not found.');
    redirect('/agency/team.php');
}

// Cannot edit owner role
if ($member['role'] === 'owner') {
    setFlash('error', 'Cannot change the owner\'s role.');
    redirect('/agency/team.php');
}

// Handle form submission
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/agency/team/edit.php?id=' . $memberId);
    }

    $newRole = sanitizeInput(post('role'));

    if (!in_array($newRole, ['admin', 'recruiter', 'viewer'])) {
        setFlash('error', 'Invalid role selected.');
        redirect('/agency/team/edit.php?id=' . $memberId);
    }

    // Only owner can promote to admin
    if ($newRole === 'admin' && $org['role'] !== 'owner') {
        setFlash('error', 'Only the organisation owner can promote members to admin.');
        redirect('/agency/team/edit.php?id=' . $memberId);
    }

    try {
        db()->update('organisation_members',
            ['role' => $newRole, 'updated_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$memberId]
        );

        logActivity('team.role_updated', $member['user_id'], [
            'old_role' => $member['role'],
            'new_role' => $newRole
        ]);

        setFlash('success', 'Team member role updated successfully.');
        redirect('/agency/team.php');
    } catch (Exception $e) {
        setFlash('error', 'Failed to update role. Please try again.');
        redirect('/agency/team/edit.php?id=' . $memberId);
    }
}

// Build role options
$roleOptions = [];
if ($org['role'] === 'owner') {
    $roleOptions['admin'] = 'Admin - Full access to manage candidates and team';
}
$roleOptions['recruiter'] = 'Recruiter - Invite and manage assigned candidates';
$roleOptions['viewer'] = 'Viewer - Read-only access to candidates';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Edit Team Member | ' . e($org['organisation_name']),
        'metaDescription' => 'Edit team member role and permissions.',
        'canonicalUrl' => APP_URL . '/agency/team/edit.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('agency/header'); ?>

    <main id="main-content" class="py-6">
        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <?php ob_start(); ?>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

            <!-- Member Info Display -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm font-medium text-gray-700">Team Member</p>
                <p class="text-lg font-semibold text-gray-900 mt-1">
                    <?php echo e($member['full_name'] ?? $member['email']); ?>
                </p>
                <p class="text-sm text-gray-500 mt-1"><?php echo e($member['email']); ?></p>
            </div>

            <?php partial('forms/form-field', [
                'type' => 'select',
                'name' => 'role',
                'label' => 'Role',
                'required' => true,
                'options' => $roleOptions,
                'value' => $member['role']
            ]); ?>

            <!-- Role descriptions -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Role Permissions</h3>
                <ul class="space-y-2 text-xs text-gray-600">
                    <?php if ($org['role'] === 'owner'): ?>
                        <li>
                            <strong class="text-blue-600">Admin:</strong>
                            Manage team members, organisation settings, all candidates
                        </li>
                    <?php endif; ?>
                    <li>
                        <strong class="text-green-600">Recruiter:</strong>
                        Invite candidates, manage assigned candidates, view CVs
                    </li>
                    <li>
                        <strong class="text-gray-600">Viewer:</strong>
                        View all candidates and CVs (read-only)
                    </li>
                </ul>
            </div>

            <?php partial('forms/form-actions', [
                'submitText' => 'Update Role',
                'cancelUrl' => '/agency/team.php',
                'cancelText' => 'Cancel'
            ]); ?>
        </form>
        <?php $formContent = ob_get_clean(); ?>

        <?php partial('forms/form-card', [
            'title' => 'Edit Team Member',
            'description' => 'Update team member role and permissions.',
            'backUrl' => '/agency/team.php',
            'backText' => 'Back to team',
            'content' => $formContent
        ]); ?>
    </main>

    <?php partial('footer'); ?>
</body>
</html>

