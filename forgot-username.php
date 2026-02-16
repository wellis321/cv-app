<?php
/**
 * Forgot Username Page
 */

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/auth.php';

$email = get('email', '');
$error = null;
$success = null;

if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = sanitizeInput(post('email', ''));

        if (empty($email) || !validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            $result = sendUsernameReminder($email);
            if ($result['success']) {
                $success = 'If an account exists for that email address, your username has been sent.';
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
    <title>Forgot Username - CV App</title>
    <link rel="stylesheet" href="/static/css/tailwind.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Forgot Username</h1>

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
            <?php else: ?>
                <form method="POST" action="/forgot-username.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="<?php echo e($email); ?>"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Enter the email address associated with your account.</p>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Email my username
                    </button>
                </form>

                <div class="mt-4 text-center space-y-2 text-sm">
                    <a href="/" class="text-blue-600 hover:text-blue-800 block">Return to login</a>
                    <a href="/forgot-password.php" class="text-blue-600 hover:text-blue-800 block">Forgot password?</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
