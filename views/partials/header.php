<!-- Skip to main content link (accessibility) -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Skip to main content</a>

<header role="banner" class="sticky top-0 z-40 bg-white/95 shadow backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">Simple CV Builder</a>
            </div>
            <nav role="navigation" aria-label="Main navigation" class="flex items-center space-x-4 text-sm">
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
                    <a href="<?php echo e($processUrl); ?>" class="text-gray-700 hover:text-blue-600">How it works</a>
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
    </div>
</header>
