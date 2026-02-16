<?php
/**
 * Main entry point - Home page
 * Also acts as router for PHP built-in server
 */

require_once __DIR__ . '/php/helpers.php';

// If this is a request for an existing PHP file (not the homepage), serve it directly
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Handle CV routes: /cv/@username -> cv.php?username=username
// This is needed for PHP built-in server which doesn't process .htaccess
if (preg_match('#^/cv/@([a-z0-9][a-z0-9\-_]+)$#', $requestPath, $matches)) {
    $_GET['username'] = $matches[1];
    require __DIR__ . '/cv.php';
    exit;
}

// Handle CV routes: /cv/userid -> cv.php?userid=userid (for backward compatibility)
if (preg_match('#^/cv/([a-f0-9\-]{36})$#', $requestPath, $matches)) {
    $_GET['userid'] = $matches[1];
    require __DIR__ . '/cv.php';
    exit;
}

// Handle organisation public pages: /agency/{slug} -> agency-public.php?slug={slug}
if (preg_match('#^/agency/([a-z0-9\-]+)$#', $requestPath, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/agency-public.php';
    exit;
}

// Public pricing page: /pricing -> pricing.php
if ($requestPath === '/pricing') {
    require __DIR__ . '/pricing.php';
    exit;
}

// Quick-add job (bookmarklet target): /quick-add-job -> quick-add-job.php
if ($requestPath === '/quick-add-job') {
    require __DIR__ . '/quick-add-job.php';
    exit;
}

// Sitemap for SEO (PHP built-in server doesn't use .htaccess)
if ($requestPath === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}

// Don't process as homepage if it's a specific file request
if ($requestPath !== '/' && $requestPath !== '/index.php' && $requestPath !== '') {
    // Check if the requested file exists
    $filePath = __DIR__ . $requestPath;
    if (file_exists($filePath) && is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
        // Serve the file directly
        require $filePath;
        exit;
    }
}

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

        $registerRedirect = sanitizeInput(post('redirect', ''));
        $result = registerUser($email, $password, $fullName, $registerRedirect ?: null);

        if ($result['success']) {
            logAuthAttempt('register', $email, true);
            setFlash('success', $result['message'] ?? 'Registration successful! Please check your email to verify your account.');
            $target = !empty($registerRedirect) ? '/?redirect=' . urlencode($registerRedirect) : '/';
            redirect($target);
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
            $redirect = post('redirect', '/profile.php');
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
            $loginRedirect = post('redirect', '');
            $target = !empty($loginRedirect) ? '/?redirect=' . urlencode($loginRedirect) : '/';
            redirect($target);
        }
    }
}

// Redirect logged-in users (only if we're on the homepage)
// Don't redirect if we're already on a specific page to avoid redirect loops
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$isHomepage = ($requestPath === '/' || $requestPath === '/index.php' || empty($requestPath) || $requestPath === '');
if (isLoggedIn() && $isHomepage) {
    $redirectTo = get('redirect', '');
    redirect(!empty($redirectTo) ? $redirectTo : '/dashboard.php');
}

// Get user data if logged in (with error handling)
$user = null;
if (isLoggedIn()) {
    try {
        $user = getCurrentUser();
    } catch (Exception $e) {
        // Log error but don't break the page
        error_log("Error getting current user: " . $e->getMessage());
        // Clear invalid session
        session_unset();
        session_destroy();
        session_start();
    }
}
$error = getFlash('error') ?: null;
$success = getFlash('success') ?: null;
$needsVerification = getFlash('needs_verification') ?: false;
$verificationEmail = getFlash('verification_email') ?: null;
$oldLoginEmail = getFlash('old_login_email') ?: null;

?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php partial('head', [
        'pageTitle' => 'Simple CV Builder | Free CV Maker UK, Job Tracker & AI Cover Letters',
        'useHomeCss' => true,
        'metaDescription' => 'Free CV builder UK with job application tracking and AI-powered cover letters. Build your CV online, share a link, and export PDFs. For job seekers and recruitment agencies.',
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
                'oldLoginEmail' => $oldLoginEmail,
                'redirect' => get('redirect', '')
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
