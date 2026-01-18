<?php
/**
 * Edit Organisation
 * Standardized form page for editing organisations (Super Admin)
 */

require_once __DIR__ . '/../../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

$orgId = sanitizeInput(get('id') ?? '');

if (empty($orgId)) {
    setFlash('error', 'Organisation not specified.');
    redirect('/admin/organisations.php');
}

$org = getOrganisationById($orgId);

if (!$org) {
    setFlash('error', 'Organisation not found.');
    redirect('/admin/organisations.php');
}

// Handle form submission
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/admin/organisations/edit.php?id=' . $orgId);
    }

    $updateData = [];
    
    if (post('name') !== null) {
        $updateData['name'] = sanitizeInput(post('name'));
    }
    if (post('plan') !== null) {
        $updateData['plan'] = sanitizeInput(post('plan'));
    }
    if (post('subscription_status') !== null) {
        $updateData['subscription_status'] = sanitizeInput(post('subscription_status'));
    }
    if (post('max_candidates') !== null) {
        $updateData['max_candidates'] = (int)post('max_candidates');
    }
    if (post('max_team_members') !== null) {
        $updateData['max_team_members'] = (int)post('max_team_members');
    }
    
    $updateData['updated_at'] = date('Y-m-d H:i:s');
    
    try {
        db()->update('organisations', $updateData, 'id = ?', [$orgId]);
        logActivity('admin.organisation.updated', null, ['organisation_id' => $orgId, 'changes' => $updateData], null);
        setFlash('success', 'Organisation updated successfully.');
        redirect('/admin/organisations.php?id=' . $orgId);
    } catch (Exception $e) {
        error_log("Failed to update organisation: " . $e->getMessage());
        setFlash('error', 'Failed to update organisation. Please try again.');
        redirect('/admin/organisations/edit.php?id=' . $orgId);
    }
}

// Get organisation members
$orgMembers = getOrganisationTeamMembers($orgId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Edit Organisation | Super Admin',
        'metaDescription' => 'Edit organisation details',
        'canonicalUrl' => APP_URL . '/admin/organisations/edit.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>

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

            <!-- Organisation Info Display -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm font-medium text-gray-700">Organisation</p>
                <p class="text-lg font-semibold text-gray-900 mt-1"><?php echo e($org['name']); ?></p>
                <p class="text-sm text-gray-500 mt-1">Slug: <?php echo e($org['slug']); ?></p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <?php partial('forms/form-field', [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Organisation Name',
                    'value' => $org['name'] ?? ''
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'select',
                    'name' => 'plan',
                    'label' => 'Plan',
                    'options' => [
                        'agency_basic' => 'Basic',
                        'agency_pro' => 'Professional',
                        'agency_enterprise' => 'Enterprise'
                    ],
                    'value' => $org['plan'] ?? 'agency_basic'
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'select',
                    'name' => 'subscription_status',
                    'label' => 'Subscription Status',
                    'options' => [
                        'inactive' => 'Inactive',
                        'active' => 'Active',
                        'cancelled' => 'Cancelled'
                    ],
                    'value' => $org['subscription_status'] ?? 'inactive'
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'number',
                    'name' => 'max_candidates',
                    'label' => 'Max Candidates',
                    'value' => $org['max_candidates'] ?? 10,
                    'min' => 1
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'number',
                    'name' => 'max_team_members',
                    'label' => 'Max Team Members',
                    'value' => $org['max_team_members'] ?? 3,
                    'min' => 1
                ]); ?>
            </div>

            <?php partial('forms/form-actions', [
                'submitText' => 'Update Organisation',
                'cancelUrl' => '/admin/organisations.php?id=' . $orgId,
                'cancelText' => 'Cancel'
            ]); ?>
        </form>
        <?php $formContent = ob_get_clean(); ?>

        <?php partial('forms/form-card', [
            'title' => 'Edit Organisation',
            'description' => 'Update organisation details and settings.',
            'backUrl' => '/admin/organisations.php?id=' . $orgId,
            'backText' => 'Back to organisation',
            'content' => $formContent
        ]); ?>

        <!-- Team Members Section -->
        <?php if (!empty($orgMembers)): ?>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Team Members</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($orgMembers as $member): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <?php echo e($member['full_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <?php echo e($member['email']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                <?php 
                                                $roleColors = [
                                                    'owner' => 'bg-purple-100 text-purple-800',
                                                    'admin' => 'bg-blue-100 text-blue-800',
                                                    'recruiter' => 'bg-green-100 text-green-800',
                                                    'viewer' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $roleColors[$member['role']] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst($member['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                <?php echo $member['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $member['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <?php partial('footer'); ?>
</body>
</html>

