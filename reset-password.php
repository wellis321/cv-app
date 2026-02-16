<?php
/**
 * Reset Password Page
 */

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/auth.php';

$token = get('token', '');
$error = null;
$success = null;
$tokenValid = false;

if (!empty($token)) {
    $user = db()->fetchOne(
        "SELECT id, password_reset_expires_at FROM profiles WHERE password_reset_token = ?",
        [$token]
    );

    if ($user && !empty($user['password_reset_expires_at']) && strtotime($user['password_reset_expires_at']) > time()) {
        $tokenValid = true;
    }
}

if (isPost()) {
    $token = post('token', '');
    $tokenValid = false;

    if (empty($token)) {
        $error = 'Reset token missing. Please use the link from your email.';
    } elseif (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $newPassword = post('password', '');
        $confirmPassword = post('password_confirm', '');

        if ($newPassword !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            $result = resetPasswordWithToken($token, $newPassword);
            if ($result['success']) {
                $success = 'Your password has been reset. You can now log in with your new password.';
            } else {
                $error = $result['error'];
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
    <title>Set a New Password - CV App</title>
    <link rel="stylesheet" href="/static/css/tailwind.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Set a New Password</h1>

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
                    <a href="/" class="text-blue-600 hover:text-blue-800">Return to login</a>
                </div>
            <?php elseif (!$tokenValid && !$error && empty($success)): ?>
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded mb-4">
                    <p>The reset link is invalid or has expired. Please request a new link.</p>
                </div>
                <div class="text-center">
                    <a href="/forgot-password.php" class="text-blue-600 hover:text-blue-800">Request a new reset link</a>
                </div>
            <?php else: ?>
                <form method="POST" action="/reset-password.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                    <input type="hidden" name="token" value="<?php echo e($token); ?>">

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Password must have at least 8 characters, including uppercase, lowercase, and a number.</p>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password"
                               id="password_confirm"
                               name="password_confirm"
                               required
                               minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
