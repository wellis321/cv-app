<?php
/**
 * Candidate Management
 * View and manage a specific candidate
 */

require_once __DIR__ . '/../php/helpers.php';

// Require authentication and organisation membership
$org = requireOrganisationAccess('viewer');

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

$candidateId = sanitizeInput(get('id') ?? '');

if (empty($candidateId)) {
    setFlash('error', 'Candidate not specified.');
    redirect('/agency/candidates.php');
}

// Get candidate details
$candidate = db()->fetchOne(
    "SELECT p.*, recruiter.full_name as recruiter_name, recruiter.id as recruiter_id
     FROM profiles p
     LEFT JOIN profiles recruiter ON p.managed_by = recruiter.id
     WHERE p.id = ? AND p.organisation_id = ? AND p.account_type = 'candidate'",
    [$candidateId, $org['organisation_id']]
);

if (!$candidate) {
    setFlash('error', 'Candidate not found or you do not have access.');
    redirect('/agency/candidates.php');
}

// Check if user can manage this candidate
$canManage = canManageCandidate($candidateId);

// Handle form submission (assign recruiter)
if (isPost() && $canManage && in_array($org['role'], ['owner', 'admin'])) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/agency/candidate.php?id=' . $candidateId);
    }

    $action = sanitizeInput(post('action'));

    if ($action === 'assign_recruiter') {
        $recruiterId = sanitizeInput(post('recruiter_id'));
        
        // If empty, unassign
        if (empty($recruiterId) || $recruiterId === '') {
            $recruiterId = null;
        } else {
            // Verify recruiter is in the organisation
            $recruiter = db()->fetchOne(
                "SELECT id FROM organisation_members 
                 WHERE user_id = ? AND organisation_id = ? AND is_active = 1 
                 AND role IN ('owner', 'admin', 'recruiter')",
                [$recruiterId, $org['organisation_id']]
            );
            
            if (!$recruiter) {
                setFlash('error', 'Invalid recruiter selected.');
                redirect('/agency/candidate.php?id=' . $candidateId);
            }
        }

        try {
            db()->update('profiles',
                ['managed_by' => $recruiterId, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$candidateId]
            );

            logActivity('candidate.recruiter_assigned', $candidateId, [
                'recruiter_id' => $recruiterId
            ]);

            setFlash('success', 'Recruiter assignment updated successfully.');
            redirect('/agency/candidate.php?id=' . $candidateId);
        } catch (Exception $e) {
            setFlash('error', 'Failed to update assignment. Please try again.');
            redirect('/agency/candidate.php?id=' . $candidateId);
        }
    }
}

// Get team members for recruiter assignment dropdown
$teamMembers = [];
if ($canManage && in_array($org['role'], ['owner', 'admin'])) {
    $teamMembers = getOrganisationTeamMembers($org['organisation_id']);
    // Filter to only recruiters/admins/owners
    $teamMembers = array_filter($teamMembers, function($member) {
        return in_array($member['role'], ['owner', 'admin', 'recruiter']);
    });
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Candidate: ' . e($candidate['full_name'] ?? $candidate['email']) . ' | ' . e($org['organisation_name']),
        'metaDescription' => 'View and manage candidate details.',
        'canonicalUrl' => APP_URL . '/agency/candidate.php',
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
                <div class="flex items-center space-x-4">
                    <a href="/agency/candidates.php" 
                       class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo e($candidate['full_name'] ?? 'Unnamed Candidate'); ?>
                        </h1>
                        <p class="mt-1 text-sm text-gray-500"><?php echo e($candidate['email']); ?></p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <?php if ($candidate['username']): ?>
                        <a href="/cv/@<?php echo e($candidate['username']); ?>" 
                           target="_blank"
                           class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            View CV
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Candidate Details -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Candidate Info Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Candidate Information</h2>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($candidate['full_name'] ?? 'Not set'); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($candidate['email']); ?></dd>
                            </div>
                            <?php if ($candidate['username']): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Username</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e($candidate['username']); ?></dd>
                            </div>
                            <?php endif; ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CV Status</dt>
                                <dd class="mt-1">
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
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CV Visibility</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php echo ucfirst($candidate['cv_visibility'] ?? 'organisation'); ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Added</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php echo date('j M Y, g:i a', strtotime($candidate['created_at'])); ?>
                                </dd>
                            </div>
                            <?php if ($candidate['updated_at'] && $candidate['updated_at'] !== $candidate['created_at']): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php echo date('j M Y, g:i a', strtotime($candidate['updated_at'])); ?>
                                </dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Recruiter Assignment (Admin/Owner only) -->
                    <?php if ($canManage && in_array($org['role'], ['owner', 'admin']) && !empty($teamMembers)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Assigned Recruiter</h2>
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                            <input type="hidden" name="action" value="assign_recruiter">

                            <div>
                                <label for="recruiter_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Recruiter
                                </label>
                                <select name="recruiter_id" id="recruiter_id"
                                        class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($teamMembers as $member): ?>
                                        <option value="<?php echo e($member['user_id']); ?>" 
                                                <?php echo ($candidate['recruiter_id'] === $member['user_id']) ? 'selected' : ''; ?>>
                                            <?php echo e($member['full_name']); ?>
                                            (<?php echo ucfirst($member['role']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                Update Assignment
                            </button>
                        </form>
                    </div>
                    <?php elseif ($candidate['recruiter_name']): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Assigned Recruiter</h2>
                        <p class="text-sm text-gray-900"><?php echo e($candidate['recruiter_name']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>

