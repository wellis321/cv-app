<?php
/**
 * Agency Team Management
 * Manage team members (recruiters, admins)
 */

require_once __DIR__ . '/../php/helpers.php';

// Require authentication and admin access
$org = requireOrganisationAccess('admin');

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Handle POST actions
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/agency/team.php');
    }

    $action = post('action');

    // Role updates are now handled in team/edit.php

    // Deactivate member
    if ($action === 'deactivate') {
        $memberId = sanitizeInput(post('member_id'));

        $member = db()->fetchOne(
            "SELECT * FROM organisation_members WHERE id = ? AND organisation_id = ?",
            [$memberId, $org['organisation_id']]
        );

        if (!$member) {
            setFlash('error', 'Team member not found.');
            redirect('/agency/team.php');
        }

        if ($member['role'] === 'owner') {
            setFlash('error', 'Cannot deactivate the organisation owner.');
            redirect('/agency/team.php');
        }

        if ($member['user_id'] === getUserId()) {
            setFlash('error', 'You cannot deactivate yourself.');
            redirect('/agency/team.php');
        }

        try {
            db()->update('organisation_members',
                ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$memberId]
            );

            logActivity('team.member_deactivated', $member['user_id']);

            setFlash('success', 'Team member has been deactivated.');
        } catch (Exception $e) {
            setFlash('error', 'Failed to deactivate member. Please try again.');
        }

        redirect('/agency/team.php');
    }

    // Reactivate member
    if ($action === 'reactivate') {
        $memberId = sanitizeInput(post('member_id'));

        $member = db()->fetchOne(
            "SELECT * FROM organisation_members WHERE id = ? AND organisation_id = ?",
            [$memberId, $org['organisation_id']]
        );

        if (!$member) {
            setFlash('error', 'Team member not found.');
            redirect('/agency/team.php');
        }

        try {
            db()->update('organisation_members',
                ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$memberId]
            );

            logActivity('team.member_reactivated', $member['user_id']);

            setFlash('success', 'Team member has been reactivated.');
        } catch (Exception $e) {
            setFlash('error', 'Failed to reactivate member. Please try again.');
        }

        redirect('/agency/team.php');
    }

    // Remove member completely
    if ($action === 'remove') {
        $memberId = sanitizeInput(post('member_id'));

        $member = db()->fetchOne(
            "SELECT * FROM organisation_members WHERE id = ? AND organisation_id = ?",
            [$memberId, $org['organisation_id']]
        );

        if (!$member) {
            setFlash('error', 'Team member not found.');
            redirect('/agency/team.php');
        }

        if ($member['role'] === 'owner') {
            setFlash('error', 'Cannot remove the organisation owner.');
            redirect('/agency/team.php');
        }

        if ($member['user_id'] === getUserId()) {
            setFlash('error', 'You cannot remove yourself.');
            redirect('/agency/team.php');
        }

        try {
            db()->delete('organisation_members', 'id = ?', [$memberId]);

            logActivity('team.member_removed', $member['user_id']);

            setFlash('success', 'Team member has been removed from the organisation.');
        } catch (Exception $e) {
            setFlash('error', 'Failed to remove member. Please try again.');
        }

        redirect('/agency/team.php');
    }
}

// Get team members
$teamMembers = getOrganisationTeamMembers($org['organisation_id']);
$teamMemberCount = getOrganisationTeamMemberCount($org['organisation_id']);

// Check if can add more team members
$canAddTeamMember = canAddTeamMember($org['organisation_id']);

// Get pending invitations
$pendingInvitations = db()->fetchAll(
    "SELECT ti.*, inviter.full_name as invited_by_name
     FROM team_invitations ti
     LEFT JOIN profiles inviter ON ti.invited_by = inviter.id
     WHERE ti.organisation_id = ? AND ti.accepted_at IS NULL AND ti.expires_at > NOW()
     ORDER BY ti.created_at DESC",
    [$org['organisation_id']]
);

// Show invite form if requested
$showInviteForm = get('action') === 'invite';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Team Management | ' . e($org['organisation_name']),
        'metaDescription' => 'Manage your organisation\'s team members.',
        'canonicalUrl' => APP_URL . '/agency/team.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('agency/header'); ?>

    <main id="main-content" class="py-6">
        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Team Management</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            <?php echo $teamMemberCount; ?> of <?php echo $org['max_team_members']; ?> team members
                        </p>
                    </div>
                    <!-- Role Permissions Info Button -->
                    <button type="button"
                            onclick="toggleRoleInfo()"
                            class="flex-shrink-0 p-2 text-gray-400 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full transition-colors"
                            aria-label="View role permissions information"
                            title="View role permissions">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-4 sm:mt-0">
                    <?php if ($canAddTeamMember): ?>
                        <a href="/agency/team/create.php"
                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                            </svg>
                            Invite Team Member
                        </a>
                    <?php else: ?>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-500">
                            Team limit reached
                            <?php if ($org['role'] === 'owner'): ?>
                                - <a href="/agency/billing.php" class="ml-1 text-blue-600 hover:text-blue-500">Upgrade</a>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Role Permissions Info (Collapsible) -->
        <div id="role-permissions-info" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 hidden">
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg shadow-sm p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                            </svg>
                            Role Permissions
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 mb-1">Owner</span>
                                <p class="text-gray-700">Full access, billing, can transfer ownership</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 mb-1">Admin</span>
                                <p class="text-gray-700">Manage team, candidates, and settings</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 mb-1">Recruiter</span>
                                <p class="text-gray-700">Invite and manage assigned candidates</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 mb-1">Viewer</span>
                                <p class="text-gray-700">View candidates only, no editing</p>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            onclick="toggleRoleInfo()"
                            class="ml-4 flex-shrink-0 text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1"
                            aria-label="Close role permissions">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Pending Invitations -->
        <?php if (!empty($pendingInvitations)): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-yellow-800 mb-3">Pending Invitations</h3>
                    <ul class="space-y-2">
                        <?php foreach ($pendingInvitations as $invitation): ?>
                            <li class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($invitation['email']); ?></span>
                                    <span class="ml-2 text-xs text-gray-500">
                                        as <?php echo ucfirst($invitation['role']); ?> -
                                        expires <?php echo date('j M Y', strtotime($invitation['expires_at'])); ?>
                                    </span>
                                </div>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                    <input type="hidden" name="action" value="cancel_invitation">
                                    <input type="hidden" name="invitation_id" value="<?php echo e($invitation['id']); ?>">
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Cancel</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Team Members List -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Card View -->
            <div class="sm:hidden space-y-4">
                <?php foreach ($teamMembers as $member): ?>
                    <div class="bg-white shadow rounded-lg p-4 <?php echo !$member['is_active'] ? 'opacity-60' : ''; ?>">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <?php if ($member['photo_url']): ?>
                                        <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e($member['photo_url']); ?>" alt="">
                                    <?php else: ?>
                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500">
                                            <span class="text-sm font-medium leading-none text-white">
                                                <?php echo strtoupper(substr($member['full_name'] ?? $member['email'], 0, 2)); ?>
                                            </span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        <?php echo e($member['full_name'] ?? 'Unnamed'); ?>
                                        <?php if ($member['user_id'] === getUserId()): ?>
                                            <span class="text-xs text-gray-500">(you)</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate"><?php echo e($member['email']); ?></p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <?php if ($member['role'] === 'owner' || $member['user_id'] === getUserId()): ?>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                <?php
                                                $roleClasses = [
                                                    'owner' => 'bg-purple-100 text-purple-800',
                                                    'admin' => 'bg-blue-100 text-blue-800',
                                                    'recruiter' => 'bg-green-100 text-green-800',
                                                    'viewer' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $roleClasses[$member['role']] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst($member['role']); ?>
                                            </span>
                                        <?php else: ?>
                                            <a href="/agency/team/edit.php?id=<?php echo e($member['membership_id']); ?>" 
                                               class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                <?php
                                                $roleClasses = [
                                                    'owner' => 'bg-purple-100 text-purple-800',
                                                    'admin' => 'bg-blue-100 text-blue-800',
                                                    'recruiter' => 'bg-green-100 text-green-800',
                                                    'viewer' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $roleClasses[$member['role']] ?? 'bg-gray-100 text-gray-800';
                                                ?> hover:opacity-80">
                                                <?php echo ucfirst($member['role']); ?>
                                            </a>
                                        <?php endif; ?>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                            <?php echo $member['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $member['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Joined <?php echo $member['joined_at'] ? date('j M Y', strtotime($member['joined_at'])) : 'Pending'; ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($member['role'] !== 'owner' && $member['user_id'] !== getUserId()): ?>
                                <div class="flex flex-col gap-1 ml-2">
                                    <a href="/agency/team/edit.php?id=<?php echo e($member['membership_id']); ?>" 
                                       class="text-xs text-blue-600 hover:text-blue-900 font-semibold px-2 py-1">
                                        Edit
                                    </a>
                                    <?php if ($member['is_active']): ?>
                                        <form method="POST" class="inline" onsubmit="return confirm('Deactivate this team member?');">
                                            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                            <input type="hidden" name="action" value="deactivate">
                                            <input type="hidden" name="member_id" value="<?php echo e($member['membership_id']); ?>">
                                            <button type="submit" class="text-xs text-yellow-600 hover:text-yellow-900 px-2 py-1">Deactivate</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                            <input type="hidden" name="action" value="reactivate">
                                            <input type="hidden" name="member_id" value="<?php echo e($member['membership_id']); ?>">
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-900 px-2 py-1">Reactivate</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" class="inline" onsubmit="return confirm('Remove this team member? This cannot be undone.');">
                                        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="member_id" value="<?php echo e($member['membership_id']); ?>">
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-900 px-2 py-1">Remove</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-hidden bg-white shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Member</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Joined</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($teamMembers as $member): ?>
                            <tr class="<?php echo !$member['is_active'] ? 'bg-gray-50' : ''; ?>">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <?php if ($member['photo_url']): ?>
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e($member['photo_url']); ?>" alt="">
                                            <?php else: ?>
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500">
                                                    <span class="text-sm font-medium leading-none text-white">
                                                        <?php echo strtoupper(substr($member['full_name'] ?? $member['email'], 0, 2)); ?>
                                                    </span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900">
                                                <?php echo e($member['full_name'] ?? 'Unnamed'); ?>
                                                <?php if ($member['user_id'] === getUserId()): ?>
                                                    <span class="text-xs text-gray-500">(you)</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-gray-500"><?php echo e($member['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <?php if ($member['role'] === 'owner' || $member['user_id'] === getUserId()): ?>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            <?php
                                            $roleClasses = [
                                                'owner' => 'bg-purple-100 text-purple-800',
                                                'admin' => 'bg-blue-100 text-blue-800',
                                                'recruiter' => 'bg-green-100 text-green-800',
                                                'viewer' => 'bg-gray-100 text-gray-800'
                                            ];
                                            echo $roleClasses[$member['role']] ?? 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo ucfirst($member['role']); ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="/agency/team/edit.php?id=<?php echo e($member['membership_id']); ?>" 
                                           class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            <?php
                                            $roleClasses = [
                                                'owner' => 'bg-purple-100 text-purple-800',
                                                'admin' => 'bg-blue-100 text-blue-800',
                                                'recruiter' => 'bg-green-100 text-green-800',
                                                'viewer' => 'bg-gray-100 text-gray-800'
                                            ];
                                            echo $roleClasses[$member['role']] ?? 'bg-gray-100 text-gray-800';
                                            ?> hover:opacity-80">
                                            <?php echo ucfirst($member['role']); ?>
                                            <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <?php if ($member['is_active']): ?>
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?php echo $member['joined_at'] ? date('j M Y', strtotime($member['joined_at'])) : 'Pending'; ?>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <?php if ($member['role'] !== 'owner' && $member['user_id'] !== getUserId()): ?>
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="/agency/team/edit.php?id=<?php echo e($member['membership_id']); ?>" 
                                               class="text-blue-600 hover:text-blue-900 font-semibold">
                                                Edit
                                            </a>
                                            <?php if ($member['is_active']): ?>
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this team member?');">
                                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <input type="hidden" name="member_id" value="<?php echo e($member['membership_id']); ?>">
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">Deactivate</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                                    <input type="hidden" name="action" value="reactivate">
                                                    <input type="hidden" name="member_id" value="<?php echo e($member['membership_id']); ?>">
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Reactivate</button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this team member? This cannot be undone.');">
                                                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="member_id" value="<?php echo e($member['membership_id']); ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>

    <script>
        function toggleRoleInfo() {
            const infoPanel = document.getElementById('role-permissions-info');
            if (infoPanel) {
                infoPanel.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
