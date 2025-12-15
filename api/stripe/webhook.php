<?php
require_once __DIR__ . '/../../php/helpers.php';

$payload = file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!stripeIsConfigured() || empty(STRIPE_WEBHOOK_SECRET)) {
    http_response_code(400);
    echo 'Stripe webhook not configured';
    exit;
}

if (!stripeVerifyWebhookSignature($payload, $signatureHeader)) {
    http_response_code(400);
    echo 'Invalid signature';
    exit;
}

$event = json_decode($payload, true);
if (!$event || !isset($event['type'])) {
    http_response_code(400);
    echo 'Invalid payload';
    exit;
}

$type = $event['type'];
$object = $event['data']['object'] ?? [];

try {
    switch ($type) {
        case 'checkout.session.completed':
            if (($object['mode'] ?? '') === 'subscription' && !empty($object['subscription'])) {
                try {
                    $subscription = stripeRequest('GET', '/subscriptions/' . $object['subscription']);
                    stripeUpdateProfileSubscription($subscription);
                } catch (Throwable $inner) {
                    error_log('Stripe webhook fetch subscription failed: ' . $inner->getMessage());
                }
            }
            break;

        case 'customer.subscription.created':
        case 'customer.subscription.updated':
        case 'customer.subscription.deleted':
            stripeUpdateProfileSubscription($object);
            break;

        case 'invoice.payment_failed':
            if (!empty($object['subscription'])) {
                try {
                    $subscription = stripeRequest('GET', '/subscriptions/' . $object['subscription']);
                    stripeUpdateProfileSubscription($subscription);
                } catch (Throwable $inner) {
                    error_log('Stripe webhook payment_failed fetch failed: ' . $inner->getMessage());
                }
            }
            break;

        default:
            // Unsupported events are ignored but acknowledged
            break;
    }
} catch (Throwable $e) {
    error_log('Stripe webhook handler error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Webhook error';
    exit;
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['received' => true]);
