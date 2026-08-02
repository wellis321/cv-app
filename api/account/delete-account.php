<?php
/**
 * Self-service account deletion.
 * Requires password + typed "DELETE" confirmation. Blocks organisation owners
 * (no ownership-transfer feature exists) and cancels any active Stripe
 * subscription before removing anything, so nobody keeps getting billed for
 * an account that no longer exists.
 */

require_once __DIR__ . '/../../php/helpers.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

$userId = getUserId();
$password = post('password', '');
$confirmation = trim(post('confirmation', ''));

if ($confirmation !== 'DELETE') {
    http_response_code(400);
    echo json_encode(['error' => 'Please type DELETE to confirm.']);
    exit;
}

$profile = db()->fetchOne(
    "SELECT id, password_hash, stripe_subscription_id FROM profiles WHERE id = ?",
    [$userId]
);

if (!$profile || !verifyPassword($password, $profile['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Incorrect password.']);
    exit;
}

if (isOrganisationOwner($userId)) {
    http_response_code(400);
    echo json_encode(['error' => "You're an organisation owner. Transfer ownership or delete your organisation before deleting your account."]);
    exit;
}

try {
    $subscriptionContext = getUserSubscriptionContext($userId);
    if (subscriptionIsPaid($subscriptionContext) && !empty($profile['stripe_subscription_id']) && stripeIsConfigured()) {
        try {
            stripeCancelSubscription($profile['stripe_subscription_id']);
        } catch (Exception $e) {
            error_log("Failed to cancel Stripe subscription during account deletion for user {$userId}: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to cancel your subscription. Please contact support before deleting your account.']);
            exit;
        }
    }

    deleteUserStorage($userId);
    db()->delete('profiles', 'id = ?', [$userId]);
    logoutUser();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Account deletion error for user {$userId}: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete your account. Please try again or contact support.']);
}
