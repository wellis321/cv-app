<?php
/**
 * Super Admin - Limit Increase Requests
 * Review and approve/deny limit increase requests from organisations
 */

require_once __DIR__ . '/../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once __DIR__ . '/../php/utils.php';
    
    $requestId = sanitizeInput($_POST['request_id'] ?? '');
    
    if ($_POST['action'] === 'approve') {
        $reviewNotes = sanitizeInput($_POST['review_notes'] ?? '');
        $result = approveLimitIncreaseRequest($requestId, $reviewNotes);
        
        if ($result['success']) {
            setFlash('success', 'Request approved successfully.');
        } else {
            setFlash('error', $result['error']);
        }
    } elseif ($_POST['action'] === 'deny') {
        $reviewNotes = sanitizeInput($_POST['review_notes'] ?? '');
        $result = denyLimitIncreaseRequest($requestId, $reviewNotes);
        
        if ($result['success']) {
            setFlash('success', 'Request denied.');
        } else {
            setFlash('error', $result['error']);
        }
    }
    
    redirect('/admin/limit-requests.php');
}

// Get all pending requests
$pendingRequests = getAllPendingLimitRequests();

// Get all requests (for history)
$allRequests = db()->fetchAll(
    "SELECT lir.*,
            o.name as organisation_name, o.slug as organisation_slug,
            requester.full_name as requester_name, requester.email as requester_email,
            reviewer.full_name as reviewer_name
     FROM limit_increase_requests lir
     JOIN organisations o ON lir.organisation_id = o.id
     LEFT JOIN profiles requester ON lir.requested_by = requester.id
     LEFT JOIN profiles reviewer ON lir.reviewed_by = reviewer.id
     ORDER BY 
        CASE lir.status
            WHEN 'pending' THEN 1
            WHEN 'approved' THEN 2
            WHEN 'denied' THEN 3
            ELSE 4
        END,
        lir.created_at DESC"
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Limit Increase Requests | Super Admin',
        'metaDescription' => 'Review limit increase requests',
        'canonicalUrl' => APP_URL . '/admin/limit-requests.php',
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
                <h1 class="text-2xl font-bold text-gray-900">Limit Increase Requests</h1>
                <p class="mt-1 text-sm text-gray-500">Review and manage limit increase requests from organisations</p>
            </div>

            <!-- Pending Requests -->
            <?php if (!empty($pendingRequests)): ?>
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Pending Requests (<?php echo count($pendingRequests); ?>)</h2>
                    <div class="space-y-4">
                        <?php foreach ($pendingRequests as $request): ?>
                            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-yellow-400">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <?php echo e($request['organisation_name']); ?>
                                            </h3>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <?php echo ucfirst(str_replace('_', ' ', $request['request_type'])); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Current Limit</p>
                                                <p class="text-lg font-semibold text-gray-900"><?php echo $request['current_limit']; ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Requested Limit</p>
                                                <p class="text-lg font-semibold text-blue-600"><?php echo $request['requested_limit']; ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Increase</p>
                                                <p class="text-lg font-semibold text-green-600">+<?php echo $request['requested_limit'] - $request['current_limit']; ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if ($request['reason']): ?>
                                            <div class="mt-4">
                                                <p class="text-sm font-medium text-gray-700">Reason:</p>
                                                <p class="text-sm text-gray-600 mt-1"><?php echo e($request['reason']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-4 text-sm text-gray-500">
                                            <p>Requested by: <?php echo e($request['requester_name'] ?? $request['requester_email']); ?></p>
                                            <p>Submitted: <?php echo date('j M Y, H:i', strtotime($request['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-6 flex flex-col space-y-2">
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="request_id" value="<?php echo e($request['id']); ?>">
                                            <textarea name="review_notes" 
                                                      placeholder="Optional notes..."
                                                      rows="2"
                                                      class="mb-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                                            <button type="submit" 
                                                    class="w-full inline-flex justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="action" value="deny">
                                            <input type="hidden" name="request_id" value="<?php echo e($request['id']); ?>">
                                            <textarea name="review_notes" 
                                                      placeholder="Reason for denial..."
                                                      rows="2"
                                                      class="mb-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to deny this request?');"
                                                    class="w-full inline-flex justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                                Deny
                                            </button>
                                        </form>
                                        
                                        <a href="/admin/organisations.php?id=<?php echo e($request['organisation_id']); ?>" 
                                           class="w-full inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                            View Organisation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-8 bg-white shadow rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests</h3>
                    <p class="mt-1 text-sm text-gray-500">All limit increase requests have been processed.</p>
                </div>
            <?php endif; ?>

            <!-- Request History -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Request History</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organisation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($allRequests)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No requests found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allRequests as $req): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($req['organisation_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo ucfirst(str_replace('_', ' ', $req['request_type'])); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $req['current_limit']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $req['requested_limit']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'denied' => 'bg-red-100 text-red-800',
                                                    'cancelled' => 'bg-gray-100 text-gray-800'
                                                ];
                                                echo $statusColors[$req['status']] ?? 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst($req['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('j M Y', strtotime($req['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($req['reviewed_at']): ?>
                                                <?php echo date('j M Y', strtotime($req['reviewed_at'])); ?>
                                                <?php if ($req['reviewer_name']): ?>
                                                    <br><span class="text-xs text-gray-400">by <?php echo e($req['reviewer_name']); ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>

