<?php
/**
 * Online CV with Username – feature page
 * Describes the unique online CV link (/cv/@username) that always shows current information.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Online CV with Unique Link';
$canonicalUrl = APP_URL . '/online-cv-username.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Your CV is a dynamic webpage with a unique link (/cv/@username). Update once, and everyone sees the latest version instantly. Share via email, LinkedIn, or QR code.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-600 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1454167574059-b80302a6923a?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 via-cyan-600/90 to-teal-600/90" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-4 py-1.5 text-sm font-semibold text-white shadow-sm border border-white/30">Always up to date</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    Online CV with Unique Link
                </h1>
                <p class="mt-6 text-xl text-blue-50 max-w-2xl mx-auto leading-relaxed">
                    Your CV isn't a static document—it's a <strong class="text-white">dynamic webpage</strong> you can share with a simple link. Update your job title or add a certification once, and <strong class="text-white">everyone with your link sees the latest version instantly</strong>.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/profile.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Set your username
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="/cv/@simple-cv-example" target="_blank" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        View example CV →
                    </a>
                </div>
            </div>
        </section>

        <!-- Your Unique Link -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Your unique CV link
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                        Every account gets a unique, memorable CV link that always shows your current information.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border-2 border-blue-200 p-8">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Your CV link format:</p>
                        <div class="bg-white rounded-lg border-2 border-blue-300 p-6 mb-6">
                            <code class="text-2xl font-mono text-blue-600 break-all">https://simple-cv-builder.com/cv/@your-username</code>
                        </div>
                        <p class="text-gray-700 mb-6">
                            Choose your username when you create your account. It's part of your CV link forever—make it memorable and professional.
                        </p>
                        <div class="grid gap-4 md:grid-cols-3 text-left">
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm font-semibold text-gray-900">Always current</span>
                                </div>
                                <p class="text-xs text-gray-600">Updates appear instantly—no new link needed</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm font-semibold text-gray-900">Works everywhere</span>
                                </div>
                                <p class="text-xs text-gray-600">Share via email, LinkedIn, social media, or QR code</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm font-semibold text-gray-900">Mobile-friendly</span>
                                </div>
                                <p class="text-xs text-gray-600">Looks great on any device—phone, tablet, desktop</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How your online CV works
                </h2>
                <div class="space-y-12">
                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 1</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Choose your username</h3>
                            <p class="mt-3 text-gray-600">
                                When you create your account, choose a username (lowercase letters, numbers, hyphens, underscores). This becomes part of your CV link: <code class="bg-gray-100 px-1 rounded text-sm">/cv/@your-username</code>
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <img src="<?php echo e($img('1586281380349-632531db7ed4', 600)); ?>" alt="Choose username" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row-reverse md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 2</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Build your CV</h3>
                            <p class="mt-3 text-gray-600">
                                Add your work experience, education, skills, certifications, and more. Your online CV updates in real-time as you make changes. Everything you add appears on your public CV link immediately.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <img src="<?php echo e($img('1504384308090-c894fdcc538d', 600)); ?>" alt="Build your CV" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:gap-12">
                        <div class="md:w-1/2">
                            <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-semibold">Step 3</span>
                            <h3 class="mt-4 text-2xl font-bold text-gray-900">Share your link</h3>
                            <p class="mt-3 text-gray-600">
                                Share your CV link anywhere—email signatures, LinkedIn profiles, social media, business cards, or job applications. When you update your CV, everyone with your link automatically sees the latest version.
                            </p>
                        </div>
                        <div class="mt-8 md:mt-0 md:w-1/2">
                            <img src="<?php echo e($img('1531403009284-440f080d1e12', 600)); ?>" alt="Share CV link" class="w-full rounded-xl border border-gray-200 shadow-sm object-cover aspect-video" width="600" height="340" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    Why an online CV?
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border-2 border-blue-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No more outdated PDFs</h3>
                        <p class="text-sm text-gray-600">Update your CV once, and everyone sees the latest version. No need to send new PDFs or wonder which version employers are viewing.</p>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-teal-50 rounded-xl border-2 border-cyan-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy sharing</h3>
                        <p class="text-sm text-gray-600">One link works everywhere—email, LinkedIn, social media, business cards. Add it to your email signature and it's always current.</p>
                    </div>

                    <div class="bg-gradient-to-br from-teal-50 to-blue-50 rounded-xl border-2 border-teal-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500 text-white mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Mobile-optimised</h3>
                        <p class="text-sm text-gray-600">Your CV looks great on any device. Employers can view it on their phone, tablet, or desktop—no app needed.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- LinkedIn Integration -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl border-2 border-blue-200 p-8 shadow-lg">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500 text-white">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">LinkedIn Integration</h2>
                    </div>
                    <p class="text-gray-700 mb-4">
                        Add your LinkedIn profile URL to your CV. When you save jobs from LinkedIn using our browser extension, we automatically extract the job title from LinkedIn's page structure—making it even easier to save job listings.
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Add LinkedIn URL to your profile—it appears on your online CV</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Browser extension automatically extracts job titles from LinkedIn job pages</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Share your CV link in your LinkedIn profile or posts</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Explore All Features -->
        <section class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Explore All Features
                </h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    This is just one of many features we offer. Discover everything Simple CV Builder can do for your job search and career development.
                </p>
                <div class="mt-8">
                    <a href="/all-features.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        View All Features
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Get your unique CV link today
                </h2>
                <p class="mt-4 text-blue-50 max-w-xl mx-auto">
                    Every account gets a unique CV link. Create your account, choose your username, and start sharing your always-current CV.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/profile.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Set your username
                        </a>
                        <a href="/cv/@simple-cv-example" target="_blank" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            View example CV
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-blue-600 shadow-lg hover:bg-blue-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/cv/@simple-cv-example" target="_blank" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            View example CV
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php partial('footer'); ?>
    <?php if (!isLoggedIn()): ?>
        <?php partial('auth-modals'); ?>
    <?php endif; ?>
</body>
</html>
