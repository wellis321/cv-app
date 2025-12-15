<?php
/**
 * Main entry point - Home page
 */

require_once __DIR__ . '/php/helpers.php';

// Handle login/register
if (isPost()) {
    // Verify CSRF token
    $token = post(CSRF_TOKEN_NAME);
    if (!verifyCsrfToken($token)) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/');
    }

    $action = post('action');
    $email = sanitizeInput(post('email', ''));
    $password = post('password', '');
    $ip = getClientIp();

    if ($action === 'register') {
        // Rate limiting: 3 registrations per IP per hour
        $rateLimitKey = 'register_ip_' . $ip;
        $rateLimit = checkRateLimit($rateLimitKey, 3, 3600);

        if (!$rateLimit['allowed']) {
            $minutesRemaining = ceil(($rateLimit['reset_at'] - time()) / 60);
            logAuthAttempt('register', $email, false, 'Rate limit exceeded');
            setFlash('error', "Too many registration attempts. Please try again in {$minutesRemaining} minute(s).");
            redirect('/');
        }

        $fullName = sanitizeInput(post('full_name', ''));
        $passwordConfirm = post('password_confirm', '');

        // Validation
        if (empty($email) || !validateEmail($email)) {
            logAuthAttempt('register', $email, false, 'Invalid email');
            setFlash('error', 'Invalid email address');
            redirect('/');
        }

        // Enhanced password strength validation
        $passwordValidation = validatePasswordStrength($password);
        if (!$passwordValidation['valid']) {
            logAuthAttempt('register', $email, false, 'Weak password');
            setFlash('error', implode('. ', $passwordValidation['errors']));
            redirect('/');
        }

        // Check password confirmation
        if ($password !== $passwordConfirm) {
            logAuthAttempt('register', $email, false, 'Password mismatch');
            setFlash('error', 'Passwords do not match');
            redirect('/');
        }

        $result = registerUser($email, $password, $fullName);

        if ($result['success']) {
            logAuthAttempt('register', $email, true);
            setFlash('success', $result['message'] ?? 'Registration successful! Please check your email to verify your account.');
            redirect('/');
        } else {
            logAuthAttempt('register', $email, false, $result['error']);
            setFlash('error', $result['error']);
            redirect('/');
        }
    } elseif ($action === 'login') {
        // Rate limiting: 5 login attempts per IP per 15 minutes
        $rateLimitKey = 'login_ip_' . $ip;
        $rateLimit = checkRateLimit($rateLimitKey, 5, 900);

        if (!$rateLimit['allowed']) {
            $minutesRemaining = ceil(($rateLimit['reset_at'] - time()) / 60);
            logAuthAttempt('login', $email, false, 'Rate limit exceeded');
            setFlash('error', "Too many login attempts. Please try again in {$minutesRemaining} minute(s).");
            redirect('/');
        }

        $result = loginUser($email, $password);

        if ($result['success']) {
            logAuthAttempt('login', $email, true);
            $redirect = get('redirect', '/profile.php');
            redirect($redirect);
        } else {
            $reason = $result['error'] ?? 'Invalid credentials';
            logAuthAttempt('login', $email, false, $reason);
            if (!empty($result['needs_verification'])) {
                setFlash('needs_verification', true);
                setFlash('verification_email', $result['email'] ?? $email);
            }
            setFlash('old_login_email', $email);
            setFlash('error', $result['error']);
            redirect('/');
        }
    }
}

// Redirect logged-in users to dashboard
if (isLoggedIn()) {
    redirect('/dashboard.php');
}

// Get user data if logged in
$user = isLoggedIn() ? getCurrentUser() : null;
$error = getFlash('error') ?: null;
$success = getFlash('success') ?: null;
$needsVerification = getFlash('needs_verification') ?: false;
$verificationEmail = getFlash('verification_email') ?: null;
$oldLoginEmail = getFlash('old_login_email') ?: null;

// Enable error reporting for debugging (remove in production)
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Simple CV Builder | Your CV, Reimagined',
        'metaDescription' => 'Create a modern CV that updates in real-time, share it instantly, and unlock premium templates with Simple CV Builder.',
        'canonicalUrl' => APP_URL . '/',
        'structuredDataType' => 'homepage',
    ]); ?>
</head>
<body>
    <?php partial('header'); ?>
    <main id="main-content" role="main">
        <!-- Marketing page for non-logged in users -->
        <?php
        try {
            partial('home', [
                'error' => $error,
                'success' => $success,
                'needsVerification' => $needsVerification,
                'verificationEmail' => $verificationEmail,
                'oldLoginEmail' => $oldLoginEmail
            ]);
        } catch (Exception $e) {
            echo '<div style="padding: 20px; background: #fee; color: #c00;">';
            echo '<h1>Error loading page</h1>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
    </main>
    <?php partial('footer'); ?>
</body>
</html>
