<?php
/**
 * Combined Usage Widget Component
 * Shows both candidates and team members in a single widget
 * 
 * Usage:
 * partial('forms/usage-widget-combined', [
 *     'candidates' => ['current' => 5, 'max' => 10],
 *     'team' => ['current' => 2, 'max' => 3],
 *     'requestUrl' => '/agency/settings.php#limits',
 *     'canRequest' => true
 * ]);
 */

$candidates = $candidates ?? ['current' => 0, 'max' => 0];
$team = $team ?? ['current' => 0, 'max' => 0];
$requestUrl = $requestUrl ?? null;
$canRequest = $canRequest ?? false;

$candidatesPercent = $candidates['max'] > 0 ? round(($candidates['current'] / $candidates['max']) * 100) : 0;
$teamPercent = $team['max'] > 0 ? round(($team['current'] / $team['max']) * 100) : 0;

$candidatesNearLimit = $candidatesPercent >= 80;
$candidatesAtLimit = $candidatesPercent >= 100;
$teamNearLimit = $teamPercent >= 80;
$teamAtLimit = $teamPercent >= 100;

$overallStatus = ($candidatesAtLimit || $teamAtLimit) ? 'at_limit' : (($candidatesNearLimit || $teamNearLimit) ? 'near_limit' : 'normal');
?>

<div class="usage-widget fixed bottom-4 right-4 z-50 max-w-sm">
    <div class="bg-white rounded-xl shadow-2xl border-2 <?php echo $overallStatus === 'at_limit' ? 'border-red-400' : ($overallStatus === 'near_limit' ? 'border-yellow-400' : 'border-blue-400'); ?> overflow-hidden">
        <!-- Widget Header -->
        <div class="bg-gradient-to-r <?php echo $overallStatus === 'at_limit' ? 'from-red-500 to-red-600' : ($overallStatus === 'near_limit' ? 'from-yellow-500 to-yellow-600' : 'from-blue-500 to-blue-600'); ?> px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="text-sm font-bold text-white">Usage Overview</h3>
            </div>
            <button type="button" 
                    onclick="toggleUsageWidget(this)"
                    class="text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-<?php echo $overallStatus === 'at_limit' ? 'red' : ($overallStatus === 'near_limit' ? 'yellow' : 'blue'); ?>-500 rounded p-1"
                    aria-label="Toggle usage widget">
                <svg class="widget-toggle-icon h-4 w-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Widget Content -->
        <div class="widget-content px-4 py-4">
            <!-- Candidates Usage -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-900">Candidates</h4>
                    <span class="text-xs font-medium text-gray-500">
                        <?php echo e($candidates['current']); ?> / <?php echo e($candidates['max']); ?>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden mb-1">
                    <div class="h-full rounded-full transition-all duration-300 <?php echo $candidatesAtLimit ? 'bg-red-500' : ($candidatesNearLimit ? 'bg-yellow-500' : 'bg-blue-500'); ?>" 
                         style="width: <?php echo min($candidatesPercent, 100); ?>%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-600 font-medium">
                        <?php echo $candidatesPercent; ?>% used
                    </p>
                    <?php if ($candidatesAtLimit): ?>
                        <span class="text-xs font-semibold text-red-600">Limit reached</span>
                    <?php elseif ($candidatesNearLimit): ?>
                        <span class="text-xs font-semibold text-yellow-600">Near limit</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Team Members Usage -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-900">Team Members</h4>
                    <span class="text-xs font-medium text-gray-500">
                        <?php echo e($team['current']); ?> / <?php echo e($team['max']); ?>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden mb-1">
                    <div class="h-full rounded-full transition-all duration-300 <?php echo $teamAtLimit ? 'bg-red-500' : ($teamNearLimit ? 'bg-yellow-500' : 'bg-blue-500'); ?>" 
                         style="width: <?php echo min($teamPercent, 100); ?>%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-600 font-medium">
                        <?php echo $teamPercent; ?>% used
                    </p>
                    <?php if ($teamAtLimit): ?>
                        <span class="text-xs font-semibold text-red-600">Limit reached</span>
                    <?php elseif ($teamNearLimit): ?>
                        <span class="text-xs font-semibold text-yellow-600">Near limit</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Warnings -->
            <?php if ($candidatesAtLimit || $teamAtLimit): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                    <p class="text-sm font-semibold text-red-800 mb-1">
                        <?php if ($candidatesAtLimit && $teamAtLimit): ?>
                            Both limits reached
                        <?php elseif ($candidatesAtLimit): ?>
                            Candidate limit reached
                        <?php else: ?>
                            Team member limit reached
                        <?php endif; ?>
                    </p>
                    <?php if ($canRequest && $requestUrl): ?>
                        <a href="<?php echo e($requestUrl); ?>" class="text-xs text-red-700 underline hover:text-red-900 mt-1 inline-block">
                            Request increase →
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif ($candidatesNearLimit || $teamNearLimit): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                    <p class="text-sm font-semibold text-yellow-800 mb-1">
                        Approaching limit
                    </p>
                    <?php if ($canRequest && $requestUrl): ?>
                        <a href="<?php echo e($requestUrl); ?>" class="text-xs text-yellow-700 underline hover:text-yellow-900 mt-1 inline-block">
                            Request increase →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($canRequest && $requestUrl && !$candidatesAtLimit && !$teamAtLimit): ?>
                <a href="<?php echo e($requestUrl); ?>" 
                   class="block w-full text-center text-xs font-medium text-blue-600 hover:text-blue-800 underline pt-2 border-t border-gray-200">
                    Request limit increase
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleUsageWidget(button) {
        const widget = button.closest('.usage-widget');
        const content = widget.querySelector('.widget-content');
        const icon = button.querySelector('.widget-toggle-icon');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }
</script>

<style>
    @media (max-width: 640px) {
        .usage-widget {
            max-width: calc(100% - 1rem);
            bottom: 1rem;
            right: 1rem;
        }
    }
</style>

