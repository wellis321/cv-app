<?php
/**
 * Create Organisation Page
 * Allows users to create a new recruitment agency organisation
 */

require_once __DIR__ . '/php/helpers.php';

requireAuth();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Check if user already has an organisation
$existingOrg = getUserOrganisation();
if ($existingOrg) {
    setFlash('error', 'You are already a member of an organisation. Please leave your current organisation first.');
    redirect('/agency/dashboard.php');
}

// Handle form submission
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/create-organisation.php');
    }

    $name = sanitizeInput(post('name'));
    $slug = sanitizeInput(post('slug'));

    // Validate name
    if (empty($name) || strlen($name) < 2) {
        setFlash('error', 'Organisation name must be at least 2 characters.');
        redirect('/create-organisation.php');
    }

    // Generate slug if not provided
    if (empty($slug)) {
        $slug = generateOrganisationSlug($name);
    } else {
        $slug = strtolower(preg_replace('/[^a-z0-9\-]/', '', $slug));
    }

    if (strlen($slug) < 3) {
        setFlash('error', 'URL slug must be at least 3 characters.');
        redirect('/create-organisation.php');
    }

    // Check if slug is available
    if (!isSlugAvailable($slug)) {
        setFlash('error', 'This URL slug is already taken. Please choose another.');
        redirect('/create-organisation.php');
    }

    // Create the organisation
    $result = createOrganisation($name, $slug, getUserId());

    if ($result['success']) {
        setFlash('success', 'Your organisation has been created successfully! Start by inviting your first candidate.');
        redirect('/agency/dashboard.php');
    } else {
        setFlash('error', $result['error']);
        redirect('/create-organisation.php');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Create Organisation | CV Builder',
        'metaDescription' => 'Create your recruitment agency organisation to manage candidate CVs.',
        'canonicalUrl' => APP_URL . '/create-organisation.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Error/Success Messages -->
            <?php if ($error): ?>
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
                </div>
            <?php endif; ?>

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create Your Organisation</h1>
                <p class="mt-2 text-gray-600">
                    Set up your recruitment agency to manage candidate CVs in one place.
                </p>
            </div>

            <div class="bg-white shadow rounded-lg">
                <form method="POST" class="p-6 space-y-6">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Organisation Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               required
                               minlength="2"
                               placeholder="Acme Recruiting"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               oninput="generateSlug(this.value)">
                        <p class="mt-1 text-xs text-gray-500">Your company or agency name.</p>
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">
                            URL Slug <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                                <?php echo e(parse_url(APP_URL, PHP_URL_HOST)); ?>/agency/
                            </span>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   required
                                   pattern="[a-z0-9\-]+"
                                   minlength="3"
                                   placeholder="acme-recruiting"
                                   class="block w-full flex-1 rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens. This will be your organisation's URL.</p>
                    </div>

                    <!-- Plan Info -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">Free Trial Included</h3>
                        <p class="text-sm text-blue-700">
                            Your organisation will start on the <strong>Basic</strong> plan which includes:
                        </p>
                        <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                            <li>Up to 10 candidate profiles</li>
                            <li>3 team members</li>
                            <li>Standard CV templates</li>
                            <li>PDF exports</li>
                        </ul>
                        <p class="mt-3 text-xs text-blue-600">
                            Upgrade anytime to add more candidates and unlock premium features.
                        </p>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="/dashboard.php"
                           class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Create Organisation
                        </button>
                    </div>
                </form>
            </div>

            <!-- Benefits -->
            <div class="mt-12">
                <h2 class="text-lg font-medium text-gray-900 mb-6 text-center">Why Create an Organisation?</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">Manage Candidates</h3>
                        <p class="mt-2 text-xs text-gray-500">Invite candidates and manage their CVs from one central dashboard.</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">Team Collaboration</h3>
                        <p class="mt-2 text-xs text-gray-500">Add recruiters and admins to help manage your candidate pipeline.</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">Custom Branding</h3>
                        <p class="mt-2 text-xs text-gray-500">Add your logo and brand colours to candidate CVs.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>

    <script>
        function generateSlug(name) {
            const slug = name
                .toLowerCase()
                .replace(/[\s_]+/g, '-')
                .replace(/[^a-z0-9\-]/g, '')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');

            document.getElementById('slug').value = slug;
        }
    </script>
</body>
</html>
