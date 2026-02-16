<?php
/**
 * Super Admin - User Feedback Management
 * View and manage user feedback submissions
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
$typeFilter = sanitizeInput(get('type') ?? '');
$page = max(1, (int)(get('page') ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Determine which column name to use (user_id or profile_id)
// Check table structure to see which column exists
$userIdColumn = 'user_id';
try {
    $columns = db()->fetchAll("SHOW COLUMNS FROM user_feedback LIKE 'user_id'");
    if (empty($columns)) {
        // Check for profile_id instead
        $columns = db()->fetchAll("SHOW COLUMNS FROM user_feedback LIKE 'profile_id'");
        if (!empty($columns)) {
            $userIdColumn = 'profile_id';
        }
    }
} catch (Exception $e) {
    // If check fails, default to user_id (migration column name)
    $userIdColumn = 'user_id';
}

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(uf.message LIKE ? OR uf.email LIKE ? OR p.email LIKE ? OR p.full_name LIKE ?)";
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if ($statusFilter) {
    $where[] = "uf.status = ?";
    $params[] = $statusFilter;
}

if ($typeFilter) {
    $where[] = "uf.feedback_type = ?";
    $params[] = $typeFilter;
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$totalQuery = "SELECT COUNT(*) as total FROM user_feedback uf 
               LEFT JOIN profiles p ON uf.{$userIdColumn} = p.id 
               {$whereClause}";
$totalResult = db()->fetchOne($totalQuery, $params);
$totalFeedback = (int)$totalResult['total'];
$totalPages = ceil($totalFeedback / $perPage);

// Get feedback items
$query = "SELECT uf.*, 
                 p.email as user_email, 
                 p.full_name as user_name,
                 p.username as user_username
          FROM user_feedback uf
          LEFT JOIN profiles p ON uf.{$userIdColumn} = p.id
          {$whereClause}
          ORDER BY uf.created_at DESC
          LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$feedbackItems = db()->fetchAll($query, $params);

// Get statistics
// Handle case where status column might not exist (old schema)
try {
    $statsQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed_count,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
        SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_count
    FROM user_feedback";
    $stats = db()->fetchOne($statsQuery);
} catch (Exception $e) {
    // Fallback if status column doesn't exist
    $statsQuery = "SELECT COUNT(*) as total FROM user_feedback";
    $totalCount = db()->fetchOne($statsQuery);
    $stats = [
        'total' => $totalCount['total'],
        'new_count' => $totalCount['total'],
        'reviewed_count' => 0,
        'resolved_count' => 0,
        'closed_count' => 0
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'User Feedback | Super Admin',
        'metaDescription' => 'Manage user feedback submissions',
        'canonicalUrl' => APP_URL . '/admin/feedback.php',
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
                <h1 class="text-2xl font-bold text-gray-900">User Feedback</h1>
                <p class="mt-1 text-sm text-gray-500">View and manage user feedback submissions</p>
            </div>

            <?php partial('admin/quick-actions'); ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-6">
                <?php
                $currentStatus = empty($statusFilter) ? 'all' : $statusFilter;
                $baseUrl = '/admin/feedback.php';
                $getParams = [];
                if (!empty($search)) $getParams['search'] = $search;
                if (!empty($typeFilter)) $getParams['type'] = $typeFilter;
                $queryString = !empty($getParams) ? '&' . http_build_query($getParams) : '';
                ?>
                
                <!-- Total Card -->
                <a href="<?php echo $baseUrl . ($queryString ? '?' . ltrim($queryString, '&') : ''); ?>" 
                   class="bg-white overflow-hidden shadow rounded-lg transition-all hover:shadow-lg cursor-pointer <?php echo $currentStatus === 'all' ? 'ring-2 ring-blue-500 ring-offset-2' : ''; ?>">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo number_format((int)($stats['total'] ?? 0)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- New Card -->
                <a href="<?php echo $baseUrl . '?status=new' . $queryString; ?>" 
                   class="bg-white overflow-hidden shadow rounded-lg transition-all hover:shadow-lg cursor-pointer <?php echo $currentStatus === 'new' ? 'ring-2 ring-yellow-500 ring-offset-2' : ''; ?>">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">New</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo number_format((int)($stats['new_count'] ?? 0)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Reviewed Card -->
                <a href="<?php echo $baseUrl . '?status=reviewed' . $queryString; ?>" 
                   class="bg-white overflow-hidden shadow rounded-lg transition-all hover:shadow-lg cursor-pointer <?php echo $currentStatus === 'reviewed' ? 'ring-2 ring-blue-500 ring-offset-2' : ''; ?>">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Reviewed</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo number_format((int)($stats['reviewed_count'] ?? 0)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Resolved Card -->
                <a href="<?php echo $baseUrl . '?status=resolved' . $queryString; ?>" 
                   class="bg-white overflow-hidden shadow rounded-lg transition-all hover:shadow-lg cursor-pointer <?php echo $currentStatus === 'resolved' ? 'ring-2 ring-green-500 ring-offset-2' : ''; ?>">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Resolved</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo number_format((int)($stats['resolved_count'] ?? 0)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Closed Card -->
                <a href="<?php echo $baseUrl . '?status=closed' . $queryString; ?>" 
                   class="bg-white overflow-hidden shadow rounded-lg transition-all hover:shadow-lg cursor-pointer <?php echo $currentStatus === 'closed' ? 'ring-2 ring-gray-500 ring-offset-2' : ''; ?>">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Closed</dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo number_format((int)($stats['closed_count'] ?? 0)); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <form method="GET" action="" id="filter-form" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="search" name="search" value="<?php echo e($search); ?>" 
                               placeholder="Search feedback..." 
                               class="block w-full rounded-lg border-2 border-gray-400 bg-white px-4 py-3 text-base font-medium text-gray-900 shadow-sm transition-colors focus:border-blue-600 focus:ring-4 focus:ring-blue-200 focus:outline-none">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" 
                                class="block rounded-lg border-2 border-gray-400 bg-white px-4 py-3 text-base font-medium text-gray-900 shadow-sm transition-colors focus:border-blue-600 focus:ring-4 focus:ring-blue-200 focus:outline-none"
                                onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Statuses</option>
                            <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="reviewed" <?php echo $statusFilter === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                            <option value="resolved" <?php echo $statusFilter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="closed" <?php echo $statusFilter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type" id="type" 
                                class="block rounded-lg border-2 border-gray-400 bg-white px-4 py-3 text-base font-medium text-gray-900 shadow-sm transition-colors focus:border-blue-600 focus:ring-4 focus:ring-blue-200 focus:outline-none"
                                onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Types</option>
                            <option value="bug" <?php echo $typeFilter === 'bug' ? 'selected' : ''; ?>>Bug Report</option>
                            <option value="spelling" <?php echo $typeFilter === 'spelling' ? 'selected' : ''; ?>>Spelling/Grammar</option>
                            <option value="feature_request" <?php echo $typeFilter === 'feature_request' ? 'selected' : ''; ?>>Feature Request</option>
                            <option value="personal_issue" <?php echo $typeFilter === 'personal_issue' ? 'selected' : ''; ?>>Personal Issue</option>
                            <option value="other" <?php echo $typeFilter === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-base font-bold shadow-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-4 focus:ring-blue-200">
                            Filter
                        </button>
                    </div>
                    <?php if ($search || $statusFilter || $typeFilter): ?>
                        <div class="flex items-end">
                            <a href="/admin/feedback.php" class="px-6 py-3 border-2 border-gray-400 rounded-lg text-base font-bold text-gray-700 bg-white hover:bg-gray-50 shadow-lg transition-colors focus:outline-none focus:ring-4 focus:ring-gray-200">
                                Clear
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Bulk Actions Toolbar -->
            <div id="bulk-actions-toolbar" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 hidden">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 w-full sm:w-auto">
                        <span id="selected-count" class="text-sm font-medium text-gray-900 whitespace-nowrap">0 selected</span>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                            <label class="text-sm text-gray-700 whitespace-nowrap">Change status to:</label>
                            <select id="bulk-status-select" class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm text-sm min-h-[44px] px-3 py-2">
                                <option value="">Select status...</option>
                                <option value="new">New</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                            <button onclick="bulkUpdateStatus()" class="w-full sm:w-auto px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 min-h-[44px] touch-manipulation">
                                Update Status
                            </button>
                        </div>
                        <button onclick="bulkDelete()" class="w-full sm:w-auto px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 min-h-[44px] touch-manipulation">
                            Delete Selected
                        </button>
                    </div>
                    <button onclick="clearSelection()" class="text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap min-h-[44px] px-2 touch-manipulation">
                        Clear Selection
                    </button>
                </div>
            </div>

            <!-- Feedback Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <?php if (empty($feedbackItems)): ?>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No feedback found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="select-all" onclick="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($feedbackItems as $item): ?>
                                    <tr class="hover:bg-gray-50" data-feedback-id="<?php echo e($item['id']); ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="feedback-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="<?php echo e($item['id']); ?>" onchange="updateBulkActionsToolbar()">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php
                                            $createdAt = strtotime($item['created_at']);
                                            $diff = time() - $createdAt;
                                            if ($diff < 3600) {
                                                echo floor($diff / 60) . 'm ago';
                                            } elseif ($diff < 86400) {
                                                echo floor($diff / 3600) . 'h ago';
                                            } else {
                                                echo date('M j, Y', $createdAt);
                                            }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php if ($item['user_name']): ?>
                                                    <?php echo e($item['user_name']); ?>
                                                <?php elseif ($item['user_email']): ?>
                                                    <?php echo e($item['user_email']); ?>
                                                <?php else: ?>
                                                    <?php echo e($item['email'] ?? 'Anonymous'); ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($item['email']) && $item['email'] !== ($item['user_email'] ?? '')): ?>
                                                <div class="text-sm text-gray-500"><?php echo e($item['email']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php
                                                $typeColors = [
                                                    'bug' => 'bg-red-100 text-red-800',
                                                    'spelling' => 'bg-yellow-100 text-yellow-800',
                                                    'feature_request' => 'bg-blue-100 text-blue-800',
                                                    'personal_issue' => 'bg-purple-100 text-purple-800',
                                                    'other' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $typeColors[$item['feedback_type']] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $item['feedback_type'])); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-md">
                                                <?php echo e(substr($item['message'] ?? '', 0, 100)); ?>
                                                <?php if (strlen($item['message'] ?? '') > 100): ?>...<?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (!empty($item['page_url'])): ?>
                                                <a href="<?php echo e($item['page_url']); ?>" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 break-all">
                                                    <?php 
                                                    $urlPath = parse_url($item['page_url'], PHP_URL_PATH);
                                                    // Remove .php extension for display
                                                    $displayPath = $urlPath ?: $item['page_url'];
                                                    $displayPath = preg_replace('/\.php$/', '', $displayPath);
                                                    echo e($displayPath); 
                                                    ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-400">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php
                                                $status = $item['status'] ?? 'new';
                                                $statusColors = [
                                                    'new' => 'bg-yellow-100 text-yellow-800',
                                                    'reviewed' => 'bg-blue-100 text-blue-800',
                                                    'resolved' => 'bg-green-100 text-green-800',
                                                    'closed' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="openFeedbackModal('<?php echo e($item['id']); ?>')" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                View/Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <?php if ($page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                <?php if ($page < $totalPages): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium"><?php echo $offset + 1; ?></span>
                                        to <span class="font-medium"><?php echo min($offset + $perPage, $totalFeedback); ?></span>
                                        of <span class="font-medium"><?php echo $totalFeedback; ?></span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                Previous
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php
                                        $startPage = max(1, $page - 2);
                                        $endPage = min($totalPages, $page + 2);
                                        for ($i = $startPage; $i <= $endPage; $i++):
                                        ?>
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                Next
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Feedback Detail Modal -->
    <div id="feedback-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center px-4 py-10 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" onclick="closeFeedbackModal()"></div>
            <div class="relative inline-block w-full max-w-3xl transform rounded-2xl bg-white px-6 py-6 text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle sm:p-8">
                <button onclick="closeFeedbackModal()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div id="feedback-modal-content">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <?php partial('footer'); ?>

    <script>
        function openFeedbackModal(feedbackId) {
            const modal = document.getElementById('feedback-modal');
            const content = document.getElementById('feedback-modal-content');
            
            modal.classList.remove('hidden');
            content.innerHTML = '<div class="text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
            
            fetch(`/api/admin/get-feedback.php?id=${feedbackId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.feedback;
                        const browserInfo = item.browser_info ? JSON.parse(item.browser_info) : {};
                        
                        content.innerHTML = `
                            <div class="mb-6">
                                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Feedback Details</h3>
                                
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="feedback-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="new" ${item.status === 'new' ? 'selected' : ''}>New</option>
                                            <option value="reviewed" ${item.status === 'reviewed' ? 'selected' : ''}>Reviewed</option>
                                            <option value="resolved" ${item.status === 'resolved' ? 'selected' : ''}>Resolved</option>
                                            <option value="closed" ${item.status === 'closed' ? 'selected' : ''}>Closed</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Type</label>
                                        <p class="mt-1 text-sm text-gray-900">${item.feedback_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">User Name</label>
                                    <p class="text-sm text-gray-900">${item.user_name || 'Not provided'}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <div class="flex items-center gap-2">
                                        <a href="mailto:${escapeHtml(item.email || item.user_email || '')}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">${escapeHtml(item.email || item.user_email || 'Not available')}</a>
                                        ${(item.email || item.user_email) ? `<button onclick="navigator.clipboard.writeText('${escapeHtml(item.email || item.user_email)}'); alert('Email copied to clipboard');" class="text-xs text-gray-500 hover:text-gray-700 underline">Copy</button>` : ''}
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Click to send an email or copy the address</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Page URL</label>
                                    ${item.page_url ? `
                                    <div class="flex items-center gap-2">
                                        <a href="${escapeHtml(item.page_url)}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 break-all flex-1">${escapeHtml(item.page_url.replace(/\\.php$/, ''))}</a>
                                        <button onclick="navigator.clipboard.writeText('${escapeHtml(item.page_url)}'); alert('URL copied to clipboard');" class="text-xs text-gray-500 hover:text-gray-700 underline whitespace-nowrap">Copy</button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">This feedback was submitted from this page</p>
                                    ` : '<p class="text-sm text-gray-400">Not available - page URL was not captured</p>'}
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                    <div class="bg-gray-50 rounded-md p-4 text-sm text-gray-900 whitespace-pre-wrap">${escapeHtml(item.message)}</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Browser & Device Info</label>
                                    <div class="bg-gray-50 rounded-md p-4 text-sm space-y-2">
                                        ${browserInfo.detected_browser ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Browser:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.detected_browser)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.detected_os ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Operating System:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.detected_os)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.device_type ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Device Type:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.device_type)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.screen_resolution ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Screen Resolution:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.screen_resolution)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.viewport_size ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Viewport Size:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.viewport_size)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.device_pixel_ratio ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Pixel Ratio:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.device_pixel_ratio)}x</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.timezone ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Timezone:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.timezone)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.language ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Language:</span>
                                            <span class="text-gray-900 font-medium">${escapeHtml(browserInfo.language)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.referrer ? `
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Referrer:</span>
                                            <span class="text-gray-900 font-medium break-all text-right">${escapeHtml(browserInfo.referrer)}</span>
                                        </div>
                                        ` : ''}
                                        ${browserInfo.user_agent ? `
                                        <details class="mt-3">
                                            <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-700">Show Full User Agent</summary>
                                            <p class="text-xs text-gray-600 mt-2 break-all font-mono">${escapeHtml(browserInfo.user_agent)}</p>
                                        </details>
                                        ` : ''}
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                                    <textarea id="feedback-admin-notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">${escapeHtml(item.admin_notes || '')}</textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Submitted</label>
                                    <p class="text-sm text-gray-900">${new Date(item.created_at).toLocaleString()}</p>
                                </div>
                                
                                <div class="flex justify-between items-center mt-6">
                                    <button onclick="deleteFeedback('${item.id}')" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                                        Delete Feedback
                                    </button>
                                    <div class="flex gap-3">
                                        <button onclick="closeFeedbackModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button onclick="saveFeedback('${item.id}')" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        content.innerHTML = '<div class="text-center py-8 text-red-600">Failed to load feedback details.</div>';
                    }
                })
                .catch(error => {
                    content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading feedback details.</div>';
                });
        }
        
        function closeFeedbackModal() {
            document.getElementById('feedback-modal').classList.add('hidden');
        }
        
        function saveFeedback(feedbackId) {
            const status = document.getElementById('feedback-status').value;
            const adminNotes = document.getElementById('feedback-admin-notes').value;
            
            const formData = new FormData();
            formData.append('id', feedbackId);
            formData.append('status', status);
            formData.append('admin_notes', adminNotes);
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo csrfToken(); ?>');
            
            fetch('/api/admin/update-feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to update feedback'));
                }
            })
            .catch(error => {
                alert('Error updating feedback');
            });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Bulk Actions Functions
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.feedback-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActionsToolbar();
        }
        
        function updateBulkActionsToolbar() {
            const checkboxes = document.querySelectorAll('.feedback-checkbox:checked');
            const toolbar = document.getElementById('bulk-actions-toolbar');
            const countSpan = document.getElementById('selected-count');
            
            if (checkboxes.length > 0) {
                toolbar.classList.remove('hidden');
                countSpan.textContent = `${checkboxes.length} selected`;
            } else {
                toolbar.classList.add('hidden');
            }
            
            // Update select-all checkbox state
            const allCheckboxes = document.querySelectorAll('.feedback-checkbox');
            const selectAll = document.getElementById('select-all');
            if (allCheckboxes.length > 0) {
                selectAll.checked = checkboxes.length === allCheckboxes.length;
            }
        }
        
        function clearSelection() {
            document.querySelectorAll('.feedback-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;
            updateBulkActionsToolbar();
        }
        
        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.feedback-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }
        
        function bulkUpdateStatus() {
            const ids = getSelectedIds();
            const status = document.getElementById('bulk-status-select').value;
            
            if (ids.length === 0) {
                alert('Please select at least one feedback item.');
                return;
            }
            
            if (!status) {
                alert('Please select a status.');
                return;
            }
            
            if (!confirm(`Update status to "${status}" for ${ids.length} feedback item(s)?`)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('status', status);
            ids.forEach(id => formData.append('ids[]', id));
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo csrfToken(); ?>');
            
            fetch('/api/admin/bulk-update-feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to update feedback'));
                }
            })
            .catch(error => {
                alert('Error updating feedback');
            });
        }
        
        function bulkDelete() {
            const ids = getSelectedIds();
            
            if (ids.length === 0) {
                alert('Please select at least one feedback item.');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${ids.length} feedback item(s)? This action cannot be undone.`)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete');
            ids.forEach(id => formData.append('ids[]', id));
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo csrfToken(); ?>');
            
            fetch('/api/admin/bulk-update-feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete feedback'));
                }
            })
            .catch(error => {
                alert('Error deleting feedback');
            });
        }
        
        function deleteFeedback(feedbackId) {
            if (!confirm('Are you sure you want to delete this feedback? This action cannot be undone.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('id', feedbackId);
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo csrfToken(); ?>');
            
            fetch('/api/admin/delete-feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Feedback deleted successfully.');
                    closeFeedbackModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete feedback'));
                }
            })
            .catch(error => {
                alert('Error deleting feedback');
            });
        }
    </script>
</body>
</html>
