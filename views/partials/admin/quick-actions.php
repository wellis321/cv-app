<?php
/**
 * Admin Quick Actions - shown in the same position on all admin pages
 * Collapsible: expanded on dashboard, collapsed on sub-pages to free space
 */
$pendingLimitRequests = function_exists('getAllPendingLimitRequests') ? getAllPendingLimitRequests() : [];
$pendingRequestsCount = count($pendingLimitRequests);
$isDashboard = (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'dashboard.php');
$quickActionsExpanded = $isDashboard;
?>
<div class="mb-6">
    <?php if (!$isDashboard): ?>
    <div class="mb-4">
        <a href="/admin/dashboard.php" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
    </div>
    <?php endif; ?>
    <div class="flex items-center justify-between gap-2">
        <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
        <button type="button"
                id="quick-actions-toggle"
                aria-expanded="<?php echo $quickActionsExpanded ? 'true' : 'false'; ?>"
                aria-controls="quick-actions-content"
                class="inline-flex items-center gap-1.5 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            <span id="quick-actions-toggle-label"><?php echo $quickActionsExpanded ? 'Hide' : 'Show'; ?></span>
            <svg id="quick-actions-chevron" class="h-4 w-4 transition-transform duration-200 <?php echo $quickActionsExpanded ? '' : '-rotate-90'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>
    <div id="quick-actions-content" class="<?php echo $quickActionsExpanded ? '' : 'hidden'; ?> mt-4" role="region" aria-label="Quick action links">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <a href="/admin/organisations.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Manage Organisations</p>
                <p class="truncate text-sm text-gray-500">View and edit all</p>
            </div>
        </a>

        <a href="/admin/users.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Manage Users</p>
                <p class="truncate text-sm text-gray-500">View all users</p>
            </div>
        </a>

        <a href="/admin/activity.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Activity Log</p>
                <p class="truncate text-sm text-gray-500">System-wide activity</p>
            </div>
        </a>

        <a href="/admin/security-logs.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Security Logs</p>
                <p class="truncate text-sm text-gray-500">Security events</p>
            </div>
        </a>

        <a href="/admin/limit-requests.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0 relative">
                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <?php if ($pendingRequestsCount > 0): ?>
                    <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                        <?php echo $pendingRequestsCount; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Limit Requests</p>
                <p class="truncate text-sm text-gray-500">
                    <?php echo $pendingRequestsCount > 0 ? $pendingRequestsCount . ' pending' : 'Review requests'; ?>
                </p>
            </div>
        </a>

        <a href="/admin/settings.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">System Settings</p>
                <p class="truncate text-sm text-gray-500">Configuration</p>
            </div>
        </a>

        <a href="/admin/remote-work-stories.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Stories</p>
                <p class="truncate text-sm text-gray-500">Remote work stories</p>
            </div>
        </a>

        <a href="/admin/feedback.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Feedback</p>
                <p class="truncate text-sm text-gray-500">User feedback</p>
            </div>
        </a>

        <a href="/admin/seo-plan.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">SEO Plan</p>
                <p class="truncate text-sm text-gray-500">Target keywords & strategy</p>
            </div>
        </a>

        <a href="/admin/competitive-analysis.php"
           class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="absolute inset-0" aria-hidden="true"></span>
                <p class="text-sm font-medium text-gray-900">Competitive Analysis</p>
                <p class="truncate text-sm text-gray-500">Competitors & improvements</p>
            </div>
        </a>
    </div>
    </div>
</div>
<script>
(function() {
    var btn = document.getElementById('quick-actions-toggle');
    var panel = document.getElementById('quick-actions-content');
    var label = document.getElementById('quick-actions-toggle-label');
    var chevron = document.getElementById('quick-actions-chevron');
    if (btn && panel && label && chevron) {
        btn.addEventListener('click', function() {
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            expanded = !expanded;
            btn.setAttribute('aria-expanded', expanded);
            panel.classList.toggle('hidden', !expanded);
            label.textContent = expanded ? 'Hide' : 'Show';
            chevron.classList.toggle('-rotate-90', !expanded);
        });
    }
})();
</script>
