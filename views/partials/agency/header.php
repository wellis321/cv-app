<?php
/**
 * Agency Header Partial
 * Navigation for agency/recruiter users
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$org = getUserOrganisation();
$isOwnerOrAdmin = $org && in_array($org['role'], ['owner', 'admin']);
?>

<!-- Skip to main content link (accessibility) -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Skip to main content</a>

<header role="banner" class="sticky top-0 z-40 bg-white/95 shadow backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <?php if ($org && $org['logo_url']): ?>
                    <img src="<?php echo e($org['logo_url']); ?>" alt="<?php echo e($org['organisation_name']); ?>" class="h-8 w-auto">
                <?php else: ?>
                    <a href="/agency/dashboard.php" class="text-xl sm:text-2xl font-bold text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                        <?php echo e($org ? $org['organisation_name'] : 'Agency Portal'); ?>
                    </a>
                <?php endif; ?>
                <span class="hidden sm:inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                    <?php echo ucfirst($org['role'] ?? 'Member'); ?>
                </span>
            </div>

            <!-- Mobile menu button -->
            <button type="button" 
                    id="mobile-menu-button"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    class="sm:hidden p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Desktop navigation -->
            <nav role="navigation" aria-label="Main navigation" class="hidden sm:flex items-center space-x-4 text-sm">
                <!-- Switch to Personal CV -->
                <a href="/dashboard.php" 
                   class="inline-flex items-center text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded px-2 py-1"
                   title="Switch to Personal CV">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="ml-1 hidden sm:inline">My CV</span>
                </a>
                <a href="/agency/dashboard.php"
                   class="<?php echo $currentPage === 'dashboard.php' ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded px-2 py-1">
                    Dashboard
                </a>

                <button type="button"
                        onclick="openOrganisationModal()"
                        class="inline-flex items-center text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded px-2 py-1"
                        aria-label="View organisation information">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="hidden sm:inline">Info</span>
                </button>

                <?php if ($isOwnerOrAdmin): ?>
                    <div class="relative group">
                        <button type="button"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="inline-flex items-center text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded px-2 py-1 <?php echo in_array($currentPage, ['candidates.php', 'team.php', 'settings.php']) ? 'text-blue-600 font-medium' : ''; ?>">
                            Admin
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div role="menu" class="absolute left-0 top-full hidden min-w-[10rem] rounded-lg border border-gray-200 bg-white py-2 shadow-lg group-hover:block z-50">
                            <a href="/agency/candidates.php" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 <?php echo $currentPage === 'candidates.php' ? 'text-blue-600 font-medium bg-blue-50' : ''; ?>">
                                Candidates
                            </a>
                            <a href="/agency/team.php" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 <?php echo $currentPage === 'team.php' ? 'text-blue-600 font-medium bg-blue-50' : ''; ?>">
                                Team
                            </a>
                            <a href="/agency/settings.php" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 <?php echo $currentPage === 'settings.php' ? 'text-blue-600 font-medium bg-blue-50' : ''; ?>">
                                Settings
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($org['role'] === 'owner'): ?>
                    <a href="/agency/billing.php"
                       class="<?php echo $currentPage === 'billing.php' ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?> focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded px-2 py-1">
                        Billing
                    </a>
                <?php endif; ?>

                <div class="relative group">
                    <button type="button"
                            aria-expanded="false"
                            aria-haspopup="true"
                            aria-label="Account menu"
                            class="flex items-center gap-1 text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded p-1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div role="menu" class="absolute right-0 top-full hidden min-w-[12rem] rounded-lg border border-gray-200 bg-white py-2 shadow-lg group-hover:block">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900"><?php echo e(getCurrentUser()['full_name'] ?? 'User'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e(getCurrentUser()['email'] ?? ''); ?></p>
                        </div>
                        <a href="/profile.php" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">
                            My CV
                        </a>
                        <a href="/logout.php" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50">
                            Sign Out
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile menu -->
        <nav id="mobile-menu" role="navigation" aria-label="Mobile navigation" class="hidden sm:hidden pb-4 border-t border-gray-200 mt-4">
            <div class="flex flex-col space-y-2 pt-4">
                <!-- Switch to Personal CV -->
                <a href="/dashboard.php" 
                   class="block px-4 py-2 rounded-md text-base font-medium transition-colors text-gray-700 hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span>Switch to My CV</span>
                    </div>
                </a>
                <a href="/agency/dashboard.php"
                   class="<?php echo $currentPage === 'dashboard.php' ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700'; ?> block px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Dashboard
                </a>

                <button type="button"
                        onclick="openOrganisationModal()"
                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center"
                        aria-label="View organisation information">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Organisation Info
                </button>

                <?php if ($isOwnerOrAdmin): ?>
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</p>
                        <a href="/agency/candidates.php"
                           class="<?php echo $currentPage === 'candidates.php' ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700'; ?> block px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Candidates
                        </a>
                        <a href="/agency/team.php"
                           class="<?php echo $currentPage === 'team.php' ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700'; ?> block px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Team
                        </a>
                        <a href="/agency/settings.php"
                           class="<?php echo $currentPage === 'settings.php' ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700'; ?> block px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Settings
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($org['role'] === 'owner'): ?>
                    <a href="/agency/billing.php"
                       class="<?php echo $currentPage === 'billing.php' ? 'text-blue-600 font-medium bg-blue-50' : 'text-gray-700'; ?> block px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Billing
                    </a>
                <?php endif; ?>

                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="px-4 py-2">
                        <p class="text-sm font-medium text-gray-900"><?php echo e(getCurrentUser()['full_name'] ?? 'User'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e(getCurrentUser()['email'] ?? ''); ?></p>
                    </div>
                    <a href="/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                        My CV
                    </a>
                    <a href="/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                        Sign Out
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', function() {
                const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
                menuButton.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>

<!-- Organisation Info Modal -->
<?php partial('agency/organisation-info-modal'); ?>
