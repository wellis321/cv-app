<?php
/**
 * Agency Dashboard
 * Overview for recruitment agency users
 */

require_once __DIR__ . '/../php/helpers.php';

// Require authentication and organisation membership
$org = requireOrganisationAccess('viewer');

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get dashboard statistics
$candidateCount = getOrganisationCandidateCount($org['organisation_id']);
$teamMemberCount = getOrganisationTeamMemberCount($org['organisation_id']);

// Get recent candidates (last 5)
$recentCandidates = getOrganisationCandidates(
    $org['organisation_id'],
    $org['role'] === 'recruiter' ? getUserId() : null,
    ['limit' => 5]
);

// Get recent activity (last 10)
$recentActivity = getOrganisationActivityLog($org['organisation_id'], 10);

// Calculate usage percentages
$candidateUsagePercent = $org['max_candidates'] > 0
    ? min(100, round(($candidateCount / $org['max_candidates']) * 100))
    : 0;

$teamUsagePercent = $org['max_team_members'] > 0
    ? min(100, round(($teamMemberCount / $org['max_team_members']) * 100))
    : 0;

// Check if user can invite candidates
$canInvite = canAddCandidate($org['organisation_id']) && in_array($org['role'], ['owner', 'admin', 'recruiter']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Agency Dashboard | ' . e($org['organisation_name']),
        'metaDescription' => 'Manage your candidates, team members, and organisation settings.',
        'canonicalUrl' => APP_URL . '/agency/dashboard.php',
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
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Dashboard Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Welcome back<?php echo $user['full_name'] ? ', ' . e($user['full_name']) : ''; ?>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Here's what's happening with <?php echo e($org['organisation_name']); ?> today.
                    </p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <?php if ($canInvite): ?>
                        <a href="/agency/candidates/create.php"
                           class="ml-3 inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                            </svg>
                            Invite Candidate
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if ($canInvite || in_array($org['role'], ['owner', 'admin'])): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php if ($canInvite): ?>
                    <a href="/agency/candidates/create.php"
                       class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-gray-900">Invite Candidate</p>
                            <p class="truncate text-sm text-gray-500">Send invitation email</p>
                        </div>
                    </a>
                <?php endif; ?>

                <?php if (in_array($org['role'], ['owner', 'admin'])): ?>
                    <a href="/agency/team.php?action=invite"
                       class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-gray-900">Invite Team Member</p>
                            <p class="truncate text-sm text-gray-500">Add a recruiter or admin</p>
                        </div>
                    </a>

                    <a href="/agency/settings.php"
                       class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-gray-900">Organisation Settings</p>
                            <p class="truncate text-sm text-gray-500">Branding and preferences</p>
                        </div>
                    </a>
                <?php endif; ?>

                <a href="/agency/candidates.php?export=1"
                   class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Export Candidates</p>
                        <p class="truncate text-sm text-gray-500">Download as CSV</p>
                    </div>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-gray-100 rounded-xl p-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Recent Candidates -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200">
                        <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900">Recent Candidates</h2>
                            <a href="/agency/candidates.php" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all</a>
                        </div>
                        <?php if (empty($recentCandidates)): ?>
                            <div class="mt-6 text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No candidates yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by inviting your first candidate.</p>
                                <?php if ($canInvite): ?>
                                    <div class="mt-6">
                                        <a href="/agency/candidates/create.php"
                                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                                            </svg>
                                            Invite Candidate
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <ul class="mt-6 divide-y divide-gray-200">
                                <?php foreach ($recentCandidates as $candidate): ?>
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <?php if ($candidate['photo_url']): ?>
                                                    <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e($candidate['photo_url']); ?>" alt="">
                                                <?php else: ?>
                                                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500">
                                                        <span class="text-sm font-medium leading-none text-white">
                                                            <?php echo strtoupper(substr($candidate['full_name'] ?? $candidate['email'], 0, 2)); ?>
                                                        </span>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900">
                                                    <?php echo e($candidate['full_name'] ?? 'Unnamed'); ?>
                                                </p>
                                                <p class="truncate text-sm text-gray-500">
                                                    <?php echo e($candidate['email']); ?>
                                                </p>
                                            </div>
                                            <div>
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                    <?php
                                                    $statusClasses = [
                                                        'draft' => 'bg-gray-100 text-gray-800',
                                                        'complete' => 'bg-green-100 text-green-800',
                                                        'published' => 'bg-blue-100 text-blue-800',
                                                        'archived' => 'bg-yellow-100 text-yellow-800'
                                                    ];
                                                    echo $statusClasses[$candidate['cv_status']] ?? 'bg-gray-100 text-gray-800';
                                                    ?>">
                                                    <?php echo ucfirst($candidate['cv_status'] ?? 'draft'); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <a href="/agency/candidate.php?id=<?php echo e($candidate['id']); ?>"
                                                   class="text-blue-600 hover:text-blue-500">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200">
                        <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
                        <?php if (empty($recentActivity)): ?>
                            <div class="mt-6 text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No activity yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Activity will appear here as you and your team work.</p>
                            </div>
                        <?php else: ?>
                            <div class="mt-6 flow-root">
                                <ul class="-mb-8">
                                    <?php foreach ($recentActivity as $index => $activity): ?>
                                        <li>
                                            <div class="relative pb-8">
                                                <?php if ($index < count($recentActivity) - 1): ?>
                                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                <?php endif; ?>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                <span class="font-medium text-gray-900"><?php echo e($activity['actor_name'] ?? 'System'); ?></span>
                                                                <?php echo e(str_replace('.', ' ', $activity['action'])); ?>
                                                                <?php if ($activity['target_name']): ?>
                                                                    <span class="font-medium text-gray-900"><?php echo e($activity['target_name']); ?></span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                            <time datetime="<?php echo $activity['created_at']; ?>">
                                                                <?php
                                                                $activityTime = strtotime($activity['created_at']);
                                                                $diff = time() - $activityTime;
                                                                if ($diff < 60) {
                                                                    echo 'Just now';
                                                                } elseif ($diff < 3600) {
                                                                    echo floor($diff / 60) . 'm ago';
                                                                } elseif ($diff < 86400) {
                                                                    echo floor($diff / 3600) . 'h ago';
                                                                } else {
                                                                    echo date('j M', $activityTime);
                                                                }
                                                                ?>
                                                            </time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>
