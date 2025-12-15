<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Simple CV Builder</h3>
                <p class="text-sm text-gray-600">
                    Create a professional CV that stands out, updates in real-time, and can be shared as a simple link.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="text-gray-600 hover:text-blue-600">Home</a></li>
                    <li><a href="/faq.php" class="text-gray-600 hover:text-blue-600">FAQ</a></li>
                    <li><a href="/resources/jobs/" class="text-gray-600 hover:text-blue-600">Job Market Insights</a></li>
                    <li><a href="/resources/passive-income/" class="text-gray-600 hover:text-blue-600">Passive Income Ideas</a></li>
                    <li><a href="/resources/career/" class="text-gray-600 hover:text-blue-600">Career Advice Hub</a></li>
                    <li><a href="/resources/extra-income/" class="text-gray-600 hover:text-blue-600">Extra Income Ideas</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/profile.php" class="text-gray-600 hover:text-blue-600">Profile</a></li>
                        <li><a href="/cv.php" class="text-gray-600 hover:text-blue-600">View CV</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Legal</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/privacy.php" class="text-gray-600 hover:text-blue-600">Privacy Policy</a></li>
                    <li><a href="/terms.php" class="text-gray-600 hover:text-blue-600">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-200 text-center text-sm text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> Simple CV Builder. All rights reserved.</p>
        </div>
    </div>
</footer>

<div
    data-cookie-banner
    class="fixed inset-x-4 bottom-4 z-50 hidden max-w-lg rounded-2xl border border-gray-200 bg-white p-5 shadow-lg sm:left-1/2 sm:right-auto sm:-translate-x-1/2">
    <div class="flex items-start gap-4">
        <div class="mt-1 hidden flex-shrink-0 rounded-full bg-blue-100 p-2 text-blue-600 sm:block">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1 text-sm text-gray-700">
            <p class="font-semibold text-gray-900">We use cookies</p>
            <p class="mt-1 leading-relaxed">
                We use cookies to keep you signed in and remember your preferences. By clicking “Accept”, you agree to our use of cookies as described in our
                <a href="/privacy.php" class="text-blue-600 underline">Privacy Policy</a>.
            </p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <button
                type="button"
                data-cookie-decline
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:border-gray-400 hover:text-gray-800">
                Decline
            </button>
            <button
                type="button"
                data-cookie-accept
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Accept
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        const storageKey = 'cv_app_cookie_consent_v1';
        const banner = document.querySelector('[data-cookie-banner]');
        if (!banner) {
            return;
        }

        const accepted = localStorage.getItem(storageKey);
        if (!accepted) {
            banner.classList.remove('hidden');
        }

        const closeBanner = (value) => {
            localStorage.setItem(storageKey, value);
            banner.classList.add('hidden');
        };

        const acceptBtn = banner.querySelector('[data-cookie-accept]');
        const declineBtn = banner.querySelector('[data-cookie-decline]');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', () => closeBanner('accepted'));
        }
        if (declineBtn) {
            declineBtn.addEventListener('click', () => closeBanner('declined'));
        }
    })();
</script>
