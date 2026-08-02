<?php
/**
 * Visibility Section Form Partial
 * Access control (who can view the CV URL) + what's included on the online CV / in
 * the PDF export. Consolidates controls that previously lived on profile.php's
 * "Visibility" tab and preview-cv.php's "Sections for..." sidebar panels.
 */

$variantId = $_GET['variant_id'] ?? null;
$isVariantContext = false;
$cvVariant = null;

$profile = db()->fetchOne("SELECT * FROM profiles WHERE id = ?", [$userId]);

if ($variantId) {
    $cvVariant = db()->fetchOne("SELECT * FROM cv_variants WHERE id = ? AND user_id = ?", [$variantId, $userId]);
    $isVariantContext = !empty($cvVariant);
}

$currentVisibility = $profile['cv_visibility'] ?? 'public';
$userOrg = getUserOrganisation($userId);
$hasOrganisation = !empty($userOrg) && !empty($userOrg['organisation_id']);

$visibilityOptions = [
    'public' => ['label' => 'Public', 'description' => 'Anyone with the link can view your CV. No login required.'],
    'organisation' => ['label' => 'Organisation Only', 'description' => 'Only members of your organisation can view your CV. Requires login.'],
    'private' => ['label' => 'Private', 'description' => 'Only you can view your CV. The link will not work for others.'],
];

// Section keys match the api/save-profile-sections-online.php + api/profile-pdf-preferences.php
// contract (camelCase), not the template-id style getSectionsOnlineForCv() returns.
$sectionKeys = ['profile', 'summary', 'work', 'education', 'areasOfExpertise', 'skills', 'projects', 'certifications', 'memberships', 'interests', 'qualificationEquivalence'];
$sectionLabels = [
    'profile' => 'Personal Profile',
    'summary' => 'Professional Summary',
    'work' => 'Work Experience',
    'education' => 'Education',
    'areasOfExpertise' => 'Areas of Expertise',
    'skills' => 'Skills',
    'projects' => 'Projects',
    'certifications' => 'Certifications',
    'memberships' => 'Professional Memberships',
    'interests' => 'Interests & Activities',
    'qualificationEquivalence' => 'Professional Qualification Equivalence',
];

// Online-CV section toggles: variant pdf_preferences.sections_online wins if present, else profile.sections_online.
$rawOnline = null;
if ($isVariantContext && !empty($cvVariant['pdf_preferences'])) {
    $decoded = json_decode($cvVariant['pdf_preferences'], true);
    $rawOnline = is_array($decoded) ? ($decoded['sections_online'] ?? null) : null;
}
if ($rawOnline === null && !empty($profile['sections_online'])) {
    $decoded = json_decode($profile['sections_online'], true);
    $rawOnline = is_array($decoded) ? $decoded : null;
}
$onlineToggles = [];
foreach ($sectionKeys as $k) {
    $onlineToggles[$k] = isset($rawOnline[$k]) ? (bool) $rawOnline[$k] : true;
}
$showResponsibilitiesOnline = getShowResponsibilitiesOnlineForCv($profile, $cvVariant);

$pdfPrefs = getPdfPreferencesForCv($profile, $cvVariant);
$pdfToggles = $pdfPrefs['sections'];
$showResponsibilitiesInPdf = $pdfPrefs['show_responsibilities_in_pdf'];

// Select Skills (PDF only - templates with a skill-count cap let the user pick which
// skills to include in the export; the online CV always shows all skills). Mirrors the
// template-id precedence preview-cv.php/getPdfPreferencesForCv() already use.
$allSkills = db()->fetchAll("SELECT id, name, level, category FROM skills WHERE profile_id = ? ORDER BY category, name", [$userId]);
$resolvedTemplateId = $profile['preferred_template_id'] ?? 'minimal';
if ($isVariantContext && !empty($cvVariant['pdf_preferences'])) {
    $decodedVariantPrefs = json_decode($cvVariant['pdf_preferences'], true);
    if (is_array($decodedVariantPrefs) && !empty($decodedVariantPrefs['preferred_template_id'])) {
        $resolvedTemplateId = $decodedVariantPrefs['preferred_template_id'];
    }
}
?>
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Visibility</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Who Can View Your CV</h2>

        <form method="POST" data-section-form data-form-type="update">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="section_id" value="visibility">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <?php foreach ($visibilityOptions as $value => $option): ?>
                    <label class="flex flex-col gap-1 p-3 border rounded-lg cursor-pointer transition-colors <?php echo $currentVisibility === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'; ?>">
                        <span class="flex items-center gap-2">
                            <input type="radio" name="cv_visibility" value="<?php echo e($value); ?>" <?php echo $currentVisibility === $value ? 'checked' : ''; ?> class="w-4 h-4 flex-shrink-0 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="font-medium text-gray-900 text-sm"><?php echo e($option['label']); ?></span>
                        </span>
                        <span class="text-gray-500 text-xs"><?php echo e($option['description']); ?></span>
                        <?php if ($value === 'organisation' && !$hasOrganisation): ?>
                            <span class="text-amber-700 text-xs">Requires being part of an organisation - without one, this keeps your CV private.</span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">Save</button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6" id="visibility-toggles" data-variant-id="<?php echo $isVariantContext ? e($variantId) : ''; ?>">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-xl font-semibold">What's Included</h2>
            <span id="visibility-toggle-status" class="text-sm text-gray-500" aria-live="polite"></span>
        </div>
        <p class="text-sm text-gray-500 mb-4">Choose which sections appear on your Online CV and in exported PDFs. Changes save automatically.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="py-2 font-medium">Section</th>
                        <th class="py-2 font-medium text-center w-24">Online CV</th>
                        <th class="py-2 font-medium text-center w-20">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($sectionKeys as $key): ?>
                        <tr>
                            <td class="py-2.5 text-gray-800"><?php echo e($sectionLabels[$key]); ?></td>
                            <td class="py-2.5 text-center">
                                <input type="checkbox" data-section-toggle data-key="<?php echo e($key); ?>" data-kind="online" <?php echo $onlineToggles[$key] ? 'checked' : ''; ?>>
                            </td>
                            <td class="py-2.5 text-center">
                                <input type="checkbox" data-section-toggle data-key="<?php echo e($key); ?>" data-kind="pdf" <?php echo $pdfToggles[$key] ? 'checked' : ''; ?>>
                            </td>
                        </tr>
                        <?php if ($key === 'profile'): ?>
                            <?php if (!$isVariantContext && !empty($profile['photo_url'])): ?>
                            <tr>
                                <td class="py-2 pl-4 text-gray-500 text-xs">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6a4 4 0 0 0 4 4h12m0 0l-4-4m4 4l-4 4" />
                                        </svg>
                                        Photo on Online CV
                                    </span>
                                </td>
                                <td class="py-2 text-center">
                                    <input type="checkbox" data-section-toggle data-kind="photo-online" <?php echo !empty($profile['show_photo']) ? 'checked' : ''; ?>>
                                </td>
                                <td class="py-2 text-center text-gray-300">&mdash;</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="py-2 pl-4 text-gray-500 text-xs">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6a4 4 0 0 0 4 4h12m0 0l-4-4m4 4l-4 4" />
                                        </svg>
                                        Photo in PDF
                                    </span>
                                </td>
                                <td class="py-2 text-center text-gray-300">&mdash;</td>
                                <td class="py-2 text-center">
                                    <input type="checkbox" data-section-toggle data-kind="include-photo-pdf" <?php echo $pdfPrefs['include_photo'] ? 'checked' : ''; ?>>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pl-4 text-gray-500 text-xs">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6a4 4 0 0 0 4 4h12m0 0l-4-4m4 4l-4 4" />
                                        </svg>
                                        QR code in PDF
                                    </span>
                                </td>
                                <td class="py-2 text-center text-gray-300">&mdash;</td>
                                <td class="py-2 text-center">
                                    <input type="checkbox" data-section-toggle data-kind="include-qr-pdf" <?php echo $pdfPrefs['include_qr'] ? 'checked' : ''; ?>>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($key === 'skills' && $pdfToggles['skills'] && !empty($allSkills)): ?>
                            <tr>
                                <td class="py-2 pl-4 text-gray-500 text-xs" colspan="3">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6a4 4 0 0 0 4 4h12m0 0l-4-4m4 4l-4 4" />
                                        </svg>
                                        <a href="#visibility-skill-selection" data-scroll-to="visibility-skill-selection" class="text-indigo-600 hover:text-indigo-800">Choose which skills appear in your PDF &darr;</a>
                                    </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($key === 'work'): ?>
                            <tr>
                                <td class="py-2 pl-4 text-gray-500 text-xs">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6a4 4 0 0 0 4 4h12m0 0l-4-4m4 4l-4 4" />
                                        </svg>
                                        Key Responsibilities bullets
                                    </span>
                                </td>
                                <td class="py-2 text-center">
                                    <input type="checkbox" data-section-toggle data-kind="responsibilities-online" <?php echo $showResponsibilitiesOnline ? 'checked' : ''; ?>>
                                </td>
                                <td class="py-2 text-center">
                                    <input type="checkbox" data-section-toggle data-kind="responsibilities-pdf" <?php echo $showResponsibilitiesInPdf ? 'checked' : ''; ?>>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php if ($pdfToggles['skills'] && !empty($allSkills)): ?>
        <?php
        $selectedSkillIds = [];
        $skillSelectionRow = db()->fetchOne(
            "SELECT selected_skill_ids FROM user_template_skill_selections WHERE user_id = ? AND template_id = ?",
            [$userId, $resolvedTemplateId]
        );
        if ($skillSelectionRow && !empty($skillSelectionRow['selected_skill_ids'])) {
            $decodedSkillIds = json_decode($skillSelectionRow['selected_skill_ids'], true);
            $selectedSkillIds = is_array($decodedSkillIds) ? $decodedSkillIds : [];
        }
        $skillsByCategory = [];
        foreach ($allSkills as $skill) {
            $cat = trim($skill['category'] ?? '') ?: 'Other';
            $skillsByCategory[$cat][] = $skill;
        }
        uksort($skillsByCategory, function ($a, $b) {
            if ($a === 'Other') return 1;
            if ($b === 'Other') return -1;
            return strcasecmp($a, $b);
        });
        ?>
        <div class="bg-white shadow rounded-lg p-6 mb-6" id="visibility-skill-selection" data-template-id="<?php echo e($resolvedTemplateId); ?>">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-xl font-semibold">Select Skills for PDF</h2>
                <span id="visibility-skill-status" class="text-sm text-gray-500" aria-live="polite"></span>
            </div>
            <p class="text-sm text-gray-500 mb-4">Templates with a skill-count limit only export what you select here. Grouped by category; changes save automatically.</p>
            <div class="border border-gray-200 rounded-md p-3 max-h-72 overflow-y-auto space-y-4">
                <?php foreach ($skillsByCategory as $cat => $skills): ?>
                    <div class="space-y-1" data-skill-category>
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-gray-200">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e($cat); ?></span>
                            <button type="button" class="visibility-skill-category-toggle text-xs font-medium text-indigo-600 hover:text-indigo-800">Select all</button>
                        </div>
                        <?php foreach ($skills as $skill): ?>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 px-2 py-1 rounded text-sm">
                                <input type="checkbox" class="mr-2 visibility-skill-checkbox" data-skill-id="<?php echo e($skill['id']); ?>" <?php echo in_array($skill['id'], $selectedSkillIds) ? 'checked' : ''; ?>>
                                <span class="text-gray-700"><?php echo e($skill['name']); ?><?php if (!empty($skill['level'])): ?> <span class="text-gray-400">(<?php echo e($skill['level']); ?>)</span><?php endif; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
