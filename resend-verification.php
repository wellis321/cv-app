<?php
/**
 * Resend Verification Email
 */

require_once __DIR__ . '/php/helpers.php';

$email = get('email', '');
$error = null;
$success = null;

if (isPost()) {
    // Verify CSRF token
    $token = post(CSRF_TOKEN_NAME);
    if (!verifyCsrfToken($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = sanitizeInput(post('email', ''));

        if (empty($email) || !validateEmail($email)) {
            $error = 'Invalid email address';
        } else {
            $db = db();
            $user = $db->fetchOne(
                "SELECT id, email, full_name, email_verified FROM profiles WHERE email = ?",
                [$email]
            );

            if (!$user) {
                $error = 'No account found with this email address.';
            } elseif (!empty($user['email_verified']) && $user['email_verified'] == 1) {
                $success = 'Your email is already verified. You can log in.';
            } else {
                // Generate new verification token
                require_once __DIR__ . '/php/utils.php';
                $verificationToken = bin2hex(random_bytes(32));
                $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

                try {
                    $db->update('profiles', [
                        'verification_token' => $verificationToken,
                        'verification_token_expires_at' => $verificationExpires,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], 'id = ?', [$user['id']]);

                    // Send verification email
                    require_once __DIR__ . '/php/email.php';
                    $emailSent = sendVerificationEmail($user['email'], $user['full_name'], $verificationToken);

                    if ($emailSent) {
                        $success = 'Verification email sent! Please check your inbox.';
                    } else {
                        $error = 'Failed to send verification email. Please try again later.';
                    }
                } catch (Exception $e) {
                    $error = 'An error occurred. Please try again.';
                    if (DEBUG) {
                        error_log("Resend verification error: " . $e->getMessage());
                    }
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Email - CV App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Resend Verification Email</h1>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <p><?php echo e($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    <p><?php echo e($success); ?></p>
                </div>
                <div class="text-center">
                    <a href="/" class="text-blue-600 hover:text-blue-800">Return to Home</a>
                </div>
            <?php else: ?>
                <form method="POST" action="/resend-verification.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="<?php echo e($email); ?>"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Enter the email address you used to register</p>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Resend Verification Email
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="/" class="text-blue-600 hover:text-blue-800">Return to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
