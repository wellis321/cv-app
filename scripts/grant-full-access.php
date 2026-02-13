<?php
/**
 * Grant full access (lifetime plan) to a user account (for local development or support).
 *
 * Usage: php scripts/grant-full-access.php <email>
 * Example: php scripts/grant-full-access.php noreply@simple-job-tracker.com
 *
 * Run from the project root. Uses the same database as the app (from .env).
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

if ($argc < 2) {
    die("Usage: php scripts/grant-full-access.php <email>\nExample: php scripts/grant-full-access.php noreply@simple-job-tracker.com\n");
}

$email = $argv[1];

require_once __DIR__ . '/../php/helpers.php';

$user = db()->fetchOne(
    "SELECT id, email, plan, subscription_status FROM profiles WHERE email = ?",
    [$email]
);

if (!$user) {
    echo "No user found with email: {$email}\n";
    echo "Tip: Check for similar emails (e.g. noreply@simple-job-tracker.com, no-reply@...)\n";
    exit(1);
}

db()->update(
    'profiles',
    [
        'plan' => 'lifetime',
        'subscription_status' => 'active',
        'subscription_current_period_end' => null,
        'subscription_cancel_at' => null,
        'updated_at' => date('Y-m-d H:i:s'),
    ],
    'id = ?',
    [$user['id']]
);

echo "Done. {$email} now has full access (lifetime plan): all templates, unlimited sections, PDF export, AI features.\n";
