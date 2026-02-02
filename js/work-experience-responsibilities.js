/**
 * Work Experience Responsibilities Editor
 * Handles categories and items for work experience responsibilities
 */

// Make function globally available
window.initResponsibilitiesEditor = function(workExperienceId, initialCategories, container, variantId) {
    let categories = initialCategories || [];
    let expandedCategories = {};
    let editingCategoryId = null;
    let editingItemId = null;
    variantId = variantId || null;

    // Initialize expanded state
    categories.forEach(cat => {
        expandedCategories[cat.id] = true;
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function render() {
        let html = '';

        if (categories.length === 0) {
            html = '<p class="mb-4 text-gray-500 italic">No responsibilities added yet.</p>';
        } else {
            html = '<div class="space-y-4">';
            categories.forEach(category => {
                const isExpanded = expandedCategories[category.id] !== false;
                const isEditingCategory = editingCategoryId === category.id;

                html += `<div class="overflow-hidden rounded-md border bg-white">
                    <div class="flex items-center justify-between bg-gray-50 p-3">
                        ${isEditingCategory ? `
                            <input type="text" id="edit-category-${category.id}" value="${escapeHtml(category.name)}"
                                   class="mr-2 flex-1 rounded-md border-gray-300 px-2 py-1" />
                            <div class="flex space-x-2">
                                <button onclick="saveCategory('${category.id}')"
                                        class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700">
                                    Save
                                </button>
                                <button onclick="cancelEditCategory()"
                                        class="rounded bg-gray-200 px-2 py-1 text-xs text-gray-700 hover:bg-gray-300">
                                    Cancel
                                </button>
                            </div>
                        ` : `
                            <button onclick="toggleCategory('${category.id}')"
                                    class="flex flex-1 items-center text-left font-medium text-gray-800">
                                <span class="mr-2 transform transition-transform duration-200 ${isExpanded ? 'rotate-90' : ''}">▶</span>
                                ${escapeHtml(category.name)}
                            </button>
                            <div class="flex space-x-2">
                                <button onclick="startEditCategory('${category.id}')"
                                        class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Edit
                                </button>
                                <button onclick="deleteCategory('${category.id}')"
                                        class="text-sm text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </div>
                        `}
                    </div>
                    ${isExpanded ? `
                        <div class="p-3">
                            ${category.items && category.items.length > 0 ? `
                                <ul class="list-disc space-y-2 pl-6">
                                    ${category.items.map(item => {
                                        const isEditingItem = editingItemId === item.id;
                                        return `
                                            <li>
                                                ${isEditingItem ? `
                                                    <div class="mt-1 flex items-center">
                                                        <input type="text" id="edit-item-${item.id}" value="${escapeHtml(item.content)}"
                                                               class="mr-2 flex-1 rounded-md border-gray-300 px-2 py-1" />
                                                        <button onclick="saveItem('${item.id}')"
                                                                class="mr-1 rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700">
                                                            Save
                                                        </button>
                                                        <button onclick="cancelEditItem()"
                                                                class="rounded bg-gray-200 px-2 py-1 text-xs text-gray-700 hover:bg-gray-300">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                ` : `
                                                    <div class="group flex items-start">
                                                        <span class="flex-1">${escapeHtml(item.content)}</span>
                                                        <div class="ml-2 hidden space-x-2 group-hover:flex">
                                                            <button onclick="startEditItem('${item.id}')"
                                                                    class="text-xs text-indigo-600 hover:text-indigo-800">
                                                                Edit
                                                            </button>
                                                            <button onclick="deleteItem('${item.id}')"
                                                                    class="text-xs text-red-600 hover:text-red-800">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                `}
                                            </li>
                                        `;
                                    }).join('')}
                                </ul>
                            ` : `
                                <p class="text-gray-500 italic">No items in this category yet.</p>
                            `}
                            <div class="mt-4">
                                <div class="flex items-center">
                                    <input type="text" id="new-item-${category.id}"
                                           placeholder="Add a new responsibility item"
                                           class="flex-1 rounded-md border-gray-300 px-2 py-1"
                                           onkeypress="if(event.key==='Enter') addItem('${category.id}')" />
                                    <button onclick="addItem('${category.id}')"
                                            class="ml-2 rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                </div>`;
            });
            html += '</div>';
        }

        html += `
            <div class="mt-6">
                <h4 class="mb-2 text-sm font-medium text-gray-700">Add a new category</h4>
                <div class="flex">
                    <input type="text" id="new-category-name"
                           placeholder="e.g. Strategic Leadership, Project Management"
                           class="flex-1 rounded-md rounded-r-none border-gray-300 px-2 py-1"
                           onkeypress="if(event.key==='Enter') addCategory()" />
                    <button onclick="addCategory()"
                            class="rounded-md rounded-l-none bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                        Add Category
                    </button>
                </div>
            </div>
        `;

        container.innerHTML = html;
    }

    function toggleCategory(categoryId) {
        expandedCategories[categoryId] = !expandedCategories[categoryId];
        render();
    }

    function startEditCategory(categoryId) {
        editingCategoryId = categoryId;
        editingItemId = null;
        render();
        setTimeout(() => {
            const input = document.getElementById('edit-category-' + categoryId);
            if (input) input.focus();
        }, 100);
    }

    function cancelEditCategory() {
        editingCategoryId = null;
        render();
    }

    function saveCategory(categoryId) {
        const input = document.getElementById('edit-category-' + categoryId);
        const name = input ? input.value.trim() : '';
        if (!name) return;

        const formData = new FormData();
        formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
        formData.append('action', 'update_category');
        formData.append('category_id', categoryId);
        formData.append('name', name);
        if (variantId) formData.append('variant_id', variantId);

        fetch('/api/responsibilities.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadResponsibilities();
            } else {
                alert('Failed to update category: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update category. Please try again.');
        });
    }

    function deleteCategory(categoryId) {
        if (!confirm('Are you sure you want to delete this category and all its items?')) {
            return;
        }

        const formData = new FormData();
        formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
        formData.append('action', 'delete_category');
        formData.append('category_id', categoryId);
        if (variantId) formData.append('variant_id', variantId);

        fetch('/api/responsibilities.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadResponsibilities();
            } else {
                alert('Failed to delete category: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete category. Please try again.');
        });
    }

    function addCategory() {
        const input = document.getElementById('new-category-name');
        const name = input ? input.value.trim() : '';
        if (!name) return;

        const formData = new FormData();
        formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
        formData.append('action', 'add_category');
        formData.append('work_experience_id', workExperienceId);
        formData.append('name', name);
        if (variantId) formData.append('variant_id', variantId);

        fetch('/api/responsibilities.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadResponsibilities();
                setTimeout(() => {
                    const newItemInput = document.querySelector(`#new-item-${data.id}`);
                    if (newItemInput) newItemInput.focus();
                }, 100);
            } else {
                alert('Failed to add category: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add category. Please try again.');
        });
    }

    function addItem(categoryId) {
        const input = document.getElementById('new-item-' + categoryId);
        const content = input ? input.value.trim() : '';
        if (!content) return;

        const formData = new FormData();
        formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
        formData.append('action', 'add_item');
        formData.append('category_id', categoryId);
        formData.append('content', content);
        if (variantId) formData.append('variant_id', variantId);

        fetch('/api/responsibilities.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadResponsibilities();
                setTimeout(() => {
                    const newItemInput = document.getElementById('new-item-' + categoryId);
                    if (newItemInput) newItemInput.focus();
                }, 100);
            } else {
                alert('Failed to add item: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add item. Please try again.');
        });
    }

    function startEditItem(itemId) {
        editingItemId = itemId;
        editingCategoryId = null;
        render();
        setTimeout(() => {
            const input = document.getElementById('edit-item-' + itemId);
            if (input) input.focus();
        }, 100);
    }

    function cancelEditItem() {
        editingItemId = null;
        render();
    }

    function saveItem(itemId) {
        const input = document.getElementById('edit-item-' + itemId);
        const content = input ? input.value.trim() : '';
        if (!content) return;

        const formData = new FormData();
        formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
        formData.append('action', 'update_item');
        formData.append('item_id', itemId);
        formData.append('content', content);
        if (variantId) formData.append('variant_id', variantId);

        fetch('/api/responsibilities.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadResponsibilities();
            } else {
                alert('Failed to update item: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update item. Please try again.');
        });
    }

    function deleteItem(itemId) {
        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }

        const formData = new FormData();
        formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
        formData.append('action', 'delete_item');
        formData.append('item_id', itemId);
        if (variantId) formData.append('variant_id', variantId);

        fetch('/api/responsibilities.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadResponsibilities();
            } else {
                alert('Failed to delete item: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete item. Please try again.');
        });
    }

    function loadResponsibilities() {
        let url = `/api/responsibilities.php?work_experience_id=${encodeURIComponent(workExperienceId)}&action=get`;
        if (variantId) url += '&variant_id=' + encodeURIComponent(variantId);
        fetch(url, {
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.categories) {
                categories = data.categories;
                categories.forEach(cat => {
                    if (expandedCategories[cat.id] === undefined) {
                        expandedCategories[cat.id] = true;
                    }
                });
                render();
            }
        })
        .catch(error => {
            console.error('Error loading responsibilities:', error);
        });
    }

    // Make functions globally available
    window.toggleCategory = toggleCategory;
    window.startEditCategory = startEditCategory;
    window.cancelEditCategory = cancelEditCategory;
    window.saveCategory = saveCategory;
    window.deleteCategory = deleteCategory;
    window.addCategory = addCategory;
    window.addItem = addItem;
    window.startEditItem = startEditItem;
    window.cancelEditItem = cancelEditItem;
    window.saveItem = saveItem;
    window.deleteItem = deleteItem;

    // Initial render
    render();
};
