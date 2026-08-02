<?php
/**
 * Shared validation/normalisation for the profile "identity" fields
 * (name, contact details) - used by both the legacy profile.php form and
 * the content-editor "Profile" section.
 */

function normaliseProfileTextField($value, $stripTags = true) {
    if (!is_string($value)) {
        return null;
    }
    $value = trim($value);
    if ($value === '') {
        return null;
    }
    return $stripTags ? strip_tags($value) : $value;
}

/**
 * Validate and normalise full_name/phone/location/linkedin_url/bio.
 * Does NOT touch username, header colours, cv_visibility, or show_photo/
 * show_qr_code - those stay with their own owners (profile.php's Account tab /
 * the Appearance/Visibility sections). show_photo/show_qr_code specifically are
 * saved by Visibility's "Photo on Online CV" toggle, not by this identity form -
 * touching them here would silently reset that setting on every unrelated save.
 *
 * Returns ['error' => string] on validation failure, or ['data' => array] of
 * columns ready for db()->update('profiles', ...) on success.
 */
function validateProfileIdentityInput(array $input) {
    $data = [];

    $data['full_name'] = normaliseProfileTextField($input['full_name'] ?? '');

    $phoneValue = normaliseProfileTextField($input['phone'] ?? '', false);
    if ($phoneValue !== null) {
        $phoneValue = preg_replace('/[^0-9\+\-\s\(\)]/', '', $phoneValue);
    }
    $data['phone'] = $phoneValue;

    $data['location'] = normaliseProfileTextField($input['location'] ?? '');
    $data['bio'] = normaliseProfileTextField($input['bio'] ?? '', true);

    $data['linkedin_url'] = normaliseProfileTextField($input['linkedin_url'] ?? '', false);
    if (!empty($data['linkedin_url']) && !preg_match('/^https?:\/\//i', $data['linkedin_url'])) {
        $data['linkedin_url'] = 'https://' . ltrim($data['linkedin_url'], '/');
    }
    if (!empty($data['linkedin_url']) && !validateUrl($data['linkedin_url'])) {
        return ['error' => 'Invalid LinkedIn URL'];
    }

    foreach (['full_name', 'location', 'bio', 'linkedin_url'] as $field) {
        if ($data[$field] !== null && checkForXss($data[$field])) {
            return ['error' => "Invalid content in {$field}"];
        }
    }

    return ['data' => $data];
}

/**
 * Resolve a requested cv_visibility value against the user's actual organisation
 * membership - "organisation" without a real organisation silently falls back to
 * "private", matching the behaviour profile.php has always had.
 *
 * Returns null if $requested isn't one of the three valid values.
 */
function resolveCvVisibility($userId, $requested) {
    if (!in_array($requested, ['public', 'organisation', 'private'], true)) {
        return null;
    }
    if ($requested === 'organisation') {
        $userOrg = getUserOrganisation($userId);
        if (!$userOrg || empty($userOrg['organisation_id'])) {
            return 'private';
        }
    }
    return $requested;
}
