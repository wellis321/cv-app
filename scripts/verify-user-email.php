<?php
/**
 * Mark a user's email as verified (for local development when verification emails don't work).
 *
 * Usage: php scripts/verify-user-email.php <email>
 * Example: php scripts/verify-user-email.php noreply@simple-job-tracker.com
 *
 * Run from the project root. Uses the same database as the app (from .env).
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

if ($argc < 2) {
    die("Usage: php scripts/verify-user-email.php <email>\nExample: php scripts/verify-user-email.php noreply@simple-job-tracker.com\n");
}

$email = $argv[1];

require_once __DIR__ . '/../php/helpers.php';

$user = db()->fetchOne(
    "SELECT id, email, email_verified FROM profiles WHERE email = ?",
    [$email]
);

if (!$user) {
    echo "No user found with email: " . $email . "\n";
    exit(1);
}

if (!empty($user['email_verified']) && $user['email_verified'] != 0) {
    echo "User {$email} is already verified. No change made.\n";
    exit(0);
}

db()->update(
    'profiles',
    [
        'email_verified' => 1,
        'email_verification_token' => null,
        'email_verification_expires' => null,
        'updated_at' => date('Y-m-d H:i:s'),
    ],
    'id = ?',
    [$user['id']]
);

echo "Done. Email for {$email} has been marked as verified. You can log in now.\n";
