<?php
/**
 * Organisation Info Modal
 * Shows organisation statistics and usage information
 * 
 * This modal is opened via JavaScript from the nav item
 */

$org = getUserOrganisation();
if (!$org) return;

// Get usage statistics
$candidateCount = getOrganisationCandidateCount($org['organisation_id']);
$teamMemberCount = getOrganisationTeamMemberCount($org['organisation_id']);

$candidatePercent = $org['max_candidates'] > 0 ? round(($candidateCount / $org['max_candidates']) * 100) : 0;
$teamPercent = $org['max_team_members'] > 0 ? round(($teamMemberCount / $org['max_team_members']) * 100) : 0;

$candidatesNearLimit = $candidatePercent >= 80;
$candidatesAtLimit = $candidatePercent >= 100;
$teamNearLimit = $teamPercent >= 80;
$teamAtLimit = $teamPercent >= 100;

$planNames = [
    'agency_basic' => 'Basic',
    'agency_pro' => 'Professional',
    'agency_enterprise' => 'Enterprise'
];
$planName = $planNames[$org['plan']] ?? ucfirst(str_replace('_', ' ', $org['plan']));
?>

<!-- Modal Overlay -->
<div id="organisation-info-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeOrganisationModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white" id="modal-title">
                        <?php echo e($org['organisation_name']); ?>
                    </h3>
                    <p class="text-sm text-blue-100 mt-1">
                        <?php echo e($planName); ?> Plan
                    </p>
                </div>
                <button type="button" 
                        onclick="closeOrganisationModal()"
                        class="text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white rounded p-1"
                        aria-label="Close modal">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="bg-white px-6 py-6">
                <!-- Subscription Status -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Subscription Status</p>
                            <p class="mt-1 text-lg font-semibold <?php echo $org['subscription_status'] === 'active' ? 'text-green-600' : 'text-yellow-600'; ?>">
                                <?php echo ucfirst($org['subscription_status']); ?>
                            </p>
                        </div>
                        <?php if ($org['subscription_status'] === 'active' && !empty($org['subscription_current_period_end'])): ?>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Renews</p>
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo date('j M Y', strtotime($org['subscription_current_period_end'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($org['role'] === 'owner'): ?>
                        <a href="/agency/billing.php" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Manage billing →
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Usage Statistics -->
                <div class="space-y-6">
                    <!-- Candidates Usage -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                                <h4 class="text-base font-semibold text-gray-900">Candidates</h4>
                            </div>
                            <span class="text-lg font-bold text-gray-900">
                                <?php echo e($candidateCount); ?> / <?php echo e($org['max_candidates']); ?>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden mb-2">
                            <div class="h-full rounded-full transition-all duration-300 <?php echo $candidatesAtLimit ? 'bg-red-500' : ($candidatesNearLimit ? 'bg-yellow-500' : 'bg-blue-500'); ?>" 
                                 style="width: <?php echo min($candidatePercent, 100); ?>%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 font-medium">
                                <?php echo $candidatePercent; ?>% used
                            </p>
                            <?php if ($candidatesAtLimit): ?>
                                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded">Limit reached</span>
                            <?php elseif ($candidatesNearLimit): ?>
                                <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-1 rounded">Near limit</span>
                            <?php endif; ?>
                        </div>
                        <a href="/agency/candidates.php" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View all candidates →
                        </a>
                    </div>

                    <!-- Team Members Usage -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h4 class="text-base font-semibold text-gray-900">Team Members</h4>
                            </div>
                            <span class="text-lg font-bold text-gray-900">
                                <?php echo e($teamMemberCount); ?> / <?php echo e($org['max_team_members']); ?>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden mb-2">
                            <div class="h-full rounded-full transition-all duration-300 <?php echo $teamAtLimit ? 'bg-red-500' : ($teamNearLimit ? 'bg-yellow-500' : 'bg-green-500'); ?>" 
                                 style="width: <?php echo min($teamPercent, 100); ?>%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 font-medium">
                                <?php echo $teamPercent; ?>% used
                            </p>
                            <?php if ($teamAtLimit): ?>
                                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded">Limit reached</span>
                            <?php elseif ($teamNearLimit): ?>
                                <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-1 rounded">Near limit</span>
                            <?php endif; ?>
                        </div>
                        <?php if (in_array($org['role'], ['owner', 'admin'])): ?>
                            <a href="/agency/team.php" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Manage team →
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Warnings -->
                    <?php if ($candidatesAtLimit || $teamAtLimit): ?>
                        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-red-800">
                                        <?php if ($candidatesAtLimit && $teamAtLimit): ?>
                                            Both limits reached
                                        <?php elseif ($candidatesAtLimit): ?>
                                            Candidate limit reached
                                        <?php else: ?>
                                            Team member limit reached
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($org['role'] === 'owner'): ?>
                                        <a href="/agency/settings.php#limits" class="text-xs text-red-700 underline hover:text-red-900 mt-1 inline-block">
                                            Request limit increase →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($candidatesNearLimit || $teamNearLimit): ?>
                        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-800">Approaching limit</p>
                                    <?php if ($org['role'] === 'owner'): ?>
                                        <a href="/agency/settings.php#limits" class="text-xs text-yellow-700 underline hover:text-yellow-900 mt-1 inline-block">
                                            Request limit increase →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <?php if (in_array($org['role'], ['owner', 'admin'])): ?>
                            <a href="/agency/settings.php" class="text-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                                Settings
                            </a>
                        <?php endif; ?>
                        <?php if ($org['role'] === 'owner'): ?>
                            <a href="/agency/billing.php" class="text-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                                Billing
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openOrganisationModal() {
        const modal = document.getElementById('organisation-info-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }

    function closeOrganisationModal() {
        const modal = document.getElementById('organisation-info-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeOrganisationModal();
        }
    });
</script>

