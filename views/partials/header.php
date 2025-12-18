<!-- Skip to main content link (accessibility) -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Skip to main content</a>

<header role="banner" class="sticky top-0 z-40 bg-white/95 shadow backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="/" class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                    <img src="/static/images/logo/black-logo-150.jpg"
                         alt="Simple CV Builder"
                         class="h-8 sm:h-10 w-auto"
                         loading="eager">
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
                    <div class="relative group">
                        <button type="button" aria-expanded="false" aria-haspopup="true" aria-label="Resources menu" class="flex items-center gap-1 text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                            Resources
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div role="menu" class="absolute right-0 top-full hidden min-w-[12rem] rounded-lg border border-gray-200 bg-white py-2 shadow-lg group-hover:block">
                            <a href="/resources/jobs/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Job Market Insights</a>
                            <a href="/resources/passive-income/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Passive Income Ideas</a>
                            <a href="/resources/career/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Career Advice Hub</a>
                            <a href="/resources/extra-income/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Extra Income Ideas</a>
                        </div>
                    </div>
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
                    <div class="relative group">
                        <button type="button" aria-expanded="false" aria-haspopup="true" aria-label="Resources menu" class="flex items-center gap-1 text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                            Resources
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div role="menu" class="absolute right-0 top-full hidden min-w-[12rem] rounded-lg border border-gray-200 bg-white py-2 shadow-lg group-hover:block">
                            <a href="/resources/jobs/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Job Market Insights</a>
                            <a href="/resources/passive-income/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Passive Income Ideas</a>
                            <a href="/resources/career/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Career Advice Hub</a>
                            <a href="/resources/extra-income/" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">Extra Income Ideas</a>
                        </div>
                    </div>
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
                    <div class="px-4 py-2">
                        <button type="button" id="mobile-resources-toggle" class="flex items-center justify-between w-full text-gray-700 hover:text-blue-600 text-sm" aria-expanded="false">
                            <span>Resources</span>
                            <svg class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="mobile-resources-menu" class="hidden mt-2 pl-4 space-y-1">
                            <a href="/resources/jobs/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Job Market Insights</a>
                            <a href="/resources/passive-income/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Passive Income Ideas</a>
                            <a href="/resources/career/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Career Advice Hub</a>
                            <a href="/resources/extra-income/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Extra Income Ideas</a>
                        </div>
                    </div>
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
                    <div class="px-4 py-2">
                        <button type="button" id="mobile-resources-toggle" class="flex items-center justify-between w-full text-gray-700 hover:text-blue-600 text-sm" aria-expanded="false">
                            <span>Resources</span>
                            <svg class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="mobile-resources-menu" class="hidden mt-2 pl-4 space-y-1">
                            <a href="/resources/jobs/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Job Market Insights</a>
                            <a href="/resources/passive-income/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Passive Income Ideas</a>
                            <a href="/resources/career/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Career Advice Hub</a>
                            <a href="/resources/extra-income/" class="block text-gray-600 hover:text-blue-600 px-4 py-2 rounded-md text-sm">Extra Income Ideas</a>
                        </div>
                    </div>
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
    const mobileResourcesToggle = document.getElementById('mobile-resources-toggle');
    const mobileResourcesMenu = document.getElementById('mobile-resources-menu');

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

    if (mobileResourcesToggle && mobileResourcesMenu) {
        mobileResourcesToggle.addEventListener('click', function() {
            const isExpanded = mobileResourcesToggle.getAttribute('aria-expanded') === 'true';
            mobileResourcesToggle.setAttribute('aria-expanded', !isExpanded);
            mobileResourcesMenu.classList.toggle('hidden');

            // Rotate arrow icon
            const arrow = mobileResourcesToggle.querySelector('svg');
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
        });
    }
})();
</script>
