<!-- Skip to main content link (accessibility) -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Skip to main content</a>

<header role="banner" class="sticky top-0 z-40 bg-white/95 shadow backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="/" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                    <img src="/static/images/logo/black-logo-150.jpg"
                         alt="Simple CV Builder"
                         class="h-10 sm:h-12 w-auto"
                         loading="eager">
                    <span class="text-xl sm:text-2xl font-bold text-blue-600">Simple CV Builder</span>
                </a>
            </div>

            <!-- Mobile menu button -->
            <button type="button" id="mobile-menu-button" class="lg:hidden p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" aria-label="Toggle menu" aria-expanded="false">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Desktop navigation -->
            <nav role="navigation" aria-label="Main navigation" class="hidden lg:flex items-center space-x-4 text-sm">
                <?php if (isLoggedIn()): ?>
                    <?php
                    $currentPage = basename($_SERVER['PHP_SELF']);
                    $isProfile = ($currentPage === 'profile.php');
                    $isDashboard = ($currentPage === 'dashboard.php');
                    $isPreview = ($currentPage === 'preview-cv.php');
                    $isSubscription = ($currentPage === 'subscription.php');
                    ?>
                    <a href="/profile.php" class="<?php echo $isProfile ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?>">Profile</a>
                    <a href="/dashboard.php" class="<?php echo $isDashboard ? 'text-blue-600 font-medium underline' : 'text-gray-700 hover:text-blue-600'; ?>">Edit CV Sections</a>
                    <a href="/preview-cv.php" class="<?php echo $isPreview ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?>">Preview & PDF</a>
                    <a href="/subscription.php" class="<?php echo $isSubscription ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?>">Subscription</a>
                    <a href="/how-it-works.php" class="text-gray-700 hover:text-blue-600">Help</a>
                    <a href="/resources/" class="text-gray-700 hover:text-blue-600">Resources</a>
                    <a href="/cv.php" class="text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">View CV</a>
                    <a href="/logout.php" class="text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">Sign Out</a>
                <?php else: ?>
                    <?php
                    $homeBase = APP_URL ?? '';
                    $featuresUrl = rtrim($homeBase, '/') . '/#features';
                    $pricingUrl = rtrim($homeBase, '/') . '/#pricing';
                    $processUrl = rtrim($homeBase, '/') . '/#process';
                    ?>
                    <a href="<?php echo e($featuresUrl); ?>" class="text-gray-700 hover:text-blue-600">Features</a>
                    <a href="<?php echo e($pricingUrl); ?>" class="text-gray-700 hover:text-blue-600">Pricing</a>
                    <a href="/how-it-works.php" class="text-gray-700 hover:text-blue-600">How it works</a>
                    <a href="/faq.php" class="text-gray-700 hover:text-blue-600">FAQ</a>
                    <a href="/resources/" class="text-gray-700 hover:text-blue-600">Resources</a>
                    <button type="button"
                            data-open-login
                            aria-label="Log in"
                            class="rounded-md border border-transparent bg-blue-50 px-4 py-2 text-blue-600 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Log in
                    </button>
                    <button type="button"
                            data-open-register
                            aria-label="Register"
                            class="rounded-md bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Register
                    </button>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="lg:hidden hidden border-t border-gray-200 py-4">
            <nav role="navigation" aria-label="Mobile navigation" class="flex flex-col space-y-1">
                <?php if (isLoggedIn()): ?>
                    <?php
                    $currentPage = basename($_SERVER['PHP_SELF']);
                    $isProfile = ($currentPage === 'profile.php');
                    $isDashboard = ($currentPage === 'dashboard.php');
                    $isPreview = ($currentPage === 'preview-cv.php');
                    $isSubscription = ($currentPage === 'subscription.php');
                    ?>
                    <a href="/profile.php" class="<?php echo $isProfile ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50'; ?> px-4 py-2 rounded-md text-sm">Profile</a>
                    <a href="/dashboard.php" class="<?php echo $isDashboard ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50'; ?> px-4 py-2 rounded-md text-sm">Edit CV Sections</a>
                    <a href="/preview-cv.php" class="<?php echo $isPreview ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50'; ?> px-4 py-2 rounded-md text-sm">Preview & PDF</a>
                    <a href="/subscription.php" class="<?php echo $isSubscription ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50'; ?> px-4 py-2 rounded-md text-sm">Subscription</a>
                    <a href="/how-it-works.php" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">Help</a>
                    <a href="/resources/" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">Resources</a>
                    <a href="/cv.php" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">View CV</a>
                    <a href="/logout.php" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">Sign Out</a>
                <?php else: ?>
                    <?php
                    $homeBase = APP_URL ?? '';
                    $featuresUrl = rtrim($homeBase, '/') . '/#features';
                    $pricingUrl = rtrim($homeBase, '/') . '/#pricing';
                    $processUrl = rtrim($homeBase, '/') . '/#process';
                    ?>
                    <a href="<?php echo e($featuresUrl); ?>" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">Features</a>
                    <a href="<?php echo e($pricingUrl); ?>" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">Pricing</a>
                    <a href="/how-it-works.php" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">How it works</a>
                    <a href="/faq.php" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">FAQ</a>
                    <a href="/resources/" class="text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm">Resources</a>
                    <div class="px-4 py-2 space-y-2">
                        <button type="button"
                                data-open-login
                                aria-label="Log in"
                                class="w-full rounded-md border border-transparent bg-blue-50 px-4 py-2 text-blue-600 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                            Log in
                        </button>
                        <button type="button"
                                data-open-register
                                aria-label="Register"
                                class="w-full rounded-md bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                            Register
                        </button>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<script>
(function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');

            // Update hamburger icon
            const icon = mobileMenuButton.querySelector('svg');
            if (icon) {
                if (isExpanded) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
                } else {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
                }
            }
        });
    }
})();
</script>
