<?php
/**
 * Newsletter signup API – stores emails for mailing list (updates, promotions).
 */

require_once __DIR__ . '/../php/helpers.php';

header('Content-Type: application/json');

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
$token = post(CSRF_TOKEN_NAME);
if (!verifyCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Rate limiting: max 5 signups per hour per IP
$rateLimitKey = 'newsletter_ip_' . getClientIp();
$rateLimit = checkRateLimit($rateLimitKey, 5, 3600);

if (!$rateLimit['allowed']) {
    $minutesRemaining = ceil(($rateLimit['reset_at'] - time()) / 60);
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => "Too many signup attempts. Please try again in {$minutesRemaining} minute(s)."
    ]);
    exit;
}

$email = trim(post('email', ''));
$source = sanitizeInput(post('source', 'blog'));

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please enter your email address.']);
    exit;
}

if (!validateEmail($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

try {
    $existing = db()->fetchOne(
        "SELECT id, unsubscribed_at FROM newsletter_subscribers WHERE email = ?",
        [strtolower($email)]
    );

    if ($existing) {
        if ($existing['unsubscribed_at']) {
            // Resubscribe
            db()->update(
                'newsletter_subscribers',
                [
                    'unsubscribed_at' => null,
                    'source' => $source,
                    'ip_address' => getClientIp(),
                ],
                'id = ?',
                [$existing['id']]
            );
        }
        // Already subscribed – return success (don’t reveal whether they were already on the list)
        echo json_encode([
            'success' => true,
            'message' => "You're on the list! We'll send you updates and promotions."
        ]);
        exit;
    }

    db()->insert('newsletter_subscribers', [
        'id' => generateUuid(),
        'email' => strtolower($email),
        'source' => $source,
        'ip_address' => getClientIp(),
    ]);

    echo json_encode([
        'success' => true,
        'message' => "You're on the list! We'll send you updates and promotions."
    ]);
} catch (Exception $e) {
    error_log("Newsletter signup error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Something went wrong. Please try again later.'
    ]);
}
