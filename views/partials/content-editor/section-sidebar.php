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
        <!-- CV Sections -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">CV Sections</h2>
            <nav class="space-y-1">
                <!-- Personal Profile – first section; links to profile page -->
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
                <?php foreach ($sections as $section): ?>
                    <a href="#<?php echo e($section['id']); ?>" 
                       class="section-nav-item flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors <?php echo $currentSectionId === $section['id'] ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'; ?>"
                       data-section-id="<?php echo e($section['id']); ?>">
                        <div class="flex items-center">
                            <?php if ($currentSectionId === $section['id']): ?>
                                <!-- Active section: right-pointing arrow in blue -->
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            <?php else: ?>
                                <!-- Inactive section: down-pointing arrow in gray -->
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            <?php endif; ?>
                            <span><?php echo e($section['name']); ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <?php if ($section['count'] > 0): ?>
                                <span class="text-xs text-gray-500"><?php echo $section['count']; ?></span>
                            <?php endif; ?>
                            <?php if ($section['isComplete']): ?>
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            <?php else: ?>
                                <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                            <?php endif; ?>
                        </div>
                    </a>
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
