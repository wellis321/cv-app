<?php
/**
 * Usage Widget Component
 * Floating info widget showing usage statistics
 * 
 * Usage:
 * partial('forms/usage-widget', [
 *     'type' => 'candidates', // or 'team'
 *     'current' => 5,
 *     'max' => 10,
 *     'requestUrl' => '/agency/settings.php#limits',
 *     'canRequest' => true
 * ]);
 */

$type = $type ?? 'candidates';
$current = $current ?? 0;
$max = $max ?? 0;
$requestUrl = $requestUrl ?? null;
$canRequest = $canRequest ?? false;

$percentage = $max > 0 ? round(($current / $max) * 100) : 0;
$isNearLimit = $percentage >= 80;
$isAtLimit = $percentage >= 100;

$typeLabel = $type === 'candidates' ? 'Candidate' : 'Team Member';
$typeLabelPlural = $type === 'candidates' ? 'Candidates' : 'Team Members';
?>

<div class="usage-widget fixed bottom-4 right-4 z-50 max-w-sm">
    <div class="bg-white rounded-xl shadow-2xl border-2 <?php echo $isAtLimit ? 'border-red-400' : ($isNearLimit ? 'border-yellow-400' : 'border-blue-400'); ?> overflow-hidden">
        <!-- Widget Header -->
        <div class="bg-gradient-to-r <?php echo $isAtLimit ? 'from-red-500 to-red-600' : ($isNearLimit ? 'from-yellow-500 to-yellow-600' : 'from-blue-500 to-blue-600'); ?> px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="text-sm font-bold text-white"><?php echo e($typeLabelPlural); ?> Usage</h3>
            </div>
            <button type="button" 
                    onclick="toggleUsageWidget(this)"
                    class="text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-<?php echo $isAtLimit ? 'red' : ($isNearLimit ? 'yellow' : 'blue'); ?>-500 rounded p-1"
                    aria-label="Toggle usage widget">
                <svg class="widget-toggle-icon h-4 w-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Widget Content -->
        <div class="widget-content px-4 py-4">
            <div class="mb-3">
                <div class="flex items-baseline justify-between mb-2">
                    <span class="text-2xl font-bold text-gray-900"><?php echo e($current); ?></span>
                    <span class="text-sm font-medium text-gray-500">of <?php echo e($max); ?></span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300 <?php echo $isAtLimit ? 'bg-red-500' : ($isNearLimit ? 'bg-yellow-500' : 'bg-blue-500'); ?>" 
                         style="width: <?php echo min($percentage, 100); ?>%"></div>
                </div>
                <p class="text-xs text-gray-600 mt-1 text-center font-medium">
                    <?php echo $percentage; ?>% used
                </p>
            </div>

            <?php if ($isAtLimit): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                    <p class="text-sm font-semibold text-red-800">
                        Limit reached
                    </p>
                    <?php if ($canRequest && $requestUrl): ?>
                        <a href="<?php echo e($requestUrl); ?>" class="text-xs text-red-700 underline hover:text-red-900 mt-1 inline-block">
                            Request increase →
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif ($isNearLimit): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                    <p class="text-sm font-semibold text-yellow-800">
                        Approaching limit
                    </p>
                    <?php if ($canRequest && $requestUrl): ?>
                        <a href="<?php echo e($requestUrl); ?>" class="text-xs text-yellow-700 underline hover:text-yellow-900 mt-1 inline-block">
                            Request increase →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($canRequest && $requestUrl && !$isAtLimit): ?>
                <a href="<?php echo e($requestUrl); ?>" 
                   class="block w-full text-center text-xs font-medium text-blue-600 hover:text-blue-800 underline">
                    Request increase
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

    // Make widget responsive - adjust on very small screens
    const widgets = document.querySelectorAll('.usage-widget');
    widgets.forEach(function(widget) {
        if (window.innerWidth < 640) {
            widget.classList.add('bottom-2', 'right-2');
            widget.style.maxWidth = 'calc(100% - 1rem)';
        }
    });
</script>

<style>
    @media (max-width: 640px) {
        .usage-widget {
            max-width: calc(100% - 1rem);
            bottom: 1rem;
            right: 1rem;
        }
    }
    
    /* Stack multiple widgets vertically */
    .usage-widget:not(:last-of-type) {
        margin-bottom: 0.5rem;
    }
</style>

