<?php
require_once __DIR__ . '/php/helpers.php';
requireAuth();
$userId = getUserId();
$error = getFlash('error');
$success = getFlash('success');
$currentSectionId = 'professional-summary';

$summary = db()->fetchOne("SELECT * FROM professional_summary WHERE profile_id = ?", [$userId]);
$strengths = [];
if ($summary) {
    $strengths = db()->fetchAll("SELECT * FROM professional_summary_strengths WHERE professional_summary_id = ? ORDER BY sort_order ASC", [$summary['id']]);
}
$subscriptionContext = getUserSubscriptionContext($userId);
$canAddStrength = planCanAddEntry($subscriptionContext, 'summary_strengths', $userId, count($strengths));

if (isPost()) {
    $token = post(CSRF_TOKEN_NAME);
    if (!verifyCsrfToken($token)) {
        setFlash('error', 'Invalid security token.');
        redirect('/professional-summary.php');
    }
    $action = post('action');

    if ($action === 'save') {
        // For text descriptions, strip tags but don't HTML-encode (entities are for display, not storage)
        $description = trim(post('description', ''));

        // Check for XSS
        if (!empty($description) && checkForXss($description)) {
            setFlash('error', 'Invalid content in description');
            redirect('/professional-summary.php');
        }

        $description = strip_tags($description);
        if ($description && planWordLimitExceeded($subscriptionContext, 'summary_description', $description)) {
            setFlash('error', getPlanWordLimitMessage($subscriptionContext, 'summary_description'));
            redirect('/professional-summary.php');
        }
        $description = $description ?: null;

        try {
            if ($summary) {
                db()->update('professional_summary', ['description' => $description, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$summary['id']]);
            } else {
                db()->insert('professional_summary', [
                    'id' => generateUuid(),
                    'profile_id' => $userId,
                    'description' => $description,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $summary = db()->fetchOne("SELECT * FROM professional_summary WHERE profile_id = ?", [$userId]);
            }
            setFlash('success', 'Professional summary saved successfully');
        } catch (Exception $e) {
            error_log("Professional summary save error: " . $e->getMessage());
            setFlash('error', 'Failed to save professional summary. Please try again.');
        }
        redirect('/professional-summary.php');
    } elseif ($action === 'add_strength') {
        if (!$summary) {
            $summaryId = generateUuid();
            db()->insert('professional_summary', [
                'id' => $summaryId,
                'profile_id' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $summary = ['id' => $summaryId];
        }
        if (!planCanAddEntry($subscriptionContext, 'summary_strengths', $userId)) {
            setFlash('error', getPlanLimitMessage($subscriptionContext, 'summary_strengths'));
            redirect('/subscription.php');
        }
        // For strengths, strip tags but don't HTML-encode
        $strength = trim(post('strength', ''));

        if (empty($strength)) {
            setFlash('error', 'Strength is required');
            redirect('/professional-summary.php');
        }

        // Check for XSS
        if (checkForXss($strength)) {
            setFlash('error', 'Invalid content in strength');
            redirect('/professional-summary.php');
        }

        // Length validation
        if (strlen($strength) > 255) {
            setFlash('error', 'Strength must be 255 characters or less');
            redirect('/professional-summary.php');
        }

        $strength = strip_tags($strength);

        try {
            $maxOrder = db()->fetchOne("SELECT MAX(sort_order) as max_order FROM professional_summary_strengths WHERE professional_summary_id = ?", [$summary['id']]);
            db()->insert('professional_summary_strengths', [
                'id' => generateUuid(),
                'professional_summary_id' => $summary['id'],
                'strength' => $strength,
                'sort_order' => ($maxOrder['max_order'] ?? 0) + 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            setFlash('success', 'Strength added successfully');
        } catch (Exception $e) {
            error_log("Strength addition error: " . $e->getMessage());
            setFlash('error', 'Failed to add strength. Please try again.');
        }
        redirect('/professional-summary.php');
    } elseif ($action === 'delete_strength') {
        $id = post('id');
        try {
            db()->delete('professional_summary_strengths', 'id = ? AND professional_summary_id = ?', [$id, $summary['id']]);
            setFlash('success', 'Strength deleted successfully');
        } catch (Exception $e) {
            error_log("Strength deletion error: " . $e->getMessage());
            setFlash('error', 'Failed to delete strength. Please try again.');
        }
        redirect('/professional-summary.php');
    }
}

// Reload data after updates
if ($summary) {
    $strengths = db()->fetchAll("SELECT * FROM professional_summary_strengths WHERE professional_summary_id = ? ORDER BY sort_order ASC", [$summary['id']]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => 'Professional Summary | Simple CV Builder',
        'metaDescription' => 'Create and manage your professional summary and key strengths.',
        'canonicalUrl' => APP_URL . '/professional-summary.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>
    <?php partial('section-nav', ['currentSectionId' => $currentSectionId]); ?>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Professional Summary</h1>
        <?php if ($error): ?>
            <div class="mb-6 rounded-md bg-red-50 p-4"><p class="text-sm font-medium text-red-800"><?php echo e($error); ?></p></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4"><p class="text-sm font-medium text-green-800"><?php echo e($success); ?></p></div>
        <?php endif; ?>
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Professional Summary Description</h2>
            <form method="POST">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                <input type="hidden" name="action" value="save">
                <div><label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <?php $summaryWordLimit = planWordLimit($subscriptionContext, 'summary_description'); ?>
                    <?php if ($summaryWordLimit): ?>
                        <p class="mt-1 text-xs text-gray-500">Free plan summaries are limited to <?php echo $summaryWordLimit; ?> words.</p>
                    <?php endif; ?>
                    <textarea id="description" name="description" rows="6" maxlength="5000" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"><?php echo e($summary['description'] ?? ''); ?></textarea></div>
                <div class="mt-6"><button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Save Summary</button></div>
            </form>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Strengths</h2>
            <form method="POST" class="mb-4">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                <input type="hidden" name="action" value="add_strength">
                <div class="flex gap-2">
                    <input type="text" name="strength" placeholder="Add a strength" required maxlength="255" class="flex-1 px-3 py-2 border border-gray-300 rounded-md">
                    <button type="submit" <?php echo !$canAddStrength ? 'disabled' : ''; ?> class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 <?php echo !$canAddStrength ? 'opacity-60 cursor-not-allowed' : ''; ?>">Add</button>
                </div>
            </form>
            <?php if (!$canAddStrength): ?>
                <div class="mb-4 rounded-md bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
                    <?php echo getPlanLimitMessage($subscriptionContext, 'summary_strengths'); ?>
                </div>
            <?php endif; ?>
            <?php if (empty($strengths)): ?>
                <p class="text-gray-500">No strengths added yet.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($strengths as $strength): ?>
                        <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                            <span><?php echo e($strength['strength']); ?></span>
                            <form method="POST" class="inline">
                                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                                <input type="hidden" name="action" value="delete_strength">
                                <input type="hidden" name="id" value="<?php echo e($strength['id']); ?>">
                                <button type="submit" onclick="return confirm('Delete this strength?');" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php partial('footer'); ?>
</body>
</html>
