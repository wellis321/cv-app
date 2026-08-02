<?php
/**
 * CV Display Page
 * Handles both /cv/@username and /cv.php (for viewing own CV when logged in)
 */

require_once __DIR__ . '/php/helpers.php';

// Get username or user ID from query parameters
$username = get('username');
$userIdParam = get('userid');
$variantId = get('variant_id'); // Support CV variants

// Determine which profile to load
$profile = null;
$profileUserId = null;
$cvVariant = null;

if ($username) {
    // Load by username (public view)
    $profile = db()->fetchOne(
        "SELECT * FROM profiles WHERE username = ?",
        [$username]
    );

    if ($profile) {
        $profileUserId = $profile['id'];
    }
} elseif ($userIdParam) {
    // Load by user ID (backward compatibility)
    $profile = db()->fetchOne(
        "SELECT * FROM profiles WHERE id = ?",
        [$userIdParam]
    );

    if ($profile) {
        $profileUserId = $profile['id'];
    }
} elseif (isLoggedIn()) {
    // Logged in user viewing their own CV
    $profileUserId = getUserId();
    $profile = db()->fetchOne(
        "SELECT * FROM profiles WHERE id = ?",
        [$profileUserId]
    );
}

// If no profile found, show error
if (!$profile) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CV Not Found</title>
        <link rel="stylesheet" href="/static/css/tailwind.css">
    </head>
    <body class="bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">CV Not Found</h1>
            <p class="text-gray-600 mb-8">The CV you're looking for doesn't exist or has been removed.</p>
            <a href="/" class="text-blue-600 hover:text-blue-800">Return to Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// CV nav bar (owner only) needs the variant list up front - fetched once here since
// it's shared by both render paths below (custom Twig template vs built-in renderer).
$isCvOwner = isLoggedIn() && getUserId() === $profileUserId;
$navCvVariants = [];
$navMasterVariantId = null;
if ($isCvOwner) {
    $navCvVariants = getUserCvVariants($profileUserId);
    $navMasterVariantId = getOrCreateMasterVariant($profileUserId);
}

// Check CV visibility/access permissions
$currentUserId = getUserId();
$canView = false;
$cvVisibility = $profile['cv_visibility'] ?? 'public';

// Owner can always view their own CV
if ($currentUserId && $currentUserId === $profileUserId) {
    $canView = true;
}
// Public CVs can be viewed by anyone
elseif ($cvVisibility === 'public') {
    $canView = true;
}
// Organisation-visible CVs require membership check
elseif ($cvVisibility === 'organisation' && $currentUserId && $profile['organisation_id']) {
    // Check if current user is in the same organisation
    $viewerMembership = db()->fetchOne(
        "SELECT id FROM organisation_members
         WHERE user_id = ? AND organisation_id = ? AND is_active = 1",
        [$currentUserId, $profile['organisation_id']]
    );
    $canView = !empty($viewerMembership);
}
// Private CVs can only be viewed by the owner (already checked above)
elseif ($cvVisibility === 'private') {
    $canView = false;
}

// Show access denied if can't view
if (!$canView) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied</title>
        <link rel="stylesheet" href="/static/css/tailwind.css">
    </head>
    <body class="bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Access Denied</h1>
            <p class="text-gray-600 mb-8">This CV is not publicly available. Please contact the owner or your organisation administrator for access.</p>
            <?php if (!isLoggedIn()): ?>
                <a href="/?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="text-blue-600 hover:text-blue-800">Log in to continue</a>
            <?php else: ?>
                <a href="/" class="text-blue-600 hover:text-blue-800">Return to Home</a>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Load CV data - either from variant or master CV
$cvData = null;
$cvVariant = null;
// Load variant when: variant_id in URL and (owner logged in, OR CV is public and variant belongs to this profile)
if ($variantId && $profileUserId) {
    $cvVariant = getCvVariant($variantId, $profileUserId);
    if ($cvVariant && (($currentUserId && $currentUserId === $profileUserId) || $canView)) {
        $cvData = loadCvVariantData($variantId);
        if ($cvData && isset($cvData['variant'])) {
            $cvData['profile'] = $profile;
        } else {
            $cvData = null;
        }
    }
}

// Fallback to master CV if variant not found or not specified
if (!$cvData) {
    $cvData = loadCvData($profileUserId);
}

// Format date helper - show only month and year (MM/YYYY)
function formatCvDate($date, $format = null) {
    if (empty($date)) return '';

    $timestamp = strtotime($date);
    if ($timestamp === false) return $date;

    // Format as MM/YYYY (month/year only, matching original implementation)
    // date('m') gives zero-padded month (01-12), date('Y') gives 4-digit year
    return date('m/Y', $timestamp);
}

// Check for custom template (new system: cv_templates table)
require_once __DIR__ . '/php/cv-templates.php';

// Check if a specific template is requested via query parameter
$requestedTemplateId = $_GET['template'] ?? null;
$activeTemplate = null;

if ($requestedTemplateId) {
    // Get the requested template (must belong to the user)
    $activeTemplate = getCvTemplate($requestedTemplateId, $profileUserId);
} else {
    // Get the active template for the user
    $activeTemplate = getActiveCvTemplate($profileUserId);
}

// Fallback to old system (profiles table) for backward compatibility
if (!$activeTemplate && !empty($profile['custom_cv_template_active']) && !empty($profile['custom_cv_template_html'])) {
    $activeTemplate = [
        'template_html' => $profile['custom_cv_template_html'],
        'template_css' => $profile['custom_cv_template_css'] ?? '',
        'template_name' => 'Custom Template'
    ];
}

$cvName = trim($profile['full_name'] ?? '');
$cvMetaDescription = $cvName !== ''
    ? "View {$cvName}'s CV on Simple CV Builder. Experience, skills, education and projects."
    : "View this CV on Simple CV Builder. Experience, skills, education and projects.";

// Normalize storage URLs (e.g. profile photo) to current request origin - fixes port mismatch in local dev
if (!empty($profile['photo_url'])) {
    $profile['photo_url'] = normalizeStorageUrlForDisplay($profile['photo_url']);
}

// Online CV preference: show/hide key responsibilities within work experience.
// Apply to all render paths (Twig templates and built-in fallback).
$showResponsibilitiesOnline = getShowResponsibilitiesOnlineForCv($profile, $cvVariant);
if (!$showResponsibilitiesOnline && !empty($cvData['work_experience']) && is_array($cvData['work_experience'])) {
    foreach ($cvData['work_experience'] as $i => $w) {
        if (is_array($w) && array_key_exists('responsibility_categories', $w)) {
            unset($cvData['work_experience'][$i]['responsibility_categories']);
        }
    }
}

if ($activeTemplate) {
    // Render custom template using Twig (secure)
    $customHtml = $activeTemplate['template_html'];
    $customCss = $activeTemplate['template_css'] ?? '';
    
    // Use Twig template service for secure rendering
    // Variables profile, cvData, sections_online, and formatCvDate() function are available in the template
    require_once __DIR__ . '/php/twig-template-service.php';
    $sectionsOnline = getSectionsOnlineForCv($profile, $cvVariant);
    $renderedContent = renderTemplate($customHtml, [
        'profile' => $profile,
        'cvData' => $cvData,
        'sections_online' => $sectionsOnline,
        'show_responsibilities_online' => $showResponsibilitiesOnline
    ]);
    
    // Output custom template
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo e($profile['full_name'] ?? 'CV'); ?> - CV</title>
        <meta name="description" content="<?php echo e($cvMetaDescription); ?>">
        <link rel="stylesheet" href="/static/css/tailwind.css">
        <?php if (!empty($customCss)): ?>
            <style><?php echo $customCss; ?></style>
        <?php endif; ?>
        <style>
            @media print {
                .no-print { display: none !important; }
            }
            /* CV toolbar button styles – harmonious secondary/primary hierarchy */
            .cv-toolbar-btn-secondary { border: 1px solid #d1d5db; background: #fff; color: #374151; }
            .cv-toolbar-btn-secondary:hover { background: #f9fafb; }
            .cv-toolbar-btn-primary { background: #2563eb; color: #fff; border: 1px solid #2563eb; }
            .cv-toolbar-btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; }
            /* Prevent left column (e.g. interests) content from bleeding into right column */
            .cv-container .grid > *,
            .cv-container [class*="col-span"] {
                min-width: 0;
            }
            .cv-container .grid .space-y-6 > section {
                overflow: hidden;
            }
            /* Ensure lists display with bullets/numbers (Tailwind preflight resets list-style) */
            .cv-container .markdown-content ul { list-style-type: disc; padding-left: 1.25em; }
            .cv-container .markdown-content ol { list-style-type: decimal; padding-left: 1.25em; }
            .cv-container .markdown-content li { display: list-item; }
            /* Keep pre/code from marked.js visible and within column */
            .cv-container .markdown-content pre,
            .cv-container .markdown-content code {
                max-width: 100%;
                overflow-wrap: break-word;
                white-space: pre-wrap;
                word-break: break-word;
            }
            .cv-container .markdown-content pre {
                margin: 0.5em 0;
                padding: 0.5em;
                background: #f3f4f6;
                border-radius: 0;
                font-size: inherit;
            }
            .cv-container .markdown-content code {
                padding: 0.125em 0.25em;
                background: #f3f4f6;
                border-radius: 0;
                font-size: 0.875em;
            }
            /* Interests & Activities: same as body text, align with title */
            #cv-interests-section .markdown-content pre,
            #cv-interests-section .markdown-content code,
            #cv-interests-section .markdown-content p,
            #cv-interests-section .markdown-content blockquote {
                font-family: inherit;
                background: transparent;
                padding: 0;
                margin: 0 0 0.5em 0;
                margin-left: 0;
                padding-left: 0;
                border-radius: 0;
                font-size: inherit;
                font-weight: inherit;
                line-height: inherit;
                color: inherit;
                text-align: left;
                text-indent: 0;
                white-space: normal;
            }
            #cv-interests-section .markdown-content blockquote {
                border-left: none;
            }
            #cv-interests-section .markdown-content > *:first-child {
                margin-top: 0;
            }
            #cv-interests-section .markdown-content ul,
            #cv-interests-section .markdown-content ol {
                margin-left: 0;
                padding-left: 1.25em;
            }
            #cv-interests-section .markdown-content {
                margin-left: 0;
                padding-left: 0;
            }
        </style>
    </head>
    <body class="bg-gray-100">
        <?php partial('header'); ?>
        <?php if ($isCvOwner): ?>
            <?php partial('content-editor/cv-nav-bar', [
                'cvVariants' => $navCvVariants,
                'masterVariantId' => $navMasterVariantId,
                'isCvPage' => true,
                'variantId' => $variantId ?? null,
            ]); ?>
        <?php endif; ?>
        <main id="main-content" role="main">
            <div class="w-full px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white cv-container max-w-6xl mx-auto shadow-md overflow-hidden">
                    <?php echo $renderedContent; ?>
                </div>
            </div>
    </main>
    <?php partial('footer'); ?>
    <script>
    // Enhance markdown rendering with marked.js for better support
    if (typeof marked !== 'undefined') {
        document.querySelectorAll('.markdown-content').forEach(function(el) {
            const originalHtml = el.innerHTML;
            // Server-side renderMarkdown() already outputs safe HTML (<br>, <strong>, etc.).
            // Re-parsing that HTML with marked can wrap lines into <p> blocks and inflate spacing.
            const alreadyRenderedHtml = /<\s*(br|strong|em|h1|h2|h3|a|ul|ol|li|p)\b/i.test(originalHtml);
            if (alreadyRenderedHtml) {
                return;
            }
            try {
                const rendered = marked.parse(originalHtml, { breaks: true, gfm: true });
                el.innerHTML = rendered;
            } catch (e) {
                // Fallback to original if parsing fails
                console.warn('Markdown parsing failed, using original:', e);
            }
        });
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-cv-link-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = this.getAttribute('data-cv-url');
                if (!url) return;
                var label = this.querySelector('.copy-cv-link-label');
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        if (label) label.textContent = 'Copied!';
                        setTimeout(function () {
                            if (label) label.textContent = 'Share CV';
                        }, 2000);
                    }).catch(function () { /* fallback below */ });
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = url;
                    ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        if (label) label.textContent = 'Copied!';
                        setTimeout(function () {
                            if (label) label.textContent = 'Share CV';
                        }, 2000);
                    } catch (e) {}
                    document.body.removeChild(ta);
                }
            });
        });
    });
    </script>
</body>
</html>
    <?php
    exit; // Stop here, don't render default template
}

// Which sections the user has toggled on/off for the online CV (Visibility section in the
// editor). Needed here (not just further down for the body sections) because the header
// block below also respects the 'profile' entry.
$sectionsOnline = getSectionsOnlineForCv($profile, $cvVariant);

// Phase 2 ("+" add-content) only applies to the master CV - several section types
// (education, skills, projects, memberships, interests, qualification-equivalence) have
// dedicated cv_variant_* tables for display but api/content-editor/save-section.php's
// create/update handlers for them don't accept a variant_id, so a "create" call while
// viewing a variant would silently land on the master profile instead. Simplest correct
// scope: only offer inline "add" while looking at the master CV.
$cvAllowInlineAdd = $isCvOwner && empty($cvVariant);
$cvSectionAddLabels = [
    'professional-summary' => 'Professional Summary',
    'work-experience' => 'Work Experience',
    'education' => 'Education',
    'skills' => 'Skill',
    'projects' => 'Project',
    'certifications' => 'Certification',
    'memberships' => 'Membership',
    'interests' => 'Interest',
    'qualification-equivalence' => 'Qualification Equivalence',
];

// Formats a stored date value (any parseable format) as YYYY-MM-DD for an <input type="date">.
function cvFormatDateForInput($date) {
    if (empty($date)) return '';
    $ts = strtotime($date);
    return $ts !== false ? date('Y-m-d', $ts) : '';
}

// Per-item edit/delete icon buttons for an existing list entry (work experience, education,
// etc.) - only rendered when $cvAllowInlineAdd is true (master CV owner, Edit Mode). $editData
// is embedded as JSON so the edit modal can be pre-filled without a extra round trip.
function renderCvItemControls($cvSId, $itemId, $editData) {
    $dataJson = htmlspecialchars(json_encode($editData), ENT_QUOTES, 'UTF-8');
    ob_start();
    ?>
    <span class="cv-edit-item-controls no-print">
        <?php echo renderCvDragHandle(); ?>
        <button type="button" class="cv-item-edit-btn" data-section-key="<?php echo e($cvSId); ?>" data-item-id="<?php echo e($itemId); ?>" data-item="<?php echo $dataJson; ?>" title="Edit" aria-label="Edit">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>
        <button type="button" class="cv-item-delete-btn" data-section-key="<?php echo e($cvSId); ?>" data-item-id="<?php echo e($itemId); ?>" title="Delete" aria-label="Delete">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </span>
    <?php
    return ob_get_clean();
}

// "+ Add another X" link appended after an already-populated list section.
function renderCvAddMoreButton($cvSId, $label) {
    ob_start();
    ?>
    <button type="button" class="cv-section-add-more-btn no-print" data-section-key="<?php echo e($cvSId); ?>">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Add another <?php echo e($label); ?></span>
    </button>
    <?php
    return ob_get_clean();
}

// Edit-only control for single-record sections (professional summary has no per-item id -
// saving is always an upsert, so there's nothing to individually delete here).
function renderCvEditOnlyControl($cvSId, $editData) {
    $dataJson = htmlspecialchars(json_encode($editData), ENT_QUOTES, 'UTF-8');
    ob_start();
    ?>
    <button type="button" class="cv-item-edit-btn cv-section-edit-only-btn no-print" data-section-key="<?php echo e($cvSId); ?>" data-item="<?php echo $dataJson; ?>" title="Edit" aria-label="Edit">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        <span>Edit</span>
    </button>
    <?php
    return ob_get_clean();
}

// Small grip icon used as the drag handle for reordering sections within their column.
// 'profile' (the header) never gets one - it stays fixed at the top, matching the same
// constraint already enforced by the content-editor sidebar's own reorder feature.
function renderCvDragHandle($compact = false) {
    $class = $compact ? 'cv-section-drag-handle cv-drag-handle-compact' : 'cv-section-drag-handle';
    $iconClass = $compact ? 'w-3 h-3' : 'w-4 h-4';
    return '<span class="' . $class . ' no-print" title="Drag to reorder" aria-hidden="true">'
        . '<svg class="' . $iconClass . '" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>'
        . '</span>';
}

// Owner-only "Edit Mode": wraps a section's rendered HTML with a visibility toggle badge
// and (when hidden) a dimmed/dashed presentation instead of omitting it entirely. Visitors
// never receive hidden markup at all - $isCvOwner gates that below, this is not just a
// display:none CSS trick for non-owners. When a toggleable section has no content at all,
// owners viewing the master CV get a dashed "+" placeholder instead (see $cvAllowInlineAdd).
function renderCvSectionWrapper($cvSId, $sectionHtml, $isCvOwner, $sectionsOnline, $allowAdd = false, $addLabel = null) {
    $hasContent = trim((string) $sectionHtml) !== '';
    $toggleable = array_key_exists($cvSId, $sectionsOnline);
    if (!$hasContent) {
        if ($allowAdd && $toggleable && $addLabel) {
            ob_start();
            ?>
            <div class="cv-edit-section-empty cv-section-draggable" data-cv-section-key="<?php echo e($cvSId); ?>">
                <div class="cv-section-drag-row no-print"><?php echo renderCvDragHandle(); ?></div>
                <button type="button" class="cv-section-add-btn" data-section-key="<?php echo e($cvSId); ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Add <?php echo e($addLabel); ?></span>
                </button>
            </div>
            <?php
            return ob_get_clean();
        }
        return '';
    }
    $visible = !$toggleable || !empty($sectionsOnline[$cvSId]);

    if (!$toggleable) {
        // Not covered by the online-visibility feature (e.g. a custom section) - always shown,
        // but still gets a drag handle for the owner so it can be repositioned in Edit Mode.
        if (!$isCvOwner) {
            return $sectionHtml;
        }
        ob_start();
        ?>
        <div class="cv-edit-section cv-section-draggable" data-cv-section-key="<?php echo e($cvSId); ?>">
            <div class="cv-section-drag-row no-print"><?php echo renderCvDragHandle(); ?></div>
            <?php echo $sectionHtml; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    if (!$visible && !$isCvOwner) {
        return '';
    }
    if (!$isCvOwner) {
        return $sectionHtml;
    }

    ob_start();
    ?>
    <div class="cv-edit-section cv-section-draggable<?php echo $visible ? '' : ' cv-edit-section-hidden'; ?>" data-cv-section-key="<?php echo e($cvSId); ?>">
        <div class="cv-section-visibility-row no-print">
            <?php echo renderCvDragHandle(); ?>
            <button type="button"
                    class="cv-section-visibility-toggle"
                    data-section-key="<?php echo e($cvSId); ?>"
                    data-visible="<?php echo $visible ? '1' : '0'; ?>"
                    aria-pressed="<?php echo $visible ? 'true' : 'false'; ?>"
                    title="<?php echo $visible ? 'Hide this section from your CV' : 'Show this section on your CV'; ?>">
                <svg class="cv-eye-icon cv-eye-open w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg class="cv-eye-icon cv-eye-closed w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                </svg>
                <span class="cv-section-visibility-label"><?php echo $visible ? 'Visible' : 'Hidden'; ?></span>
            </button>
        </div>
        <?php echo $sectionHtml; ?>
    </div>
    <?php
    return ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($profile['full_name'] ?? 'CV'); ?> - CV</title>
    <meta name="description" content="<?php echo e($cvMetaDescription); ?>">
    <link rel="stylesheet" href="/static/css/tailwind.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
            .cv-container { box-shadow: none; }
        }
        /* CV toolbar button styles – harmonious secondary/primary hierarchy */
        .cv-toolbar-btn-secondary { border: 1px solid #d1d5db; background: #fff; color: #374151; }
        .cv-toolbar-btn-secondary:hover { background: #f9fafb; }
        .cv-toolbar-btn-primary { background: #2563eb; color: #fff; border: 1px solid #2563eb; }
        .cv-toolbar-btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; }
        .icon {
            display: inline-block;
            width: 1em;
            height: 1em;
            vertical-align: middle;
            margin-right: 0.25em;
        }
        /* Owner-only "Edit Mode": every section becomes its own demarcated card (border +
           tinted background against the white CV page) so the different parts read as
           distinct, separable pieces while editing - not just a "how does visibility work"
           affordance. Hidden sections stay out of the DOM entirely for visitors; for the
           owner they're always rendered - normally display:none (matches what a visitor
           sees), revealed dimmed/dashed (layered on top of the same card look) only while
           Edit Mode is active. */
        body.cv-edit-mode-active .cv-edit-section {
            padding: 0.85rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            background: #f9fafb;
        }
        .cv-edit-section-hidden { display: none; }
        body.cv-edit-mode-active .cv-edit-section-hidden {
            display: block;
            opacity: 0.55;
            border: 2px dashed #9ca3af;
            border-radius: 0;
            padding: 1rem;
            background: rgba(156, 163, 175, 0.06);
        }
        .cv-section-visibility-row { display: none; margin-bottom: 0.5rem; }
        body.cv-edit-mode-active .cv-section-visibility-row { display: flex; justify-content: space-between; align-items: center; }
        .cv-section-drag-row { display: none; margin-bottom: 0.35rem; }
        body.cv-edit-mode-active .cv-section-drag-row { display: flex; }
        .cv-section-drag-handle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0;
            background: #fff;
            color: #9ca3af;
            cursor: grab;
            flex-shrink: 0;
        }
        body.cv-edit-mode-active .cv-section-drag-handle { display: flex; }
        .cv-section-drag-handle:hover { color: #4f46e5; border-color: #a5b4fc; }
        .cv-drag-handle-compact { width: 1.15rem; height: 1.15rem; border: none; background: transparent; }
        /* Which edge lights up shows whether releasing here drops above or below the
           hovered item - without this a drop always meant "before", so moving something
           down past just one neighbour meant aiming at the item after it instead. */
        .cv-item-draggable.cv-dragging { opacity: 0.4; }
        .cv-item-draggable.cv-drag-over-before { box-shadow: 0 -3px 0 0 #6366f1; }
        .cv-item-draggable.cv-drag-over-after { box-shadow: 0 3px 0 0 #6366f1; }
        .cv-section-draggable.cv-dragging { opacity: 0.4; }
        .cv-section-draggable.cv-drag-over-before { box-shadow: 0 -3px 0 0 #6366f1; }
        .cv-section-draggable.cv-drag-over-after { box-shadow: 0 3px 0 0 #6366f1; }
        .cv-section-visibility-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 0;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            cursor: pointer;
        }
        .cv-section-visibility-toggle:hover { background: #f9fafb; }
        .cv-section-visibility-toggle[data-visible="0"] { color: #6b7280; background: #f9fafb; }
        .cv-section-visibility-toggle .cv-eye-closed { display: none; }
        .cv-section-visibility-toggle[data-visible="0"] .cv-eye-open { display: none; }
        .cv-section-visibility-toggle[data-visible="0"] .cv-eye-closed { display: inline; }
        /* Owner-only "+" placeholder for an empty section, shown only in Edit Mode. */
        .cv-edit-section-empty { display: none; }
        body.cv-edit-mode-active .cv-edit-section-empty { display: block; }
        .cv-section-add-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.1rem;
            border: 2px dashed #c7d2fe;
            border-radius: 0;
            color: #4f46e5;
            font-size: 0.875rem;
            font-weight: 600;
            background: rgba(79, 70, 229, 0.03);
            cursor: pointer;
        }
        .cv-section-add-btn:hover { background: rgba(79, 70, 229, 0.08); border-color: #a5b4fc; }
        /* Per-item edit/delete controls and "add another" - all owner+Edit Mode only. */
        .cv-edit-item { position: relative; }
        .cv-edit-item-controls {
            display: none;
            position: absolute;
            top: 0.35rem;
            right: 0.35rem;
            gap: 0.25rem;
            z-index: 2;
        }
        body.cv-edit-mode-active .cv-edit-item-controls { display: flex; }
        .cv-edit-item-controls-inline { position: static; margin-left: 0.15rem; }
        .cv-item-edit-btn, .cv-item-delete-btn {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            border-radius: 0;
            background: #fff;
            color: #374151;
            cursor: pointer;
            flex-shrink: 0;
        }
        .cv-item-edit-btn:hover { background: #eff6ff; border-color: #93c5fd; color: #2563eb; }
        .cv-item-delete-btn:hover { background: #fef2f2; border-color: #fca5a5; color: #dc2626; }
        .cv-section-edit-only-btn {
            display: none;
            width: auto;
            height: auto;
            padding: 0.2rem 0.6rem;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 0;
        }
        body.cv-edit-mode-active .cv-section-edit-only-btn { display: inline-flex; }
        .cv-section-add-more-btn {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            margin-top: 0.5rem;
            padding: 0.6rem;
            border: 1.5px dashed #c7d2fe;
            border-radius: 0;
            color: #4f46e5;
            font-size: 0.8rem;
            font-weight: 600;
            background: transparent;
            cursor: pointer;
        }
        body.cv-edit-mode-active .cv-section-add-more-btn { display: flex; }
        .cv-section-add-more-btn:hover { background: rgba(79, 70, 229, 0.05); border-color: #a5b4fc; }
        /* Ensure lists display with bullets/numbers (Tailwind preflight resets list-style) */
        .cv-container .markdown-content ul { list-style-type: disc; padding-left: 1.25em; }
        .cv-container .markdown-content ol { list-style-type: decimal; padding-left: 1.25em; }
        .cv-container .markdown-content li { display: list-item; }
        /* Keep pre/code from marked.js visible and within column (no overflow) */
        .cv-container .markdown-content pre,
        .cv-container .markdown-content code {
            max-width: 100%;
            overflow-wrap: break-word;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .cv-container .markdown-content pre {
            margin: 0.5em 0;
            padding: 0.5em;
            background: #f3f4f6;
            border-radius: 0;
            font-size: inherit;
        }
        .cv-container .markdown-content code {
            padding: 0.125em 0.25em;
            background: #f3f4f6;
            border-radius: 0;
            font-size: 0.875em;
        }
        /* Interests & Activities: render like normal body text, not code; align with title */
        #cv-interests-section .markdown-content pre,
        #cv-interests-section .markdown-content code,
        #cv-interests-section .markdown-content p,
        #cv-interests-section .markdown-content blockquote {
            font-family: inherit;
            background: transparent;
            padding: 0;
            margin: 0 0 0.5em 0;
            margin-left: 0;
            padding-left: 0;
            border-radius: 0;
            font-size: inherit;
            font-weight: inherit;
            line-height: inherit;
            color: inherit;
            text-align: left;
            text-indent: 0;
            white-space: normal;
        }
        #cv-interests-section .markdown-content blockquote {
            border-left: none;
        }
        #cv-interests-section .markdown-content > *:first-child {
            margin-top: 0;
        }
        #cv-interests-section .markdown-content ul,
        #cv-interests-section .markdown-content ol {
            margin-left: 0;
            padding-left: 1.25em;
        }
        #cv-interests-section .markdown-content {
            margin-left: 0;
            padding-left: 0;
        }
    </style>
    </head>
<body class="bg-gray-100">
    <?php partial('header'); ?>
    <?php if ($isCvOwner): ?>
        <?php partial('content-editor/cv-nav-bar', [
            'cvVariants' => $navCvVariants,
            'masterVariantId' => $navMasterVariantId,
            'isCvPage' => true,
            'variantId' => $variantId ?? null,
        ]); ?>
        <div class="w-full px-4 sm:px-6 lg:px-8 pt-6 no-print">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-wrap items-center justify-between gap-3 border border-gray-200 bg-white px-4 py-2.5 shadow-sm">
                    <p id="cv-edit-mode-status" class="text-sm text-gray-600">You're viewing your CV as visitors see it.</p>
                    <button type="button" id="cv-edit-mode-toggle" class="inline-flex items-center gap-2 border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-pressed="false">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>Edit Mode</span>
                    </button>
                </div>
            </div>
        </div>
        <script>
        window.cvEditModeData = {
            csrfToken: <?php echo json_encode(csrfToken()); ?>,
            csrfTokenName: <?php echo json_encode(CSRF_TOKEN_NAME); ?>,
            variantId: <?php echo json_encode($variantId ?: null); ?>,
            isVariant: <?php echo json_encode(!empty($cvVariant)); ?>,
            saveOnlineUrl: <?php echo json_encode('/api/save-profile-sections-online.php'); ?>,
            saveVariantUrl: <?php echo json_encode('/api/variant-pdf-preferences.php'); ?>
        };
        </script>
    <?php endif; ?>
    <main id="main-content" role="main">
    <!-- CV Container - Full Width with Padding -->
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white cv-container max-w-6xl mx-auto shadow-md overflow-hidden">
            <?php
            ob_start();
            ?>
            <!-- CV Header with Gradient -->
            <div style="background: linear-gradient(to right, <?php echo e($profile['cv_header_from_color'] ?? '#4338ca'); ?>, <?php echo e($profile['cv_header_to_color'] ?? '#7e22ce'); ?>);" class="text-white p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-3xl sm:text-4xl font-bold leading-tight break-words">
                            <?php echo e($profile['full_name'] ?? 'Your Name'); ?>
                        </h1>
                        <?php if (!empty($profile['location'])): ?>
                            <p class="text-white/90 mt-3 flex items-center text-sm sm:text-base gap-2">
                                <svg class="icon mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <?php echo e($profile['location']); ?>
                            </p>
                        <?php endif; ?>
                        <div class="flex flex-wrap gap-3 mt-4 text-xs sm:text-sm">
                            <?php if (!empty($profile['email'])): ?>
                                <a href="mailto:<?php echo e($profile['email']); ?>" class="text-white/90 hover:text-white flex items-center">
                                    <svg class="icon mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                    <?php echo e($profile['email']); ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($profile['phone'])): ?>
                                <span class="text-white/90 flex items-center">
                                    <svg class="icon mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                    <?php echo e($profile['phone']); ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($profile['linkedin_url'])): ?>
                                <a href="<?php echo e($profile['linkedin_url']); ?>" target="_blank" class="text-white/90 hover:text-white flex items-center">
                                    <svg class="icon mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                    </svg>
                                    LinkedIn
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($profile['bio'])): ?>
                            <div class="mt-4 pt-4 border-t border-white/20 text-sm sm:text-base">
                                <div class="text-white/90 leading-relaxed markdown-content"><?php echo renderMarkdown($profile['bio']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($profile['photo_url']) && (!isset($profile['show_photo']) || $profile['show_photo'] == 1)): ?>
                        <?php
                        // Get responsive image attributes for profile photo (context: 'cv' for CV page)
                        $photoResponsiveData = isset($profile['photo_responsive']) ? $profile['photo_responsive'] : null;
                        $photoImgAttrs = getResponsiveImageAttributes($photoResponsiveData, $profile['photo_url'], 'cv');
                        ?>
                        <img src="<?php echo e($photoImgAttrs['src']); ?>"
                             <?php if (!empty($photoImgAttrs['srcset'])): ?>
                                 srcset="<?php echo e($photoImgAttrs['srcset']); ?>"
                                 sizes="<?php echo e($photoImgAttrs['sizes']); ?>"
                             <?php endif; ?>
                             alt="<?php echo e($profile['full_name'] ?? 'Profile'); ?>"
                             class="w-32 h-32 sm:w-40 sm:h-40 lg:w-48 lg:h-48 object-cover border-4 border-white/20 mx-auto lg:mx-0"
                             loading="lazy"
                             width="192"
                             height="192">
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $cvHeaderHtml = ob_get_clean();
            echo renderCvSectionWrapper('profile', $cvHeaderHtml, $isCvOwner, $sectionsOnline);
            ?>

            <!-- CV Content -->
            <div class="p-6 sm:p-8">
                <?php
                // Determine user's preferred section order for this CV page
                $cvPageOrder = null;
                if (!empty($profile['section_order'])) {
                    $decoded = json_decode($profile['section_order'], true);
                    if (is_array($decoded)) $cvPageOrder = $decoded;
                }
                $cvLeftDefault  = ['certifications', 'education', 'skills', 'interests'];
                $cvRightDefault = ['professional-summary', 'work-experience', 'projects', 'qualification-equivalence', 'memberships'];

                // Load custom sections for this profile
                $cvCustomSections = db()->fetchAll(
                    "SELECT * FROM custom_sections WHERE profile_id = ? ORDER BY sort_order ASC, created_at ASC",
                    [$profileUserId]
                );
                // Load items for each custom section
                foreach ($cvCustomSections as &$cs) {
                    $cs['items'] = db()->fetchAll(
                        "SELECT * FROM custom_section_items WHERE custom_section_id = ? ORDER BY sort_order ASC, created_at ASC",
                        [$cs['id']]
                    );
                }
                unset($cs);
                // Add custom section IDs to right column default (after standard sections)
                foreach ($cvCustomSections as $cs) {
                    $cvRightDefault[] = 'custom-' . $cs['id'];
                }

                // Which column (left/right) a section sits in on cv.php - separate from
                // section_order (which only controls position within whichever column a
                // section is already in) and from the content-editor sidebar's own reorder
                // tool, which keeps its unrelated, unchanged Main/Sidebar split.
                $cvColumnOverrides = [];
                if (!empty($profile['cv_page_columns'])) {
                    $decodedCols = json_decode($profile['cv_page_columns'], true);
                    if (is_array($decodedCols)) $cvColumnOverrides = $decodedCols;
                }
                foreach ($cvColumnOverrides as $cvOverrideId => $cvOverrideCol) {
                    if ($cvOverrideCol === 'left' && in_array($cvOverrideId, $cvRightDefault, true)) {
                        $cvRightDefault = array_values(array_diff($cvRightDefault, [$cvOverrideId]));
                        $cvLeftDefault[] = $cvOverrideId;
                    } elseif ($cvOverrideCol === 'right' && in_array($cvOverrideId, $cvLeftDefault, true)) {
                        $cvLeftDefault = array_values(array_diff($cvLeftDefault, [$cvOverrideId]));
                        $cvRightDefault[] = $cvOverrideId;
                    }
                }

                if ($cvPageOrder) {
                    $cvPagePos = array_flip($cvPageOrder);
                    $cvSortFn  = function($a, $b) use ($cvPagePos) {
                        return ($cvPagePos[$a] ?? 999) - ($cvPagePos[$b] ?? 999);
                    };
                    usort($cvLeftDefault, $cvSortFn);
                    usort($cvRightDefault, $cvSortFn);
                }

                // $sectionsOnline was already computed above (also used by the header block).
                // getSectionsOnlineForCv() returns keys in the same naming as
                // $cvLeftDefault/$cvRightDefault below (e.g. 'professional-summary',
                // 'work-experience'), so no key mapping is needed here.

                // Capture each section's rendered HTML so we can output in the right order
                $cvSectionBlocks = [];

                // --- certifications ---
                ob_start(); ?>
                <?php if (!empty($cvData['certifications'])): ?>
                    <section<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="certifications"' : ''; ?>>
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                            Certifications
                        </h2>
                        <?php foreach ($cvData['certifications'] as $cert): ?>
                            <div class="mb-3 cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($cert['id']) . '"' : ''; ?>>
                                <?php if ($cvAllowInlineAdd): ?>
                                    <?php echo renderCvItemControls('certifications', $cert['id'], [
                                        'id' => $cert['id'],
                                        'name' => $cert['name'],
                                        'issuer' => $cert['issuer'],
                                        'date_obtained' => cvFormatDateForInput($cert['date_obtained'] ?? null),
                                        'expiry_date' => cvFormatDateForInput($cert['expiry_date'] ?? null),
                                    ]); ?>
                                <?php endif; ?>
                                <h3 class="font-semibold text-gray-900 text-sm"><?php echo e($cert['name']); ?></h3>
                                <p class="text-gray-700 text-sm"><?php echo e($cert['issuer']); ?></p>
                                <p class="text-gray-600 text-xs mt-1">
                                    <?php echo formatCvDate($cert['date_obtained']); ?>
                                    <?php if (!empty($cert['expiry_date'])): ?>
                                        <br>Expires: <?php echo formatCvDate($cert['expiry_date']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('certifications', 'Certification'); ?><?php endif; ?>
                    </section>
                <?php endif; ?>
                <?php $cvSectionBlocks['certifications'] = ob_get_clean(); ?>

                <!-- Education -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['education'])): ?>
                            <section<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="education"' : ''; ?>>
                                <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                                    Education
                                </h2>
                                <?php foreach ($cvData['education'] as $edu): ?>
                                    <div class="mb-4 cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($edu['id']) . '"' : ''; ?>>
                                        <?php if ($cvAllowInlineAdd): ?>
                                            <?php echo renderCvItemControls('education', $edu['id'], [
                                                'id' => $edu['id'],
                                                'degree' => $edu['degree'],
                                                'institution' => $edu['institution'],
                                                'field_of_study' => $edu['field_of_study'] ?? '',
                                                'start_date' => cvFormatDateForInput($edu['start_date'] ?? null),
                                                'end_date' => cvFormatDateForInput($edu['end_date'] ?? null),
                                            ]); ?>
                                        <?php endif; ?>
                                        <p class="font-semibold text-gray-900 text-sm"><span class="text-gray-500 font-normal">Qual:</span> <?php echo e($edu['degree']); ?></p>
                                        <p class="text-gray-700 text-sm"><span class="text-gray-500 font-normal">Institution:</span> <?php echo e($edu['institution']); ?></p>
                                        <?php if (!empty($edu['field_of_study'])): ?>
                                            <p class="text-gray-600 text-sm"><span class="text-gray-500 font-normal">Subject:</span> <?php echo e($edu['field_of_study']); ?></p>
                                        <?php endif; ?>
                                        <?php if (empty($edu['hide_date']) && (!empty($edu['start_date']) || !empty($edu['end_date']))): ?>
                                        <p class="text-gray-600 text-xs mt-1">
                                            <?php if (!empty($edu['start_date'])): ?>
                                                <?php echo formatCvDate($edu['start_date']); ?>
                                                <?php if (!empty($edu['end_date'])): ?>
                                                    - <?php echo formatCvDate($edu['end_date']); ?>
                                                <?php else: ?>
                                                    - Present
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo formatCvDate($edu['end_date']); ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('education', 'Education'); ?><?php endif; ?>
                            </section>
                        <?php endif; ?>
                <?php $cvSectionBlocks['education'] = ob_get_clean(); ?>

                <!-- Skills -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['skills'])): ?>
                    <section>
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                            Skills
                        </h2>
                        <?php
                        $skillsByCategory = [];
                        foreach ($cvData['skills'] as $skill) {
                            $category = $skill['category'] ?? 'Other';
                            if (!isset($skillsByCategory[$category])) {
                                $skillsByCategory[$category] = [];
                            }
                            $skillsByCategory[$category][] = $skill;
                        }
                        ?>
                        <?php foreach ($skillsByCategory as $category => $skills): ?>
                            <div class="mb-3">
                                <h3 class="font-semibold text-gray-800 text-sm mb-1"><?php echo e($category); ?>:</h3>
                                <div class="flex flex-wrap gap-1.5"<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="skills"' : ''; ?>>
                                    <?php foreach ($skills as $skill): ?>
                                        <span class="bg-gray-100 px-2 py-0.5 text-gray-700 text-xs inline-flex items-center gap-1<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($skill['id']) . '"' : ''; ?>>
                                            <?php if ($cvAllowInlineAdd): ?><?php echo renderCvDragHandle(true); ?><?php endif; ?>
                                            <span>
                                                <?php echo e($skill['name']); ?>
                                                <?php if (!empty($skill['level'])): ?>
                                                    <span class="text-gray-500">(<?php echo e($skill['level']); ?>)</span>
                                                <?php endif; ?>
                                            </span>
                                            <?php if ($cvAllowInlineAdd): ?>
                                                <span class="cv-edit-item-controls cv-edit-item-controls-inline no-print">
                                                    <button type="button" class="cv-item-edit-btn" data-section-key="skills" data-item-id="<?php echo e($skill['id']); ?>" data-item="<?php echo htmlspecialchars(json_encode([
                                                        'id' => $skill['id'],
                                                        'name' => $skill['name'],
                                                        'category' => $skill['category'] ?? '',
                                                        'level' => $skill['level'] ?? '',
                                                    ]), ENT_QUOTES, 'UTF-8'); ?>" title="Edit" aria-label="Edit">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </button>
                                                    <button type="button" class="cv-item-delete-btn" data-section-key="skills" data-item-id="<?php echo e($skill['id']); ?>" title="Delete" aria-label="Delete">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('skills', 'Skill'); ?><?php endif; ?>
                    </section>
                <?php endif; ?>
                <?php $cvSectionBlocks['skills'] = ob_get_clean(); ?>

                <!-- Interests & Activities -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['interests'])): ?>
                    <section id="cv-interests-section">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                            Interests & Activities
                        </h2>
                        <div class="space-y-3"<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="interests"' : ''; ?>>
                            <?php foreach ($cvData['interests'] as $interest): ?>
                                <div class="min-w-0 border border-gray-200 bg-white/70 p-4 shadow-sm cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($interest['id']) . '"' : ''; ?>>
                                    <?php if ($cvAllowInlineAdd): ?>
                                        <?php echo renderCvItemControls('interests', $interest['id'], [
                                            'id' => $interest['id'],
                                            'name' => $interest['name'],
                                            'description' => $interest['description'] ?? '',
                                        ]); ?>
                                    <?php endif; ?>
                                    <h3 class="text-sm font-semibold text-gray-800">
                                        <?php echo e($interest['name']); ?>
                                    </h3>
                                    <?php if (!empty($interest['description'])): ?>
                                        <div class="mt-2 text-sm text-gray-600 leading-relaxed break-words markdown-content">
                                            <?php echo renderMarkdown(trim($interest['description'] ?? '')); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('interests', 'Interest'); ?><?php endif; ?>
                    </section>
                <?php endif; ?>
                <?php $cvSectionBlocks['interests'] = ob_get_clean(); ?>

                <!-- Right column section captures start here -->
                <!-- Professional Summary -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['professional_summary'])): ?>
                    <section class="cv-edit-item">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2 flex items-center justify-between gap-2">
                            <span>Professional Summary</span>
                            <?php if ($cvAllowInlineAdd): ?>
                                <?php echo renderCvEditOnlyControl('professional-summary', [
                                    'description' => $cvData['professional_summary']['description'] ?? '',
                                ]); ?>
                            <?php endif; ?>
                        </h2>
                        <?php if (!empty($cvData['professional_summary']['description'])): ?>
                            <div class="text-gray-700 mb-3 text-sm leading-relaxed markdown-content"><?php echo renderMarkdown($cvData['professional_summary']['description'] ?? ''); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($cvData['professional_summary']['strengths'])): ?>
                            <h3 class="font-semibold text-gray-800 mb-2 text-sm">Key Strengths:</h3>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                <?php foreach ($cvData['professional_summary']['strengths'] as $strength): ?>
                                    <li class="text-gray-700"><?php echo e(html_entity_decode($strength['strength'], ENT_QUOTES, 'UTF-8')); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
                <?php $cvSectionBlocks['professional-summary'] = ob_get_clean(); ?>

                <!-- Work Experience -->
                <?php ob_start(); ?>
                        <?php if (!empty($cvData['work_experience'])): ?>
                            <section<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="work-experience"' : ''; ?>>
                                <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                                    Work Experience
                                </h2>
                                <?php foreach ($cvData['work_experience'] as $work): ?>
                                    <div class="mb-6 cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($work['id']) . '"' : ''; ?>>
                                        <?php if ($cvAllowInlineAdd): ?>
                                            <?php echo renderCvItemControls('work-experience', $work['id'], [
                                                'id' => $work['id'],
                                                'position' => html_entity_decode($work['position'], ENT_QUOTES, 'UTF-8'),
                                                'company_name' => html_entity_decode($work['company_name'], ENT_QUOTES, 'UTF-8'),
                                                'start_date' => cvFormatDateForInput($work['start_date'] ?? null),
                                                'end_date' => cvFormatDateForInput($work['end_date'] ?? null),
                                                'description' => $work['description'] ?? '',
                                            ]); ?>
                                        <?php endif; ?>
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between mb-2">
                                            <div class="min-w-0">
                                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e(html_entity_decode($work['position'], ENT_QUOTES, 'UTF-8')); ?></h3>
                                                <p class="text-base text-gray-700"><?php echo e(html_entity_decode($work['company_name'], ENT_QUOTES, 'UTF-8')); ?></p>
                                            </div>
                                            <?php if (!$work['hide_date']): ?>
                                                <div class="text-gray-600 text-sm sm:text-right whitespace-nowrap flex-shrink-0">
                                                    <?php echo formatCvDate($work['start_date']); ?>
                                                    <?php if (!empty($work['end_date'])): ?>
                                                        - <?php echo formatCvDate($work['end_date']); ?>
                                                    <?php else: ?>
                                                        - Present
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($work['description'])): ?>
                                            <?php
                                            $workDescriptionForDisplay = trim((string)($work['description'] ?? ''));
                                            $renderedWorkDescription = renderMarkdown($workDescriptionForDisplay);
                                            ?>
                                            <div class="text-gray-700 mb-3 text-sm leading-relaxed markdown-content"><?php echo $renderedWorkDescription; ?></div>
                                        <?php endif; ?>

                                        <!-- Responsibilities -->
                                        <?php if (!empty($work['responsibility_categories'])): ?>
                                            <?php $toggleId = 'responsibilities-' . $work['id']; ?>
                                            <button
                                                type="button"
                                                class="inline-flex w-full sm:w-auto items-center justify-center bg-indigo-100 px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                                                data-toggle="collapse"
                                                data-target="<?php echo e($toggleId); ?>"
                                                data-view-label="View Responsibilities"
                                                data-hide-label="Hide Responsibilities"
                                                aria-expanded="false"
                                            >
                                                <svg class="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path class="icon-plus" fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                    <path class="icon-minus hidden" fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="toggle-label">View Responsibilities</span>
                                            </button>

                                            <div id="<?php echo e($toggleId); ?>" class="mt-3 hidden space-y-4 border-l-2 border-indigo-100 pl-4 text-sm text-gray-700 print:block">
                                                <?php foreach ($work['responsibility_categories'] as $category): ?>
                                                    <?php if (!empty($category['items'])): ?>
                                                        <div>
                                                            <?php if (!empty($category['name'])): ?>
                                                                <h4 class="font-semibold text-gray-800 mb-1 text-sm"><?php echo e($category['name']); ?></h4>
                                                            <?php endif; ?>
                                                            <ul class="list-disc space-y-1 pl-5">
                                                                <?php foreach ($category['items'] as $item): ?>
                                                                    <li><?php echo e($item['content']); ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($work !== end($cvData['work_experience'])): ?>
                                        <hr class="my-3 border-gray-200">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('work-experience', 'Work Experience'); ?><?php endif; ?>
                            </section>
                        <?php endif; ?>
                <?php $cvSectionBlocks['work-experience'] = ob_get_clean(); ?>

                <!-- Projects -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['projects'])): ?>
                    <section<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="projects"' : ''; ?>>
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                            Projects
                        </h2>
                        <?php foreach ($cvData['projects'] as $project): ?>
                            <div class="mb-4 cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($project['id']) . '"' : ''; ?>>
                                <?php if ($cvAllowInlineAdd): ?>
                                    <?php echo renderCvItemControls('projects', $project['id'], [
                                        'id' => $project['id'],
                                        'title' => $project['title'],
                                        'url' => isset($project['url']) ? html_entity_decode($project['url'], ENT_QUOTES, 'UTF-8') : '',
                                        'start_date' => cvFormatDateForInput($project['start_date'] ?? null),
                                        'end_date' => cvFormatDateForInput($project['end_date'] ?? null),
                                        'description' => $project['description'] ?? '',
                                    ]); ?>
                                <?php endif; ?>
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between mb-1">
                                    <?php
                                    $projectUrl = !empty($project['url']) ? html_entity_decode($project['url'], ENT_QUOTES, 'UTF-8') : '';
                                    ?>
                                            <h3 class="text-lg font-semibold text-gray-900 flex items-center min-w-0">
                                                <?php if (!empty($projectUrl)): ?>
                                                    <a href="<?php echo e($projectUrl); ?>" target="_blank" class="inline-flex items-center text-blue-700 hover:text-blue-900">
                                                        <span><?php echo e($project['title']); ?></span>
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                        </svg>
                                                    </a>
                                                <?php else: ?>
                                                    <span><?php echo e($project['title']); ?></span>
                                                <?php endif; ?>
                                            </h3>
                                            <?php if (!empty($project['start_date'])): ?>
                                                <div class="text-gray-600 text-sm whitespace-nowrap flex-shrink-0 sm:text-right">
                                                    <?php echo formatCvDate($project['start_date']); ?>
                                                    <?php if (!empty($project['end_date'])): ?>
                                                        - <?php echo formatCvDate($project['end_date']); ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($project['description'])): ?>
                                            <div class="text-gray-700 text-sm leading-relaxed markdown-content"><?php echo renderMarkdown($project['description'] ?? ''); ?></div>
                                        <?php endif; ?>
                                        <?php
                                        $projectImagePath = isset($project['image_path']) ? html_entity_decode($project['image_path'], ENT_QUOTES, 'UTF-8') : null;
                                        $projectImageUrlRaw = isset($project['image_url']) ? html_entity_decode($project['image_url'], ENT_QUOTES, 'UTF-8') : '';
                                        $projectImageUrl = '';

                                        if (!empty($projectImageUrlRaw)) {
                                            $projectImageUrl = $projectImageUrlRaw;
                                        } elseif (!empty($projectImagePath)) {
                                            $projectImageUrl = '/storage/' . ltrim($projectImagePath, '/');
                                        }
                                        
                                        // Get responsive image attributes (context: 'cv' for CV page)
                                        $responsiveData = isset($project['image_responsive']) ? $project['image_responsive'] : null;
                                        $imgAttrs = getResponsiveImageAttributes($responsiveData, $projectImageUrl, 'cv');
                                        ?>
                                        <?php if (!empty($imgAttrs['src'])): ?>
                                            <div class="mt-3">
                                                <?php if (!empty($projectUrl)): ?>
                                                    <a href="<?php echo e($projectUrl); ?>" target="_blank" aria-label="View <?php echo e($project['title']); ?> project">
                                                        <img 
                                                            src="<?php echo e($imgAttrs['src']); ?>" 
                                                            <?php if (!empty($imgAttrs['srcset'])): ?>
                                                                srcset="<?php echo e($imgAttrs['srcset']); ?>"
                                                                sizes="<?php echo e($imgAttrs['sizes']); ?>"
                                                            <?php endif; ?>
                                                            alt="<?php echo e($project['title']); ?> - Project image"
                                                            class="w-full border border-gray-200"
                                                            loading="lazy"
                                                            width="800"
                                                            height="600"
                                                            decoding="async">
                                                    </a>
                                                <?php else: ?>
                                                    <img 
                                                        src="<?php echo e($imgAttrs['src']); ?>" 
                                                        <?php if (!empty($imgAttrs['srcset'])): ?>
                                                            srcset="<?php echo e($imgAttrs['srcset']); ?>"
                                                            sizes="<?php echo e($imgAttrs['sizes']); ?>"
                                                        <?php endif; ?>
                                                        alt="<?php echo e($project['title']); ?> - Project image"
                                                        class="w-full border border-gray-200"
                                                        loading="lazy"
                                                        width="800"
                                                        height="600"
                                                        decoding="async">
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('projects', 'Project'); ?><?php endif; ?>
                            </section>
                        <?php endif; ?>
                <?php $cvSectionBlocks['projects'] = ob_get_clean(); ?>

                <!-- Professional Qualification Equivalence -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['qualification_equivalence'])): ?>
                            <section<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="qualification-equivalence"' : ''; ?>>
                                <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                                    Professional Qualification Equivalence
                                </h2>
                                <?php foreach ($cvData['qualification_equivalence'] as $qual): ?>
                                    <div class="mb-4 cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($qual['id']) . '"' : ''; ?>>
                                        <?php if ($cvAllowInlineAdd): ?>
                                            <?php echo renderCvItemControls('qualification-equivalence', $qual['id'], [
                                                'id' => $qual['id'],
                                                'level' => $qual['level'],
                                                'description' => $qual['description'] ?? '',
                                            ]); ?>
                                        <?php endif; ?>
                                        <h3 class="font-semibold text-gray-900 text-sm mb-1"><?php echo e($qual['level']); ?></h3>
                                        <?php if (!empty($qual['description'])): ?>
                                            <div class="text-gray-700 text-sm leading-relaxed markdown-content"><?php echo renderMarkdown($qual['description'] ?? ''); ?></div>
                                        <?php endif; ?>
                                            <?php if (!empty($qual['evidence'])): ?>
                                                <?php $evidenceId = 'evidence-' . $qual['id']; ?>
                                                <button
                                                    type="button"
                                                    class="mt-2 inline-flex w-full sm:w-auto items-center justify-center bg-indigo-100 px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                                                    data-toggle="collapse"
                                                    data-target="<?php echo e($evidenceId); ?>"
                                                    data-view-label="View Supporting Evidence"
                                                    data-hide-label="Hide Supporting Evidence"
                                                    aria-expanded="false"
                                                >
                                                    <svg class="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path class="icon-plus" fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                        <path class="icon-minus hidden" fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="toggle-label">View Supporting Evidence</span>
                                                </button>
                                                <div id="<?php echo e($evidenceId); ?>" class="mt-2 hidden bg-gray-50 p-4 text-sm text-gray-700 print:block">
                                                    <ul class="list-disc space-y-1 pl-5">
                                                        <?php foreach ($qual['evidence'] as $evidence): ?>
                                                            <li><?php echo e($evidence['content']); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('qualification-equivalence', 'Qualification Equivalence'); ?><?php endif; ?>
                            </section>
                        <?php endif; ?>
                <?php $cvSectionBlocks['qualification-equivalence'] = ob_get_clean(); ?>

                <!-- Professional Memberships -->
                <?php ob_start(); ?>
                <?php if (!empty($cvData['memberships'])): ?>
                            <section<?php echo $cvAllowInlineAdd ? ' data-cv-items-list="memberships"' : ''; ?>>
                                <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                                    Professional Memberships
                                </h2>
                                <?php foreach ($cvData['memberships'] as $membership): ?>
                                    <div class="mb-3 cv-edit-item<?php echo $cvAllowInlineAdd ? ' cv-item-draggable' : ''; ?>"<?php echo $cvAllowInlineAdd ? ' data-cv-item-id="' . e($membership['id']) . '"' : ''; ?>>
                                        <?php if ($cvAllowInlineAdd): ?>
                                            <?php echo renderCvItemControls('memberships', $membership['id'], [
                                                'id' => $membership['id'],
                                                'organisation' => $membership['organisation'],
                                                'role' => $membership['role'] ?? '',
                                                'description' => $membership['description'] ?? '',
                                                'start_date' => cvFormatDateForInput($membership['start_date'] ?? null),
                                                'end_date' => cvFormatDateForInput($membership['end_date'] ?? null),
                                            ]); ?>
                                        <?php endif; ?>
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <h3 class="font-semibold text-gray-900 text-sm"><?php echo e($membership['organisation']); ?></h3>
                                                <?php if (!empty($membership['role'])): ?>
                                                    <p class="text-gray-700 text-sm"><?php echo e($membership['role']); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($membership['description'])): ?>
                                                    <div class="text-gray-700 text-sm leading-relaxed markdown-content mt-1"><?php echo renderMarkdown($membership['description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($membership['start_date']) || !empty($membership['end_date'])): ?>
                                            <div class="text-gray-600 text-sm sm:text-right whitespace-nowrap flex-shrink-0">
                                                <?php if (!empty($membership['start_date'])): ?>
                                                    <?php echo formatCvDate($membership['start_date']); ?>
                                                    <?php if (!empty($membership['end_date'])): ?>
                                                        - <?php echo formatCvDate($membership['end_date']); ?>
                                                    <?php else: ?>
                                                        - Present
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php echo formatCvDate($membership['end_date']); ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($cvAllowInlineAdd): ?><?php echo renderCvAddMoreButton('memberships', 'Membership'); ?><?php endif; ?>
                            </section>
                        <?php endif; ?>
                <?php $cvSectionBlocks['memberships'] = ob_get_clean(); ?>

                <!-- Custom Sections -->
                <?php foreach ($cvCustomSections as $cs): ?>
                    <?php ob_start(); ?>
                    <?php if (!empty($cs['items'])): ?>
                    <section>
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b-2 border-gray-300 pb-2">
                            <?php echo e($cs['title']); ?>
                        </h2>
                        <?php foreach ($cs['items'] as $item): ?>
                            <div class="mb-3">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm"><?php echo e($item['title']); ?></h3>
                                        <?php if (!empty($item['subtitle'])): ?>
                                            <p class="text-gray-700 text-sm"><?php echo e($item['subtitle']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($item['item_date'])): ?>
                                        <div class="text-gray-600 text-sm whitespace-nowrap flex-shrink-0"><?php echo e($item['item_date']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($item['url'])): ?>
                                    <p class="text-sm mt-1"><a href="<?php echo e(html_entity_decode($item['url'], ENT_QUOTES, 'UTF-8')); ?>" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 break-all"><?php echo e(html_entity_decode($item['url'], ENT_QUOTES, 'UTF-8')); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($item['description'])): ?>
                                    <div class="mt-1 text-sm text-gray-600 leading-relaxed markdown-content"><?php echo renderMarkdown($item['description']); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </section>
                    <?php endif; ?>
                    <?php $cvSectionBlocks['custom-' . $cs['id']] = ob_get_clean(); ?>
                <?php endforeach; ?>

                <!-- Render columns in user-defined order -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    <!-- Left Column (Narrower) -->
                    <div id="cv-left-column" class="lg:col-span-1 min-w-0 overflow-hidden space-y-6 order-2 lg:order-1">
                        <?php foreach ($cvLeftDefault as $cvSId): ?>
                            <?php echo renderCvSectionWrapper($cvSId, $cvSectionBlocks[$cvSId] ?? '', $isCvOwner, $sectionsOnline, $cvAllowInlineAdd, $cvSectionAddLabels[$cvSId] ?? null); ?>
                        <?php endforeach; ?>
                    </div>
                    <!-- Right Column (Wider) -->
                    <div id="cv-right-column" class="lg:col-span-2 min-w-0 space-y-6 order-1 lg:order-2">
                        <?php foreach ($cvRightDefault as $cvSId): ?>
                            <?php echo renderCvSectionWrapper($cvSId, $cvSectionBlocks[$cvSId] ?? '', $isCvOwner, $sectionsOnline, $cvAllowInlineAdd, $cvSectionAddLabels[$cvSId] ?? null); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>

    <?php partial('footer'); ?>

    <?php partial('auth-modals'); ?>

    <?php if ($cvAllowInlineAdd): ?>
    <div id="cv-add-modal-overlay" class="hidden no-print fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h3 id="cv-add-modal-title" class="text-lg font-semibold text-gray-900">Add</h3>
                <button type="button" id="cv-add-modal-close" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="cv-add-modal-form" class="px-5 py-4">
                <div id="cv-add-modal-fields" class="space-y-4"></div>
                <p id="cv-add-modal-error" class="hidden mt-3 text-sm text-red-600"></p>
                <div class="flex items-center justify-between gap-2 mt-5 pt-4 border-t border-gray-200">
                    <button type="button" id="cv-add-modal-delete" class="hidden px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 border border-red-200">Delete</button>
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" id="cv-add-modal-cancel" class="px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-300">Cancel</button>
                        <button type="submit" id="cv-add-modal-submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Owner-only "Edit Mode" - reveal hidden-but-populated sections dimmed/dashed and
        // let the owner flip any section's online visibility without leaving the page.
        if (window.cvEditModeData) {
            (function () {
                var data = window.cvEditModeData;
                var STORAGE_KEY = 'cvEditModeActive';
                var body = document.body;
                var toggleBtn = document.getElementById('cv-edit-mode-toggle');
                var statusEl = document.getElementById('cv-edit-mode-status');

                // Maps the template section id used in the DOM/sections_online to the
                // camelCase key the save endpoints expect (api/save-profile-sections-online.php,
                // api/variant-pdf-preferences.php).
                var SECTION_API_KEYS = {
                    'profile': 'profile',
                    'professional-summary': 'summary',
                    'work-experience': 'work',
                    'education': 'education',
                    'skills': 'skills',
                    'projects': 'projects',
                    'certifications': 'certifications',
                    'memberships': 'memberships',
                    'interests': 'interests',
                    'qualification-equivalence': 'qualificationEquivalence'
                };

                function showCvEditToast(message) {
                    var toast = document.createElement('div');
                    toast.textContent = message;
                    toast.className = 'fixed bottom-4 right-4 z-50 bg-gray-900 text-white text-sm px-4 py-2 shadow-lg';
                    document.body.appendChild(toast);
                    setTimeout(function () { toast.remove(); }, 3000);
                }

                // Section drag-and-drop reordering. profiles.section_order has no per-variant
                // override (same as the content-editor sidebar's own reorder tool), so this is
                // enabled on both the master CV and any variant page - reordering here changes
                // the section layout everywhere, matching how that setting already behaves.
                var leftColumn = document.getElementById('cv-left-column');
                var rightColumn = document.getElementById('cv-right-column');
                var sectionReorder = (function () {
                    var lists = [leftColumn, rightColumn].filter(Boolean);
                    var dragSrc = null;
                    var dragSrcOriginalParent = null;

                    function onDragStart(e) {
                        dragSrc = this;
                        dragSrcOriginalParent = this.parentElement;
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/plain', this.dataset.cvSectionKey || '');
                        this.classList.add('cv-dragging');
                    }
                    function onDragOver(e) {
                        e.preventDefault();
                        e.dataTransfer.dropEffect = 'move';
                        if (dragSrc === this) return;
                        // Which half of the target the cursor is over decides whether the drop
                        // inserts before or after it - without this, dropping "on" an item always
                        // means "before it", so moving something down past just one neighbour
                        // meant targeting the item after that neighbour instead. Confusing.
                        var rect = this.getBoundingClientRect();
                        var after = e.clientY > rect.top + rect.height / 2;
                        this.classList.add('cv-drag-over');
                        this.classList.toggle('cv-drag-over-after', after);
                        this.classList.toggle('cv-drag-over-before', !after);
                        this._cvInsertAfter = after;
                    }
                    function onDragLeave() {
                        this.classList.remove('cv-drag-over', 'cv-drag-over-before', 'cv-drag-over-after');
                    }
                    function onDrop(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        this.classList.remove('cv-drag-over', 'cv-drag-over-before', 'cv-drag-over-after');
                        // Unlike item reordering, sections are allowed to cross from one column
                        // into the other - `lists` only ever contains the two known columns, so
                        // there's nothing else a section could accidentally land in.
                        if (dragSrc && dragSrc !== this) {
                            var targetParent = this.parentElement;
                            if (this._cvInsertAfter) {
                                targetParent.insertBefore(dragSrc, this.nextSibling);
                            } else {
                                targetParent.insertBefore(dragSrc, this);
                            }
                            saveOrder();
                            if (dragSrcOriginalParent !== targetParent) {
                                saveColumn(dragSrc, targetParent);
                            }
                        }
                    }
                    function onDragEnd() {
                        this.classList.remove('cv-dragging');
                        lists.forEach(function (list) {
                            list.querySelectorAll('.cv-section-draggable').forEach(function (el) {
                                el.classList.remove('cv-drag-over', 'cv-drag-over-before', 'cv-drag-over-after');
                            });
                        });
                    }

                    function onColumnDragOver(e) {
                        e.preventDefault();
                        e.dataTransfer.dropEffect = 'move';
                    }
                    function onColumnDrop(e) {
                        // Fires only when dropping on the column's own empty space, not on a
                        // section (those call stopPropagation() so this never double-handles
                        // it) - covers dropping below the last item, or into an empty column.
                        if (e.target !== this) return;
                        e.preventDefault();
                        if (dragSrc) {
                            var wasSameParent = dragSrcOriginalParent === this;
                            this.appendChild(dragSrc);
                            saveOrder();
                            if (!wasSameParent) saveColumn(dragSrc, this);
                        }
                    }

                    function enable() {
                        lists.forEach(function (list) {
                            list.addEventListener('dragover', onColumnDragOver);
                            list.addEventListener('drop', onColumnDrop);
                            list.querySelectorAll('.cv-section-draggable').forEach(function (el) {
                                el.setAttribute('draggable', 'true');
                                el.addEventListener('dragstart', onDragStart);
                                el.addEventListener('dragover', onDragOver);
                                el.addEventListener('dragleave', onDragLeave);
                                el.addEventListener('drop', onDrop);
                                el.addEventListener('dragend', onDragEnd);
                            });
                        });
                    }
                    function disable() {
                        lists.forEach(function (list) {
                            list.removeEventListener('dragover', onColumnDragOver);
                            list.removeEventListener('drop', onColumnDrop);
                            list.querySelectorAll('.cv-section-draggable').forEach(function (el) {
                                el.setAttribute('draggable', 'false');
                                el.classList.remove('cv-dragging', 'cv-drag-over');
                                el.removeEventListener('dragstart', onDragStart);
                                el.removeEventListener('dragover', onDragOver);
                                el.removeEventListener('dragleave', onDragLeave);
                                el.removeEventListener('drop', onDrop);
                                el.removeEventListener('dragend', onDragEnd);
                            });
                        });
                    }
                    function saveOrder() {
                        var order = [];
                        lists.forEach(function (list) {
                            list.querySelectorAll('.cv-section-draggable').forEach(function (el) {
                                if (el.dataset.cvSectionKey) order.push(el.dataset.cvSectionKey);
                            });
                        });
                        var body = new URLSearchParams();
                        body.append('section_order', JSON.stringify(order));
                        body.append(data.csrfTokenName, data.csrfToken);
                        fetch('/api/save-section-order.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: body.toString()
                        }).then(function (r) { return r.json(); }).then(function (res) {
                            if (!res || !res.success) throw new Error('save failed');
                        }).catch(function () {
                            showCvEditToast('Could not save the new order. Please try again.');
                        });
                    }

                    function saveColumn(el, targetParent) {
                        var sectionKey = el.dataset.cvSectionKey;
                        if (!sectionKey) return;
                        var column = targetParent === leftColumn ? 'left' : 'right';
                        var body = new URLSearchParams();
                        body.append('section_id', sectionKey);
                        body.append('column', column);
                        body.append(data.csrfTokenName, data.csrfToken);
                        fetch('/api/save-cv-page-column.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: body.toString()
                        }).then(function (r) { return r.json(); }).then(function (res) {
                            if (!res || !res.success) throw new Error('save failed');
                        }).catch(function () {
                            showCvEditToast('Could not save the new column. Please try again.');
                        });
                    }

                    return { enable: enable, disable: disable };
                })();

                // Item-level drag-and-drop reordering within a section (e.g. dragging one job
                // above another). Independent per list - skills get one list per category (a
                // skill can't be dragged out of its category), every other section is one list
                // for the whole section. Work Experience and Certifications already had their
                // own dedicated reorder endpoints (predating this feature); the other five
                // share the generic one built alongside this.
                var ITEM_REORDER_ENDPOINTS = {
                    'work-experience': { url: '/api/reorder-work-experience.php', useAction: true },
                    'certifications': { url: '/api/reorder-certifications.php', useAction: true },
                    'education': { url: '/api/reorder-section-items.php', useAction: false },
                    'skills': { url: '/api/reorder-section-items.php', useAction: false },
                    'projects': { url: '/api/reorder-section-items.php', useAction: false },
                    'memberships': { url: '/api/reorder-section-items.php', useAction: false },
                    'interests': { url: '/api/reorder-section-items.php', useAction: false },
                    'qualification-equivalence': { url: '/api/reorder-section-items.php', useAction: false }
                };

                var itemReorder = (function () {
                    var lists = Array.from(document.querySelectorAll('[data-cv-items-list]'));
                    var dragSrc = null;

                    function onDragStart(e) {
                        dragSrc = this;
                        e.stopPropagation();
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/plain', this.dataset.cvItemId || '');
                        this.classList.add('cv-dragging');
                    }
                    function onDragOver(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.dataTransfer.dropEffect = 'move';
                        if (dragSrc === this) return;
                        var rect = this.getBoundingClientRect();
                        var after = e.clientY > rect.top + rect.height / 2;
                        this.classList.add('cv-drag-over');
                        this.classList.toggle('cv-drag-over-after', after);
                        this.classList.toggle('cv-drag-over-before', !after);
                        this._cvInsertAfter = after;
                    }
                    function onDragLeave() {
                        this.classList.remove('cv-drag-over', 'cv-drag-over-before', 'cv-drag-over-after');
                    }
                    function onDrop(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        this.classList.remove('cv-drag-over', 'cv-drag-over-before', 'cv-drag-over-after');
                        if (dragSrc && dragSrc !== this && dragSrc.parentElement === this.parentElement) {
                            if (this._cvInsertAfter) {
                                this.parentElement.insertBefore(dragSrc, this.nextSibling);
                            } else {
                                this.parentElement.insertBefore(dragSrc, this);
                            }
                            saveItemOrder(this.parentElement);
                        }
                    }
                    function onDragEnd() {
                        this.classList.remove('cv-dragging');
                        lists.forEach(function (list) {
                            list.querySelectorAll('.cv-item-draggable').forEach(function (el) {
                                el.classList.remove('cv-drag-over', 'cv-drag-over-before', 'cv-drag-over-after');
                            });
                        });
                    }

                    function enable() {
                        lists.forEach(function (list) {
                            list.querySelectorAll('.cv-item-draggable').forEach(function (el) {
                                el.setAttribute('draggable', 'true');
                                el.addEventListener('dragstart', onDragStart);
                                el.addEventListener('dragover', onDragOver);
                                el.addEventListener('dragleave', onDragLeave);
                                el.addEventListener('drop', onDrop);
                                el.addEventListener('dragend', onDragEnd);
                            });
                        });
                    }
                    function disable() {
                        lists.forEach(function (list) {
                            list.querySelectorAll('.cv-item-draggable').forEach(function (el) {
                                el.setAttribute('draggable', 'false');
                                el.classList.remove('cv-dragging', 'cv-drag-over');
                                el.removeEventListener('dragstart', onDragStart);
                                el.removeEventListener('dragover', onDragOver);
                                el.removeEventListener('dragleave', onDragLeave);
                                el.removeEventListener('drop', onDrop);
                                el.removeEventListener('dragend', onDragEnd);
                            });
                        });
                    }

                    function fixWorkExperienceSeparators(list) {
                        // Work Experience renders a plain <hr> between (not around) items - after
                        // a drag reorder those separators are still in their old DOM positions, so
                        // rebuild them fresh between the now-reordered items.
                        Array.from(list.querySelectorAll(':scope > hr')).forEach(function (hr) { hr.remove(); });
                        var items = Array.from(list.querySelectorAll(':scope > .cv-item-draggable'));
                        items.forEach(function (item, index) {
                            if (index < items.length - 1) {
                                var hr = document.createElement('hr');
                                hr.className = 'my-3 border-gray-200';
                                item.insertAdjacentElement('afterend', hr);
                            }
                        });
                    }

                    function saveItemOrder(list) {
                        var sectionKey = list.dataset.cvItemsList;
                        var config = ITEM_REORDER_ENDPOINTS[sectionKey];
                        if (!config) return;

                        if (sectionKey === 'work-experience') fixWorkExperienceSeparators(list);

                        var orderedIds = Array.from(list.querySelectorAll(':scope > .cv-item-draggable'))
                            .map(function (el) { return el.dataset.cvItemId; })
                            .filter(Boolean);
                        if (!orderedIds.length) return;

                        var body = new URLSearchParams();
                        body.append('ordered_ids', JSON.stringify(orderedIds));
                        body.append(data.csrfTokenName, data.csrfToken);
                        if (config.useAction) {
                            body.append('action', 'reorder');
                        } else {
                            body.append('section_id', sectionKey);
                        }

                        fetch(config.url, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: body.toString()
                        }).then(function (r) { return r.json(); }).then(function (res) {
                            if (!res || !res.success) throw new Error('save failed');
                        }).catch(function () {
                            showCvEditToast('Could not save the new order. Please try again.');
                        });
                    }

                    return { enable: enable, disable: disable };
                })();

                function setEditMode(active) {
                    body.classList.toggle('cv-edit-mode-active', active);
                    if (toggleBtn) {
                        toggleBtn.setAttribute('aria-pressed', active ? 'true' : 'false');
                        toggleBtn.classList.toggle('bg-blue-600', active);
                        toggleBtn.classList.toggle('text-white', active);
                        toggleBtn.classList.toggle('border-blue-600', active);
                        toggleBtn.classList.toggle('hover:bg-blue-700', active);
                        toggleBtn.classList.toggle('bg-white', !active);
                        toggleBtn.classList.toggle('text-gray-700', !active);
                        toggleBtn.classList.toggle('hover:bg-gray-50', !active);
                    }
                    if (statusEl) {
                        statusEl.textContent = active
                            ? 'Edit Mode: hidden sections show dimmed with a dashed outline. Drag the grip handle to reorder, or use the badge on any section to toggle its visibility.'
                            : "You're viewing your CV as visitors see it.";
                    }
                    if (active) {
                        sectionReorder.enable();
                        itemReorder.enable();
                    } else {
                        sectionReorder.disable();
                        itemReorder.disable();
                    }
                    try { sessionStorage.setItem(STORAGE_KEY, active ? '1' : '0'); } catch (e) {}
                }

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function () {
                        setEditMode(!body.classList.contains('cv-edit-mode-active'));
                    });
                }

                var initialActive = false;
                try { initialActive = sessionStorage.getItem(STORAGE_KEY) === '1'; } catch (e) {}
                setEditMode(initialActive);

                function applyToggleState(btn, wrapper, visible) {
                    btn.setAttribute('data-visible', visible ? '1' : '0');
                    btn.setAttribute('aria-pressed', visible ? 'true' : 'false');
                    btn.title = visible ? 'Hide this section from your CV' : 'Show this section on your CV';
                    var label = btn.querySelector('.cv-section-visibility-label');
                    if (label) label.textContent = visible ? 'Visible' : 'Hidden';
                    if (wrapper) wrapper.classList.toggle('cv-edit-section-hidden', !visible);
                }

                document.querySelectorAll('.cv-section-visibility-toggle').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var sectionKey = btn.getAttribute('data-section-key');
                        var apiKey = SECTION_API_KEYS[sectionKey];
                        if (!apiKey) return;
                        var currentlyVisible = btn.getAttribute('data-visible') === '1';
                        var nextVisible = !currentlyVisible;
                        var wrapper = btn.closest('.cv-edit-section');

                        applyToggleState(btn, wrapper, nextVisible);

                        var payload = { sections_online: {} };
                        payload.sections_online[apiKey] = nextVisible;
                        payload[data.csrfTokenName] = data.csrfToken;
                        if (data.isVariant) payload.variant_id = data.variantId;

                        var url = data.isVariant ? data.saveVariantUrl : data.saveOnlineUrl;

                        fetch(url, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        }).then(function (r) { return r.json(); }).then(function (res) {
                            if (!res || !res.success) throw new Error('save failed');
                        }).catch(function () {
                            applyToggleState(btn, wrapper, currentlyVisible);
                            showCvEditToast('Could not save that change. Please try again.');
                        });
                    });
                });

                // "+" add-content modal (master CV only - see $cvAllowInlineAdd in cv.php).
                var addModalOverlay = document.getElementById('cv-add-modal-overlay');
                if (addModalOverlay) {
                    var ADD_SECTION_CONFIG = {
                        'professional-summary': {
                            addTitle: 'Add Professional Summary',
                            editTitle: 'Edit Professional Summary',
                            action: 'save',
                            fields: [
                                { name: 'description', label: 'Summary', type: 'textarea', rows: 5 }
                            ]
                        },
                        'work-experience': {
                            addTitle: 'Add Work Experience',
                            editTitle: 'Edit Work Experience',
                            action: 'create',
                            fields: [
                                { name: 'position', label: 'Position / Job Title', type: 'text', required: true },
                                { name: 'company_name', label: 'Company', type: 'text', required: true },
                                { name: 'start_date', label: 'Start Date', type: 'date', required: true },
                                { name: 'end_date', label: 'End Date (leave blank if current)', type: 'date' },
                                { name: 'description', label: 'Description', type: 'textarea' }
                            ]
                        },
                        'education': {
                            addTitle: 'Add Education',
                            editTitle: 'Edit Education',
                            action: 'create',
                            fields: [
                                { name: 'degree', label: 'Degree / Qualification', type: 'text', required: true },
                                { name: 'institution', label: 'Institution', type: 'text', required: true },
                                { name: 'field_of_study', label: 'Field of Study', type: 'text' },
                                { name: 'start_date', label: 'Start Date', type: 'date', required: true },
                                { name: 'end_date', label: 'End Date', type: 'date' }
                            ]
                        },
                        'skills': {
                            addTitle: 'Add Skill',
                            editTitle: 'Edit Skill',
                            action: 'create',
                            fields: [
                                { name: 'name', label: 'Skill Name', type: 'text', required: true },
                                { name: 'category', label: 'Category', type: 'text', placeholder: 'e.g. Languages, Tools' },
                                { name: 'level', label: 'Level', type: 'text', placeholder: 'e.g. Beginner, Advanced' }
                            ]
                        },
                        'projects': {
                            addTitle: 'Add Project',
                            editTitle: 'Edit Project',
                            action: 'create',
                            fields: [
                                { name: 'title', label: 'Project Title', type: 'text', required: true },
                                { name: 'url', label: 'Project URL', type: 'text', placeholder: 'https://…' },
                                { name: 'start_date', label: 'Start Date', type: 'date' },
                                { name: 'end_date', label: 'End Date', type: 'date' },
                                { name: 'description', label: 'Description', type: 'textarea' }
                            ]
                        },
                        'certifications': {
                            addTitle: 'Add Certification',
                            editTitle: 'Edit Certification',
                            action: 'create',
                            fields: [
                                { name: 'name', label: 'Certification Name', type: 'text', required: true },
                                { name: 'issuer', label: 'Issuing Organisation', type: 'text', required: true },
                                { name: 'date_obtained', label: 'Date Obtained', type: 'date' },
                                { name: 'expiry_date', label: 'Expiry Date', type: 'date' }
                            ]
                        },
                        'memberships': {
                            addTitle: 'Add Professional Membership',
                            editTitle: 'Edit Professional Membership',
                            action: 'create',
                            fields: [
                                { name: 'organisation', label: 'Organisation', type: 'text', required: true },
                                { name: 'role', label: 'Role', type: 'text' },
                                { name: 'start_date', label: 'Start Date', type: 'date' },
                                { name: 'end_date', label: 'End Date', type: 'date' }
                            ]
                        },
                        'interests': {
                            addTitle: 'Add Interest',
                            editTitle: 'Edit Interest',
                            action: 'create',
                            fields: [
                                { name: 'name', label: 'Interest', type: 'text', required: true },
                                { name: 'description', label: 'Description', type: 'textarea' }
                            ]
                        },
                        'qualification-equivalence': {
                            addTitle: 'Add Qualification Equivalence',
                            editTitle: 'Edit Qualification Equivalence',
                            action: 'create',
                            fields: [
                                { name: 'level', label: 'Qualification Level', type: 'text', required: true },
                                { name: 'description', label: 'Description', type: 'textarea' }
                            ]
                        }
                    };

                    var addModalTitle = document.getElementById('cv-add-modal-title');
                    var addModalFields = document.getElementById('cv-add-modal-fields');
                    var addModalError = document.getElementById('cv-add-modal-error');
                    var addModalForm = document.getElementById('cv-add-modal-form');
                    var addModalSubmit = document.getElementById('cv-add-modal-submit');
                    var addModalDelete = document.getElementById('cv-add-modal-delete');
                    var currentSectionKey = null;
                    var currentMode = 'add'; // 'add' | 'edit'
                    var currentItemId = null;

                    function fieldHtml(f) {
                        var fieldId = 'cv-add-field-' + f.name;
                        var reqMark = f.required ? ' <span class="text-red-500">*</span>' : '';
                        var reqAttr = f.required ? 'required' : '';
                        var placeholder = f.placeholder || '';
                        if (f.type === 'textarea') {
                            return '<div><label for="' + fieldId + '" class="block text-sm font-medium text-gray-700 mb-1">' + f.label + reqMark + '</label>' +
                                '<textarea id="' + fieldId + '" name="' + f.name + '" rows="' + (f.rows || 3) + '" ' + reqAttr + ' placeholder="' + placeholder + '" class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>';
                        }
                        var inputType = f.type === 'date' ? 'date' : 'text';
                        return '<div><label for="' + fieldId + '" class="block text-sm font-medium text-gray-700 mb-1">' + f.label + reqMark + '</label>' +
                            '<input id="' + fieldId + '" name="' + f.name + '" type="' + inputType + '" ' + reqAttr + ' placeholder="' + placeholder + '" class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>';
                    }

                    function openItemModal(sectionKey, mode, itemId, prefill) {
                        var config = ADD_SECTION_CONFIG[sectionKey];
                        if (!config) return;
                        currentSectionKey = sectionKey;
                        currentMode = mode;
                        currentItemId = itemId || null;
                        addModalTitle.textContent = mode === 'edit' ? config.editTitle : config.addTitle;
                        addModalFields.innerHTML = config.fields.map(fieldHtml).join('');
                        if (prefill) {
                            config.fields.forEach(function (f) {
                                var el = document.getElementById('cv-add-field-' + f.name);
                                if (el && prefill[f.name] !== undefined && prefill[f.name] !== null) {
                                    el.value = prefill[f.name];
                                }
                            });
                        }
                        addModalError.classList.add('hidden');
                        addModalError.textContent = '';
                        addModalSubmit.disabled = false;
                        addModalSubmit.textContent = mode === 'edit' ? 'Save' : 'Add';
                        addModalDelete.classList.toggle('hidden', !(mode === 'edit' && currentItemId));
                        addModalOverlay.classList.remove('hidden');
                        var firstField = addModalFields.querySelector('input, textarea');
                        if (firstField) firstField.focus();
                    }

                    function closeItemModal() {
                        addModalOverlay.classList.add('hidden');
                        currentSectionKey = null;
                        currentItemId = null;
                        currentMode = 'add';
                    }

                    document.querySelectorAll('.cv-section-add-btn, .cv-section-add-more-btn').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            openItemModal(btn.getAttribute('data-section-key'), 'add', null, null);
                        });
                    });

                    document.querySelectorAll('.cv-item-edit-btn').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var sectionKey = btn.getAttribute('data-section-key');
                            var itemId = btn.getAttribute('data-item-id') || null;
                            var prefill = null;
                            try { prefill = JSON.parse(btn.getAttribute('data-item') || 'null'); } catch (err) { prefill = null; }
                            openItemModal(sectionKey, 'edit', itemId, prefill);
                        });
                    });

                    document.querySelectorAll('.cv-item-delete-btn').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var sectionKey = btn.getAttribute('data-section-key');
                            var itemId = btn.getAttribute('data-item-id');
                            if (!sectionKey || !itemId) return;
                            if (!window.confirm('Delete this entry? This cannot be undone.')) return;
                            deleteCvItem(sectionKey, itemId, btn);
                        });
                    });

                    function deleteCvItem(sectionKey, itemId, triggerBtn) {
                        if (triggerBtn) triggerBtn.disabled = true;
                        var formEl = new FormData();
                        formEl.append('section_id', sectionKey);
                        formEl.append('action', 'delete');
                        formEl.append('id', itemId);
                        formEl.append('entry_id', itemId);
                        formEl.append(data.csrfTokenName, data.csrfToken);
                        fetch('/api/content-editor/save-section.php', { method: 'POST', body: formEl })
                            .then(function (r) { return r.json(); })
                            .then(function (res) {
                                if (res && res.success) {
                                    try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (err) {}
                                    window.location.reload();
                                } else {
                                    showCvEditToast((res && res.error) || 'Could not delete. Please try again.');
                                    if (triggerBtn) triggerBtn.disabled = false;
                                }
                            })
                            .catch(function () {
                                showCvEditToast('Could not delete. Please try again.');
                                if (triggerBtn) triggerBtn.disabled = false;
                            });
                    }

                    addModalDelete.addEventListener('click', function () {
                        if (!currentSectionKey || !currentItemId) return;
                        if (!window.confirm('Delete this entry? This cannot be undone.')) return;
                        addModalDelete.disabled = true;
                        addModalDelete.textContent = 'Deleting…';
                        deleteCvItem(currentSectionKey, currentItemId, addModalDelete);
                    });

                    document.getElementById('cv-add-modal-close').addEventListener('click', closeItemModal);
                    document.getElementById('cv-add-modal-cancel').addEventListener('click', closeItemModal);
                    addModalOverlay.addEventListener('click', function (e) {
                        if (e.target === addModalOverlay) closeItemModal();
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && !addModalOverlay.classList.contains('hidden')) closeItemModal();
                    });

                    addModalForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        if (!currentSectionKey) return;
                        var config = ADD_SECTION_CONFIG[currentSectionKey];
                        addModalError.classList.add('hidden');

                        var isEdit = currentMode === 'edit';
                        // professional-summary always upserts via 'save'; every other section
                        // uses 'create' for a new entry, 'update' (with its id) for an existing one.
                        var action = config.action === 'save' ? 'save' : (isEdit ? 'update' : 'create');

                        var formEl = new FormData();
                        formEl.append('section_id', currentSectionKey);
                        formEl.append('action', action);
                        formEl.append(data.csrfTokenName, data.csrfToken);
                        if (isEdit && currentItemId) {
                            formEl.append('id', currentItemId);
                        }
                        config.fields.forEach(function (f) {
                            var el = document.getElementById('cv-add-field-' + f.name);
                            formEl.append(f.name, el ? el.value : '');
                        });

                        addModalSubmit.disabled = true;
                        addModalSubmit.textContent = isEdit ? 'Saving…' : 'Adding…';

                        fetch('/api/content-editor/save-section.php', { method: 'POST', body: formEl })
                            .then(function (r) { return r.json(); })
                            .then(function (res) {
                                if (res && res.success) {
                                    try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (err) {}
                                    window.location.reload();
                                } else {
                                    addModalError.textContent = (res && res.error) || 'Could not save. Please try again.';
                                    addModalError.classList.remove('hidden');
                                    addModalSubmit.disabled = false;
                                    addModalSubmit.textContent = isEdit ? 'Save' : 'Add';
                                }
                            })
                            .catch(function () {
                                addModalError.textContent = 'Could not save. Please try again.';
                                addModalError.classList.remove('hidden');
                                addModalSubmit.disabled = false;
                                addModalSubmit.textContent = isEdit ? 'Save' : 'Add';
                            });
                    });
                }
            })();
        }

        const toggleButtons = document.querySelectorAll('[data-toggle="collapse"]');
        toggleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-target');
                const target = document.getElementById(targetId);
                if (!target) {
                    return;
                }

                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                const nextState = !isExpanded;
                button.setAttribute('aria-expanded', nextState ? 'true' : 'false');

                if (nextState) {
                    target.classList.remove('hidden');
                } else {
                    target.classList.add('hidden');
                }

                const plusIcon = button.querySelector('.icon-plus');
                const minusIcon = button.querySelector('.icon-minus');
                if (plusIcon && minusIcon) {
                    plusIcon.classList.toggle('hidden', nextState);
                    minusIcon.classList.toggle('hidden', !nextState);
                }

                const label = button.querySelector('.toggle-label');
                const viewText = button.getAttribute('data-view-label') || 'View';
                const hideText = button.getAttribute('data-hide-label') || 'Hide';
                if (label) {
                    label.textContent = nextState ? hideText : viewText;
                }
            });
        });

        document.querySelectorAll('.copy-cv-link-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = this.getAttribute('data-cv-url');
                if (!url) return;
                var label = this.querySelector('.copy-cv-link-label');
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        if (label) label.textContent = 'Copied!';
                        setTimeout(function () {
                            if (label) label.textContent = 'Share CV';
                        }, 2000);
                    }).catch(function () {});
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = url;
                    ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        if (label) label.textContent = 'Copied!';
                        setTimeout(function () {
                            if (label) label.textContent = 'Share CV';
                        }, 2000);
                    } catch (e) {}
                    document.body.removeChild(ta);
                }
            });
        });
    });
</script>

</body>
</html>
