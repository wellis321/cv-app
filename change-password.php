<?php
/**
 * Change Password Page (logged-in users)
 */

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/auth.php';

requireAuth();

$error = null;
$success = null;

if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $currentPassword = post('current_password', '');
        $newPassword = post('new_password', '');
        $confirmPassword = post('confirm_password', '');

        if ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } else {
            $result = changePasswordForUser(getUserId(), $currentPassword, $newPassword);
            if ($result['success']) {
                $success = 'Your password has been updated successfully.';
            } else {
                $error = $result['error'];
            }
        }
    }
}

$user = getCurrentUser();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - CV App</title>
    <link rel="stylesheet" href="/static/css/tailwind.css">
</head>
<body class="bg-gray-50">
<?php partial('header'); ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Change Password</h1>
        <p class="mb-6 text-sm text-gray-600">Logged in as <?php echo e($user['email'] ?? ''); ?></p>

        <?php if ($error): ?>
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                <?php echo e($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/change-password.php" class="space-y-6">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password"
                       id="new_password"
                       name="new_password"
                       required
                       minlength="8"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">Must contain at least 8 characters, including uppercase, lowercase, and a number.</p>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       required
                       minlength="8"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-between">
                <a href="/profile.php" class="text-sm text-blue-600 hover:text-blue-800">Back to profile</a>
                <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php partial('footer'); ?>
</body>
</html>
