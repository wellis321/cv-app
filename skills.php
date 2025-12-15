<?php
require_once __DIR__ . '/php/helpers.php';
requireAuth();
$userId = getUserId();
$error = getFlash('error');
$success = getFlash('success');
$currentSectionId = 'skills';

$skills = db()->fetchAll("SELECT * FROM skills WHERE profile_id = ? ORDER BY category ASC, name ASC", [$userId]);
$subscriptionContext = getUserSubscriptionContext($userId);
$canAddSkill = planCanAddEntry($subscriptionContext, 'skills', $userId, count($skills));

if (isPost()) {
    $token = post(CSRF_TOKEN_NAME);
    if (!verifyCsrfToken($token)) {
        setFlash('error', 'Invalid security token.');
        redirect('/skills.php');
    }
    $action = post('action');

    if ($action === 'create') {
        if (!planCanAddEntry($subscriptionContext, 'skills', $userId)) {
            setFlash('error', getPlanLimitMessage($subscriptionContext, 'skills'));
            redirect('/subscription.php');
        }

        $name = sanitizeInput(post('name', ''));
        $level = sanitizeInput(post('level', ''));
        $category = sanitizeInput(post('category', ''));

        // Validate name
        if (empty($name)) {
            setFlash('error', 'Skill name is required');
            redirect('/skills.php');
        }

        // Check for XSS
        if (checkForXss($name)) {
            setFlash('error', 'Invalid content in skill name');
            redirect('/skills.php');
        }

        if (!empty($level) && checkForXss($level)) {
            setFlash('error', 'Invalid content in skill level');
            redirect('/skills.php');
        }

        if (!empty($category) && checkForXss($category)) {
            setFlash('error', 'Invalid content in skill category');
            redirect('/skills.php');
        }

        // Length validation
        if (strlen($name) > 255) {
            setFlash('error', 'Skill name must be 255 characters or less');
            redirect('/skills.php');
        }

        if (!empty($level) && strlen($level) > 50) {
            setFlash('error', 'Skill level must be 50 characters or less');
            redirect('/skills.php');
        }

        if (!empty($category) && strlen($category) > 100) {
            setFlash('error', 'Skill category must be 100 characters or less');
            redirect('/skills.php');
        }

        $data = [
            'id' => generateUuid(),
            'profile_id' => $userId,
            'name' => $name,
            'level' => !empty($level) ? $level : null,
            'category' => !empty($category) ? $category : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            db()->insert('skills', $data);
            setFlash('success', 'Skill added successfully');
        } catch (Exception $e) {
            error_log("Skill creation error: " . $e->getMessage());
            setFlash('error', 'Failed to add skill. Please try again.');
        }
        redirect('/skills.php');
    } elseif ($action === 'delete') {
        $id = post('id');
        try {
            db()->delete('skills', 'id = ? AND profile_id = ?', [$id, $userId]);
            setFlash('success', 'Skill deleted successfully');
        } catch (Exception $e) {
            error_log("Skill deletion error: " . $e->getMessage());
            setFlash('error', 'Failed to delete skill. Please try again.');
        }
        redirect('/skills.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Skills | Simple CV Builder',
        'metaDescription' => 'Manage your skills and expertise.',
        'canonicalUrl' => APP_URL . '/skills.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>
    <?php partial('section-nav', ['currentSectionId' => $currentSectionId]); ?>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Skills</h1>
        <?php if ($error): ?>
            <div class="mb-6 rounded-md bg-red-50 p-4"><p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4"><p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p></div>
        <?php endif; ?>
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Add Skill</h2>
            <?php if (!$canAddSkill): ?>
                <div class="mb-4 rounded-md bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
                    <?php echo getPlanLimitMessage($subscriptionContext, 'skills'); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                <input type="hidden" name="action" value="create">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div><label for="name" class="block text-sm font-medium text-gray-700">Skill Name *</label>
                        <input type="text" id="name" name="name" required maxlength="255" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></div>
                    <div><label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <input type="text" id="category" name="category" placeholder="e.g., Technical, Soft Skills" maxlength="100" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></div>
                    <div><label for="level" class="block text-sm font-medium text-gray-700">Level</label>
                        <input type="text" id="level" name="level" placeholder="e.g., Beginner, Intermediate, Expert" maxlength="50" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></div>
                </div>
                <div class="mt-6"><button type="submit" <?php echo !$canAddSkill ? 'disabled' : ''; ?> class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 <?php echo !$canAddSkill ? 'opacity-60 cursor-not-allowed' : ''; ?>">Add Skill</button></div>
            </form>
        </div>
        <?php if (empty($skills)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">No skills added yet.</div>
        <?php else: ?>
            <?php
            $skillsByCategory = [];
            foreach ($skills as $skill) {
                $cat = $skill['category'] ?: 'Other';
                if (!isset($skillsByCategory[$cat])) $skillsByCategory[$cat] = [];
                $skillsByCategory[$cat][] = $skill;
            }
            foreach ($skillsByCategory as $category => $categorySkills): ?>
                <div class="bg-white shadow rounded-lg p-6 mb-4">
                    <h3 class="text-lg font-semibold mb-3"><?php echo e($category); ?></h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($categorySkills as $skill): ?>
                            <span class="inline-flex items-center bg-gray-100 px-3 py-1 rounded-md">
                                <span class="text-gray-700"><?php echo e($skill['name']); ?></span>
                                <?php if ($skill['level']): ?><span class="ml-2 text-xs text-gray-500">(<?php echo e($skill['level']); ?>)</span><?php endif; ?>
                                <form method="POST" class="inline ml-2">
                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo e($skill['id']); ?>">
                                    <button type="submit" onclick="return confirm('Delete this skill?');" class="text-red-600 hover:text-red-800 text-xs">×</button>
                                </form>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php partial('footer'); ?>
</body>
</html>
