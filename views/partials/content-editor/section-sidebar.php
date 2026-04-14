<?php
/**
 * Section Sidebar Component
 * Displays accordion-style navigation for CV sections
 */

if (!isset($sections) || !isset($currentSectionId)) {
    return;
}

$userId = getUserId();

// Get completion status for each section
foreach ($sections as &$section) {
    $count = 0;
    $isComplete = false;

    switch ($section['id']) {
        case 'professional-summary':
            $summary = db()->fetchOne("SELECT id FROM professional_summary WHERE profile_id = ?", [$userId]);
            $isComplete = !empty($summary);
            $count = $isComplete ? 1 : 0;
            break;
        case 'work-experience':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM work_experience WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'education':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM education WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'skills':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM skills WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'projects':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM projects WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'certifications':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM certifications WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'qualification-equivalence':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM professional_qualification_equivalence WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'memberships':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM professional_memberships WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
        case 'interests':
            $count = db()->fetchOne("SELECT COUNT(*) as count FROM interests WHERE profile_id = ?", [$userId])['count'];
            $isComplete = $count > 0;
            break;
    }

    $section['count'] = $count;
    $section['isComplete'] = $isComplete;
}
unset($section);
?>
<?php
// Get job application stats for Jobs section
if (function_exists('getJobApplicationStats')) {
    $jobStats = getJobApplicationStats();
} else {
    require_once __DIR__ . '/../../php/job-applications.php';
    $jobStats = getJobApplicationStats();
}
$jobCount = $jobStats['total'] ?? 0;

// Profile completion: name and username set
$profileRow = db()->fetchOne("SELECT full_name, username FROM profiles WHERE id = ?", [$userId]);
$profileComplete = !empty($profileRow['full_name']) && !empty($profileRow['username']);
?>
<div class="bg-white border-r border-gray-200 h-full overflow-y-auto">
    <div class="p-4 space-y-6">
        <?php
        // Split sections into sidebar (left column) and main body (right column) groups
        $sidebarColIds = ['certifications', 'education', 'skills', 'interests'];
        $mainColIds    = ['professional-summary', 'work-experience', 'projects', 'qualification-equivalence', 'memberships'];
        $sidebarSections = array_filter($sections, fn($s) => in_array($s['id'], $sidebarColIds));
        $mainSections    = array_filter($sections, fn($s) => in_array($s['id'], $mainColIds));
        ?>
        <!-- CV Sections -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">CV Sections</h2>
                <button id="toggle-section-reorder-btn" type="button"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium focus:outline-none">
                    Reorder
                </button>
            </div>
            <!-- Reorder info bar (hidden by default) -->
            <div id="section-reorder-info" class="hidden mb-3 p-2 bg-blue-50 border border-blue-200 rounded-md text-xs text-blue-700">
                Drag to reorder within each group. Sections stay in their column on your CV.
                <button id="save-section-order-btn" type="button"
                        class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium">
                    Save order
                </button>
            </div>

            <!-- Personal Profile – always fixed at top, never reorderable -->
            <nav class="space-y-1 mb-3">
                <a href="/profile.php"
                   class="section-nav-item flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors text-gray-700 hover:bg-gray-50"
                   data-section-id="profile">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Personal Profile</span>
                    </div>
                    <?php if ($profileComplete): ?>
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    <?php else: ?>
                        <div class="w-2 h-2 rounded-full bg-amber-400" title="Complete name and username"></div>
                    <?php endif; ?>
                </a>
            </nav>

            <!-- Main body sections (right column on CV) -->
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide px-1 mb-1 section-group-label">Main</p>
            <nav id="main-sections-list" class="space-y-1 mb-3">
                <?php foreach ($mainSections as $section): ?>
                    <?php include __DIR__ . '/_section-nav-item.php'; ?>
                <?php endforeach; ?>
            </nav>

            <!-- Sidebar sections (left column on CV) -->
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide px-1 mb-1 section-group-label">Sidebar</p>
            <nav id="sidebar-sections-list" class="space-y-1">
                <?php foreach ($sidebarSections as $section): ?>
                    <?php include __DIR__ . '/_section-nav-item.php'; ?>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Jobs Section -->
        <div class="border-t border-gray-200 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Management</h2>
            <nav class="space-y-1">
                <a href="#jobs" 
                   class="section-nav-item flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo $currentSectionId === 'jobs' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50'; ?>"
                   data-section-id="jobs">
                    <div class="flex items-center">
                        <?php if ($currentSectionId === 'jobs'): ?>
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        <?php endif; ?>
                        <span>Manage Jobs</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <?php if ($jobCount > 0): ?>
                            <span class="text-xs text-gray-500"><?php echo $jobCount; ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            </nav>
        </div>

        <!-- CV Variants Section -->
        <div class="border-t border-gray-200 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">CV Management</h2>
            <nav class="space-y-1">
                <a href="#cv-variants" 
                   class="section-nav-item flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo $currentSectionId === 'cv-variants' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50'; ?>"
                   data-section-id="cv-variants">
                    <div class="flex items-center">
                        <?php if ($currentSectionId === 'cv-variants'): ?>
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        <?php endif; ?>
                        <span>CV Variants</span>
                    </div>
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
            </nav>
        </div>

        <!-- AI Tools Section -->
        <div class="border-t border-gray-200 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">AI Tools</h2>
            <nav class="space-y-1">
                <a href="#ai-tools" 
                   class="section-nav-item flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo $currentSectionId === 'ai-tools' ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50'; ?>"
                   data-section-id="ai-tools">
                    <div class="flex items-center">
                        <?php if ($currentSectionId === 'ai-tools'): ?>
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        <?php endif; ?>
                        <span>AI Tools</span>
                    </div>
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </a>
            </nav>
        </div>
    </div>
</div>
