<?php
/**
 * Reset a user's password (for local development when you don't know the current password).
 *
 * Usage: php scripts/reset-user-password.php <email> <new_password>
 * Example: php scripts/reset-user-password.php noreply@simple-job-tracker.com mynewpassword
 *
 * Run from the project root. Uses the same database as the app (from .env).
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

if ($argc < 3) {
    die("Usage: php scripts/reset-user-password.php <email> <new_password>\nExample: php scripts/reset-user-password.php noreply@simple-job-tracker.com mynewpassword\n");
}

$email = $argv[1];
$newPassword = $argv[2];

require_once __DIR__ . '/../php/helpers.php';

$user = db()->fetchOne(
    "SELECT id, email FROM profiles WHERE email = ?",
    [$email]
);

if (!$user) {
    echo "No user found with email: {$email}\n";
    exit(1);
}

$passwordHash = hashPassword($newPassword);

db()->update(
    'profiles',
    [
        'password_hash' => $passwordHash,
        'updated_at' => date('Y-m-d H:i:s'),
    ],
    'id = ?',
    [$user['id']]
);

echo "Done. Password for {$email} has been reset. You can log in with your new password.\n";
