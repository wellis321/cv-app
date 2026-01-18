<?php
/**
 * Accept Invitation Page
 * Handles both candidate and team member invitations
 */

require_once __DIR__ . '/php/helpers.php';

$error = getFlash('error');
$success = getFlash('success');

$token = sanitizeInput(get('token') ?? '');
$type = sanitizeInput(get('type') ?? 'candidate');

if (empty($token)) {
    setFlash('error', 'Invalid invitation link. Please check the link and try again.');
    redirect('/');
}

// Validate the invitation
if ($type === 'team') {
    $validation = validateTeamInvitation($token);
} else {
    $validation = validateCandidateInvitation($token);
}

if (!$validation['valid']) {
    setFlash('error', $validation['error']);
    redirect('/');
}

$invitation = $validation['invitation'];

// If user is already logged in, handle differently
$isLoggedIn = isLoggedIn();
$currentUser = $isLoggedIn ? getCurrentUser() : null;

// Handle form submission
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/accept-invitation.php?token=' . urlencode($token) . '&type=' . $type);
    }

    $password = post('password');
    $fullName = sanitizeInput(post('full_name'));

    // Accept invitation
    if ($type === 'team') {
        $result = acceptTeamInvitation($token, $password, $fullName);
    } else {
        $result = acceptCandidateInvitation($token, $password, $fullName);
    }

    if ($result['success']) {
        // Log the user in
        $_SESSION['user_id'] = $result['user_id'];
        session_regenerate_id(true);

        setFlash('success', 'Welcome! Your account has been set up successfully.');

        if ($type === 'team') {
            redirect('/agency/dashboard.php');
        } else {
            redirect('/dashboard.php');
        }
    } else {
        setFlash('error', $result['error']);
        redirect('/accept-invitation.php?token=' . urlencode($token) . '&type=' . $type);
    }
}

$pageTitle = $type === 'team' ? 'Join Team' : 'Accept Invitation';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | ' . e($invitation['organisation_name']),
        'metaDescription' => 'Accept your invitation to join ' . e($invitation['organisation_name']),
        'canonicalUrl' => APP_URL . '/accept-invitation.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h1 class="text-center text-3xl font-bold text-gray-900">
            <?php echo $type === 'team' ? 'Join the Team' : 'You\'re Invited!'; ?>
        </h1>
        <p class="mt-2 text-center text-sm text-gray-600">
            <?php if ($type === 'team'): ?>
                You've been invited to join <strong><?php echo e($invitation['organisation_name']); ?></strong>
                as <?php echo ucfirst($invitation['role']); ?>.
            <?php else: ?>
                <?php echo e($invitation['organisation_name']); ?> has invited you to create your professional CV.
            <?php endif; ?>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="mb-4 rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-4 rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if ($isLoggedIn && $currentUser['email'] === $invitation['email']): ?>
                <!-- User is logged in with matching email - just accept -->
                <form method="POST">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

                    <div class="text-center mb-6">
                        <p class="text-gray-700">
                            You're logged in as <strong><?php echo e($currentUser['email']); ?></strong>.
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            Click below to accept the invitation.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Accept Invitation
                    </button>
                </form>

            <?php elseif ($isLoggedIn): ?>
                <!-- User is logged in with different email -->
                <div class="text-center">
                    <div class="rounded-md bg-yellow-50 p-4 mb-6">
                        <p class="text-sm text-yellow-800">
                            You're logged in as <strong><?php echo e($currentUser['email']); ?></strong>, but this invitation
                            was sent to <strong><?php echo e($invitation['email']); ?></strong>.
                        </p>
                    </div>

                    <p class="text-gray-600 mb-4">Please log out and try again with the correct account, or contact the person who sent this invitation.</p>

                    <a href="/logout.php?redirect=<?php echo urlencode('/accept-invitation.php?token=' . $token . '&type=' . $type); ?>"
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Log out and try again
                    </a>
                </div>

            <?php else: ?>
                <!-- User not logged in - create account or log in -->
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

                    <div class="bg-gray-50 rounded-md p-4 mb-6">
                        <p class="text-sm text-gray-600">
                            <strong>Email:</strong> <?php echo e($invitation['email']); ?>
                        </p>
                        <?php if ($type === 'team'): ?>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>Role:</strong> <?php echo ucfirst($invitation['role']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">
                            Full Name
                        </label>
                        <input type="text"
                               name="full_name"
                               id="full_name"
                               required
                               value="<?php echo e($invitation['full_name'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Create Password
                        </label>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               minlength="8"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <input type="password"
                               name="password_confirm"
                               id="password_confirm"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <?php echo $type === 'team' ? 'Join Team' : 'Create Account & Get Started'; ?>
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-2 text-gray-500">Already have an account?</span>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="/index.php?redirect=<?php echo urlencode('/accept-invitation.php?token=' . $token . '&type=' . $type); ?>"
                           class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Log in to accept this invitation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <p class="mt-8 text-center text-xs text-gray-500">
            This invitation expires on <?php echo date('j F Y', strtotime($invitation['expires_at'])); ?>
        </p>
    </div>

    <script>
        // Password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirm');

            if (form && password && confirm) {
                form.addEventListener('submit', function(e) {
                    if (password.value !== confirm.value) {
                        e.preventDefault();
                        alert('Passwords do not match. Please try again.');
                        confirm.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>
