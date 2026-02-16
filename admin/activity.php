<?php
/**
 * Super Admin - Activity Log
 * View system-wide activity log
 */

require_once __DIR__ . '/../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get filter parameters
$organisationFilter = sanitizeInput(get('organisation_id') ?? '');
$userFilter = sanitizeInput(get('user_id') ?? '');
$actionFilter = sanitizeInput(get('action') ?? '');
$page = max(1, (int)(get('page') ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Build filters array
$filters = [];

if ($organisationFilter) {
    $filters['organisation_id'] = $organisationFilter;
}

if ($userFilter) {
    $filters['user_id'] = $userFilter;
}

if ($actionFilter) {
    $filters['action'] = $actionFilter;
}

// Get activity log
$activities = getSystemActivityLog($perPage, $offset, $filters);

// Get total count (approximate, for pagination)
$totalActivities = count(getSystemActivityLog(1000, 0, $filters)); // Get more for better count
$totalPages = ceil($totalActivities / $perPage);

// Get all organisations for filter
$allOrganisations = getAllOrganisations();

// Get all unique action types from activity log for dropdown
$uniqueActions = db()->fetchAll(
    "SELECT DISTINCT action FROM activity_log ORDER BY action ASC"
);
$actionOptions = [];
foreach ($uniqueActions as $actionRow) {
    $actionParts = explode('.', $actionRow['action']);
    $category = $actionParts[0] ?? '';
    $action = $actionParts[1] ?? $actionRow['action'];
    $displayName = ucfirst(str_replace('_', ' ', $category)) . ' - ' . ucfirst(str_replace('_', ' ', $action));
    $actionOptions[$actionRow['action']] = $displayName;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Activity Log | Super Admin',
        'metaDescription' => 'System-wide activity log',
        'canonicalUrl' => APP_URL . '/admin/activity.php',
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
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Activity Log</h1>
                <p class="mt-1 text-sm text-gray-500">System-wide activity and audit trail</p>
            </div>

            <?php partial('admin/quick-actions'); ?>

            <!-- Filters -->
            <div class="bg-white shadow-lg rounded-xl border-2 border-gray-200 p-6 mb-6">
                <form method="GET" action="" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="organisation_id" class="block text-base font-semibold text-gray-700 mb-3">Organisation</label>
                            <select id="organisation_id" name="organisation_id" class="block w-full border-2 border-gray-400 rounded-lg px-4 py-3 text-base focus:ring-4 focus:ring-blue-200 focus:border-blue-500">
                                <option value="">All Organisations</option>
                                <?php foreach ($allOrganisations as $org): ?>
                                    <option value="<?php echo e($org['id']); ?>" <?php echo $organisationFilter === $org['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($org['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="action" class="block text-base font-semibold text-gray-700 mb-3">Action Type</label>
                            <select id="action" name="action" class="block w-full border-2 border-gray-400 rounded-lg px-4 py-3 text-base focus:ring-4 focus:ring-blue-200 focus:border-blue-500">
                                <option value="">All Actions</option>
                                <?php foreach ($actionOptions as $actionValue => $actionLabel): ?>
                                    <option value="<?php echo e($actionValue); ?>" <?php echo $actionFilter === $actionValue ? 'selected' : ''; ?>>
                                        <?php echo e($actionLabel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg text-base font-bold shadow-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                                Filter
                            </button>
                            <?php if ($organisationFilter || $actionFilter): ?>
                                <a href="/admin/activity.php" class="px-6 py-3 border-2 border-gray-400 rounded-lg text-base font-semibold text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition-colors">
                                    Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Activity Log -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <?php if (empty($activities)): ?>
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No activity found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters.</p>
                    </div>
                <?php else: ?>
                    <div class="flow-root">
                        <ul class="-mb-8 divide-y divide-gray-200">
                            <?php foreach ($activities as $index => $activity): ?>
                                <li>
                                    <div class="relative pb-8">
                                        <?php if ($index < count($activities) - 1): ?>
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
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-500">
                                                        <span class="font-medium text-gray-900"><?php echo e($activity['actor_name'] ?? 'System'); ?></span>
                                                        <?php echo e(str_replace('.', ' ', $activity['action'])); ?>
                                                        <?php if ($activity['target_name']): ?>
                                                            <span class="font-medium text-gray-900"><?php echo e($activity['target_name']); ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($activity['organisation_name']): ?>
                                                            <span class="text-gray-400">in <?php echo e($activity['organisation_name']); ?></span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if ($activity['details']): ?>
                                                        <?php
                                                        $details = json_decode($activity['details'], true);
                                                        if ($details && is_array($details)):
                                                        ?>
                                                            <div class="mt-1 text-xs text-gray-400">
                                                                <?php foreach ($details as $key => $value): ?>
                                                                    <span class="mr-4">
                                                                        <strong><?php echo e($key); ?>:</strong> 
                                                                        <?php echo is_array($value) ? json_encode($value) : e($value); ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
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
                                                            echo date('j M Y, H:i', $activityTime);
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

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $perPage, $totalActivities); ?> of <?php echo $totalActivities; ?> activities
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $organisationFilter ? '&organisation_id=' . urlencode($organisationFilter) : ''; ?><?php echo $actionFilter ? '&action=' . urlencode($actionFilter) : ''; ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $organisationFilter ? '&organisation_id=' . urlencode($organisationFilter) : ''; ?><?php echo $actionFilter ? '&action=' . urlencode($actionFilter) : ''; ?>" 
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

