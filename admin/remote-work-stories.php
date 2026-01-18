<?php
/**
 * Remote Work Story Submissions
 * Super admin page to view and manage remote work story submissions
 */

require_once __DIR__ . '/../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Handle status updates
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/admin/remote-work-stories.php');
    }

    $action = post('action');
    $storyId = sanitizeInput(post('story_id'));

    if ($action === 'update_status') {
        $newStatus = sanitizeInput(post('status'));
        $reviewNotes = sanitizeInput(post('review_notes', ''));

        if (!in_array($newStatus, ['pending', 'approved', 'rejected', 'featured'])) {
            setFlash('error', 'Invalid status.');
            redirect('/admin/remote-work-stories.php');
        }

        try {
            db()->update('remote_work_stories',
                [
                    'status' => $newStatus,
                    'review_notes' => !empty($reviewNotes) ? $reviewNotes : null,
                    'reviewed_by' => getUserId(),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'id = ?',
                [$storyId]
            );

            logActivity('remote_work_story.status_updated', null, [
                'story_id' => $storyId,
                'status' => $newStatus
            ]);

            setFlash('success', 'Story status updated successfully.');
        } catch (Exception $e) {
            setFlash('error', 'Failed to update status. Please try again.');
        }

        redirect('/admin/remote-work-stories.php');
    }
}

// Get filter
$statusFilter = get('status', 'all');
$searchQuery = get('search', '');

// Build query
$whereConditions = [];
$params = [];

if ($statusFilter !== 'all') {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(name LIKE ? OR email LIKE ? OR job_title LIKE ? OR company LIKE ?)";
    $searchParam = '%' . $searchQuery . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get submissions
$stories = db()->fetchAll(
    "SELECT rws.*, reviewer.full_name as reviewer_name
     FROM remote_work_stories rws
     LEFT JOIN profiles reviewer ON rws.reviewed_by = reviewer.id
     $whereClause
     ORDER BY rws.created_at DESC
     LIMIT 100",
    $params
);

// Get counts
$counts = db()->fetchAll(
    "SELECT status, COUNT(*) as count
     FROM remote_work_stories
     GROUP BY status"
);
$statusCounts = [];
foreach ($counts as $count) {
    $statusCounts[$count['status']] = $count['count'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Remote Work Stories | Admin',
        'metaDescription' => 'View and manage remote work story submissions.',
        'canonicalUrl' => APP_URL . '/admin/remote-work-stories.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>

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
                    <h1 class="text-2xl font-bold text-gray-900">Remote Work Story Submissions</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        View and manage remote work story submissions from users
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="<?php echo e($searchQuery); ?>" 
                               placeholder="Search by name, email, job title, or company..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending (<?php echo $statusCounts['pending'] ?? 0; ?>)</option>
                            <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved (<?php echo $statusCounts['approved'] ?? 0; ?>)</option>
                            <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected (<?php echo $statusCounts['rejected'] ?? 0; ?>)</option>
                            <option value="featured" <?php echo $statusFilter === 'featured' ? 'selected' : ''; ?>>Featured (<?php echo $statusCounts['featured'] ?? 0; ?>)</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Filter
                    </button>
                    <?php if ($statusFilter !== 'all' || !empty($searchQuery)): ?>
                        <a href="/admin/remote-work-stories.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Stories List -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($stories)): ?>
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-500">No submissions found.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($stories as $story): ?>
                        <div class="bg-white rounded-lg shadow border-l-4 <?php
                            echo match($story['status']) {
                                'pending' => 'border-yellow-500',
                                'approved' => 'border-green-500',
                                'rejected' => 'border-red-500',
                                'featured' => 'border-blue-500',
                                default => 'border-gray-300'
                            };
                        ?>">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo e($story['name']); ?></h3>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php
                                                echo match($story['status']) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'featured' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            ?>">
                                                <?php echo ucfirst($story['status']); ?>
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 space-y-1">
                                            <p><strong>Email:</strong> <a href="mailto:<?php echo e($story['email']); ?>" class="text-blue-600 hover:underline"><?php echo e($story['email']); ?></a></p>
                                            <p><strong>Job Title:</strong> <?php echo e($story['job_title']); ?></p>
                                            <?php if (!empty($story['company'])): ?>
                                                <p><strong>Company:</strong> <?php echo e($story['company']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($story['category'])): ?>
                                                <p><strong>Category:</strong> <?php echo e($story['category']); ?></p>
                                            <?php endif; ?>
                                            <p><strong>Submitted:</strong> <?php echo date('j M Y, g:i a', strtotime($story['created_at'])); ?></p>
                                            <?php if ($story['reviewer_name']): ?>
                                                <p><strong>Reviewed by:</strong> <?php echo e($story['reviewer_name']); ?> on <?php echo date('j M Y', strtotime($story['updated_at'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Story:</h4>
                                    <p class="text-gray-800 whitespace-pre-wrap"><?php echo e($story['story']); ?></p>
                                </div>

                                <?php if (!empty($story['review_notes'])): ?>
                                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                        <h4 class="text-sm font-semibold text-blue-700 mb-2">Review Notes:</h4>
                                        <p class="text-blue-800 whitespace-pre-wrap"><?php echo e($story['review_notes']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" class="mt-4 pt-4 border-t border-gray-200">
                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="story_id" value="<?php echo e($story['id']); ?>">
                                    
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <select name="status" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="pending" <?php echo $story['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo $story['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $story['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="featured" <?php echo $story['status'] === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                        </select>
                                        <textarea name="review_notes" 
                                                  placeholder="Review notes (optional)"
                                                  rows="2"
                                                  class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e($story['review_notes'] ?? ''); ?></textarea>
                                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            Update Status
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>

