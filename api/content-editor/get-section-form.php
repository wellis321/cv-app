<?php
/**
 * API endpoint to get HTML form for a section
 * Returns rendered form partial
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo '<div class="bg-red-50 border border-red-200 rounded-md p-4"><p class="text-sm font-medium text-red-800">Authentication required</p></div>';
    exit;
}

$userId = getUserId();
$sectionId = $_GET['section_id'] ?? '';
$editingId = $_GET['edit'] ?? null;
$viewId = $_GET['view'] ?? null;
$addParam = $_GET['add'] ?? null;
$createParam = $_GET['create'] ?? null;
$variantId = $_GET['variant_id'] ?? null;

// Make edit/view/add/variant_id parameters available to form partials via $_GET
if ($editingId) {
    $_GET['edit'] = $editingId;
}
if ($viewId) {
    $_GET['view'] = $viewId;
}
if ($addParam) {
    $_GET['add'] = $addParam;
}
if ($createParam) {
    $_GET['create'] = $createParam;
}
if ($variantId) {
    $_GET['variant_id'] = $variantId;
}

if (empty($sectionId)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-md p-4"><p class="text-sm font-medium text-red-800">Section ID required</p></div>';
    exit;
}

$subscriptionContext = getUserSubscriptionContext($userId);

// Handle special sections
if ($sectionId === 'cv-variants') {
    if ($createParam) {
        // Create new CV variant form
        $partialPath = __DIR__ . '/../../views/partials/content-editor/cv-variants-create.php';
        if (file_exists($partialPath)) {
            include $partialPath;
        } else {
            echo '<div class="p-6"><p class="text-yellow-800">CV Variants create form not found.</p></div>';
        }
    } else {
        // CV Variants list panel
        $partialPath = __DIR__ . '/../../views/partials/content-editor/cv-variants-panel.php';
        if (file_exists($partialPath)) {
            include $partialPath;
        } else {
            echo '<div class="p-6"><p class="text-yellow-800">CV Variants partial not found.</p></div>';
        }
    }
    exit;
} elseif ($sectionId === 'jobs') {
    require_once __DIR__ . '/../../php/job-applications.php';
    if ($editingId) {
        // Edit job inline in content-editor
        $job = getJobApplication($editingId, $userId);
        $partialPath = __DIR__ . '/../../views/partials/content-editor/jobs-panel-edit.php';
        if ($job && file_exists($partialPath)) {
            include $partialPath;
        } elseif (!$job) {
            echo '<div class="p-6"><p class="text-red-600">Application not found.</p><p class="mt-2"><a href="#" onclick="window.location.hash=\'#jobs\'; return false;" class="text-blue-600 hover:underline">Back to list</a></p></div>';
        } else {
            echo '<div class="p-6"><p class="text-yellow-800">Edit partial not found.</p><p class="mt-2"><a href="#" onclick="window.location.hash=\'#jobs\'; return false;" class="text-blue-600 hover:underline">Back to list</a></p></div>';
        }
    } elseif ($viewId) {
        // Single job view within content-editor
        $job = getJobApplication($viewId, $userId);
        if ($job) {
            $job['questions'] = getJobApplicationQuestions($viewId, $userId);
            $job['interview_tasks'] = getJobInterviewTasks($viewId, $userId);
            $job['files'] = getJobApplicationFiles($viewId, $userId);
            require_once __DIR__ . '/../../php/cv-variants.php';
            $linkedVariant = getCvVariantByJobApplication($viewId, $userId);
            $job['linked_cv_variant'] = $linkedVariant;
        }
        $partialPath = __DIR__ . '/../../views/partials/content-editor/jobs-panel-view.php';
        if ($job && file_exists($partialPath)) {
            include $partialPath;
        } elseif (!$job) {
            echo '<div class="p-6"><p class="text-red-600">Application not found.</p><p class="mt-2"><a href="#" onclick="window.location.hash=\'#jobs\'; return false;" class="text-blue-600 hover:underline">Back to list</a></p></div>';
        } else {
            echo '<div class="p-6"><p class="text-yellow-800">View partial not found.</p><p class="mt-2"><a href="#" onclick="window.location.hash=\'#jobs\'; return false;" class="text-blue-600 hover:underline">Back to list</a></p></div>';
        }
    } elseif ($addParam) {
        // Add new job application inline in content-editor
        $partialPath = __DIR__ . '/../../views/partials/content-editor/jobs-panel-add.php';
        if (file_exists($partialPath)) {
            include $partialPath;
        } else {
            echo '<div class="p-6"><p class="text-yellow-800">Add partial not found.</p><p class="mt-2"><a href="#" onclick="window.location.hash=\'#jobs\'; return false;" class="text-blue-600 hover:underline">Back to list</a></p></div>';
        }
    } else {
        // Jobs list panel
        $partialPath = __DIR__ . '/../../views/partials/content-editor/jobs-panel.php';
        if (file_exists($partialPath)) {
            include $partialPath;
        } else {
            echo '<div class="bg-yellow-50 border border-yellow-200 rounded-md p-4"><p class="text-sm font-medium text-yellow-800">Jobs panel is not yet implemented.</p></div>';
        }
    }
    exit;
}

if ($sectionId === 'ai-tools') {
    // Load AI tools panel with CV quality assessment
    $partialPath = __DIR__ . '/../../views/partials/content-editor/ai-tools-panel.php';
    if (file_exists($partialPath)) {
        include $partialPath;
    } else {
        echo '<div class="p-6"><h2 class="text-xl font-bold mb-4">AI Tools</h2><p class="text-gray-600">AI tools panel coming soon.</p></div>';
    }
    exit;
}

if ($sectionId === 'profile') {
    require_once __DIR__ . '/../../php/cv-variants.php';
    $profileRelatedJobId = null;
    if ($variantId) {
        $profileVariant = getCvVariant($variantId, $userId);
        $profileRelatedJobId = !empty($profileVariant['job_application_id']) ? $profileVariant['job_application_id'] : null;
    }
    ?>
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Personal Profile</h2>
        <p class="text-gray-600 mb-4">Your name, contact details, photo, header colours, and CV visibility are edited on a separate page.</p>
        <?php if ($profileRelatedJobId): ?>
        <a href="/content-editor.php#jobs&view=<?php echo e($profileRelatedJobId); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Related Job
        </a>
        <?php else: ?>
        <a href="/profile.php" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Edit profile
        </a>
        <?php endif; ?>
    </div>
    <?php
    exit;
}

// Handle custom sections (IDs like "custom-<uuid>")
if (str_starts_with($sectionId, 'custom-')) {
    // Strip "custom-" prefix to get DB id
    $customSectionId = substr($sectionId, 7);
    // Verify ownership
    $customSection = db()->fetchOne(
        "SELECT * FROM custom_sections WHERE id = ? AND profile_id = ?",
        [$customSectionId, $userId]
    );
    if (!$customSection) {
        echo '<div class="bg-red-50 border border-red-200 rounded-md p-4"><p class="text-sm font-medium text-red-800">Section not found</p></div>';
        exit;
    }
    $partialPath = __DIR__ . '/../../views/partials/content-editor/custom-section-form.php';
    if (file_exists($partialPath)) {
        include $partialPath;
    }
    exit;
}

// Validate section ID
$validSections = ['professional-summary', 'work-experience', 'education', 'skills', 'projects', 'certifications', 'memberships', 'interests', 'qualification-equivalence'];
if (!in_array($sectionId, $validSections)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-md p-4"><p class="text-sm font-medium text-red-800">Invalid section ID</p></div>';
    exit;
}

try {
    // Render the appropriate form partial
    $formPartial = "content-editor/{$sectionId}-form";
    
    // Check if partial exists
    $partialPath = __DIR__ . '/../../views/partials/' . $formPartial . '.php';
    if (!file_exists($partialPath)) {
        echo '<div class="bg-yellow-50 border border-yellow-200 rounded-md p-4"><p class="text-sm font-medium text-yellow-800">Form for this section is not yet implemented.</p></div>';
        exit;
    }
    
    // Render partial with required variables
    $currentSectionId = $sectionId;
    include $partialPath;
    
} catch (Exception $e) {
    error_log("Get section form error: " . $e->getMessage());
    echo '<div class="bg-red-50 border border-red-200 rounded-md p-4"><p class="text-sm font-medium text-red-800">Error loading form</p></div>';
}
