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

// Pre-check: Stripe must be configured
if (!stripeIsConfigured()) {
    error_log('Stripe checkout: STRIPE_SECRET_KEY is not configured');
    jsonResponse(['error' => 'Payment system is not configured. Please contact support.'], 503);
}

// Pre-check: Price ID must exist for this plan
$priceId = getStripePriceIdForPlan($planId);
if (empty($priceId)) {
    error_log('Stripe checkout: No price ID for plan ' . $planId . '. Check STRIPE_PRICE_PRO_* in .env');
    jsonResponse(['error' => 'This plan is not available for checkout. Please select another plan or contact support.'], 400);
}

try {
    $session = stripeCreateCheckoutSession(getUserId(), $planId);
    if (empty($session['url'])) {
        throw new RuntimeException('Stripe session missing URL.');
    }
    jsonResponse(['url' => $session['url']]);
} catch (Throwable $e) {
    error_log('Stripe checkout session error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (defined('DEBUG') && DEBUG) {
        jsonResponse(['error' => $e->getMessage(), 'debug' => true], 500);
    } else {
        jsonResponse(['error' => 'We were unable to start checkout. Please try again shortly.'], 500);
    }
}
