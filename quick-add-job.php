<?php
/**
 * Quick-add job: bookmarklet target and paste-link entry.
 * GET: url (required), title (optional), closing_date (optional Y-m-d), priority (optional low|medium|high).
 * Renders a confirmation form; on POST creates the job and redirects to content-editor#jobs&view=id.
 */

require_once __DIR__ . '/php/helpers.php';

requireAuth();

$userId = getUserId();
$error = null;
$successId = null;

if (isPost()) {
    if (!verifyCsrfToken(post(CSRF_TOKEN_NAME))) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $url = trim((string) post('url', ''));
        $title = trim((string) post('title', ''));
        $closingDate = trim((string) post('closing_date', ''));
        $priority = trim((string) post('priority', ''));
        if (empty($url)) {
            $error = 'Job URL is required.';
        } else {
            $data = [
                'quick_add' => true,
                'application_url' => $url,
                'job_title' => $title !== '' ? $title : deriveJobTitleFromUrl($url),
                'company_name' => '—',
                'status' => 'interested',
                'next_follow_up' => $closingDate !== '' ? $closingDate : null,
                'csrf_token' => csrfToken(),
            ];
            if (in_array($priority, ['low', 'medium', 'high'], true)) {
                $data['priority'] = $priority;
            }
            $result = createJobApplication($data, $userId);
            if ($result['success']) {
                $successId = $result['id'];
                setFlash('success', 'Job saved. You can add more details in your job list.');
                redirect('/content-editor.php#jobs&view=' . urlencode($result['id']));
                exit;
            }
            $error = $result['error'] ?? 'Failed to save job.';
        }
    }
}

// GET: pre-fill from query string
$url = isset($_GET['url']) ? trim((string) $_GET['url']) : (isPost() ? trim((string) post('url', '')) : '');
$title = isset($_GET['title']) ? trim((string) $_GET['title']) : (isPost() ? trim((string) post('title', '')) : '');
$closingDate = isset($_GET['closing_date']) ? trim((string) $_GET['closing_date']) : (isPost() ? trim((string) post('closing_date', '')) : '');
$priority = isset($_GET['priority']) ? trim((string) $_GET['priority']) : (isPost() ? trim((string) post('priority', '')) : '');
if ($priority !== '' && !in_array($priority, ['low', 'medium', 'high'], true)) {
    $priority = '';
}

$pageTitle = 'Quick save job | Simple CV Builder';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle,
        'metaDescription' => 'Save a job to your list from any job page.',
        'canonicalUrl' => APP_URL . '/quick-add-job.php',
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" role="main" class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">Quick save job</h1>
            <p class="text-sm text-gray-600 mb-6">Save this job to your list. You can add company name and other details later in your job applications.</p>

            <?php if ($error): ?>
                <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-800"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post" action="" class="space-y-4">
                <input type="hidden" name="<?php echo e(CSRF_TOKEN_NAME); ?>" value="<?php echo e(csrfToken()); ?>">
                <div>
                    <label for="quick-add-url" class="block text-sm font-semibold text-gray-900 mb-1">Job URL <span class="text-red-600">*</span></label>
                    <input type="url" id="quick-add-url" name="url" value="<?php echo e($url); ?>" required
                           class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="quick-add-title" class="block text-sm font-semibold text-gray-900 mb-1">Job title (optional)</label>
                    <input type="text" id="quick-add-title" name="title" value="<?php echo e($title); ?>"
                           placeholder="e.g. Senior Developer"
                           class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="quick-add-closing" class="block text-sm font-semibold text-gray-900 mb-1">Closing date (optional)</label>
                    <input type="date" id="quick-add-closing" name="closing_date" value="<?php echo e($closingDate); ?>"
                           class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">We’ll remind you before this date if you haven’t applied yet.</p>
                </div>
                <div>
                    <label for="quick-add-priority" class="block text-sm font-semibold text-gray-900 mb-1">Priority (optional)</label>
                    <select id="quick-add-priority" name="priority" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">None</option>
                        <option value="low" <?php echo $priority === 'low' ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo $priority === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo $priority === 'high' ? 'selected' : ''; ?>>High</option>
                    </select>
                </div>
                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Confirm and save
                    </button>
                    <a href="/content-editor.php#jobs" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>
