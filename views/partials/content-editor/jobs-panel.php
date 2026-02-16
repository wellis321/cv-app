<?php
/**
 * Jobs Panel Component
 * Job management interface for content editor
 */

if (!function_exists('getJobApplicationStats')) {
    require_once __DIR__ . '/../../../php/job-applications.php';
}

$userId = getUserId();
$stats = getJobApplicationStats($userId);
?>
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Job Applications</h1>
            <p class="mt-1 text-sm text-gray-500">Track and manage your job applications</p>
        </div>

        <!-- Statistics: single row, compact, no wrap -->
        <div class="flex flex-nowrap gap-2 overflow-x-auto pb-1 mb-6 scrollbar-thin">
            <button type="button" 
                    onclick="filterJobsByStatus('all')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-blue-300 whitespace-nowrap"
                    data-status="all">
                <span class="text-xs text-gray-500">Total</span> <span class="text-lg font-bold text-gray-900"><?php echo $stats['total']; ?></span>
            </button>
            <button type="button" 
                    onclick="filterJobsByStatus('applied')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-yellow-300 whitespace-nowrap"
                    data-status="applied">
                <span class="text-xs text-gray-500">Applied</span> <span class="text-lg font-bold text-yellow-600"><?php echo $stats['applied']; ?></span>
            </button>
            <button type="button" 
                    onclick="filterJobsByStatus('interviewing')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-purple-300 whitespace-nowrap"
                    data-status="interviewing">
                <span class="text-xs text-gray-500">Interviewing</span> <span class="text-lg font-bold text-purple-600"><?php echo $stats['interviewing']; ?></span>
            </button>
            <button type="button" 
                    onclick="filterJobsByStatus('offered')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-blue-300 whitespace-nowrap"
                    data-status="offered">
                <span class="text-xs text-gray-500">Offered</span> <span class="text-lg font-bold text-blue-600"><?php echo $stats['offered']; ?></span>
            </button>
            <button type="button" 
                    onclick="filterJobsByStatus('accepted')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-green-300 whitespace-nowrap"
                    data-status="accepted">
                <span class="text-xs text-gray-500">Accepted</span> <span class="text-lg font-bold text-green-600"><?php echo $stats['accepted']; ?></span>
            </button>
            <button type="button" 
                    onclick="filterJobsByStatus('rejected')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-red-300 whitespace-nowrap"
                    data-status="rejected">
                <span class="text-xs text-gray-500">Rejected</span> <span class="text-lg font-bold text-red-600"><?php echo $stats['rejected']; ?></span>
            </button>
            <button type="button" 
                    onclick="filterJobsByStatus('all')"
                    class="stat-card flex-shrink-0 bg-white rounded-lg shadow px-3 py-2 hover:shadow-lg transition-shadow cursor-pointer text-left border-2 border-transparent hover:border-indigo-300 whitespace-nowrap"
                    data-filter="interview">
                <span class="text-xs text-gray-500">Interviews</span> <span class="text-lg font-bold text-indigo-600"><?php echo $stats['had_interview']; ?></span>
            </button>
        </div>

        <!-- Actions Bar -->
        <div class="mb-6 flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap gap-3 flex-1 items-center min-w-0">
                <select id="jobs-status-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="all">All Status</option>
                    <option value="applied">Applied</option>
                    <option value="interviewing">Interviewing</option>
                    <option value="offered">Offered</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                    <option value="withdrawn">Withdrawn</option>
                    <option value="in_progress">In Progress</option>
                </select>
                <input type="text" 
                       id="jobs-search-input" 
                       placeholder="Search by company or job title..."
                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm flex-1 min-w-64">
                <!-- View Toggle: Card vs Table -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1" role="group" aria-label="View">
                    <button type="button" id="jobs-view-toggle-cards" 
                            class="jobs-view-toggle-btn px-2.5 py-1.5 rounded-md text-sm font-medium transition-colors bg-white text-gray-900 shadow-sm"
                            title="Card view">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button type="button" id="jobs-view-toggle-table" 
                            class="jobs-view-toggle-btn px-2.5 py-1.5 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900"
                            title="Table view">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
                <!-- Column visibility dropdown -->
                <div class="relative" id="jobs-columns-toggle-wrap">
                    <button type="button" id="jobs-columns-toggle-btn"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            title="Choose which columns to show">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Columns
                    </button>
                    <div id="jobs-columns-dropdown" class="hidden absolute left-0 top-full mt-1 min-w-[10rem] py-2 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <style>.jobs-col-check{width:1rem;height:1rem;flex-shrink:0;cursor:pointer;accent-color:#2563eb}</style>
                        <div class="px-3 py-2 text-xs font-medium text-gray-500 uppercase border-b border-gray-100">Show columns</div>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="company" class="jobs-col-check"> Company</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="job_title" class="jobs-col-check"> Job Title</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="status" class="jobs-col-check"> Status</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="priority" class="jobs-col-check"> Priority</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="closing_date" class="jobs-col-check"> Closing</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="location" class="jobs-col-check"> Location</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="salary" class="jobs-col-check"> Salary</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="date_added" class="jobs-col-check"> Date</label>
                        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm"><input type="checkbox" data-column="actions" class="jobs-col-check"> Actions</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0 items-center">
                <button type="button" id="jobs-quick-add-link-btn"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors"
                        title="Paste a job URL to quickly add it to your list (no extension needed)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Quick add from link
                </button>
                <button id="jobs-add-application-btn" 
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Application
                </button>
            </div>
        </div>

        <!-- Applications Container (data-csrf set by JS when jobs load) -->
        <div id="jobs-applications-container" class="bg-white rounded-lg shadow" data-csrf="">
            <div class="p-6">
                <!-- Cards View -->
                <div id="jobs-applications-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="text-center py-12 text-gray-500 col-span-full">Loading applications...</div>
                </div>
                <!-- Table View -->
                <style>
                    #jobs-applications-table {
                        position: relative;
                    }
                    /* Column visibility - hide columns when jobs-hide-{column} is on wrapper */
                    .jobs-table-wrap.jobs-hide-company th[data-column="company"],
                    .jobs-table-wrap.jobs-hide-company td[data-column="company"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-job_title th[data-column="job_title"],
                    .jobs-table-wrap.jobs-hide-job_title td[data-column="job_title"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-status th[data-column="status"],
                    .jobs-table-wrap.jobs-hide-status td[data-column="status"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-priority th[data-column="priority"],
                    .jobs-table-wrap.jobs-hide-priority td[data-column="priority"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-closing_date th[data-column="closing_date"],
                    .jobs-table-wrap.jobs-hide-closing_date td[data-column="closing_date"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-location th[data-column="location"],
                    .jobs-table-wrap.jobs-hide-location td[data-column="location"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-salary th[data-column="salary"],
                    .jobs-table-wrap.jobs-hide-salary td[data-column="salary"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-date_added th[data-column="date_added"],
                    .jobs-table-wrap.jobs-hide-date_added td[data-column="date_added"] { display: none !important; }
                    .jobs-table-wrap.jobs-hide-actions th[data-column="actions"],
                    .jobs-table-wrap.jobs-hide-actions td[data-column="actions"] { display: none !important; }
                    #jobs-applications-table::-webkit-scrollbar {
                        height: 12px;
                    }
                    #jobs-applications-table::-webkit-scrollbar-track {
                        background: #f1f1f1;
                    }
                    #jobs-applications-table::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 6px;
                    }
                    #jobs-applications-table::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }
                    /* Always show horizontal scrollbar */
                    #jobs-applications-table {
                        overflow-x: scroll !important;
                        overflow-y: auto;
                    }
                </style>
                <div id="jobs-applications-table" class="hidden jobs-table-wrap" style="max-height: calc(100vh - 300px); overflow-x: scroll; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-200 jobs-table">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th data-column="company" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Company</th>
                                <th data-column="job_title" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Job Title</th>
                                <th data-column="status" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Status</th>
                                <th data-column="priority" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Priority</th>
                                <th data-column="closing_date" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Closing Date</th>
                                <th data-column="location" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Location</th>
                                <th data-column="salary" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Salary</th>
                                <th data-column="date_added" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Date Added</th>
                                <th data-column="actions" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="jobs-table-body" class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">Loading applications...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick add from link modal -->
<div id="jobs-quick-add-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto" aria-hidden="true">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Quick add from link</h2>
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800 font-medium mb-1">No extension needed!</p>
                <p class="text-xs text-blue-700">This is a simple way to save jobs without installing the browser extension. Just paste the job URL below and click Save.</p>
            </div>
            <p class="text-sm text-gray-600 mb-4">Copy the link from the job page (browser address bar or the job listing), then paste it below. We can't read your other tabs for security reasons—so paste the URL here. Add title and deadline if you like, then save.</p>
            <p class="text-xs text-gray-500 mb-4">💡 <strong>Want one-click save?</strong> Use the <a href="/save-job-token.php" target="_blank" rel="noopener" class="text-blue-600 hover:underline font-medium">browser extension</a>—then save from any job page without leaving it or copying URLs.</p>
            <form id="jobs-quick-add-form" class="space-y-4">
                <input type="hidden" name="csrf_token" id="jobs-quick-add-csrf" value="">
                <div>
                    <label for="jobs-quick-add-url" class="block text-sm font-semibold text-gray-900 mb-1">Job URL <span class="text-red-600">*</span></label>
                    <input type="url" id="jobs-quick-add-url" name="url" required placeholder="Paste the job page link here (e.g. https://...)"
                           class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="jobs-quick-add-title" class="block text-sm font-semibold text-gray-900 mb-1">Job title (optional)</label>
                    <input type="text" id="jobs-quick-add-title" name="title" placeholder="e.g. Senior Developer"
                           class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="jobs-quick-add-closing" class="block text-sm font-semibold text-gray-900 mb-1">Closing date (optional)</label>
                    <input type="date" id="jobs-quick-add-closing" name="closing_date"
                           class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="jobs-quick-add-priority" class="block text-sm font-semibold text-gray-900 mb-1">Priority (optional)</label>
                    <select id="jobs-quick-add-priority" name="priority" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">None</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div id="jobs-quick-add-error" class="hidden rounded-md bg-red-50 p-2 text-sm text-red-800"></div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" id="jobs-quick-add-submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Save job</button>
                    <button type="button" id="jobs-quick-add-cancel" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Job Application Modal (will be loaded dynamically) -->
<div id="jobs-application-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto">
    <!-- Modal content will be loaded here -->
</div>

<script>
// Initialize jobs panel - adapt JobApplications from job-applications.php
(function() {
    function initJobsPanel() {
        // Check if we're in the content editor jobs panel
        const container = document.getElementById('jobs-applications-container');
        if (!container) return;
        
        // Column visibility is set up by jobs-panel-content-editor.js to avoid duplicate handlers
        loadJobsData();
    }
    
    async function loadJobsData() {
        try {
            const response = await fetch('/api/job-applications.php');
            const data = await response.json();
            const applications = data.applications || data;
            const csrfToken = data.csrf_token || '';
            
            var container = document.getElementById('jobs-applications-container');
            if (container && csrfToken) container.setAttribute('data-csrf', csrfToken);
            renderJobsList(applications, csrfToken);
            setupJobsEventListeners(applications, csrfToken);
        } catch (error) {
            console.error('Error loading jobs:', error);
            var cardsEl = document.getElementById('jobs-applications-cards');
            var tableBody = document.getElementById('jobs-table-body');
            if (cardsEl) cardsEl.innerHTML = '<div class="text-center py-12 text-red-500 col-span-full">Error loading job applications. Please refresh the page.</div>';
            if (tableBody) tableBody.innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-red-500">Error loading job applications. Please refresh the page.</td></tr>';
        }
    }
    
    function renderJobsList(applications, csrfToken) {
        const cardsEl = document.getElementById('jobs-applications-cards');
        const tableBody = document.getElementById('jobs-table-body');
        if (!cardsEl && !tableBody) return;
        
        const statusFilter = document.getElementById('jobs-status-filter')?.value || 'all';
        const searchTerm = (document.getElementById('jobs-search-input')?.value || '').toLowerCase();
        
        const filtered = applications.filter(app => {
            const matchesStatus = statusFilter === 'all' || app.status === statusFilter;
            const matchesSearch = !searchTerm || 
                (app.company_name || '').toLowerCase().includes(searchTerm) ||
                (app.job_title || '').toLowerCase().includes(searchTerm);
            return matchesStatus && matchesSearch;
        });
        
        const cardsHtml = filtered.length === 0
            ? '<div class="text-center py-12 text-gray-500 col-span-full">No applications found.</div>'
            : filtered.map(app => {
                var dueSoon = getDueSoon(app.next_follow_up);
                var hasLeftBorder = dueSoon.urgent || dueSoon.soon;
                var borderClass = dueSoon.urgent ? 'border-l-4' : (dueSoon.soon ? 'border-l-4' : '');
                var borderStyle = dueSoon.urgent ? ' style="border-left-color: #f87171 !important;"' : (dueSoon.soon ? ' style="border-left-color: #f59e0b !important;"' : '');
                // When there's a left border, only round right corners; otherwise round all corners
                var roundedClass = hasLeftBorder ? 'rounded-tr-lg rounded-br-lg' : 'rounded-lg';
                // Hover border - only apply if no left border to avoid overriding the left border color
                var hoverBorderClass = hasLeftBorder ? '' : 'hover:border-green-300';
                var priorityBadge = app.priority ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' + (app.priority === 'high' ? 'bg-red-100 text-red-800' : (app.priority === 'medium' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600')) + '">' + escapeHtml(app.priority) + '</span>' : '';
                var dueBadge = dueSoon.label ? '<span class="text-xs font-medium ' + (dueSoon.urgent ? 'text-red-600' : (dueSoon.soon ? 'text-amber-700' : 'text-gray-600')) + '">' + dueSoon.label + '</span>' : '';
                const viewHash = '#jobs&view=' + app.id;
                const editHash = '#jobs&edit=' + app.id;
                const deleteCall = "event.stopPropagation(); deleteJob('" + (app.id || '').replace(/'/g, "\\'") + "', '" + (csrfToken || '').replace(/'/g, "\\'") + "'); return false;";
                return `
                <div class="border border-gray-200 ${roundedClass} p-4 hover:shadow-lg ${hoverBorderClass} transition-all bg-white ${borderClass}"${borderStyle}>
                    <div class="mb-3 flex flex-wrap items-start justify-between gap-2" onclick="window.location.hash='${viewHash}'" style="cursor:pointer">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">${escapeHtml(app.job_title || '')}</h3>
                            <p class="text-sm text-gray-600 font-medium">${escapeHtml(app.company_name || '')}</p>
                        </div>
                        <div class="flex flex-wrap gap-1.5 items-center">${priorityBadge} ${dueBadge}</div>
                    </div>
                    <div class="space-y-2 mb-4" onclick="window.location.hash='${viewHash}'" style="cursor:pointer">
                        ${app.job_location ? '<p class="text-sm text-gray-500 flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' + escapeHtml(app.job_location) + '</p>' : ''}
                        ${app.salary_range ? '<p class="text-sm text-gray-500 flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0-7v1m0-1c-1.11 0-2.08.402-2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' + escapeHtml(app.salary_range) + '</p>' : ''}
                        <p class="text-xs text-gray-400">${app.application_date ? 'Applied: ' + new Date(app.application_date).toLocaleDateString() : (app.created_at ? 'Added: ' + new Date(app.created_at).toLocaleDateString() + ', ' + new Date(app.created_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}) : '—')}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100" onclick="event.stopPropagation()">
                        <a href="${viewHash}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View</a>
                        <a href="${editHash}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Edit</a>
                        <button type="button" onclick="${deleteCall}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>
                    </div>
                </div>
            `;
            }).join('');
        const tableRowsHtml = filtered.length === 0
            ? '<tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">No applications found.</td></tr>'
            : filtered.map(app => {
                const dateLabel = app.application_date ? ('Applied: ' + new Date(app.application_date).toLocaleDateString()) : (app.created_at ? new Date(app.created_at).toLocaleDateString() : '—');
                const viewHash = '#jobs&view=' + app.id;
                const editHash = '#jobs&edit=' + app.id;
                const dueSoon = getDueSoon(app.next_follow_up);
                const priorityCell = app.priority ? '<span class="inline-flex px-2 py-0.5 rounded text-xs font-medium ' + (app.priority === 'high' ? 'bg-red-100 text-red-800' : (app.priority === 'medium' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600')) + '">' + escapeHtml(app.priority) + '</span>' : '—';
                const dueCell = dueSoon.label ? '<span class="text-xs font-medium ' + (dueSoon.urgent ? 'text-red-600' : (dueSoon.soon ? 'text-amber-700' : 'text-gray-600')) + '">' + escapeHtml(dueSoon.label) + '</span>' : '—';
                return '<tr class="hover:bg-gray-50 cursor-pointer" role="button" tabindex="0" onclick="window.location.hash=\'' + viewHash.replace(/'/g, "\\'") + '\'" onkeydown="if(event.key===\'Enter\'||event.key===\' \'){event.preventDefault();window.location.hash=\'' + viewHash.replace(/'/g, "\\'") + '\'}">' +
                    '<td data-column="company" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(app.company_name || '') + '</td>' +
                    '<td data-column="job_title" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(app.job_title || '') + '</td>' +
                    '<td data-column="status" class="px-6 py-4 whitespace-nowrap"><span class="status-badge status-' + (app.status || 'applied') + '">' + formatStatus(app.status) + '</span></td>' +
                    '<td data-column="priority" class="px-6 py-4 whitespace-nowrap text-sm">' + priorityCell + '</td>' +
                    '<td data-column="closing_date" class="px-6 py-4 whitespace-nowrap text-sm">' + dueCell + '</td>' +
                    '<td data-column="location" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(app.job_location || '') + '</td>' +
                    '<td data-column="salary" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(app.salary_range || '') + '</td>' +
                    '<td data-column="date_added" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + dateLabel + '</td>' +
                    '<td data-column="actions" class="px-6 py-4 whitespace-nowrap text-sm" onclick="event.stopPropagation()">' +
                    '<div style="display:flex;flex-direction:row;flex-wrap:nowrap;align-items:center;gap:8px;">' +
                    '<a href="' + viewHash + '" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:13px;font-weight:500;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:6px;text-decoration:none;">' +
                    '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View</a>' +
                    '<a href="' + editHash + '" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:13px;font-weight:500;color:#15803d;background:#dcfce7;border:1px solid #bbf7d0;border-radius:6px;text-decoration:none;">' +
                    '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Edit</a>' +
                    '<button type="button" onclick="event.stopPropagation(); deleteJob(\'' + (app.id || '').replace(/'/g, "\\'") + '\', \'' + (csrfToken || '').replace(/'/g, "\\'") + '\'); return false;" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:13px;font-weight:500;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;border-radius:6px;cursor:pointer;">' +
                    '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>' +
                    '</div></td></tr>';
            }).join('');
        if (cardsEl) cardsEl.innerHTML = cardsHtml;
        if (tableBody) tableBody.innerHTML = tableRowsHtml;
    }
    
    const JOBS_COLUMNS = ['company', 'job_title', 'status', 'priority', 'closing_date', 'location', 'salary', 'date_added', 'actions'];
    const JOBS_COL_VISIBILITY_KEY = 'jobApplicationsTableColumns';

    function getJobsColumnVisibility() {
        try {
            var saved = localStorage.getItem(JOBS_COL_VISIBILITY_KEY);
            if (saved) {
                var parsed = JSON.parse(saved);
                var out = {};
                JOBS_COLUMNS.forEach(function(c) { out[c] = parsed[c] !== false; });
                return out;
            }
        } catch (e) {}
        var def = {};
        JOBS_COLUMNS.forEach(function(c) { def[c] = true; });
        return def;
    }

    function setJobsColumnVisibility(vis) {
        try { localStorage.setItem(JOBS_COL_VISIBILITY_KEY, JSON.stringify(vis)); } catch (e) {}
    }

    function applyJobsColumnVisibility() {
        var wrap = document.getElementById('jobs-applications-table');
        if (!wrap) return;
        var vis = getJobsColumnVisibility();
        JOBS_COLUMNS.forEach(function(c) {
            wrap.classList.toggle('jobs-hide-' + c, !vis[c]);
        });
    }

    function setupJobsColumnVisibility() {
        var btn = document.getElementById('jobs-columns-toggle-btn');
        var dropdown = document.getElementById('jobs-columns-dropdown');
        if (!btn || !dropdown) return;
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', function() { dropdown.classList.add('hidden'); });
        dropdown.addEventListener('click', function(e) { e.stopPropagation(); });
        var checks = dropdown.querySelectorAll('.jobs-col-check');
        var vis = getJobsColumnVisibility();
        checks.forEach(function(ch) {
            var col = ch.getAttribute('data-column');
            ch.checked = vis[col] !== false;
            ch.addEventListener('change', function() {
                vis[col] = ch.checked;
                setJobsColumnVisibility(vis);
                applyJobsColumnVisibility();
            });
        });
        applyJobsColumnVisibility();
    }

    function setupJobsEventListeners(applications, csrfToken) {
        const statusFilter = document.getElementById('jobs-status-filter');
        const searchInput = document.getElementById('jobs-search-input');
        const addBtn = document.getElementById('jobs-add-application-btn');
        
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                loadJobsData();
            });
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                loadJobsData();
            });
        }
        
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var currentHash = window.location.hash.substring(1);
                window.location.hash = '#jobs&add=1';
                // If we're already on jobs section, manually trigger loadSection since hashchange might not fire
                if (currentHash === 'jobs' || currentHash.startsWith('jobs&')) {
                    setTimeout(function() {
                        if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                            window.contentEditor.loadSection('jobs');
                        } else {
                            window.dispatchEvent(new HashChangeEvent('hashchange', { oldURL: window.location.href, newURL: window.location.href }));
                        }
                    }, 50);
                }
            });
        }
        
        var quickAddBtn = document.getElementById('jobs-quick-add-link-btn');
        var quickAddModal = document.getElementById('jobs-quick-add-modal');
        var quickAddForm = document.getElementById('jobs-quick-add-form');
        var quickAddError = document.getElementById('jobs-quick-add-error');
        var quickAddCancel = document.getElementById('jobs-quick-add-cancel');
        if (quickAddBtn && quickAddModal) {
            quickAddBtn.addEventListener('click', function() {
                var csrf = (document.getElementById('jobs-applications-container') || {}).getAttribute('data-csrf') || '';
                document.getElementById('jobs-quick-add-csrf').value = csrf;
                document.getElementById('jobs-quick-add-url').value = '';
                document.getElementById('jobs-quick-add-title').value = '';
                document.getElementById('jobs-quick-add-closing').value = '';
                document.getElementById('jobs-quick-add-priority').value = '';
                if (quickAddError) { quickAddError.classList.add('hidden'); quickAddError.textContent = ''; }
                quickAddModal.classList.remove('hidden');
            });
        }
        if (quickAddCancel && quickAddModal) {
            quickAddCancel.addEventListener('click', function() { quickAddModal.classList.add('hidden'); });
        }
        if (quickAddModal) {
            quickAddModal.addEventListener('click', function(e) {
                if (e.target === quickAddModal) quickAddModal.classList.add('hidden');
            });
        }
        if (quickAddForm) {
            quickAddForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                var urlInput = document.getElementById('jobs-quick-add-url');
                var titleInput = document.getElementById('jobs-quick-add-title');
                var closingInput = document.getElementById('jobs-quick-add-closing');
                var priorityInput = document.getElementById('jobs-quick-add-priority');
                var submitBtn = document.getElementById('jobs-quick-add-submit');
                var url = (urlInput && urlInput.value) ? urlInput.value.trim() : '';
                if (!url) {
                    if (quickAddError) { quickAddError.textContent = 'Job URL is required.'; quickAddError.classList.remove('hidden'); }
                    return;
                }
                var csrf = (document.getElementById('jobs-quick-add-csrf') || {}).value;
                var payload = {
                    quick_add: true,
                    application_url: url,
                    job_title: (titleInput && titleInput.value) ? titleInput.value.trim() : '',
                    company_name: '—',
                    status: 'interested',
                    csrf_token: csrf
                };
                if (closingInput && closingInput.value) payload.next_follow_up = closingInput.value;
                if (priorityInput && priorityInput.value) payload.priority = priorityInput.value;
                if (quickAddError) { quickAddError.classList.add('hidden'); quickAddError.textContent = ''; }
                if (submitBtn) submitBtn.disabled = true;
                try {
                    var res = await fetch('/api/job-applications.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    var data = await res.json().catch(function() { return {}; });
                    if (res.ok && data.success && data.id) {
                        quickAddModal.classList.add('hidden');
                        loadJobsData();
                        window.location.hash = '#jobs&view=' + encodeURIComponent(data.id);
                    } else {
                        if (quickAddError) {
                            quickAddError.textContent = data.error || 'Failed to save job.';
                            quickAddError.classList.remove('hidden');
                        }
                    }
                } catch (err) {
                    if (quickAddError) {
                        quickAddError.textContent = 'Network error. Please try again.';
                        quickAddError.classList.remove('hidden');
                    }
                }
                if (submitBtn) submitBtn.disabled = false;
            });
        }
    }
    
    window.filterJobsByStatus = function(status) {
        const filter = document.getElementById('jobs-status-filter');
        if (filter) {
            filter.value = status;
            filter.dispatchEvent(new Event('change'));
        }
    };
    
    function getDueSoon(nextFollowUp) {
        if (!nextFollowUp) return { soon: false, urgent: false, label: '', dateStr: '' };
        // Parse date consistently across browsers - handle ISO format (YYYY-MM-DD) explicitly
        let d;
        if (typeof nextFollowUp === 'string' && nextFollowUp.match(/^\d{4}-\d{2}-\d{2}/)) {
            // ISO date format - parse explicitly to avoid timezone issues
            const parts = nextFollowUp.split('T')[0].split('-');
            d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        } else {
            d = new Date(nextFollowUp);
        }
        const today = new Date();
        // Normalize both to midnight for accurate day calculation
        today.setHours(0, 0, 0, 0);
        d.setHours(0, 0, 0, 0);
        // Check if date is valid
        if (isNaN(d.getTime())) return { soon: false, urgent: false, label: '', dateStr: '' };
        // Use floor instead of ceil for consistent calculation
        const days = Math.floor((d - today) / 86400000);
        const dateStr = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        if (days < 0) return { soon: false, urgent: false, label: 'Past due', dateStr: dateStr };
        if (days === 0) return { soon: true, urgent: true, label: 'Due today', dateStr: dateStr };
        if (days === 1) return { soon: true, urgent: true, label: 'Due tomorrow', dateStr: dateStr };
        if (days <= 7) return { soon: true, urgent: false, label: 'Due in ' + days + ' days', dateStr: dateStr };
        return { soon: false, urgent: false, label: dateStr, dateStr: dateStr };
    }
    
    function formatStatus(status) {
        const statusMap = {
            'applied': 'Applied',
            'interviewing': 'Interviewing',
            'offered': 'Offered',
            'accepted': 'Accepted',
            'rejected': 'Rejected',
            'withdrawn': 'Withdrawn',
            'in_progress': 'In Progress'
        };
        return statusMap[status] || status;
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    window.deleteJob = async function(id, csrfToken) {
        if (!confirm('Are you sure you want to delete this application?')) {
            return;
        }
        
        // Prevent multiple clicks
        var deleteBtn = event && event.target ? event.target : null;
        if (deleteBtn && (deleteBtn.disabled || deleteBtn.dataset.deleting === 'true')) {
            return;
        }
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.dataset.deleting = 'true';
        }
        
        try {
            const response = await fetch(`/api/job-applications.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ csrf_token: csrfToken })
            });
            
            const data = await response.json();
            
            if (response.ok && data && data.success) {
                if (typeof loadJobsData === 'function') {
                    loadJobsData();
                } else {
                    window.location.reload();
                }
            } else {
                alert(data && data.error ? data.error : 'Error deleting application');
                if (deleteBtn) {
                    deleteBtn.disabled = false;
                    delete deleteBtn.dataset.deleting;
                }
            }
        } catch (error) {
            console.error('Error deleting:', error);
            alert('Error deleting application. Please try again.');
            if (deleteBtn) {
                deleteBtn.disabled = false;
                delete deleteBtn.dataset.deleting;
            }
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initJobsPanel);
    } else {
        initJobsPanel();
    }
})();
</script>
