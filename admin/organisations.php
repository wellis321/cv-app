<?php
/**
 * Super Admin - Organisations Management
 * List, search, and manage all organisations
 */

require_once __DIR__ . '/../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get filter parameters
$search = sanitizeInput(get('search') ?? '');
$statusFilter = sanitizeInput(get('status') ?? '');
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

if ($statusFilter) {
    $filters['subscription_status'] = $statusFilter;
}

// Get organisations
$organisations = getAllOrganisations($filters);

// Get total count for pagination
$totalFilters = $filters;
unset($totalFilters['limit'], $totalFilters['offset']);
$allOrganisations = getAllOrganisations($totalFilters);
$totalOrganisations = count($allOrganisations);
$totalPages = ceil($totalOrganisations / $perPage);

// Handle organisation view (for displaying details, but forms are on separate pages)
$viewingOrg = null;
if (get('id')) {
    $viewingOrg = getOrganisationById(sanitizeInput(get('id')));
}

// Get organisation members if viewing
$orgMembers = [];
if ($viewingOrg) {
    $orgMembers = getOrganisationTeamMembers($viewingOrg['id']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Manage Organisations | Super Admin',
        'metaDescription' => 'Manage all organisations',
        'canonicalUrl' => APP_URL . '/admin/organisations.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>

    <main id="main-content" class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Error/Success Messages -->
            <?php if ($error): ?>
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Organisations</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage all organisations in the system</p>
                </div>
                <?php if (!$viewingOrg): ?>
                    <a href="/admin/organisations/create.php"
                       class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                        </svg>
                        Create Organisation
                    </a>
                <?php endif; ?>
            </div>

            <?php partial('admin/quick-actions'); ?>

            <?php if ($viewingOrg): ?>
                <!-- Organisation Details View -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900"><?php echo e($viewingOrg['name']); ?></h2>
                        <a href="/admin/organisations/edit.php?id=<?php echo e($viewingOrg['id']); ?>" 
                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                            Edit Organisation
                        </a>
                    </div>
                    
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($viewingOrg['slug']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plan</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo ucfirst(str_replace('_', ' ', $viewingOrg['plan'])); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Subscription Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    <?php echo $viewingOrg['subscription_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo ucfirst($viewingOrg['subscription_status'] ?? 'inactive'); ?>
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Max Candidates</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($viewingOrg['max_candidates']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Max Team Members</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($viewingOrg['max_team_members']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo date('j M Y', strtotime($viewingOrg['created_at'])); ?></dd>
                        </div>
                    </dl>
                </div>
                
                <!-- Current Members -->
                <?php if (!empty($orgMembers)): ?>
                    <div class="bg-white shadow rounded-lg p-6 mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Team Members</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
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
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                <?php echo $member['joined_at'] ? date('j M Y', strtotime($member['joined_at'])) : '—'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <form method="GET" action="" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="<?php echo e($search); ?>" 
                               placeholder="Search organisations..." 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <select name="status" class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Filter
                    </button>
                    <?php if ($search || $statusFilter): ?>
                        <a href="/admin/organisations.php" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Organisations Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($organisations)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No organisations found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($organisations as $org): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($org['name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?php echo e($org['slug']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst(str_replace('_', ' ', $org['plan'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $org['subscription_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst($org['subscription_status'] ?? 'inactive'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $org['team_member_count'] ?? 0; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $org['candidate_count'] ?? 0; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('j M Y', strtotime($org['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/admin/organisations/edit.php?id=<?php echo e($org['id']); ?>" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $perPage, $totalOrganisations); ?> of <?php echo $totalOrganisations; ?> organisations
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $statusFilter ? '&status=' . urlencode($statusFilter) : ''; ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $statusFilter ? '&status=' . urlencode($statusFilter) : ''; ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php partial('footer'); ?>
    
</body>
</html>

