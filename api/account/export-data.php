<?php
/**
 * Download all of the logged-in user's data as JSON (data portability).
 * Structured/database data only - uploaded file attachments are listed by
 * metadata/URL, not bundled as binaries.
 */

require_once __DIR__ . '/../../php/helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = getUserId();

$data = [
    'exported_at' => date('c'),
    'cv' => loadCvData($userId),
];

// Never include credentials/security tokens in a data export.
foreach (['password_hash', 'email_verification_token', 'email_verification_expires', 'password_reset_token', 'password_reset_expires', 'invitation_token', 'job_saver_token'] as $sensitiveField) {
    unset($data['cv']['profile'][$sensitiveField]);
}

$data['cv']['custom_sections'] = db()->fetchAll(
    "SELECT * FROM custom_sections WHERE profile_id = ? ORDER BY sort_order ASC, created_at ASC",
    [$userId]
);
foreach ($data['cv']['custom_sections'] as &$customSection) {
    $customSection['items'] = db()->fetchAll(
        "SELECT * FROM custom_section_items WHERE custom_section_id = ? ORDER BY sort_order ASC, created_at ASC",
        [$customSection['id']]
    );
}
unset($customSection);

$data['job_applications'] = getUserJobApplications($userId, []);

$data['cover_letters'] = db()->fetchAll(
    "SELECT * FROM cover_letters WHERE user_id = ? ORDER BY created_at ASC",
    [$userId]
);

$variants = getUserCvVariants($userId);
$data['cv_variants'] = [];
foreach ($variants as $variant) {
    $data['cv_variants'][] = [
        'variant' => $variant,
        'data' => loadCvVariantData($variant['id']),
    ];
}

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="my-data-' . date('Y-m-d') . '.json"');
echo json_encode($data, JSON_PRETTY_PRINT);
exit;
