<?php
/**
 * Content Editor – shared notification and confirm modals
 * Use window.showNotificationModal() and window.showConfirmModal() from any section.
 */
?>
<div id="content-editor-notification-modal" class="hidden fixed inset-0 z-[60] overflow-y-auto" aria-modal="true" role="dialog" aria-labelledby="notification-modal-title">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-600/50 transition-opacity" id="notification-modal-backdrop"></div>
        <div id="notification-modal-dialog" class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 border border-gray-200">
            <div class="flex gap-4">
                <div id="notification-modal-icon" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" aria-hidden="true"></div>
                <div class="flex-1 min-w-0">
                    <h3 id="notification-modal-title" class="text-lg font-semibold text-gray-900"></h3>
                    <p id="notification-modal-message" class="mt-1 text-sm text-gray-600"></p>
                </div>
            </div>
            <div id="notification-modal-actions" class="mt-6 flex justify-end gap-3"></div>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('content-editor-notification-modal');
    var backdrop = document.getElementById('notification-modal-backdrop');
    var dialog = document.getElementById('notification-modal-dialog');
    var titleEl = document.getElementById('notification-modal-title');
    var messageEl = document.getElementById('notification-modal-message');
    var iconEl = document.getElementById('notification-modal-icon');
    var actionsEl = document.getElementById('notification-modal-actions');

    var resolveConfirm = null;

    var typeStyles = {
        error: {
            icon: '<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
            iconBg: 'bg-red-100'
        },
        success: {
            icon: '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            iconBg: 'bg-green-100'
        },
        info: {
            icon: '<svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
            iconBg: 'bg-blue-100'
        }
    };

    function open() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        titleEl.focus();
    }

    function close() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        if (resolveConfirm !== null) {
            resolveConfirm(false);
            resolveConfirm = null;
        }
    }

    function setContent(opts) {
        var type = (opts.type || 'info').toLowerCase();
        if (type !== 'error' && type !== 'success') type = 'info';
        var style = typeStyles[type];
        titleEl.textContent = opts.title || (type === 'error' ? 'Error' : type === 'success' ? 'Success' : 'Notice');
        messageEl.textContent = opts.message || '';
        iconEl.className = 'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ' + style.iconBg;
        iconEl.innerHTML = style.icon;
    }

    function renderNotification(opts) {
        setContent(opts);
        actionsEl.innerHTML = '<button type="button" id="notification-modal-ok" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">OK</button>';
        actionsEl.querySelector('#notification-modal-ok').onclick = function() { close(); };
    }

    function renderConfirm(opts, onConfirm) {
        setContent(opts);
        var confirmLabel = opts.confirmLabel || 'Confirm';
        var cancelLabel = opts.cancelLabel || 'Cancel';
        actionsEl.innerHTML = '<button type="button" id="notification-modal-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">' + cancelLabel + '</button><button type="button" id="notification-modal-confirm" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">' + confirmLabel + '</button>';
        actionsEl.querySelector('#notification-modal-cancel').onclick = function() { close(); };
        actionsEl.querySelector('#notification-modal-confirm').onclick = function() { if (resolveConfirm) resolveConfirm(true); resolveConfirm = null; close(); if (onConfirm) onConfirm(); };
    }

    if (backdrop) backdrop.addEventListener('click', close);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) close();
    });
    modal.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') close();
    });

    window.showNotificationModal = function(opts) {
        if (typeof opts === 'string') opts = { message: opts };
        renderNotification(opts);
        open();
    };

    window.showConfirmModal = function(opts) {
        if (typeof opts === 'string') opts = { title: 'Confirm', message: opts };
        return new Promise(function(resolve) {
            resolveConfirm = resolve;
            renderConfirm(opts, function() { resolve(true); });
            open();
        });
    };
})();
</script>
