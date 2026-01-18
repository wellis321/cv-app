<?php
/**
 * Create Organisation
 * Standardized form page for creating organisations (Super Admin)
 */

require_once __DIR__ . '/../../php/helpers.php';

// Require super admin access
requireSuperAdmin();

$user = getCurrentUser();
$error = getFlash('error');
$success = getFlash('success');

// Handle form submission
if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect('/admin/organisations/create.php');
    }

    $name = sanitizeInput(post('name') ?? '');
    $slug = sanitizeInput(post('slug') ?? '');
    $plan = sanitizeInput(post('plan') ?? 'agency_basic');
    $subscriptionStatus = sanitizeInput(post('subscription_status') ?? 'inactive');
    $maxCandidates = (int)(post('max_candidates') ?? 10);
    $maxTeamMembers = (int)(post('max_team_members') ?? 3);
    $ownerEmail = sanitizeInput(post('owner_email') ?? '');
    
    // Validate
    if (empty($name) || strlen($name) < 2) {
        setFlash('error', 'Organisation name must be at least 2 characters.');
        redirect('/admin/organisations/create.php');
    }
    
    // Generate slug if not provided
    if (empty($slug)) {
        $slug = generateOrganisationSlug($name);
    } else {
        $slug = strtolower(preg_replace('/[^a-z0-9\-]/', '', $slug));
    }
    
    if (strlen($slug) < 3) {
        setFlash('error', 'URL slug must be at least 3 characters.');
        redirect('/admin/organisations/create.php');
    }
    
    // Check if slug is available
    if (!isSlugAvailable($slug)) {
        setFlash('error', 'This URL slug is already taken. Please choose another.');
        redirect('/admin/organisations/create.php');
    }
    
    try {
        $db = db();
        $db->beginTransaction();
        
        $organisationId = generateUuid();
        
        // Create the organisation
        $db->insert('organisations', [
            'id' => $organisationId,
            'name' => $name,
            'slug' => $slug,
            'plan' => $plan,
            'subscription_status' => $subscriptionStatus,
            'max_candidates' => $maxCandidates,
            'max_team_members' => $maxTeamMembers,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // If owner email provided, add them as owner
        if (!empty($ownerEmail)) {
            $owner = db()->fetchOne("SELECT id FROM profiles WHERE email = ?", [$ownerEmail]);
            if ($owner) {
                $db->insert('organisation_members', [
                    'id' => generateUuid(),
                    'organisation_id' => $organisationId,
                    'user_id' => $owner['id'],
                    'role' => 'owner',
                    'is_active' => 1,
                    'joined_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        $db->commit();
        
        logActivity('admin.organisation.created', null, ['organisation_id' => $organisationId, 'name' => $name, 'slug' => $slug], null);
        setFlash('success', 'Organisation created successfully.');
        redirect('/admin/organisations.php?id=' . $organisationId);
    } catch (Exception $e) {
        $db->rollback();
        error_log("Failed to create organisation: " . $e->getMessage());
        setFlash('error', 'Failed to create organisation. Please try again.');
        redirect('/admin/organisations/create.php');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Create Organisation | Super Admin',
        'metaDescription' => 'Create a new organisation',
        'canonicalUrl' => APP_URL . '/admin/organisations/create.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('admin/header'); ?>

    <main id="main-content" class="py-6">
        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <?php ob_start(); ?>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">

            <?php partial('forms/form-field', [
                'type' => 'text',
                'name' => 'name',
                'label' => 'Organisation Name',
                'required' => true,
                'min' => 2,
                'help' => 'The organisation name'
            ]); ?>

            <?php partial('forms/form-field', [
                'type' => 'text',
                'name' => 'slug',
                'id' => 'slug',
                'label' => 'URL Slug',
                'required' => true,
                'pattern' => '[a-z0-9\-]+',
                'min' => 3,
                'help' => 'Only lowercase letters, numbers, and hyphens. Will be used in the URL: ' . parse_url(APP_URL, PHP_URL_HOST) . '/agency/[slug]',
                'classes' => 'font-mono'
            ]); ?>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <?php partial('forms/form-field', [
                    'type' => 'select',
                    'name' => 'plan',
                    'label' => 'Plan',
                    'options' => [
                        'agency_basic' => 'Basic',
                        'agency_pro' => 'Professional',
                        'agency_enterprise' => 'Enterprise'
                    ],
                    'value' => 'agency_basic'
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'select',
                    'name' => 'subscription_status',
                    'label' => 'Subscription Status',
                    'options' => [
                        'inactive' => 'Inactive',
                        'active' => 'Active',
                        'cancelled' => 'Cancelled'
                    ],
                    'value' => 'inactive'
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'number',
                    'name' => 'max_candidates',
                    'label' => 'Max Candidates',
                    'value' => 10,
                    'min' => 1
                ]); ?>

                <?php partial('forms/form-field', [
                    'type' => 'number',
                    'name' => 'max_team_members',
                    'label' => 'Max Team Members',
                    'value' => 3,
                    'min' => 1
                ]); ?>
            </div>

            <?php partial('forms/form-field', [
                'type' => 'email',
                'name' => 'owner_email',
                'label' => 'Owner Email (Optional)',
                'placeholder' => 'user@example.com',
                'help' => 'If provided, this user will be added as the organisation owner'
            ]); ?>

            <?php partial('forms/form-actions', [
                'submitText' => 'Create Organisation',
                'cancelUrl' => '/admin/organisations.php',
                'cancelText' => 'Cancel'
            ]); ?>
        </form>
        <?php $formContent = ob_get_clean(); ?>

        <?php partial('forms/form-card', [
            'title' => 'Create Organisation',
            'description' => 'Create a new organisation in the system.',
            'backUrl' => '/admin/organisations.php',
            'backText' => 'Back to organisations',
            'content' => $formContent
        ]); ?>
    </main>

    <script>
        // Auto-generate slug from name
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');
            
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
                        const slug = this.value
                            .toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .trim();
                        slugInput.value = slug;
                        slugInput.dataset.autoGenerated = 'true';
                    }
                });
                
                slugInput.addEventListener('input', function() {
                    this.dataset.autoGenerated = 'false';
                });
            }
        });
    </script>

    <?php partial('footer'); ?>
</body>
</html>

