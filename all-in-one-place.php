<?php
/**
 * All in One Place – feature page
 * Describes managing CV and job applications together in a single platform.
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'All in One Place';
$canonicalUrl = APP_URL . '/all-in-one-place.php';
$img = function($id, $w = 800) { return 'https://images.unsplash.com/photo-' . $id . '?w=' . $w . '&q=80'; };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => 'Manage your CV and job applications together in a single platform for maximum efficiency. Build your CV, track applications, and generate tailored CV variants—all in one place.',
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content">
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex flex-col justify-center bg-gradient-to-br from-green-900 via-emerald-900 to-green-900 text-white overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" style="background-image: url('https://images.unsplash.com/photo-1456308015183-dcb539243142?w=1920&q=80');" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-green-900/80 via-emerald-900/80 to-green-900/80" aria-hidden="true"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-flex items-center rounded-full bg-green-500/90 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">Integrated platform</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white">
                    All in One Place
                </h1>
                <p class="mt-6 text-xl text-green-100 max-w-2xl mx-auto leading-relaxed">
                    Manage your CV and job applications together in a <strong class="text-white">single platform</strong> for maximum efficiency. Build, track, and tailor—all seamlessly connected.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/dashboard.php" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-green-700 transition-colors">
                            Open dashboard
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-green-600 px-8 py-3 text-base font-semibold text-white shadow-lg hover:bg-green-700 transition-colors">
                            Create free account
                        </button>
                    <?php endif; ?>
                    <a href="#benefits" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                        See the benefits
                    </a>
                </div>
            </div>
        </section>

        <!-- Why One Platform -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Why manage everything together?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Your CV and job applications are deeply connected. Managing them together saves time, reduces errors, and helps you stay organised throughout your job search.
                    </p>
                </div>

                <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                    <div class="flex flex-col">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Seamless workflow</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            When you find a job you want to apply for, you shouldn't have to switch between different tools or platforms. With Simple CV Builder, you can save the job, review your CV, generate a tailored variant, and track the application—all without leaving the platform.
                        </p>
                        <p class="text-gray-600 leading-relaxed">
                            This seamless workflow means less time switching tabs and copying information, and more time focusing on what matters: crafting strong applications. Everything you need is right where you need it, when you need it.
                        </p>
                    </div>
                    <div class="flex items-center">
                        <img src="/static/images/all-in-one/seamless-workflow.png" alt="Seamless workflow - integrated CV and job application dashboard" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover aspect-video" width="800" height="450" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section id="benefits" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        How everything works together
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                        Your CV and job applications are integrated in ways that make your job search more efficient and effective.
                    </p>
                </div>

                <div class="space-y-20">
                    <!-- Generate CV variants -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="lg:order-2 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Generate CV variants for each application</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                When you save a job application, you can instantly generate a tailored CV variant using free Browser AI. The AI reads the job description, extracts keywords, and creates a version of your CV that emphasises the most relevant experience and skills for that specific role.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Each CV variant stays linked to its application forever, so you always know which version you used for which job. This integration means you can quickly create tailored applications without manually editing your CV each time, saving hours of work.
                            </p>
                        </div>
                        <div class="lg:order-1 flex items-center">
                            <img src="/static/images/all-in-one/cv-variants.png" alt="Generate CV variants for each application - job-specific AI CV creation" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover aspect-video" width="800" height="450" />
                        </div>
                    </div>

                    <!-- Update once, use everywhere -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Update once, use everywhere</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                When you add new work experience, education, or skills to your CV, those updates are immediately available for all your job applications. You don't need to manually update multiple documents or worry about using outdated information.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                This single source of truth means your CV is always current, and all your applications benefit from your latest updates. Whether you're viewing your online CV, generating a PDF, or creating a new variant, everything uses the same up-to-date information.
                            </p>
                        </div>
                        <div class="flex items-center">
                            <img src="/static/images/all-in-one/update-once.png" alt="Update once, use everywhere - single CV source for all applications" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover aspect-video" width="800" height="450" />
                        </div>
                    </div>

                    <!-- Track everything together -->
                    <div class="grid gap-12 lg:grid-cols-2 lg:items-stretch">
                        <div class="lg:order-2 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Track everything together</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                See your CV and all your job applications in one dashboard. View your online CV, manage your applications, generate variants, and track your progress—all from a single place. No need to remember which tool has what information or switch between platforms.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                This unified view helps you stay organised and see the big picture. You can quickly see which applications are active, which CV variants you've created, and how your job search is progressing—all while having easy access to update your CV whenever needed.
                            </p>
                        </div>
                        <div class="lg:order-1 flex items-center">
                            <img src="/static/images/all-in-one/track-everything.png" alt="Track everything together - unified dashboard for CV and applications" class="w-full rounded-xl border border-gray-200 shadow-lg object-cover aspect-video" width="800" height="450" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl text-center mb-12">
                    How the integrated platform works
                </h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-bold text-lg">1</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Build your master CV</h3>
                            <p class="text-gray-600 leading-relaxed">Start by building your comprehensive CV with all your work experience, education, skills, and achievements. This becomes your master CV—the foundation for everything else.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-bold text-lg">2</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Save jobs and generate variants</h3>
                            <p class="text-gray-600 leading-relaxed">When you find a job you want to apply for, save it to your job list. Then generate a tailored CV variant using free Browser AI—the variant is automatically linked to that application and uses your master CV as its source.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-bold text-lg">3</div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Update once, benefit everywhere</h3>
                            <p class="text-gray-600 leading-relaxed">When you update your master CV—adding new experience, skills, or achievements—those updates are immediately available for generating new variants and updating your online CV. Everything stays in sync automatically.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gradient-to-br from-green-600 to-emerald-700">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-2xl font-bold sm:text-3xl">
                    Experience the integrated platform
                </h2>
                <p class="mt-4 text-green-100 max-w-xl mx-auto">
                    Everything you need for your job search is included with every account. Build your CV, track applications, and generate variants—all in one place.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/dashboard.php" class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                            Open dashboard
                        </a>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            All features
                        </a>
                    <?php else: ?>
                        <button type="button" data-open-register class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-semibold text-green-600 shadow-lg hover:bg-green-50 transition-colors">
                            Create free account
                        </button>
                        <a href="/job-applications-features.php" class="inline-flex items-center justify-center rounded-lg border-2 border-white/80 bg-white/10 px-8 py-3 text-base font-semibold text-white hover:bg-white/20 transition-colors">
                            Explore features
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
