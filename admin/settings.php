<?php
/**
 * Super Admin - System Settings
 * System-wide configuration and settings
 */

require_once __DIR__ . '/../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Get system statistics
$stats = getSystemStatistics();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'System Settings | Super Admin',
        'metaDescription' => 'System configuration',
        'canonicalUrl' => APP_URL . '/admin/settings.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>

    <main id="main-content" class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
                <p class="mt-1 text-sm text-gray-500">System-wide configuration and information</p>
            </div>

            <!-- System Information -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">System Information</h2>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Organisations</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['total_organisations']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Active Subscriptions</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['active_subscriptions']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Users</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['total_users']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Individual Users</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['individual_users']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Candidates</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['candidates']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Organisation Members</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['organisation_members']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Super Admins</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo $stats['super_admins']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?php echo PHP_VERSION; ?></dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <a href="/admin/organisations.php" class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Manage Organisations</span>
                    </a>
                    <a href="/admin/users.php" class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Manage Users</span>
                    </a>
                    <a href="/admin/activity.php" class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="h-6 w-6 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">View Activity Log</span>
                    </a>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Super admin accounts should only be created directly in the database</li>
                                <li>All super admin actions are logged in the activity log</li>
                                <li>Super admins have access to all organisations and users</li>
                                <li>Use caution when modifying organisation settings or user accounts</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>

