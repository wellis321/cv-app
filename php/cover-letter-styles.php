<?php
/**
 * Cover letter style utilities
 * Maps CV template IDs to colors so cover letter PDF matches the user's CV design
 */

/**
 * Template color palettes - must match templates/index.js for consistency
 */
function getCoverLetterTemplatePalettes(): array {
    return [
        'professional' => [
            'accent' => '#2563eb',
            'header' => '#1f2937',
            'body' => '#374151',
            'muted' => '#6b7280',
            'link' => '#2563eb',
        ],
        'minimal' => [
            'accent' => '#111827',
            'header' => '#111827',
            'body' => '#374151',
            'muted' => '#6b7280',
            'link' => '#1f2937',
        ],
        'classic' => [
            'accent' => '#1e3a8a',
            'header' => '#1e3a8a',
            'body' => '#475569',
            'muted' => '#64748b',
            'link' => '#1e40af',
        ],
        'structured' => [
            'accent' => '#0ea5e9',
            'header' => '#1e3a8a',
            'body' => '#374151',
            'muted' => '#64748b',
            'link' => '#0284c7',
        ],
        'academic' => [
            'accent' => '#c41e3a',
            'header' => '#c41e3a',
            'body' => '#374151',
            'muted' => '#64748b',
            'link' => '#b91c1c',
        ],
        'modern' => [
            'accent' => '#0d9488',
            'header' => '#0f172a',
            'body' => '#334155',
            'muted' => '#64748b',
            'link' => '#0891b2',
        ],
    ];
}

/**
 * Get cover letter style colors for a user based on their CV template preference
 * When $cvVariantId is provided and that variant has pdf_preferences.preferred_template_id,
 * uses the variant's template so cover letter matches the variant's PDF style.
 * Falls back to profile preference, then subscription default.
 *
 * @param string|int $userId User ID (UUID string or int)
 * @param string|null $cvVariantId Optional CV variant ID (e.g. linked to job application)
 */
function getCoverLetterTemplateColors(string|int $userId, ?string $cvVariantId = null): array {
    $palettes = getCoverLetterTemplatePalettes();
    $templateId = null;

    if ($cvVariantId) {
        $variant = db()->fetchOne(
            "SELECT pdf_preferences FROM cv_variants WHERE id = ? AND user_id = ?",
            [$cvVariantId, $userId]
        );
        if (!empty($variant['pdf_preferences'])) {
            $prefs = is_string($variant['pdf_preferences'])
                ? json_decode($variant['pdf_preferences'], true)
                : $variant['pdf_preferences'];
            if (!empty($prefs['preferred_template_id']) && isset($palettes[$prefs['preferred_template_id']])) {
                $templateId = $prefs['preferred_template_id'];
            }
        }
    }

    if (!$templateId) {
        try {
            $profile = db()->fetchOne(
                "SELECT preferred_template_id FROM profiles WHERE id = ?",
                [$userId]
            );
            $templateId = !empty($profile['preferred_template_id']) ? $profile['preferred_template_id'] : null;
        } catch (Throwable $e) {
            // Column may not exist if migration not run
        }
    }
    if (!$templateId || !isset($palettes[$templateId])) {
        $subContext = getUserSubscriptionContext($userId);
        $templateId = planDefaultTemplateId($subContext);
    }
    if (!isset($palettes[$templateId])) {
        $templateId = 'professional';
    }
    return $palettes[$templateId];
}
