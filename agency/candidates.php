<?php
/**
 * Agency Candidates Management
 * List, search, and manage candidates
 */

require_once __DIR__ . '/../php/helpers.php';

// Require authentication and organisation membership
$org = requireOrganisationAccess('viewer');

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get filter parameters
$search = sanitizeInput(get('search') ?? '');
$statusFilter = sanitizeInput(get('status') ?? '');
$recruiterFilter = sanitizeInput(get('recruiter') ?? '');
$page = max(1, (int)(get('page') ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build filters array
$filters = [
    'limit' => $perPage,
    'offset' => $offset
];

if ($search) {
    $filters['search'] = $search;
}

if ($statusFilter && $statusFilter !== 'pending') {
    $filters['cv_status'] = $statusFilter;
}

// Determine if we should filter by recruiter
$recruiterId = null;
if ($org['role'] === 'recruiter') {
    // Recruiters can only see their own candidates
    $recruiterId = getUserId();
} elseif ($recruiterFilter && in_array($org['role'], ['owner', 'admin'])) {
    // Admins/owners can filter by recruiter
    $recruiterId = $recruiterFilter;
}

// If status filter is "pending", show pending invitations instead of candidates
if ($statusFilter === 'pending') {
    // Get pending invitations
    $pendingInvitations = getPendingCandidateInvitations($org['organisation_id']);
    
    // Filter by recruiter if specified
    if ($recruiterId !== null) {
        $pendingInvitations = array_filter($pendingInvitations, function($inv) use ($recruiterId) {
            return $inv['assigned_recruiter'] === $recruiterId;
        });
    }
    
    // Apply search filter
    if ($search) {
        $searchLower = strtolower($search);
        $pendingInvitations = array_filter($pendingInvitations, function($inv) use ($searchLower) {
            return strpos(strtolower($inv['email'] ?? ''), $searchLower) !== false ||
                   strpos(strtolower($inv['full_name'] ?? ''), $searchLower) !== false;
        });
    }
    
    // Convert to candidate-like format for display
    $pendingFormatted = [];
    foreach ($pendingInvitations as $inv) {
        $pendingFormatted[] = [
            'id' => $inv['id'],
            'full_name' => $inv['full_name'] ?? '',
            'email' => $inv['email'],
            'username' => null, // Pending invitations don't have usernames yet
            'photo_url' => null,
            'cv_status' => 'pending',
            'recruiter_name' => $inv['recruiter_name'] ?? null,
            'recruiter_id' => $inv['assigned_recruiter'] ?? null,
            'created_at' => $inv['created_at'],
            'expires_at' => $inv['expires_at'],
            'is_pending' => true // Flag to identify pending invitations
        ];
    }
    
    // Apply pagination
    $totalCandidates = count($pendingFormatted);
    $totalPages = ceil($totalCandidates / $perPage);
    $candidates = array_slice($pendingFormatted, $offset, $perPage);
} else {
    // Get candidates (normal flow)
    $candidates = getOrganisationCandidates($org['organisation_id'], $recruiterId, $filters);

    // Get total count for pagination
    $totalFilters = $filters;
    unset($totalFilters['limit'], $totalFilters['offset']);
    $allCandidates = getOrganisationCandidates($org['organisation_id'], $recruiterId, $totalFilters);
    $totalCandidates = count($allCandidates);
    $totalPages = ceil($totalCandidates / $perPage);
}

// Get team members for filter dropdown (admins/owners only)
$teamMembers = [];
if (in_array($org['role'], ['owner', 'admin'])) {
    $teamMembers = getOrganisationTeamMembers($org['organisation_id']);
}

// Handle CSV export
if (get('export') === '1' && in_array($org['role'], ['owner', 'admin', 'recruiter'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="candidates-' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'Username', 'Status', 'Recruiter', 'Created']);

    foreach ($allCandidates as $candidate) {
        fputcsv($output, [
            $candidate['full_name'] ?? '',
            $candidate['email'],
            $candidate['username'],
            $candidate['cv_status'] ?? 'draft',
            $candidate['recruiter_name'] ?? '',
            $candidate['created_at']
        ]);
    }

    fclose($output);
    exit;
}

// Check if user can invite candidates
$canInvite = canAddCandidate($org['organisation_id']) && in_array($org['role'], ['owner', 'admin', 'recruiter']);
$canManage = in_array($org['role'], ['owner', 'admin', 'recruiter']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Candidates | ' . e($org['organisation_name']),
        'metaDescription' => 'Manage your organisation\'s candidates and their CVs.',
        'canonicalUrl' => APP_URL . '/agency/candidates.php',
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
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Candidates</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        <?php echo $totalCandidates; ?> candidate<?php echo $totalCandidates !== 1 ? 's' : ''; ?>
                        <?php if ($org['role'] === 'recruiter'): ?>
                            assigned to you
                        <?php endif; ?>
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-4 flex space-x-3">
                    <?php if ($canManage): ?>
                        <a href="?export=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $statusFilter ? '&status=' . urlencode($statusFilter) : ''; ?>"
                           class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd"/>
                            </svg>
                            Export CSV
                        </a>
                    <?php endif; ?>
                    <?php if ($canInvite): ?>
                        <a href="/agency/candidates/create.php"
                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                            </svg>
                            Invite Candidate
                        </a>
                    <?php elseif (!canAddCandidate($org['organisation_id'])): ?>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-500">
                            Candidate limit reached
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <form method="GET" class="bg-white shadow rounded-lg p-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div class="sm:col-span-2">
                        <label for="search" class="sr-only">Search candidates</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="<?php echo e($search); ?>"
                                   placeholder="Search by name or email..."
                                   class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="sr-only">CV Status</label>
                        <select name="status"
                                id="status"
                                class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                            <option value="">All statuses</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="complete" <?php echo $statusFilter === 'complete' ? 'selected' : ''; ?>>Complete</option>
                            <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>

                    <!-- Recruiter Filter (admins/owners only) -->
                    <?php if (in_array($org['role'], ['owner', 'admin']) && !empty($teamMembers)): ?>
                    <div>
                        <label for="recruiter" class="sr-only">Assigned Recruiter</label>
                        <select name="recruiter"
                                id="recruiter"
                                class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                            <option value="">All recruiters</option>
                            <?php foreach ($teamMembers as $member): ?>
                                <?php if (in_array($member['role'], ['owner', 'admin', 'recruiter'])): ?>
                                    <option value="<?php echo e($member['user_id']); ?>" <?php echo $recruiterFilter === $member['user_id'] ? 'selected' : ''; ?>>
                                        <?php echo e($member['full_name']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <button type="submit"
                                class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            Filter
                        </button>
                        <?php if ($search || $statusFilter || $recruiterFilter): ?>
                            <a href="/agency/candidates.php" class="ml-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Candidates List -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($candidates)): ?>
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No candidates found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <?php if ($search || $statusFilter): ?>
                            Try adjusting your filters or search terms.
                        <?php else: ?>
                            Get started by inviting your first candidate.
                        <?php endif; ?>
                    </p>
                    <?php if ($canInvite && !$search && !$statusFilter): ?>
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
                <!-- Mobile Card View -->
                <div class="sm:hidden space-y-4">
                    <?php foreach ($candidates as $candidate): ?>
                        <div class="bg-white shadow rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <div class="h-10 w-10 flex-shrink-0">
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
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($candidate['full_name'] ?? 'Unnamed'); ?></p>
                                        <p class="text-xs text-gray-500 truncate"><?php echo e($candidate['email']); ?></p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                <?php
                                                $statusClasses = [
                                                    'pending' => 'bg-orange-100 text-orange-800',
                                                    'draft' => 'bg-gray-100 text-gray-800',
                                                    'complete' => 'bg-green-100 text-green-800',
                                                    'published' => 'bg-blue-100 text-blue-800',
                                                    'archived' => 'bg-yellow-100 text-yellow-800'
                                                ];
                                                echo $statusClasses[$candidate['cv_status']] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst($candidate['cv_status'] ?? 'draft'); ?>
                                            </span>
                                            <?php if (isset($candidate['is_pending']) && $candidate['is_pending']): ?>
                                                <span class="text-xs text-gray-500">Expires <?php echo date('j M Y', strtotime($candidate['expires_at'])); ?></span>
                                            <?php elseif (in_array($org['role'], ['owner', 'admin']) && $candidate['recruiter_name']): ?>
                                                <span class="text-xs text-gray-500">Assigned: <?php echo e($candidate['recruiter_name']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php if (isset($candidate['is_pending']) && $candidate['is_pending']): ?>
                                                Invited <?php echo date('j M Y', strtotime($candidate['created_at'])); ?>
                                            <?php else: ?>
                                                Added <?php echo date('j M Y', strtotime($candidate['created_at'])); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <?php if ($canManage && (!isset($candidate['is_pending']) || !$candidate['is_pending'])): ?>
                                    <div class="ml-2">
                                        <a href="/agency/candidate.php?id=<?php echo e($candidate['id']); ?>" 
                                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800">
                                            View
                                        </a>
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
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Candidate</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <?php if (in_array($org['role'], ['owner', 'admin'])): ?>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Assigned To</th>
                                <?php endif; ?>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Added</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php foreach ($candidates as $candidate): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
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
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900"><?php echo e($candidate['full_name'] ?? 'Unnamed'); ?></div>
                                                <div class="text-gray-500"><?php echo e($candidate['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            <?php
                                            $statusClasses = [
                                                'pending' => 'bg-orange-100 text-orange-800',
                                                'draft' => 'bg-gray-100 text-gray-800',
                                                'complete' => 'bg-green-100 text-green-800',
                                                'published' => 'bg-blue-100 text-blue-800',
                                                'archived' => 'bg-yellow-100 text-yellow-800'
                                            ];
                                            echo $statusClasses[$candidate['cv_status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo ucfirst($candidate['cv_status'] ?? 'draft'); ?>
                                        </span>
                                        <?php if (isset($candidate['is_pending']) && $candidate['is_pending']): ?>
                                            <span class="ml-2 text-xs text-gray-500">Expires <?php echo date('j M Y', strtotime($candidate['expires_at'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (in_array($org['role'], ['owner', 'admin'])): ?>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <?php echo e($candidate['recruiter_name'] ?? 'Unassigned'); ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <?php if (isset($candidate['is_pending']) && $candidate['is_pending']): ?>
                                            Invited <?php echo date('j M Y', strtotime($candidate['created_at'])); ?>
                                        <?php else: ?>
                                            <?php echo date('j M Y', strtotime($candidate['created_at'])); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex items-center justify-end space-x-2">
                                            <?php if (!isset($candidate['is_pending']) || !$candidate['is_pending']): ?>
                                                <a href="/cv/@<?php echo e($candidate['username']); ?>"
                                                   target="_blank"
                                                   class="text-gray-400 hover:text-gray-500"
                                                   title="View CV">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <?php if ($canManage): ?>
                                                    <a href="/agency/candidate.php?id=<?php echo e($candidate['id']); ?>"
                                                       class="text-blue-600 hover:text-blue-900"
                                                       title="Manage">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 italic">No actions available</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4 rounded-lg shadow" aria-label="Pagination">
                        <div class="hidden sm:block">
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to
                                <span class="font-medium"><?php echo min($offset + $perPage, $totalCandidates); ?></span> of
                                <span class="font-medium"><?php echo $totalCandidates; ?></span> results
                            </p>
                        </div>
                        <div class="flex flex-1 justify-between sm:justify-end">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $statusFilter ? '&status=' . urlencode($statusFilter) : ''; ?><?php echo $recruiterFilter ? '&recruiter=' . urlencode($recruiterFilter) : ''; ?>"
                                   class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $statusFilter ? '&status=' . urlencode($statusFilter) : ''; ?><?php echo $recruiterFilter ? '&recruiter=' . urlencode($recruiterFilter) : ''; ?>"
                                   class="relative ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>
