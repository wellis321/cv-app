<?php
require_once __DIR__ . '/../../php/helpers.php';

if (!isPost()) {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

requireAuth();

$rawBody = file_get_contents('php://input');
$parsed = json_decode($rawBody, true);

if (!is_array($parsed)) {
    $parsed = [];
}

$planId = $parsed['plan'] ?? post('plan', '');
$planId = sanitizeInput((string)$planId);

$csrfToken = $parsed[CSRF_TOKEN_NAME] ?? ($parsed['csrf_token'] ?? null);
if (!$csrfToken && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
}
if (!$csrfToken && isset($_POST[CSRF_TOKEN_NAME])) {
    $csrfToken = $_POST[CSRF_TOKEN_NAME];
}

if (!verifyCsrfToken((string)$csrfToken)) {
    jsonResponse(['error' => 'Invalid security token. Please refresh and try again.'], 422);
}

if (empty($planId)) {
    jsonResponse(['error' => 'Plan is required.'], 422);
}

$plans = getSubscriptionPlansConfig();
if (!isset($plans[$planId]) || $planId === 'free') {
    jsonResponse(['error' => 'Selected plan is not available for checkout.'], 400);
}

try {
    $session = stripeCreateCheckoutSession(getUserId(), $planId);
    if (empty($session['url'])) {
        throw new RuntimeException('Stripe session missing URL.');
    }
    jsonResponse(['url' => $session['url']]);
} catch (Throwable $e) {
    error_log('Stripe checkout session error: ' . $e->getMessage());
    jsonResponse(['error' => 'We were unable to start checkout. Please try again shortly.'], 500);
}
