<?php
/**
 * Invite Team Member
 * Standardized form page for inviting team members
 */

require_once __DIR__ . '/../../php/helpers.php';

// Require authentication and admin access
$org = requireOrganisationAccess('admin');

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Check if organisation can add more team members
if (!canAddTeamMember($org['organisation_id'])) {
    setFlash('error', 'Your organisation has reached its team member limit. Please request a limit increase in Settings.');
    redirect('/agency/team.php');
}

// Handle form submission
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/agency/team/create.php');
    }

    $email = sanitizeInput(post('email'));
    $role = sanitizeInput(post('role'));
    $message = sanitizeInput(post('message'));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
        redirect('/agency/team/create.php');
    }

    // Validate role
    if (!in_array($role, ['admin', 'recruiter', 'viewer'])) {
        setFlash('error', 'Invalid role selected.');
        redirect('/agency/team/create.php');
    }

    // Only owners can invite admins
    if ($role === 'admin' && $org['role'] !== 'owner') {
        setFlash('error', 'Only organisation owners can invite administrators.');
        redirect('/agency/team/create.php');
    }

    // Create invitation
    $result = createTeamInvitation(
        $org['organisation_id'],
        $email,
        $role,
        getUserId(),
        $message
    );

    if ($result['success']) {
        setFlash('success', 'Team invitation sent successfully to ' . $email);
        redirect('/agency/team.php');
    } else {
        setFlash('error', $result['error']);
        redirect('/agency/team/create.php');
    }
}

// Get pending team invitations
$pendingInvitations = getPendingTeamInvitations($org['organisation_id']);

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
        'pageTitle' => 'Invite Team Member | ' . e($org['organisation_name']),
        'metaDescription' => 'Send an invitation to a team member to join your organisation.',
        'canonicalUrl' => APP_URL . '/agency/team/create.php',
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

            <?php partial('forms/form-field', [
                'type' => 'email',
                'name' => 'email',
                'label' => 'Email Address',
                'required' => true,
                'placeholder' => 'colleague@example.com',
                'help' => 'They will receive an invitation email at this address.'
            ]); ?>

            <?php partial('forms/form-field', [
                'type' => 'select',
                'name' => 'role',
                'label' => 'Role',
                'required' => true,
                'options' => $roleOptions,
                'value' => 'recruiter'
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

            <?php partial('forms/form-field', [
                'type' => 'textarea',
                'name' => 'message',
                'label' => 'Personal Message',
                'rows' => 3,
                'placeholder' => 'Looking forward to working with you...',
                'help' => 'Optional. Add a personal note to the invitation email.'
            ]); ?>

            <?php partial('forms/form-actions', [
                'submitText' => 'Send Invitation',
                'cancelUrl' => '/agency/team.php',
                'cancelText' => 'Cancel'
            ]); ?>
        </form>
        <?php $formContent = ob_get_clean(); ?>

        <?php partial('forms/form-card', [
            'title' => 'Invite Team Member',
            'description' => 'Invite recruiters and administrators to help manage candidates.',
            'backUrl' => '/agency/team.php',
            'backText' => 'Back to team',
            'content' => $formContent
        ]); ?>

        <!-- Pending Invitations -->
        <?php if (!empty($pendingInvitations)): ?>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pending Team Invitations</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($pendingInvitations as $invitation): ?>
                            <li class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($invitation['email']); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                <?php
                                                $roleClasses = [
                                                    'admin' => 'bg-blue-100 text-blue-800',
                                                    'recruiter' => 'bg-green-100 text-green-800',
                                                    'viewer' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $roleClasses[$invitation['role']] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst($invitation['role']); ?>
                                            </span>
                                            - Invited <?php echo date('j M Y', strtotime($invitation['created_at'])); ?>
                                            - Expires <?php echo date('j M Y', strtotime($invitation['expires_at'])); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <form method="POST" action="/api/agency/cancel-invitation.php" class="inline" onsubmit="return confirm('Cancel this invitation?');">
                                            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                            <input type="hidden" name="invitation_id" value="<?php echo e($invitation['id']); ?>">
                                            <input type="hidden" name="type" value="team">
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 px-3 py-1 rounded hover:bg-red-50">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <?php partial('footer'); ?>
</body>
</html>

