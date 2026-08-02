/**
 * Job application closing date reminders – browser notifications.
 * Run on dashboard, content-editor, job-applications. Fetches upcoming closing dates
 * and shows a notification for each (once per day per job via localStorage).
 *
 * Notification.requestPermission() must be called from a real user gesture (click) -
 * browsers reject/ignore it otherwise. So permission is only requested when there's
 * actually something to notify about, via a small "Enable" prompt the user clicks.
 */
(function () {
    const STORAGE_PREFIX = 'closing_reminder_';
    const PERMISSION_PROMPT_DISMISSED_KEY = 'closing_reminder_permission_prompt_dismissed';
    const TODAY = new Date().toISOString().slice(0, 10);

    function storageKey(applicationId) {
        return STORAGE_PREFIX + applicationId + '_' + TODAY;
    }

    function wasNotifiedToday(applicationId) {
        try {
            return localStorage.getItem(storageKey(applicationId)) === '1';
        } catch (e) {
            return false;
        }
    }

    function markNotifiedToday(applicationId) {
        try {
            localStorage.setItem(storageKey(applicationId), '1');
        } catch (e) {}
    }

    function showNotification(reminder) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        var title = 'Closing date soon';
        var body = reminder.days_until === 0
            ? 'Today: ' + (reminder.job_title || 'Application') + ' at ' + (reminder.company_name || '')
            : (reminder.days_until === 1 ? 'Tomorrow' : reminder.days_until + ' days') + ': ' + (reminder.job_title || 'Application') + ' at ' + (reminder.company_name || '');
        try {
            var n = new Notification(title, { body: body, icon: '/static/favicon.ico' });
            n.onclick = function () {
                n.close();
                window.focus();
                if (window.location.pathname.indexOf('/job-applications') !== -1) {
                    return;
                }
                window.location.href = '/content-editor.php#jobs';
            };
        } catch (e) {}
    }

    function notifyReminders(reminders) {
        reminders.forEach(function (reminder) {
            if (wasNotifiedToday(reminder.id)) return;
            showNotification(reminder);
            markNotifiedToday(reminder.id);
        });
    }

    function showEnableNotificationsPrompt(reminders, onEnabled) {
        if (document.getElementById('closing-reminder-permission-banner')) return;
        try {
            if (localStorage.getItem(PERMISSION_PROMPT_DISMISSED_KEY) === TODAY) return;
        } catch (e) {}

        var count = reminders.length;
        var message = count === 1
            ? 'You have an application closing date coming up.'
            : 'You have ' + count + ' application closing dates coming up.';

        var banner = document.createElement('div');
        banner.id = 'closing-reminder-permission-banner';
        banner.setAttribute('role', 'status');
        banner.style.cssText = 'position:fixed;bottom:16px;right:16px;z-index:9999;max-width:320px;font-family:inherit;';
        banner.innerHTML =
            '<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;box-shadow:0 4px 12px rgba(0,0,0,0.12);">' +
                '<p style="margin:0 0 10px 0;font-size:13px;line-height:1.4;color:#1e3a8a;">' + message + ' Get a browser notification as a reminder?</p>' +
                '<div style="display:flex;gap:8px;">' +
                    '<button type="button" id="closing-reminder-enable-btn" style="background:#2563eb;color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:500;cursor:pointer;">Enable notifications</button>' +
                    '<button type="button" id="closing-reminder-dismiss-btn" style="background:transparent;color:#1e40af;border:none;border-radius:6px;padding:6px 12px;font-size:12px;cursor:pointer;">Not now</button>' +
                '</div>' +
            '</div>';
        document.body.appendChild(banner);

        function removeBanner() {
            if (banner.parentNode) banner.parentNode.removeChild(banner);
        }

        document.getElementById('closing-reminder-enable-btn').addEventListener('click', function () {
            removeBanner();
            // Real user gesture (this click) - safe to request permission here.
            Notification.requestPermission().then(function (permission) {
                if (permission === 'granted') onEnabled();
            });
        });
        document.getElementById('closing-reminder-dismiss-btn').addEventListener('click', function () {
            removeBanner();
            try { localStorage.setItem(PERMISSION_PROMPT_DISMISSED_KEY, TODAY); } catch (e) {}
        });
    }

    function run() {
        if (!('Notification' in window)) return;

        fetch('/api/upcoming-closing-dates.php', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.enabled || !data.reminders || !data.reminders.length) return;

                var pending = data.reminders.filter(function (reminder) {
                    return !wasNotifiedToday(reminder.id);
                });
                if (!pending.length) return;

                if (Notification.permission === 'granted') {
                    notifyReminders(pending);
                } else if (Notification.permission === 'default') {
                    showEnableNotificationsPrompt(pending, function () {
                        notifyReminders(pending);
                    });
                }
                // 'denied' - respect it, do nothing.
            })
            .catch(function () {});
    }

    function shouldRun() {
        var path = window.location.pathname || '';
        return path === '/dashboard.php' || path === '/content-editor.php' || path.indexOf('/job-applications') !== -1;
    }

    if (!shouldRun()) return;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
