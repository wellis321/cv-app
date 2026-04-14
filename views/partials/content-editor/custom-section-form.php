<?php
/**
 * Custom Section Form Partial
 * $customSectionId is set by get-section-form.php (full "custom-<uuid>" string is unused;
 * the DB id without prefix is available via $dbId, also set by the loader).
 *
 * Variables in scope (set by get-section-form.php before include):
 *   $customSection   – row from custom_sections
 *   $customSectionId – the DB uuid (no "custom-" prefix)
 *   $userId          – current user id
 */

$editingId   = $_GET['edit'] ?? null;
$editingItem = null;

if ($editingId) {
    $editingItem = db()->fetchOne(
        "SELECT csi.* FROM custom_section_items csi
         JOIN custom_sections cs ON cs.id = csi.custom_section_id
         WHERE csi.id = ? AND cs.id = ? AND cs.profile_id = ?",
        [$editingId, $customSectionId, $userId]
    );
}

$items = db()->fetchAll(
    "SELECT * FROM custom_section_items WHERE custom_section_id = ? ORDER BY sort_order ASC, created_at ASC",
    [$customSectionId]
);
?>
<div class="max-w-3xl mx-auto">
    <!-- Section heading with rename and delete controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><?php echo e($customSection['title']); ?></h1>
        <div class="flex items-center gap-2 flex-shrink-0">
            <!-- Rename form (inline) -->
            <form id="rename-custom-section-form" method="POST" action="/api/content-editor/save-custom-section.php"
                  class="flex items-center gap-1" onsubmit="return false;">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
                <input type="hidden" name="action" value="rename">
                <input type="hidden" name="id" value="<?php echo e($customSectionId); ?>">
                <input type="text" name="title" id="rename-title-input"
                       value="<?php echo e($customSection['title']); ?>"
                       maxlength="255"
                       class="hidden px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 w-40"
                       aria-label="Section title">
                <button type="button" id="rename-edit-btn"
                        class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
                    Rename
                </button>
                <button type="submit" id="rename-save-btn"
                        class="hidden text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md transition-colors">
                    Save
                </button>
                <button type="button" id="rename-cancel-btn"
                        class="hidden text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-md transition-colors">
                    Cancel
                </button>
            </form>
            <!-- Delete section -->
            <button type="button"
                    data-action="delete-custom-section"
                    data-section-id="<?php echo e($customSectionId); ?>"
                    class="text-xs bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 hover:border-red-300 px-2 py-1 rounded-md transition-colors">
                Delete section
            </button>
        </div>
    </div>

    <!-- Add / Edit Item Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo $editingItem ? 'Edit Item' : 'Add New Item'; ?>
        </h2>
        <form method="POST"
              action="/api/content-editor/save-custom-item.php"
              data-section-form
              data-form-type="<?php echo $editingItem ? 'update' : 'create'; ?>">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo csrfToken(); ?>">
            <input type="hidden" name="action" value="<?php echo $editingItem ? 'update' : 'create'; ?>">
            <input type="hidden" name="custom_section_id" value="<?php echo e($customSectionId); ?>">
            <?php if ($editingItem): ?>
                <input type="hidden" name="id" value="<?php echo e($editingItem['id']); ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label for="item-title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="item-title" name="title"
                           value="<?php echo $editingItem ? e($editingItem['title']) : ''; ?>"
                           required maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="item-subtitle" class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" id="item-subtitle" name="subtitle"
                               value="<?php echo $editingItem ? e($editingItem['subtitle'] ?? '') : ''; ?>"
                               maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="item-date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="text" id="item-date" name="item_date"
                               value="<?php echo $editingItem ? e($editingItem['item_date'] ?? '') : ''; ?>"
                               maxlength="100" placeholder="e.g. 2024 or Jan 2024"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label for="item-url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="url" id="item-url" name="url"
                           value="<?php echo $editingItem ? e($editingItem['url'] ?? '') : ''; ?>"
                           placeholder="https://"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="item-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="item-description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo $editingItem ? e($editingItem['description'] ?? '') : ''; ?></textarea>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <?php echo $editingItem ? 'Update Item' : 'Add Item'; ?>
                </button>
                <?php if ($editingItem): ?>
                    <button type="button" data-action="cancel"
                            class="ml-4 text-gray-700 hover:text-gray-900">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Existing Items List -->
    <div id="custom-item-entries-list">
        <?php if (empty($items)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
                <p>No items added yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($items as $item): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start gap-4">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e($item['title']); ?></h3>
                                <?php if (!empty($item['subtitle'])): ?>
                                    <p class="text-gray-700"><?php echo e($item['subtitle']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['item_date'])): ?>
                                    <p class="text-sm text-gray-500"><?php echo e($item['item_date']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['url'])): ?>
                                    <p class="text-sm mt-1">
                                        <a href="<?php echo e($item['url']); ?>" target="_blank" rel="noopener"
                                           class="text-blue-600 hover:text-blue-800 break-all"><?php echo e($item['url']); ?></a>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($item['description'])): ?>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo e(mb_strimwidth($item['description'], 0, 120, '…')); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <button type="button"
                                        data-action="edit"
                                        data-entry-id="<?php echo e($item['id']); ?>"
                                        class="px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-200 hover:bg-green-100 hover:border-green-300 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">Edit</button>
                                <button type="button"
                                        data-action="delete"
                                        data-entry-id="<?php echo e($item['id']); ?>"
                                        data-entry-type="custom-item"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-md border border-red-200 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    // Inline rename toggle
    var editBtn    = document.getElementById('rename-edit-btn');
    var saveBtn    = document.getElementById('rename-save-btn');
    var cancelBtn  = document.getElementById('rename-cancel-btn');
    var titleInput = document.getElementById('rename-title-input');
    var form       = document.getElementById('rename-custom-section-form');
    var heading    = document.querySelector('.max-w-3xl h1');
    var originalTitle = titleInput ? titleInput.value : '';

    function showRenameInput() {
        if (titleInput) titleInput.classList.remove('hidden');
        if (editBtn)    editBtn.classList.add('hidden');
        if (saveBtn)    saveBtn.classList.remove('hidden');
        if (cancelBtn)  cancelBtn.classList.remove('hidden');
        if (titleInput) titleInput.focus();
    }

    function hideRenameInput() {
        if (titleInput) titleInput.classList.add('hidden');
        if (editBtn)    editBtn.classList.remove('hidden');
        if (saveBtn)    saveBtn.classList.add('hidden');
        if (cancelBtn)  cancelBtn.classList.add('hidden');
    }

    if (editBtn)   editBtn.addEventListener('click', showRenameInput);
    if (cancelBtn) cancelBtn.addEventListener('click', function () {
        titleInput.value = originalTitle;
        hideRenameInput();
    });

    if (titleInput) {
        titleInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); saveBtn && saveBtn.click(); }
            if (e.key === 'Escape') { cancelBtn && cancelBtn.click(); }
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            var newTitle = (titleInput.value || '').trim();
            if (!newTitle) { titleInput.focus(); return; }

            var csrfToken     = (window.contentEditorData && window.contentEditorData.csrfToken) || '';
            var csrfTokenName = (window.contentEditorData && window.contentEditorData.csrfTokenName) || '_csrf_token';
            var sectionId     = form.querySelector('input[name="id"]').value;

            var body = new URLSearchParams();
            body.append('action', 'rename');
            body.append('id', sectionId);
            body.append('title', newTitle);
            body.append(csrfTokenName, csrfToken);

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving…';

            fetch('/api/content-editor/save-custom-section.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    originalTitle = newTitle;
                    if (heading) heading.textContent = newTitle;
                    // Update sidebar nav label
                    var navLink = document.querySelector('a[data-section-id="custom-' + sectionId + '"] .truncate');
                    if (navLink) navLink.textContent = newTitle;
                    hideRenameInput();
                } else {
                    alert('Could not rename section. Please try again.');
                }
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
            })
            .catch(function () {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
                alert('Network error. Please try again.');
            });
        });
    }

    // Custom item delete — handled via data-action="delete" with data-entry-type="custom-item"
    // The main content-editor.js deleteEntry function calls save-section.php, but for custom items
    // we need to call save-custom-item.php instead. Intercept here.
    var contentArea = document.getElementById('section-content');
    if (contentArea) {
        contentArea.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-action="delete"][data-entry-type="custom-item"]');
            if (!btn) return;
            e.stopImmediatePropagation();
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this item?')) return;

            var itemId        = btn.dataset.entryId;
            var csrfToken     = (window.contentEditorData && window.contentEditorData.csrfToken) || '';
            var csrfTokenName = (window.contentEditorData && window.contentEditorData.csrfTokenName) || '_csrf_token';

            var body = new URLSearchParams();
            body.append('action', 'delete');
            body.append('id', itemId);
            body.append(csrfTokenName, csrfToken);

            fetch('/api/content-editor/save-custom-item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    if (window.contentEditor && window.contentEditor.loadSection) {
                        window.contentEditor.loadSection(window.location.hash.substring(1).split('&')[0]);
                    }
                } else {
                    alert('Could not delete item. Please try again.');
                }
            })
            .catch(function () { alert('Network error. Please try again.'); });
        }, true); // capture phase so it fires before the generic handler
    }
})();
</script>
