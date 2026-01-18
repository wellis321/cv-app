<?php
/**
 * Email Verification Page
 */

require_once __DIR__ . '/php/helpers.php';

$token = get('token', '');
$error = null;
$success = null;

if (!empty($token)) {
    $db = db();

    // Find user with this verification token
    $user = $db->fetchOne(
        "SELECT id, email, email_verified, email_verification_expires FROM profiles
         WHERE email_verification_token = ?",
        [$token]
    );

    if (!$user) {
        $error = 'Invalid verification token.';
    } elseif (!empty($user['email_verified']) && $user['email_verified'] == 1) {
        $success = 'Your email has already been verified. You can now log in.';
    } elseif (strtotime($user['email_verification_expires']) < time()) {
        $error = 'This verification link has expired. Please request a new verification email.';
    } else {
        // Verify the email
        try {
            $db->update('profiles', [
                'email_verified' => 1,
                'email_verification_token' => null,
                'email_verification_expires' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$user['id']]);

            $success = 'Email verified successfully! You can now log in.';
        } catch (Exception $e) {
            $error = 'An error occurred during verification. Please try again.';
            if (DEBUG) {
                error_log("Verification error: " . $e->getMessage());
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
    <title>Email Verification - CV App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Email Verification</h1>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <p><?php echo e($error); ?></p>
                </div>
                <div class="text-center">
                    <a href="/" class="text-blue-600 hover:text-blue-800">Return to Home</a>
                </div>
            <?php elseif ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    <p><?php echo e($success); ?></p>
                </div>
                <div class="text-center">
                    <a href="/" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Go to Login
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center text-gray-600">
                    <p>Please check your email for the verification link.</p>
                    <p class="mt-4">
                        <a href="/" class="text-blue-600 hover:text-blue-800">Return to Home</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
