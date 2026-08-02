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
<div class="bg-white border-r border-gray-300 h-full overflow-y-auto">
    <div class="p-4 space-y-6">
        <?php
        // Split sections into sidebar (left column) and main body (right column) groups
        $sidebarColIds = ['certifications', 'education', 'skills', 'interests'];
        $mainColIds    = ['professional-summary', 'work-experience', 'projects', 'qualification-equivalence', 'memberships'];
        $sidebarSections = array_values(array_filter($sections, fn($s) => in_array($s['id'], $sidebarColIds)));
        $mainSections    = array_values(array_filter($sections, fn($s) => in_array($s['id'], $mainColIds)));

        // Apply the user's saved section order so the sidebar reflects the same order as the CV
        $savedOrderRaw = db()->fetchOne("SELECT section_order FROM profiles WHERE id = ?", [$userId]);
        if (!empty($savedOrderRaw['section_order'])) {
            $savedOrder = json_decode($savedOrderRaw['section_order'], true);
            if (is_array($savedOrder)) {
                $savedPos = array_flip($savedOrder);
                $sortByPos = fn($a, $b) => ($savedPos[$a['id']] ?? 999) - ($savedPos[$b['id']] ?? 999);
                usort($sidebarSections, $sortByPos);
                usort($mainSections,    $sortByPos);
            }
        }
        ?>
        <!-- CV Sections -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">CV Sections</h2>
                <button id="toggle-section-reorder-btn" type="button"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Reorder
                </button>
            </div>
            <!-- Reorder info bar (hidden by default) -->
            <div id="section-reorder-info" class="hidden mb-3 p-2 bg-blue-50 border border-blue-200 text-xs text-blue-700">
                Drag to reorder within each group.
                <button id="save-section-order-btn" type="button"
                        class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 text-xs font-medium">
                    Save order
                </button>
            </div>

            <!-- Personal Profile / Visibility / Appearance – always fixed at top, never reorderable -->
            <div class="border border-gray-300 bg-gray-50/70 p-2 mb-3">
                <nav class="space-y-1.5">
                    <a href="#profile"
                       class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-l-4 border-gray-300 border-l-gray-300 bg-white shadow-sm text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 hover:shadow"
                       data-section-id="profile">
                        <div class="flex items-center min-w-0">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="truncate">Personal Profile</span>
                        </div>
                        <?php if ($profileComplete): ?>
                            <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        <?php else: ?>
                            <div class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0" title="Complete name and username"></div>
                        <?php endif; ?>
                    </a>
                    <a href="#visibility"
                       class="section-nav-item flex items-center gap-2 px-3 py-2.5 border border-l-4 border-gray-300 border-l-gray-300 bg-white shadow-sm text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 hover:shadow"
                       data-section-id="visibility">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="truncate">Visibility</span>
                    </a>
                    <a href="#appearance"
                       class="section-nav-item flex items-center gap-2 px-3 py-2.5 border border-l-4 border-gray-300 border-l-gray-300 bg-white shadow-sm text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 hover:shadow"
                       data-section-id="appearance">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-4"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17h.01"></path>
                        </svg>
                        <span class="truncate">Appearance</span>
                    </a>
                </nav>
            </div>

            <!-- Main body sections (right column on CV) — includes custom sections -->
            <?php
            $customSections = db()->fetchAll(
                "SELECT * FROM custom_sections WHERE profile_id = ? ORDER BY sort_order ASC, created_at ASC",
                [$userId]
            );
            // Apply saved order to custom sections too
            if (!empty($savedOrderRaw['section_order'])) {
                $savedOrder = json_decode($savedOrderRaw['section_order'], true);
                if (is_array($savedOrder)) {
                    $savedPos = array_flip($savedOrder);
                    usort($customSections, fn($a, $b) =>
                        ($savedPos['custom-' . $a['id']] ?? 999) - ($savedPos['custom-' . $b['id']] ?? 999)
                    );
                }
            }
            ?>
            <div class="border border-gray-300 bg-gray-50/70 p-2 mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide px-1 mb-1.5 section-group-label">Main</p>
                <nav id="main-sections-list" class="space-y-1.5">
                    <?php foreach ($mainSections as $section): ?>
                        <?php include __DIR__ . '/_section-nav-item.php'; ?>
                    <?php endforeach; ?>
                    <?php foreach ($customSections as $cs):
                        $csId = 'custom-' . $cs['id'];
                        $isActive = $currentSectionId === $csId;
                    ?>
                        <div class="section-nav-wrapper relative" data-section-id="<?php echo e($csId); ?>" draggable="false">
                            <div class="drag-handle-sidebar hidden absolute left-0 top-0 bottom-0 flex items-center pl-1 cursor-move text-gray-400" style="z-index:1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                </svg>
                            </div>
                            <a href="#<?php echo e($csId); ?>"
                               class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-l-4 shadow-sm text-sm font-medium transition-all <?php echo $isActive ? 'border-gray-300 border-l-blue-500 bg-blue-100 text-blue-700' : 'border-gray-300 border-l-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 hover:shadow'; ?>"
                               data-section-id="<?php echo e($csId); ?>">
                                <div class="flex items-center min-w-0">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0 <?php echo $isActive ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="truncate"><?php echo e($cs['title']); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Sidebar sections (left column on CV) -->
            <div class="border border-gray-300 bg-gray-50/70 p-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide px-1 mb-1.5 section-group-label">Sidebar</p>
                <nav id="sidebar-sections-list" class="space-y-1.5">
                    <?php foreach ($sidebarSections as $section): ?>
                        <?php include __DIR__ . '/_section-nav-item.php'; ?>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>

        <!-- Custom Sections — "+ Add" only; items live in the Main list above -->
        <div class="border-t border-gray-300 pt-4 mt-2">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide px-1">Custom Sections</p>
                <button id="add-custom-section-btn" type="button"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium focus:outline-none">+ Add</button>
            </div>
            <div id="add-custom-section-form" class="hidden mb-2">
                <div class="flex gap-1">
                    <input type="text" id="new-custom-section-title" placeholder="Section name" maxlength="255"
                           class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <button id="create-custom-section-btn" type="button"
                            class="px-2 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Add</button>
                </div>
            </div>
        </div>

        <!-- Jobs Section -->
        <div class="border-t border-gray-300 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Job Management</h2>
            <nav class="space-y-1.5">
                <a href="#jobs"
                   class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-l-4 shadow-sm text-sm font-medium transition-all <?php echo $currentSectionId === 'jobs' ? 'border-gray-300 border-l-green-500 bg-green-100 text-green-700' : 'border-gray-300 border-l-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 hover:shadow'; ?>"
                   data-section-id="jobs">
                    <div class="flex items-center min-w-0">
                        <span class="truncate">Manage Jobs</span>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <?php if ($jobCount > 0): ?>
                            <span class="text-xs text-gray-500"><?php echo $jobCount; ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            </nav>
        </div>

        <!-- CV Variants Section -->
        <div class="border-t border-gray-300 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">CV Management</h2>
            <nav class="space-y-1.5">
                <a href="#cv-variants"
                   class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-l-4 shadow-sm text-sm font-medium transition-all <?php echo $currentSectionId === 'cv-variants' ? 'border-gray-300 border-l-indigo-500 bg-indigo-100 text-indigo-700' : 'border-gray-300 border-l-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 hover:shadow'; ?>"
                   data-section-id="cv-variants">
                    <div class="flex items-center min-w-0">
                        <span class="truncate">CV Variants</span>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
            </nav>
        </div>

        <!-- AI Tools Section -->
        <div class="border-t border-gray-300 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">AI Tools</h2>
            <nav class="space-y-1.5">
                <a href="#ai-tools"
                   class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-l-4 shadow-sm text-sm font-medium transition-all <?php echo $currentSectionId === 'ai-tools' ? 'border-gray-300 border-l-purple-500 bg-purple-100 text-purple-700' : 'border-gray-300 border-l-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 hover:shadow'; ?>"
                   data-section-id="ai-tools">
                    <div class="flex items-center min-w-0">
                        <span class="truncate">AI Tools</span>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </a>
            </nav>
        </div>
    </div>
</div>
